<?php

namespace App\Services;

use App\Models\CourseExtensionOrder;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseExtensionService
{
    protected StripeService $stripeService;
    protected CurrencyService $currencyService;

    public function __construct(StripeService $stripeService, CurrencyService $currencyService)
    {
        $this->stripeService = $stripeService;
        $this->currencyService = $currencyService;
    }

    public function index()
    {
        $student = Auth::user();

        $enrolledCourses = ProductStudent::where('student_id', $student->id)
            ->with(['product.variations', 'product.teacher'])
            ->whereNotNull('expiration_date')
            ->orderBy('expiration_date', 'asc')
            ->get();

        $expiredCourses = collect();
        $expiringCourses = collect();
        $activeCourses = collect();

        foreach ($enrolledCourses as $productStudent) {
            $isExpired = $productStudent->is_expired || ($productStudent->expiration_date && $productStudent->expiration_date->isPast());

            if ($isExpired) {
                $expiredCourses->push($productStudent);
                continue;
            }

            if (!$productStudent->expiration_date || !$productStudent->expiration_date->isFuture()) {
                continue;
            }

            if ($productStudent->expiration_date->diffInDays(now()) <= 30) {
                $expiringCourses->push($productStudent);
            } else {
                $activeCourses->push($productStudent);
            }
        }

        $extensionOrders = CourseExtensionOrder::where('student_id', $student->id)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('student-dashboard.pages.course-extensions', compact(
            'expiredCourses',
            'expiringCourses',
            'activeCourses',
            'extensionOrders'
        ));
    }

    public function checkout(string $locale, Product $product)
    {
        $student = Auth::user();

        $productStudent = ProductStudent::where('student_id', $student->id)
            ->where('product_id', $product->id)
            ->first();

        if (!$productStudent) {
            return redirect()->route('student-dashboard.course-extensions.index')
                ->with('error', __('student_dashboard.ext_not_enrolled'));
        }

        if (!$product->validity_months || !$product->renewal_price) {
            return redirect()->route('student-dashboard.course-extensions.index')
                ->with('error', __('student_dashboard.ext_not_available'));
        }

        $pendingOrder = CourseExtensionOrder::where('student_id', $student->id)
            ->where('product_id', $product->id)
            ->where('payment_status', 'pending')
            ->first();

        if ($pendingOrder) {
            return redirect()->route('student-dashboard.course-extensions.index')
                ->with('warning', __('student_dashboard.ext_pending_exists'));
        }

        $extensionPrice = $product->renewal_price;
        $extensionMonths = $product->validity_months;

        return view('student-dashboard.pages.course-extension-checkout', compact(
            'product',
            'productStudent',
            'extensionPrice',
            'extensionMonths'
        ));
    }

    public function processPayment(Request $request, string $locale, Product $product)
    {
        $student = Auth::user();

        $request->validate([
            'payment_method' => 'required|in:credit_card,bank_transfer,cash_on_delivery',
            'payment_description' => 'nullable|string|max:1000',
            'payment_receipt' => 'required_if:payment_method,bank_transfer|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $productStudent = ProductStudent::where('student_id', $student->id)
            ->where('product_id', $product->id)
            ->first();

        if (!$productStudent || !$product->validity_months || !$product->renewal_price) {
            return redirect()->route('student-dashboard.course-extensions.index')
                ->with('error', __('student_dashboard.ext_not_available'));
        }

        $extensionPrice = $product->renewal_price;
        $extensionMonths = $product->validity_months;

        if ($request->payment_method === 'credit_card') {
            return $this->processStripeExtension($product, $productStudent, (float) $extensionPrice, (int) $extensionMonths);
        }

        if ($request->payment_method === 'cash_on_delivery') {
            return $this->processCashOnDeliveryExtension($request, $product, $productStudent, (float) $extensionPrice, (int) $extensionMonths);
        }

        return $this->processBankTransferExtension($request, $product, $productStudent, (float) $extensionPrice, (int) $extensionMonths);
    }

    public function stripeSuccess(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('student-dashboard.course-extensions.index')
                ->with('error', __('student_dashboard.ext_invalid_session'));
        }

        try {
            $stripeSession = $this->stripeService->retrieveSession($sessionId);

            if ($stripeSession->payment_status !== 'paid') {
                return redirect()->route('student-dashboard.course-extensions.index')
                    ->with('error', __('student_dashboard.ext_payment_not_completed'));
            }

            $pendingData = session('pending_extension_data');
            if (!$pendingData) {
                return redirect()->route('student-dashboard.course-extensions.index')
                    ->with('error', __('student_dashboard.ext_data_not_found'));
            }

            DB::beginTransaction();

            try {
                $student = Auth::user();
                $product = Product::find($pendingData['product_id']);

                $extensionOrder = CourseExtensionOrder::create([
                    'student_id' => $student->id,
                    'product_id' => $pendingData['product_id'],
                    'price' => $pendingData['price'],
                    'payment_method' => 'credit_card',
                    'payment_status' => 'approved',
                    'payment_success' => true,
                    'stripe_session_id' => $sessionId,
                    'stripe_payment_intent' => $stripeSession->payment_intent,
                    'extension_months' => $pendingData['extension_months'],
                    'old_expiration_date' => $pendingData['old_expiration_date'],
                ]);

                $extensionOrder->applyExtension();

                Notification::create([
                    'notifiable_type' => 'App\Models\Student',
                    'notifiable_id' => $student->id,
                    'type' => 'course_extension',
                    'title' => 'Course Extended',
                    'message' => 'Your access to "' . ($product->titre ?? 'Course') . '" has been extended by ' . $pendingData['extension_months'] . ' months.',
                    'data' => json_encode(['extension_order_id' => $extensionOrder->id]),
                    'is_read' => false,
                ]);

                DB::commit();

                session()->forget('pending_extension_data');

                return redirect()->route('student-dashboard.course-extensions.index')
                    ->with('success', __('student_dashboard.ext_success'));
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Extension processing error', ['error' => $e->getMessage()]);

                return redirect()->route('student-dashboard.course-extensions.index')
                    ->with('error', __('student_dashboard.ext_processing_error'));
            }
        } catch (\Exception $e) {
            Log::error('Stripe extension session error', ['error' => $e->getMessage()]);

            return redirect()->route('student-dashboard.course-extensions.index')
                ->with('error', __('student_dashboard.ext_payment_error'));
        }
    }

    public function stripeCancel()
    {
        session()->forget('pending_extension_data');

        return redirect()->route('student-dashboard.course-extensions.index')
            ->with('warning', __('student_dashboard.ext_payment_cancelled'));
    }

    protected function processStripeExtension(Product $product, ProductStudent $productStudent, float $price, int $months)
    {
        try {
            $student = Auth::user();

            $selectedCurrency = $this->currencyService->getSelectedCurrency();
            $stripeCurrency = strtolower($selectedCurrency->code);
            $convertedPrice = $this->currencyService->convert($price, $selectedCurrency->code);

            $lineItems = [[
                'name' => __('student_dashboard.ext_renewal_for') . ' ' . $product->titre,
                'description' => __('student_dashboard.ext_extend_by_months', ['months' => $months]),
                'price' => $convertedPrice,
                'quantity' => 1,
            ]];

            $metadata = [
                'student_id' => $student->id,
                'customer_email' => $student->email,
                'product_id' => $product->id,
                'extension_months' => $months,
                'type' => 'course_extension',
            ];

            session(['pending_extension_data' => [
                'product_id' => $product->id,
                'price' => $price,
                'extension_months' => $months,
                'old_expiration_date' => $productStudent->expiration_date,
                'display_currency' => $selectedCurrency->code,
            ]]);

            $successUrl = route('student-dashboard.course-extensions.stripe-success', ['locale' => app()->getLocale()]) . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = route('student-dashboard.course-extensions.stripe-cancel', ['locale' => app()->getLocale()]);

            $session = $this->stripeService->createCheckoutSession(
                $lineItems,
                $metadata,
                $successUrl,
                $cancelUrl,
                $stripeCurrency
            );

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Stripe extension checkout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'student_id' => Auth::id(),
                'product_id' => $product->id,
                'currency' => $stripeCurrency ?? 'unknown',
                'price' => $convertedPrice ?? $price,
            ]);

            return redirect()->route('student-dashboard.course-extensions.index')
                ->with('error', __('student_dashboard.ext_payment_error') . ' (' . $e->getMessage() . ')');
        }
    }

    protected function processCashOnDeliveryExtension(Request $request, Product $product, ProductStudent $productStudent, float $price, int $months)
    {
        try {
            $student = Auth::user();

            CourseExtensionOrder::create([
                'student_id' => $student->id,
                'product_id' => $product->id,
                'price' => $price,
                'payment_method' => 'cash_on_delivery',
                'payment_status' => 'pending',
                'payment_success' => false,
                'payment_description' => $request->payment_description,
                'extension_months' => $months,
                'old_expiration_date' => $productStudent->expiration_date,
                'notes' => $request->notes,
            ]);

            Notification::create([
                'notifiable_type' => 'App\Models\Student',
                'notifiable_id' => $student->id,
                'type' => 'course_extension',
                'title' => 'Extension Request Submitted',
                'message' => 'Your cash on delivery extension request for "' . ($product->titre ?? 'Course') . '" has been submitted and is pending admin approval.',
                'data' => json_encode(['product_id' => $product->id]),
                'is_read' => false,
            ]);

            return redirect()->route('student-dashboard.course-extensions.index')
                ->with('success', __('student_dashboard.ext_cash_on_delivery_submitted'));
        } catch (\Exception $e) {
            Log::error('Cash on delivery extension error', [
                'error' => $e->getMessage(),
                'student_id' => Auth::id(),
                'product_id' => $product->id,
            ]);

            return redirect()->route('student-dashboard.course-extensions.index')
                ->with('error', __('student_dashboard.ext_payment_error'));
        }
    }

    protected function processBankTransferExtension(Request $request, Product $product, ProductStudent $productStudent, float $price, int $months)
    {
        try {
            $student = Auth::user();

            $receiptPath = null;
            if ($request->hasFile('payment_receipt')) {
                $receiptPath = $request->file('payment_receipt')->store('extension-receipts', 'public');
            }

            CourseExtensionOrder::create([
                'student_id' => $student->id,
                'product_id' => $product->id,
                'price' => $price,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'pending',
                'payment_success' => false,
                'payment_receipt' => $receiptPath,
                'payment_description' => $request->payment_description,
                'extension_months' => $months,
                'old_expiration_date' => $productStudent->expiration_date,
                'notes' => $request->notes,
            ]);

            Notification::create([
                'notifiable_type' => 'App\Models\Student',
                'notifiable_id' => $student->id,
                'type' => 'course_extension',
                'title' => 'Extension Request Submitted',
                'message' => 'Your extension request for "' . ($product->titre ?? 'Course') . '" has been submitted and is pending admin approval.',
                'data' => json_encode(['product_id' => $product->id]),
                'is_read' => false,
            ]);

            return redirect()->route('student-dashboard.course-extensions.index')
                ->with('success', __('student_dashboard.ext_bank_transfer_submitted'));
        } catch (\Exception $e) {
            Log::error('Bank transfer extension error', [
                'error' => $e->getMessage(),
                'student_id' => Auth::id(),
                'product_id' => $product->id,
            ]);

            return redirect()->route('student-dashboard.course-extensions.index')
                ->with('error', __('student_dashboard.ext_payment_error'));
        }
    }
}
