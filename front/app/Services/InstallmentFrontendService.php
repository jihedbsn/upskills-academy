<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\OrderSpecifique;
use App\Models\Product;
use App\Models\ProductStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ReferralDiscountService;

class InstallmentFrontendService
{
    protected InstallmentService $installmentService;
    protected StripeService $stripeService;

    public function __construct(InstallmentService $installmentService, StripeService $stripeService)
    {
        $this->installmentService = $installmentService;
        $this->stripeService = $stripeService;
    }

    public function index()
    {
        $student = Auth::user();
        $orders = $this->installmentService->getStudentInstallmentOrders($student->id);

        return view('student-dashboard.pages.installments', compact('orders'));
    }

    public function show($locale, OrderSpecifique $orderSpecifique)
    {
        $student = Auth::user();

        if ($orderSpecifique->student_id !== $student->id) {
            abort(403);
        }

        $orderSpecifique->load(['product', 'productVariation', 'installments', 'lateFees']);

        $contentInfo = $this->installmentService->getUnlockedContent(
            $orderSpecifique->product_id,
            $student->id
        );

        $unlockedStudies = collect();
        $unlockedStudyIds = $contentInfo['unlocked_study_ids'] ?? [];
        if (!empty($unlockedStudyIds)) {
            $unlockedStudies = ProductStudy::whereIn('id', $unlockedStudyIds)->get();
        }

        $order = $orderSpecifique;

        return view('student-dashboard.pages.installment-detail', compact('order', 'unlockedStudies'));
    }

    public function paymentOptions($locale, $orderSpecifique)
    {
        $student = Auth::user();
        $order = OrderSpecifique::with(['product', 'installments'])->findOrFail($orderSpecifique);

        if ($order->student_id != $student->id) {
            abort(403);
        }

        $nextInstallment = $order->installments
            ->whereIn('status', ['pending', 'overdue'])
            ->sortBy('installment_number')
            ->first();

        if (!$nextInstallment) {
            return redirect()->route('student-dashboard.installments.show', ['locale' => $locale, 'orderSpecifique' => $order->id])
                ->with('info', __('messages.all_installments_paid', ['default' => 'All installments are already paid.']));
        }

        $amountDue = (float) $nextInstallment->amount + (float) ($nextInstallment->late_fee ?? 0);

        return view('student-dashboard.pages.installment-payment-options', compact('order', 'nextInstallment', 'amountDue'));
    }

    public function checkout($locale, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', __('messages.login_required'));
        }

        $student = Auth::user();

        if (!$product->allowsInstallments()) {
            return redirect()->back()->with('error', __('messages.installment_not_available'));
        }

        $existingOrder = OrderSpecifique::where('student_id', $student->id)
            ->where('product_id', $product->id)
            ->whereIn('status', ['pending', 'partial'])
            ->first();

        if ($existingOrder) {
            return redirect()->route('student-dashboard.installments.show', [
                'locale' => $locale,
                'orderSpecifique' => $existingOrder->id,
            ])->with('info', __('messages.installment_order_exists'));
        }

        if ($product->hasStudentAccess($student->id)) {
            return redirect()->back()->with('info', __('messages.already_enrolled'));
        }

        $installmentCount = $product->installment_count;
        $originalTotal = (float) $product->prix;
        $originalInstallmentPrice = (float) $product->installment_price;

        $referralPct = ReferralDiscountService::percentFor($student->id);
        $referralDiscount = ReferralDiscountService::amountFor($student->id, $originalTotal);
        $factor = 1 - $referralPct / 100;

        $totalPrice = round($originalTotal - $referralDiscount, 2);
        $installmentPrice = $referralPct > 0
            ? round($originalInstallmentPrice * $factor, 2)
            : $originalInstallmentPrice;

        return view('student-dashboard.pages.installment-checkout', compact(
            'product', 'installmentCount', 'installmentPrice', 'totalPrice',
            'originalTotal', 'originalInstallmentPrice', 'referralDiscount', 'referralPct'
        ));
    }

    public function checkoutStartPayment(Request $request, $locale, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', __('messages.login_required'));
        }

        $student = Auth::user();

        if (!$product->allowsInstallments()) {
            return redirect()->back()->with('error', __('messages.installment_not_available'));
        }

        $existingOrder = OrderSpecifique::where('student_id', $student->id)
            ->where('product_id', $product->id)
            ->whereIn('status', ['pending', 'partial'])
            ->first();

        if ($existingOrder) {
            return redirect()->route('student-dashboard.installments.payment-options', [
                'locale' => $locale,
                'orderSpecifique' => $existingOrder->id,
            ])->with('info', __('messages.installment_order_exists', ['default' => 'You already have an installment plan for this course.']));
        }

        if ($product->hasStudentAccess($student->id)) {
            return redirect()->back()->with('info', __('messages.already_enrolled', ['default' => 'You are already enrolled in this course.']));
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:stripe,cash_on_delivery,bank_transfer',
        ]);

        try {
            $order = $this->installmentService->createInstallmentOrder(
                studentId: $student->id,
                productId: $product->id,
                totalPrice: round(((float) ($product->prix ?? $product->price)) * (1 - ReferralDiscountService::percentFor($student->id) / 100), 2)
            );

            if ($validated['payment_method'] === 'bank_transfer') {
                $url = route('student-dashboard.installments.payment-options', [
                    'locale' => $locale,
                    'orderSpecifique' => $order->id,
                ]);

                return redirect()->to($url . '#installment-bank-transfer');
            }

            $request->merge(['payment_method' => $validated['payment_method']]);

            return $this->payInstallment($request, $locale, $order->id);
        } catch (\Exception $e) {
            Log::error('Error starting installment checkout payment: ' . $e->getMessage());

            return redirect()->back()->with('error', __('messages.checkout_error', ['default' => 'Error processing checkout. Please try again.']));
        }
    }

    public function processCheckout(Request $request, $locale, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', __('messages.login_required'));
        }

        $student = Auth::user();

        if (!$product->allowsInstallments()) {
            return redirect()->back()->with('error', __('messages.installment_not_available'));
        }

        $existingOrder = OrderSpecifique::where('student_id', $student->id)
            ->where('product_id', $product->id)
            ->whereIn('status', ['pending', 'partial'])
            ->first();

        if ($existingOrder) {
            return redirect()->route('student-dashboard.installments.payment-options', [
                'locale' => $locale,
                'orderSpecifique' => $existingOrder->id,
            ])->with('info', __('messages.installment_order_exists', ['default' => 'You already have an installment plan for this course.']));
        }

        if ($product->hasStudentAccess($student->id)) {
            return redirect()->back()->with('info', __('messages.already_enrolled', ['default' => 'You are already enrolled in this course.']));
        }

        try {
            $order = $this->installmentService->createInstallmentOrder(
                studentId: $student->id,
                productId: $product->id,
                totalPrice: round(((float) ($product->prix ?? $product->price)) * (1 - ReferralDiscountService::percentFor($student->id) / 100), 2)
            );

            return redirect()->route('student-dashboard.installments.payment-options', [
                'locale' => $locale,
                'orderSpecifique' => $order->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing installment checkout: ' . $e->getMessage());

            return redirect()->back()->with('error', __('messages.checkout_error', ['default' => 'Error processing checkout. Please try again.']));
        }
    }

    public function paymentSuccess(Request $request)
    {
        $student = Auth::user();
        $orderId = $request->query('order_id');
        $order = OrderSpecifique::findOrFail($orderId);

        if ($order->student_id !== $student->id) {
            abort(403);
        }

        try {
            $nextInstallment = $order->installments()
                ->whereIn('status', ['pending', 'overdue'])
                ->orderBy('installment_number')
                ->first();

            if ($nextInstallment) {
                $this->installmentService->processFirstInstallment($order, $nextInstallment);
            }

            return redirect()->route('student-dashboard.installments.show', $order->id)
                ->with('success', __('messages.payment_successful', ['default' => 'Payment processed successfully!']));
        } catch (\Exception $e) {
            Log::error('Error in installment payment success handler: ' . $e->getMessage());

            return redirect()->route('student-dashboard.installments.show', $order->id)
                ->with('error', __('messages.payment_processing_error', ['default' => 'Error processing payment. Contact support.']));
        }
    }

    public function paymentCancel(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = OrderSpecifique::findOrFail($orderId);

        if ($order->student_id !== Auth::id()) {
            abort(403);
        }

        return redirect()->route('student-dashboard.installments.show', $order->id)
            ->with('info', __('messages.payment_cancelled', ['default' => 'Payment was cancelled.']));
    }

    public function payInstallment(Request $request, $locale, $orderSpecifique)
    {
        $student = Auth::user();

        $order = OrderSpecifique::find($orderSpecifique);

        if (!$order) {
            Log::warning('Installment order not found', ['order_id' => $orderSpecifique]);
            abort(404);
        }

        if ($order->student_id != $student->id) {
            Log::warning('Installment payment forbidden', [
                'order_student_id' => $order->student_id,
                'auth_student_id' => $student->id,
                'order_id' => $order->id,
            ]);
            abort(403);
        }

        $nextInstallment = $order->installments()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('installment_number')
            ->first();

        if (!$nextInstallment) {
            return redirect()->back()->with('info', __('messages.all_installments_paid', ['default' => 'All installments are already paid.']));
        }

        $paymentMethod = $request->input('payment_method');

        if ($paymentMethod === null || $paymentMethod === '') {
            return redirect()->back()->with('error', __('messages.choose_payment_method', ['default' => 'Please choose a payment method.']));
        }

        if ($paymentMethod === 'bank_transfer') {
            return $this->processInstallmentBankTransfer($request, $order, $nextInstallment, $student);
        }

        if ($paymentMethod === 'cash_on_delivery') {
            return $this->processInstallmentCashPayment($order, $nextInstallment, $student);
        }

        try {
            $product = $order->product;
            $amountDue = (float) $nextInstallment->amount + (float) ($nextInstallment->late_fee ?? 0);

            $items = [
                [
                    'name' => ($product->titre ?? $product->title) . ' - ' . __('messages.installment', ['default' => 'Installment']) . " {$nextInstallment->installment_number}/{$order->total_installments}",
                    'price' => $amountDue,
                    'quantity' => 1,
                ],
            ];

            $metadata = [
                'student_id' => $student->id,
                'order_specifique_id' => $order->id,
                'installment_id' => $nextInstallment->id,
                'installment_number' => $nextInstallment->installment_number,
                'type' => 'installment_payment',
                'customer_email' => $student->email,
            ];

            $successUrl = route('student-dashboard.installments.payment-success', [
                'locale' => $locale,
            ]) . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id;

            $cancelUrl = route('student-dashboard.installments.payment-cancel', [
                'locale' => $locale,
            ]) . '?order_id=' . $order->id;

            $session = $this->stripeService->createCheckoutSession($items, $metadata, $successUrl, $cancelUrl, 'usd');

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Error processing installment payment: ' . $e->getMessage());

            return redirect()->back()->with('error', __('messages.payment_error', ['default' => 'Payment processing error. Please try again.']));
        }
    }

    public function suspended()
    {
        $student = Auth::user();

        $suspendedOrders = OrderSpecifique::where('student_id', $student->id)
            ->where('is_suspended', true)
            ->with(['product', 'installments' => function ($query) {
                $query->where('status', 'overdue');
            }])
            ->get();

        return view('student-dashboard.pages.suspended', compact('suspendedOrders'));
    }

    protected function processInstallmentBankTransfer(Request $request, OrderSpecifique $order, $installment, $student)
    {
        try {
            $receiptPath = null;
            if ($request->hasFile('payment_receipt')) {
                $file = $request->file('payment_receipt');
                $fileName = 'installment_' . $order->id . '_' . $installment->installment_number . '_' . time() . '.' . $file->getClientOriginalExtension();
                $receiptPath = $file->storeAs('installment_receipts', $fileName, 'public');
            }

            $installment->update([
                'payment_method' => 'bank_transfer',
                'status' => 'awaiting_payment',
                'payment_receipt' => $receiptPath,
                'notes' => $request->input('payment_description'),
            ]);

            $product = $order->product;
            $amountDue = (float) $installment->amount + (float) ($installment->late_fee ?? 0);

            $adminBase = rtrim((string) config('app.admin_url', env('ADMIN_URL', '')), '/');
            $installmentAdminUrl = $adminBase . '/order-specifiques/installments/' . $installment->id . '/detail';

            Notification::notifyAllAdmins(
                Notification::TYPE_INSTALLMENT,
                'New Installment Bank Transfer Request',
                "Student {$student->first_name} {$student->last_name} submitted bank transfer for installment #{$installment->installment_number} of " . ($product->titre ?? $product->title) . ' - Amount: $' . number_format($amountDue, 2) . ($receiptPath ? ' (Receipt uploaded)' : ''),
                $installmentAdminUrl,
                ['order_id' => $order->id, 'installment_id' => $installment->id, 'amount' => $amountDue, 'receipt' => $receiptPath],
                null,
                'warning'
            );

            Log::info('Bank transfer submitted for installment', [
                'student_id' => $student->id,
                'order_id' => $order->id,
                'installment_id' => $installment->id,
                'amount' => $amountDue,
                'receipt' => $receiptPath,
            ]);

            return redirect()->route('student-dashboard.installments.show', ['locale' => app()->getLocale(), 'orderSpecifique' => $order->id])
                ->with('success', __('messages.bank_transfer_installment_success', ['default' => 'Bank transfer submitted successfully. Please wait for admin confirmation.']));
        } catch (\Exception $e) {
            Log::error('Error processing installment bank transfer: ' . $e->getMessage());

            return redirect()->back()->with('error', __('messages.payment_error', ['default' => 'Payment processing error. Please try again.']));
        }
    }

    protected function processInstallmentCashPayment(OrderSpecifique $order, $installment, $student)
    {
        try {
            $installment->update([
                'payment_method' => 'cash_on_delivery',
                'status' => 'awaiting_payment',
            ]);

            $product = $order->product;
            $amountDue = (float) $installment->amount + (float) ($installment->late_fee ?? 0);

            $adminBase = rtrim((string) config('app.admin_url', env('ADMIN_URL', '')), '/');
            $installmentAdminUrl = $adminBase . '/order-specifiques/installments/' . $installment->id . '/detail';

            Notification::notifyAllAdmins(
                Notification::TYPE_INSTALLMENT,
                'New Installment Cash Payment Request',
                "Student {$student->first_name} {$student->last_name} requested cash payment for installment #{$installment->installment_number} of " . ($product->titre ?? $product->title) . ' - Amount: $' . number_format($amountDue, 2),
                $installmentAdminUrl,
                ['order_id' => $order->id, 'installment_id' => $installment->id, 'amount' => $amountDue],
                null,
                'warning'
            );

            Log::info('Cash payment requested for installment', [
                'student_id' => $student->id,
                'order_id' => $order->id,
                'installment_id' => $installment->id,
                'amount' => $amountDue,
            ]);

            return redirect()->route('student-dashboard.installments.show', $order->id)
                ->with('success', __('messages.cash_payment_installment_success', ['default' => 'Cash payment request submitted. Please visit our office to complete the payment.']));
        } catch (\Exception $e) {
            Log::error('Error processing installment cash payment: ' . $e->getMessage());

            return redirect()->back()->with('error', __('messages.payment_error', ['default' => 'Payment processing error. Please try again.']));
        }
    }
}
