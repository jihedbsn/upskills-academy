<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStudent;
use App\Models\Referral;
use App\Models\ReferralReward;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    protected PointsService $pointsService;

    public function __construct(PointsService $pointsService)
    {
        $this->pointsService = $pointsService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['student', 'product', 'book']);

        if ($request->filled('status')) {
            $query->where('payment_success', $request->status);
        }

        if ($request->filled('type')) {
            if ($request->type === 'course') {
                $query->whereNotNull('product_id');
            } elseif ($request->type === 'book') {
                $query->whereNotNull('book_id');
            }
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('student')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->student . '%')
                    ->orWhere('last_name', 'like', '%' . $request->student . '%')
                    ->orWhere('email', 'like', '%' . $request->student . '%');
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => Order::count(),
            'paid' => Order::where('payment_success', true)->count(),
            'pending' => Order::where('payment_success', false)->count(),
            'revenue' => Order::where('payment_success', true)->sum('price'),
            'orphaned' => Order::whereDoesntHave('student')->count(),
        ];

        return view('orders.index', compact('orders', 'stats'));
    }

    public function create()
    {
        $students = Student::all();
        $products = Product::all();
        $books = Book::all();

        return view('orders.create', compact('students', 'products', 'books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'product_id' => 'nullable|exists:products,id',
            'book_id' => 'nullable|exists:books,id',
            'price' => 'required|numeric|min:0',
            'payment_success' => 'boolean',
        ]);

        if (!$request->product_id && !$request->book_id) {
            return back()->withErrors(['error' => 'Veuillez sélectionner un produit ou un livre.']);
        }

        if ($request->product_id) {
            $existingPurchase = ProductStudent::where('product_id', $request->product_id)
                ->where('student_id', $request->student_id)
                ->exists();
            if ($existingPurchase) {
                return back()->withErrors(['error' => 'Cet étudiant a déjà acheté ce cours.']);
            }
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'student_id' => $request->student_id,
                'product_id' => $request->product_id,
                'book_id' => $request->book_id,
                'price' => $request->price,
                'payment_success' => $request->payment_success ?? false,
            ]);

            if ($order->payment_success && $order->product_id) {
                ProductStudent::create([
                    'product_id' => $order->product_id,
                    'student_id' => $order->student_id,
                    'date' => now(),
                ]);
            }

            $itemName = $order->product ? $order->product->title : ($order->book ? $order->book->title : 'Item');
            $student = $order->student;
            $studentName = $student ? $student->first_name . ' ' . $student->last_name : 'Unknown';

            Notification::notifyAllAdmins(
                Notification::TYPE_PURCHASE,
                'New Purchase',
                "New order from {$studentName} for {$itemName}",
                route('admin.orders.show', $order->id),
                ['order_id' => $order->id, 'price' => $order->price],
                '🛒',
                'green'
            );

            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Commande créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la création de la commande.']);
        }
    }

    public function show(Order $order)
    {
        $order->load(['student', 'product', 'book']);

        $referralInfo = $this->buildReferralInfo($order);

        return view('orders.show', compact('order', 'referralInfo'));
    }

    /**
     * Compute referral-related discount info for an order:
     *  - referredDiscount: 5% off applied because this is the referred student's first purchase.
     *  - creditApplied: total referral credit consumed against this order by a referrer.
     */
    protected function buildReferralInfo(Order $order): array
    {
        $info = [
            'isReferredFirstPurchase' => false,
            'referredDiscount'        => 0.0,
            'referrerName'            => null,
            'creditApplied'           => 0.0,
            'referralId'              => null,
        ];

        $referral = Referral::where('completed_order_id', $order->id)->first();
        if ($referral) {
            $originalPrice = $order->product ? (float) $order->product->prix
                : ($order->book ? (float) $order->book->prix : (float) $order->price);
            $info['isReferredFirstPurchase'] = true;
            $info['referredDiscount']        = round($originalPrice * 0.05, 2);
            $info['referralId']              = $referral->id;

            $referrer = Student::find($referral->referrer_id);
            if ($referrer) {
                $name = trim(($referrer->first_name ?? '') . ' ' . ($referrer->last_name ?? ''));
                $info['referrerName'] = $name !== '' ? $name : $referrer->email;
            }
        }

        $info['creditApplied'] = (float) ReferralReward::where('spent_order_id', $order->id)->sum('amount');

        return $info;
    }

    public function edit(Order $order)
    {
        $students = Student::all();
        $products = Product::all();
        $books = Book::all();

        return view('orders.edit', compact('order', 'students', 'products', 'books'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'product_id' => 'nullable|exists:products,id',
            'book_id' => 'nullable|exists:books,id',
            'price' => 'required|numeric|min:0',
            'payment_success' => 'boolean',
        ]);

        if (!$request->product_id && !$request->book_id) {
            return back()->withErrors(['error' => 'Veuillez sélectionner un produit ou un livre.']);
        }

        DB::beginTransaction();
        try {
            $oldPaymentStatus = $order->payment_success;
            $oldProductId = $order->product_id;

            $order->update([
                'student_id' => $request->student_id,
                'product_id' => $request->product_id,
                'book_id' => $request->book_id,
                'price' => $request->price,
                'payment_success' => $request->payment_success ?? false,
            ]);

            if ($oldProductId && $order->product_id) {
                if (!$oldPaymentStatus && $order->payment_success) {
                    ProductStudent::firstOrCreate([
                        'product_id' => $order->product_id,
                        'student_id' => $order->student_id,
                    ], [
                        'date' => now(),
                    ]);
                } elseif ($oldPaymentStatus && !$order->payment_success) {
                    ProductStudent::where('product_id', $order->product_id)
                        ->where('student_id', $order->student_id)
                        ->delete();
                }
            }

            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Commande mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour de la commande.']);
        }
    }

    public function destroy(Order $order)
    {
        DB::beginTransaction();
        try {
            if ($order->payment_success && $order->product_id) {
                ProductStudent::where('product_id', $order->product_id)
                    ->where('student_id', $order->student_id)
                    ->delete();
            }

            $order->delete();
            DB::commit();

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression.']);
            }

            return redirect()->route('admin.orders.index')->with('error', 'Error deleting order.');
        }
    }

    public function togglePayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_success' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $order->payment_success;
            $order->update(['payment_success' => $request->payment_success]);

            if ($order->product_id) {
                if (!$oldStatus && $request->payment_success) {
                    ProductStudent::firstOrCreate([
                        'product_id' => $order->product_id,
                        'student_id' => $order->student_id,
                    ], [
                        'date' => now(),
                    ]);
                } elseif ($oldStatus && !$request->payment_success) {
                    ProductStudent::where('product_id', $order->product_id)
                        ->where('student_id', $order->student_id)
                        ->delete();
                }
            }

            // Trigger referral reward if payment was just flipped to success
            if (!$oldStatus && $request->payment_success) {
                try {
                    app(\App\Services\ReferralService::class)
                        ->processFirstPurchaseReward($order->student_id, $order->id);
                } catch (\Exception $e) {
                    Log::error('Referral reward processing on togglePayment failed: ' . $e->getMessage());
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erreur lors de la modification du statut.']);
        }
    }

    public function approvePayment(Order $order)
    {
        DB::beginTransaction();
        try {
            $order->update([
                'payment_status' => 'approved',
                'payment_success' => true,
            ]);

            $this->processPointsForApprovedOrder($order);

            if ($order->product_id) {
                $product = Product::find($order->product_id);

                $expirationDate = null;
                if ($product && $product->validity_months) {
                    $expirationDate = now()->addMonths($product->validity_months);
                }

                ProductStudent::updateOrCreate(
                    [
                        'product_id' => $order->product_id,
                        'student_id' => $order->student_id,
                    ],
                    [
                        'date' => now(),
                        'is_active' => true,
                        'access_granted_at' => now(),
                        'expiration_date' => $expirationDate,
                        'is_expired' => false
                    ]
                );
            }

            $relatedOrders = Order::where('student_id', $order->student_id)
                ->where('payment_method', $order->payment_method)
                ->where('payment_status', 'pending')
                ->where(function ($query) use ($order) {
                    if ($order->payment_receipt) {
                        $query->where('payment_receipt', $order->payment_receipt);
                    } else {
                        $query->whereNull('payment_receipt');
                    }
                })
                ->where('id', '!=', $order->id)
                ->get();

            /** @var \Illuminate\Database\Eloquent\Collection<int, Order> $relatedOrders */
            foreach ($relatedOrders as $relatedOrder) {
                $relatedOrder->update([
                    'payment_status' => 'approved',
                    'payment_success' => true,
                ]);

                if ($relatedOrder->product_id) {
                    $product = Product::find($relatedOrder->product_id);

                    $expirationDate = null;
                    if ($product && $product->validity_months) {
                        $expirationDate = now()->addMonths($product->validity_months);
                    }

                    ProductStudent::updateOrCreate(
                        [
                            'product_id' => $relatedOrder->product_id,
                            'student_id' => $relatedOrder->student_id,
                        ],
                        [
                            'date' => now(),
                            'is_active' => true,
                            'access_granted_at' => now(),
                            'expiration_date' => $expirationDate,
                            'is_expired' => false
                        ]
                    );
                }
            }

            $student = $order->student;
            if ($student) {
                Notification::notifyStudent(
                    $student->id,
                    Notification::TYPE_PURCHASE,
                    'Payment Approved',
                    'Your payment has been approved. You now have access to your courses.',
                    env('USER_URL') . '/student-dashboard/orders',
                    ['order_id' => $order->id],
                    null,
                    'success',
                    true
                );

                try {
                    Mail::to($student->email)->send(
                        new \App\Mail\PaymentApprovedEmail($order, $student)
                    );

                    \App\Models\EmailLog::logSent(
                        $student->email,
                        'payment_approved',
                        'Payment Approved - Order #' . $order->id,
                        $student->id,
                        ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                        'Order',
                        $order->id
                    );
                } catch (\Exception $e) {
                    Log::error('Payment approval email error: ' . $e->getMessage());

                    \App\Models\EmailLog::logFailed(
                        $student->email,
                        'payment_approved',
                        'Payment Approved - Order #' . $order->id,
                        $e->getMessage(),
                        $student->id,
                        ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                        'Order',
                        $order->id
                    );
                }
            }

            // Trigger referral reward if this is the referred student's first
            // successful purchase after being referred (no-op otherwise)
            try {
                app(\App\Services\ReferralService::class)
                    ->processFirstPurchaseReward($order->student_id, $order->id);
            } catch (\Exception $e) {
                Log::error('Referral reward processing on order approval failed: ' . $e->getMessage());
            }

            DB::commit();
            return redirect()->back()->with('success', 'Payment approved and course access granted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error approving payment: ' . $e->getMessage());
        }
    }

    public function rejectPayment(Request $request, Order $order)
    {
        $request->validate([
            'rejection_comment' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $order->update([
                'payment_status' => 'rejected',
                'payment_success' => false,
                'rejection_comment' => $request->rejection_comment,
            ]);

            $relatedOrders = Order::where('student_id', $order->student_id)
                ->where('payment_method', $order->payment_method)
                ->where('payment_status', 'pending')
                ->where(function ($query) use ($order) {
                    if ($order->payment_receipt) {
                        $query->where('payment_receipt', $order->payment_receipt);
                    } else {
                        $query->whereNull('payment_receipt');
                    }
                })
                ->where('id', '!=', $order->id)
                ->get();

            /** @var \Illuminate\Database\Eloquent\Collection<int, Order> $relatedOrders */
            foreach ($relatedOrders as $relatedOrder) {
                $relatedOrder->update([
                    'payment_status' => 'rejected',
                    'payment_success' => false,
                    'rejection_comment' => $request->rejection_comment,
                ]);
            }

            $student = $order->student;
            if ($student) {
                Notification::notifyStudent(
                    $student->id,
                    Notification::TYPE_PURCHASE,
                    'Payment Rejected',
                    'Your payment has been rejected. Reason: ' . $request->rejection_comment,
                    env('USER_URL') . '/student-dashboard/orders',
                    ['order_id' => $order->id],
                    null,
                    'danger',
                    true
                );

                try {
                    Mail::to($student->email)->send(
                        new \App\Mail\PaymentRejectedEmail($order, $student, $request->rejection_comment)
                    );

                    \App\Models\EmailLog::logSent(
                        $student->email,
                        'payment_rejected',
                        'Payment Rejected - Order #' . $order->id,
                        $student->id,
                        ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                        'Order',
                        $order->id
                    );
                } catch (\Exception $e) {
                    Log::error('Payment rejection email error: ' . $e->getMessage());

                    \App\Models\EmailLog::logFailed(
                        $student->email,
                        'payment_rejected',
                        'Payment Rejected - Order #' . $order->id,
                        $e->getMessage(),
                        $student->id,
                        ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                        'Order',
                        $order->id
                    );
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Payment rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error rejecting payment: ' . $e->getMessage());
        }
    }

    public function downloadReceipt(Order $order)
    {
        if (!$order->payment_receipt) {
            abort(404, 'Receipt not found');
        }

        $userStoragePath = dirname(dirname(dirname(__DIR__))) . '/../user/storage/app/public/';
        $filePath = $userStoragePath . $order->payment_receipt;

        if (!file_exists($filePath)) {
            abort(404, 'Receipt file not found');
        }

        $fileName = basename($order->payment_receipt);

        return response()->download($filePath, $fileName);
    }

    private function processPointsForApprovedOrder(Order $order): void
    {
        if ($order->points_processed) {
            return;
        }

        try {
            if ($order->points_used > 0) {
                $this->pointsService->redeemPoints(
                    $order->student_id,
                    $order->points_used,
                    $order->id,
                    "Points redeemed for order #{$order->id}"
                );
                Log::info("Redeemed {$order->points_used} points for order #{$order->id}");
            }

            $earnableAmount = $order->price - ($order->points_discount ?? 0);
            if ($earnableAmount > 0) {
                $earnedPoints = $this->pointsService->awardPointsForPurchase(
                    $order->student_id,
                    $earnableAmount,
                    $order->id,
                    "Points earned from order #{$order->id}"
                );
                Log::info("Awarded {$earnedPoints} points for order #{$order->id}");
            }

            $order->update(['points_processed' => true]);
        } catch (\Exception $e) {
            Log::error("Error processing points for order #{$order->id}: " . $e->getMessage());
        }
    }
}
