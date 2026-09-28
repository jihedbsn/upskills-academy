<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderFrontendService
{
    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function index()
    {
        $orders = Order::with(['product', 'book', 'student'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $books = Book::where('is_active', true)->get();

        return view('orders.create', compact('products', 'books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'book_id' => 'nullable|exists:books,id',
            'price' => 'required|numeric|min:0',
            'payment_success' => 'boolean',
            'payment_method' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if (!$request->product_id && !$request->book_id) {
            return back()->withErrors(['error' => 'Vous devez sélectionner un produit ou un livre.']);
        }

        try {
            DB::beginTransaction();

            Order::create($request->all());

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Commande créée avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Erreur lors de la création de la commande: ' . $e->getMessage()]);
        }
    }

    public function show(Order $order)
    {
        $order->load(['product', 'book', 'student']);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $products = Product::where('is_active', true)->get();
        $books = Book::where('is_active', true)->get();

        return view('orders.edit', compact('order', 'products', 'books'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'book_id' => 'nullable|exists:books,id',
            'price' => 'required|numeric|min:0',
            'payment_success' => 'boolean',
            'payment_method' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $order->update($request->all());

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Commande mise à jour avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();

            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Commande supprimée avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'book_id' => 'nullable|exists:books,id',
        ]);

        $studentId = Auth::id();

        if ($request->product_id) {
            $existingPurchase = ProductStudent::where('product_id', $request->product_id)
                ->where('student_id', $studentId)
                ->exists();

            if ($existingPurchase) {
                return back()->withErrors(['error' => 'Vous avez déjà acheté ce cours.']);
            }
        }

        return back()->with('success', 'Produit ajouté au panier !');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.book_id' => 'nullable|exists:books,id',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $studentId = Auth::id();
        $user = Auth::user();
        $appliedCoupon = session('applied_coupon');
        $totalDiscount = 0;

        try {
            DB::beginTransaction();

            $totalAmount = collect($request->items)->sum('price');

            foreach ($request->items as $item) {
                if (isset($item['product_id'])) {
                    $existingPurchase = ProductStudent::where('product_id', $item['product_id'])
                        ->where('student_id', $studentId)
                        ->exists();

                    if ($existingPurchase) {
                        continue;
                    }
                }

                $itemPrice = $item['price'];
                $itemDiscount = 0;

                if ($appliedCoupon) {
                    $discountRatio = $appliedCoupon['discount_amount'] / $totalAmount;
                    $itemDiscount = $itemPrice * $discountRatio;
                    $itemPrice = $itemPrice - $itemDiscount;
                    $totalDiscount += $itemDiscount;
                }

                $order = Order::create([
                    'product_id' => $item['product_id'] ?? null,
                    'book_id' => $item['book_id'] ?? null,
                    'student_id' => $studentId,
                    'price' => $item['price'],
                    'coupon_id' => $appliedCoupon['id'] ?? null,
                    'discount_amount' => $itemDiscount,
                    'final_price' => $itemPrice,
                    'payment_success' => true,
                    'payment_method' => 'online',
                ]);

                if ($order->product_id) {
                    $product = Product::find($order->product_id);

                    $expirationDate = null;
                    if ($product && $product->validity_months) {
                        $expirationDate = now()->addMonths($product->validity_months);
                    }

                    ProductStudent::create([
                        'product_id' => $order->product_id,
                        'student_id' => $studentId,
                        'date' => now()->toDateString(),
                        'is_active' => true,
                        'access_granted_at' => now(),
                        'expiration_date' => $expirationDate,
                        'is_expired' => false,
                    ]);
                }
            }

            if ($appliedCoupon && $totalDiscount > 0) {
                $coupon = Coupon::find($appliedCoupon['id']);
                if ($coupon) {
                    $firstOrder = Order::where('student_id', $studentId)
                        ->latest()
                        ->first();

                    $this->couponService->recordCouponUsage(
                        $coupon,
                        $user,
                        $firstOrder->id,
                        $totalDiscount
                    );
                }
            }

            session()->forget('applied_coupon');

            DB::commit();

            $successMessage = 'Achat effectué avec succès !';
            if ($totalDiscount > 0) {
                $successMessage .= ' Vous avez économisé $' . number_format($totalDiscount, 2) . ' avec votre coupon !';
            }

            return redirect()->route('student-dashboard.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Erreur lors de l\'achat: ' . $e->getMessage()]);
        }
    }
}
