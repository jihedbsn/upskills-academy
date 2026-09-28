<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\OrderSpecifique;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderSpecifiqueService
{
    protected InstallmentService $installmentService;

    public function __construct(InstallmentService $installmentService)
    {
        $this->installmentService = $installmentService;
    }

    public function index(Request $request)
    {
        try {
            $query = OrderSpecifique::with(['student', 'product', 'productVariation', 'installments']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('student_id')) {
                $query->where('student_id', $request->student_id);
            }

            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $orderSpecifiques = $query->orderBy('created_at', 'desc')->paginate(15);

            $stats = [
                'total' => OrderSpecifique::count(),
                'pending' => OrderSpecifique::where('status', 'pending')->count(),
                'partial' => OrderSpecifique::where('status', 'partial')->count(),
                'paid' => OrderSpecifique::where('status', 'paid')->count(),
                'cancelled' => OrderSpecifique::where('status', 'cancelled')->count(),
                'total_revenue' => OrderSpecifique::where('status', 'paid')->sum('total_price'),
                'pending_revenue' => OrderSpecifique::whereIn('status', ['pending', 'partial'])->sum('remaining_amount'),
            ];

            return view('order-specifiques.index', compact('orderSpecifiques', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error in OrderSpecifiqueController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading the orders.');
        }
    }

    public function create()
    {
        try {
            $students = Student::orderBy('first_name')->get();
            $products = Product::with(['variations' => function ($query) {
                $query->orderBy('langue', 'asc');
            }])->orderBy('id')->get();

            return view('order-specifiques.create', compact('students', 'products'));
        } catch (\Exception $e) {
            Log::error('Error in OrderSpecifiqueController@create: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading the form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'product_id' => 'required|exists:products,id',
                'product_variation_id' => 'nullable|exists:product_variations,id',
                'total_price' => 'required|numeric|min:0',
                'total_installments' => 'required|integer|min:1',
                'notes' => 'nullable|string|max:1000',
            ]);

            $orderSpecifique = $this->installmentService->createInstallmentOrder(
                studentId: $request->student_id,
                productId: $request->product_id,
                totalPrice: $request->total_price,
                productVariationId: $request->product_variation_id,
                notes: $request->notes,
                paymentType: 'admin_created'
            );

            return redirect()->route('admin.order-specifiques.show', $orderSpecifique->id)
                ->with('success', 'Installment order created successfully!');
        } catch (\Exception $e) {
            Log::error('Error in OrderSpecifiqueController@store: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the order.');
        }
    }

    public function show(OrderSpecifique $orderSpecifique)
    {
        try {
            $orderSpecifique->load(['student', 'product', 'productVariation', 'installments']);

            return view('order-specifiques.show', compact('orderSpecifique'));
        } catch (\Exception $e) {
            Log::error('Error in OrderSpecifiqueController@show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading the order details.');
        }
    }

    public function edit(OrderSpecifique $orderSpecifique)
    {
        try {
            $students = Student::orderBy('first_name')->get();
            $products = Product::with(['variations' => function ($query) {
                $query->orderBy('langue', 'asc');
            }])->orderBy('id')->get();

            return view('order-specifiques.edit', compact('orderSpecifique', 'students', 'products'));
        } catch (\Exception $e) {
            Log::error('Error in OrderSpecifiqueController@edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading the form.');
        }
    }

    public function update(Request $request, OrderSpecifique $orderSpecifique)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'product_id' => 'required|exists:products,id',
                'product_variation_id' => 'nullable|exists:product_variations,id',
                'total_price' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($orderSpecifique->paid_amount > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot modify order after payments have been made.');
            }

            $orderSpecifique->update([
                'student_id' => $request->student_id,
                'product_id' => $request->product_id,
                'product_variation_id' => $request->product_variation_id,
                'total_price' => $request->total_price,
                'remaining_amount' => $request->total_price,
                'notes' => $request->notes,
            ]);

            return redirect()->route('admin.order-specifiques.show', $orderSpecifique->id)
                ->with('success', 'Order updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error in OrderSpecifiqueController@update: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the order.');
        }
    }

    public function destroy(OrderSpecifique $orderSpecifique)
    {
        try {
            if ($orderSpecifique->paid_amount > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete order after payments have been made.');
            }

            $orderSpecifique->delete();

            return redirect()->route('admin.order-specifiques.index')
                ->with('success', 'Order deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error in OrderSpecifiqueController@destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the order.');
        }
    }

    public function addPayment(Request $request, OrderSpecifique $orderSpecifique)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01|max:' . $orderSpecifique->remaining_amount,
                'payment_method' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
            ]);

            DB::beginTransaction();

            $orderSpecifique->addInstallmentPayment(
                $request->amount,
                $request->payment_method,
                $request->notes
            );

            DB::commit();

            return redirect()->route('admin.order-specifiques.show', $orderSpecifique->id)
                ->with('success', 'Payment of $' . number_format($request->amount, 2) . ' added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in OrderSpecifiqueController@addPayment: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while processing the payment.');
        }
    }

    public function showInstallment(Installment $installment)
    {
        try {
            $installment->load('orderSpecifique.student', 'orderSpecifique.product');
            $orderSpecifique = $installment->orderSpecifique;

            return view('order-specifiques.installment-detail', compact('installment', 'orderSpecifique'));
        } catch (\Exception $e) {
            Log::error('Error in OrderSpecifiqueController@showInstallment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading the installment details.');
        }
    }

    public function markInstallmentPaid(Request $request, Installment $installment)
    {
        try {
            $request->validate([
                'payment_method' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
                'paid_date' => 'nullable|date',
            ]);

            $orderSpecifique = $this->installmentService->processInstallmentPayment(
                $installment,
                $request->payment_method,
                $request->notes,
                $request->paid_date
            );

            return redirect()->route('admin.order-specifiques.show', $orderSpecifique->id)
                ->with('success', "Installment #{$installment->id} marked as paid successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in OrderSpecifiqueController@markInstallmentPaid: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while marking the installment as paid.');
        }
    }

    public function markInstallmentPending(Installment $installment)
    {
        try {
            DB::beginTransaction();

            $installment->update([
                'status' => 'pending',
                'paid_date' => null,
                'payment_method' => null,
                'notes' => null,
            ]);

            $orderSpecifique = $installment->orderSpecifique;
            $orderSpecifique->paid_amount = $orderSpecifique->installments()->where('status', 'paid')->sum('amount');
            $orderSpecifique->remaining_amount = max(0, $orderSpecifique->total_price - $orderSpecifique->paid_amount);
            $orderSpecifique->paid_installments = $orderSpecifique->installments()->where('status', 'paid')->count();
            $orderSpecifique->updateStatus();

            DB::commit();

            return redirect()->route('admin.order-specifiques.show', $orderSpecifique->id)
                ->with('success', "Installment #{$installment->id} marked as pending successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in OrderSpecifiqueController@markInstallmentPending: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while marking the installment as pending.');
        }
    }

    public function updateInstallmentDueDate(Request $request, Installment $installment)
    {
        try {
            $request->validate([
                'due_date' => 'required|date',
            ]);

            if ($installment->status === 'paid') {
                return redirect()->back()
                    ->with('error', 'Cannot modify due date for a paid installment.');
            }

            $newDueDate = \Carbon\Carbon::parse($request->due_date)->startOfDay();

            $installment->update([
                'due_date' => $newDueDate,
                // If admin moves date to today/future, recover overdue installment back to pending.
                'status' => $newDueDate->isFuture() || $newDueDate->isToday() ? 'pending' : $installment->status,
            ]);

            return redirect()->route('admin.order-specifiques.show', $installment->order_specifique_id)
                ->with('success', "Installment #{$installment->installment_number} due date updated successfully.");
        } catch (\Exception $e) {
            Log::error('Error in OrderSpecifiqueController@updateInstallmentDueDate: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating installment due date.');
        }
    }

    public function updateInstallmentPaidDate(Request $request, Installment $installment)
    {
        try {
            $request->validate([
                'paid_date' => 'required|date',
            ]);

            if ($installment->status !== 'paid') {
                return redirect()->back()
                    ->with('error', 'Only paid installments can have a payment date.');
            }

            $installment->update([
                'paid_date' => \Carbon\Carbon::parse($request->paid_date)->startOfDay(),
            ]);

            return redirect()->route('admin.order-specifiques.show', $installment->order_specifique_id)
                ->with('success', "Installment #{$installment->installment_number} payment date updated successfully.");
        } catch (\Exception $e) {
            Log::error('Error in OrderSpecifiqueController@updateInstallmentPaidDate: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating installment payment date.');
        }
    }

    public function downloadInstallmentReceipt(Installment $installment)
    {
        if (!$installment->payment_receipt) {
            abort(404, 'Receipt not found');
        }

        $filePath = base_path('../user/storage/app/public/' . ltrim($installment->payment_receipt, '/'));

        if (!is_file($filePath)) {
            abort(404, 'Receipt file not found at: ' . $filePath);
        }

        $fileName = basename($installment->payment_receipt);

        return response()->download($filePath, $fileName);
    }

    public function getProductVariations(Request $request)
    {
        try {
            $productId = $request->product_id;
            $variations = ProductVariation::where('products_id', $productId)
                ->orderBy('langue', 'asc')
                ->get();

            return response()->json($variations);
        } catch (\Exception $e) {
            Log::error('Error in OrderSpecifiqueController@getProductVariations: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load product variations'], 500);
        }
    }
}
