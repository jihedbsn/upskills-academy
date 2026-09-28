<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponDetaille;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\AffiliatePartner;
use App\Models\CouponUsageLog;
use App\Models\ActiveCartCoupon;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CouponService
{
    /**
     * Display a listing of coupons with advanced filtering
     */
    public function index(Request $request)
    {
        $coupons = $this->getFilteredCoupons($request);
        $affiliatePartners = AffiliatePartner::where('status', 'approved')->get();

        return view('admin.coupons.index', compact('coupons', 'affiliatePartners'));
    }

    /**
     * Creation form for a new coupon with advanced features
     */
    public function create()
    {
        $products = Product::where('statut', 1)
            ->with(['variations' => function ($query) {
                $query->where('langue', 'en');
            }])
            ->get();

        $affiliatePartners = AffiliatePartner::where('status', 'approved')
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('titre')->get();

        return view('admin.coupons.create', compact('products', 'affiliatePartners', 'categories'));
    }

    /**
     * Show the form for editing the specified coupon
     */
    public function edit(Coupon $coupon)
    {
        $products = Product::where('statut', 1)
            ->with(['variations' => function ($query) {
                $query->where('langue', 'en');
            }])
            ->get();

        $affiliatePartners = AffiliatePartner::where('status', 'approved')
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('titre')->get();

        $applicationData = $this->determineProductApplicationType($coupon);

        return view('admin.coupons.edit', compact(
            'coupon',
            'products',
            'affiliatePartners',
            'categories'
        ) + [
            'selectedProducts' => $applicationData['selected_products'],
            'applicationType' => $applicationData['application_type'],
            'selectedCategories' => $applicationData['selected_categories']
        ]);
    }

    /**
     * Get filtered coupons with pagination
     */
    public function getFilteredCoupons(Request $request)
    {
        $query = Coupon::with(['detailles.product', 'affiliatePartner']);

        // Apply filters
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where('date_fin', '<', Carbon::now());
                    break;
                case 'upcoming':
                    $query->where('date_debut', '>', Carbon::now());
                    break;
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        if ($request->filled('is_stackable')) {
            $query->where('is_stackable', $request->is_stackable);
        }

        if ($request->filled('auto_apply')) {
            $query->where('auto_apply', $request->auto_apply);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%")
                  ->orWhere('nom', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    /**
     * Create a new coupon with all advanced features
     */
    public function createCoupon(array $data): Coupon
    {
        Log::info('Coupon creation attempt', [
            'request_data' => $data,
            'validation_passed' => true
        ]);

        try {
            DB::beginTransaction();

            // Generate unique code
            $code = !empty($data['code'])
                ? strtoupper($data['code'])
                : $this->generateUniqueCode();

            // Validate percentage value
            if ($data['type'] === 'percentage' && $data['valeur'] > 100) {
                throw new \InvalidArgumentException('Percentage discount cannot exceed 100%');
            }

            // Create coupon with all fields
            $coupon = Coupon::create([
                'code' => $code,
                'nom' => $data['nom'],
                'description' => $data['description'] ?? null,
                'valeur' => $data['valeur'],
                'min_purchase_amount' => $data['min_purchase_amount'] ?? null,
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'type' => $data['type'],
                'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : false,
                'usage_limit' => $data['limit_utilise'] ?? $data['usage_limit'] ?? null,
                'usage_count' => 0,

                // Advanced features
                'is_stackable' => isset($data['is_stackable']) ? (bool)$data['is_stackable'] : false,
                'stack_priority' => isset($data['is_stackable']) && $data['is_stackable'] ? ($data['stack_priority'] ?? 1) : 1,
                'customer_type' => $data['customer_type'] ?? 'all',
                'auto_apply' => isset($data['auto_apply']) ? (bool)$data['auto_apply'] : false,
                'auto_apply_conditions' => isset($data['auto_apply']) && $data['auto_apply'] ? $this->buildAutoApplyConditions($data) : null,
                'max_discount_amount' => $data['max_discount_amount'] ?? null,
                'min_items' => $data['min_items'] ?? null,
                'min_cart_items' => $data['min_cart_items'] ?? 1,
                'course_types' => isset($data['course_types']) ? json_decode($data['course_types'], true) : null,
                'max_uses_per_user' => $data['max_uses_per_user'] ?? null,
                'affiliate_partner_id' => $data['affiliate_partner_id'] ?? null,
                'commission_rate' => $data['affiliate_partner_id'] ? $data['commission_rate'] : null,
                'is_public' => isset($data['is_public']) ? (bool)$data['is_public'] : false,
                'first_purchase_only' => isset($data['first_purchase_only']) ? (bool)$data['first_purchase_only'] : false,
                'cumulative_enabled' => isset($data['cumulative_enabled']) ? (bool)$data['cumulative_enabled'] : false,
                'allow_multiple_uses' => isset($data['allow_multiple_uses']) ? (bool)$data['allow_multiple_uses'] : false,
            ]);

            // Attach products and categories based on application type
            $this->attachProductsToCoupon($coupon, $data);

            DB::commit();

            Log::info('Coupon created successfully', [
                'coupon_id' => $coupon->id,
                'coupon_code' => $code
            ]);

            return $coupon;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Coupon creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing coupon
     */
    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        try {
            DB::beginTransaction();

            // Validate percentage value
            if ($data['type'] === 'percentage' && $data['valeur'] > 100) {
                throw new \InvalidArgumentException('Percentage discount cannot exceed 100%');
            }

            // Update coupon with all fields
            $coupon->update([
                'code' => isset($data['code']) ? strtoupper($data['code']) : $coupon->code,
                'nom' => $data['nom'],
                'description' => $data['description'] ?? null,
                'valeur' => $data['valeur'],
                'min_purchase_amount' => $data['min_purchase_amount'] ?? null,
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'type' => $data['type'],
                'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : false,
                'usage_limit' => $data['limit_utilise'] ?? $data['usage_limit'] ?? null,

                // Advanced features
                'is_stackable' => isset($data['is_stackable']) ? (bool)$data['is_stackable'] : false,
                'stack_priority' => isset($data['is_stackable']) && $data['is_stackable'] ? ($data['stack_priority'] ?? $coupon->stack_priority ?? 1) : 1,
                'customer_type' => $data['customer_type'] ?? 'all',
                'auto_apply' => isset($data['auto_apply']) ? (bool)$data['auto_apply'] : false,
                'auto_apply_conditions' => isset($data['auto_apply']) && $data['auto_apply'] ? $this->buildAutoApplyConditions($data) : null,
                'max_discount_amount' => $data['max_discount_amount'] ?? null,
                'min_items' => $data['min_items'] ?? null,
                'min_cart_items' => $data['min_cart_items'] ?? $coupon->min_cart_items ?? 1,
                'course_types' => isset($data['course_types']) ? json_decode($data['course_types'], true) : null,
                'max_uses_per_user' => $data['max_uses_per_user'] ?? null,
                'affiliate_partner_id' => $data['affiliate_partner_id'] ?? null,
                'commission_rate' => $data['affiliate_partner_id'] ? $data['commission_rate'] : null,
                'is_public' => isset($data['is_public']) ? (bool)$data['is_public'] : false,
                'first_purchase_only' => isset($data['first_purchase_only']) ? (bool)$data['first_purchase_only'] : false,
                'cumulative_enabled' => isset($data['cumulative_enabled']) ? (bool)$data['cumulative_enabled'] : false,
                'allow_multiple_uses' => isset($data['allow_multiple_uses']) ? (bool)$data['allow_multiple_uses'] : false,
            ]);

            // Clear existing product and category associations
            $coupon->detailles()->delete();
            $coupon->categories()->detach();

            // Rebuild product and category associations based on application type
            $this->attachProductsToCoupon($coupon, $data);

            DB::commit();

            return $coupon;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get coupon with detailed statistics
     */
    public function getCouponWithStats(Coupon $coupon): array
    {
        $coupon->load([
            'detailles.product.variations' => function($query) {
                $query->where('langue', 'en');
            },
            'affiliatePartner',
            'usageLogs' => function($query) {
                $query->latest()->limit(50);
            }
        ]);

        // Calculate statistics
        $stats = [
            'total_uses' => $coupon->usage_count,
            'total_discount_given' => $coupon->usageLogs()->sum('discount_amount'),
            'total_revenue' => $coupon->usageLogs()->sum('final_price'),
            'average_discount' => $coupon->usageLogs()->avg('discount_amount'),
            'unique_users' => $coupon->usageLogs()->distinct('student_id')->count('student_id'),
            'stacked_usage_count' => $coupon->usageLogs()->where('was_stacked', true)->count(),
            'auto_applied_count' => 0, // Column was_auto_applied doesn't exist
            'usage_by_device' => $coupon->usageLogs()
                ->selectRaw('device_type, COUNT(*) as count')
                ->groupBy('device_type')
                ->pluck('count', 'device_type'),
            'active_in_carts' => ActiveCartCoupon::where('coupon_id', $coupon->id)
                ->where(function($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                })
                ->count()
        ];

        return compact('coupon', 'stats');
    }

    /**
     * Delete a coupon with validation or deactivate if used
     */
    public function deleteCoupon(Coupon $coupon): array
    {
        // Check if coupon has been used
        if ($coupon->usage_count > 0) {
            // Deactivate instead of deleting
            $coupon->update(['is_active' => false]);

            return [
                'action' => 'deactivated',
                'message' => "Coupon '{$coupon->code}' has been used {$coupon->usage_count} times and cannot be deleted. It has been deactivated instead to preserve order history."
            ];
        }

        // Check if coupon is in active carts
        $activeInCarts = ActiveCartCoupon::where('coupon_id', $coupon->id)->count();
        if ($activeInCarts > 0) {
            // Deactivate instead of deleting
            $coupon->update(['is_active' => false]);

            return [
                'action' => 'deactivated',
                'message' => "Coupon '{$coupon->code}' is currently active in {$activeInCarts} cart(s) and cannot be deleted. It has been deactivated instead."
            ];
        }

        // Safe to delete - no usage history
        $coupon->delete();

        return [
            'action' => 'deleted',
            'message' => "Coupon '{$coupon->code}' deleted successfully!"
        ];
    }

    /**
     * Toggle coupon active status
     */
    public function toggleCouponStatus(Coupon $coupon): bool
    {
        $newStatus = !$coupon->is_active;
        $coupon->update(['is_active' => $newStatus]);
        return $newStatus;
    }

    /**
     * Duplicate an existing coupon
     */
    public function duplicateCoupon(Coupon $coupon): Coupon
    {
        try {
            DB::beginTransaction();

            $newCode = $this->generateUniqueCode();
            $newCoupon = $coupon->replicate();
            $newCoupon->code = $newCode;
            $newCoupon->nom = $coupon->nom . ' (Copy)';
            $newCoupon->usage_count = 0;
            $newCoupon->is_active = false; // Deactivate by default
            $newCoupon->save();

            // Duplicate product associations
            foreach ($coupon->detailles as $detail) {
                CouponDetaille::create([
                    'coupon_id' => $newCoupon->id,
                    'product_id' => $detail->product_id
                ]);
            }

            DB::commit();

            return $newCoupon;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if a coupon code is available
     */
    public function isCodeAvailable(string $code, ?int $exceptId = null): bool
    {
        return !Coupon::where('code', strtoupper($code))
            ->when($exceptId, function($q) use ($exceptId) {
                $q->where('id', '!=', $exceptId);
            })
            ->exists();
    }

    /**
     * Check if a coupon name is available
     */
    public function isNameAvailable(string $name, ?int $exceptId = null): bool
    {
        return !Coupon::where('nom', $name)
            ->when($exceptId, function($q) use ($exceptId) {
                $q->where('id', '!=', $exceptId);
            })
            ->exists();
    }

    /**
     * Check coupon stacking compatibility
     */
    public function checkStackingCompatibility(Coupon $coupon, array $existingCouponIds): array
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Coupon> $existingCoupons */
        $existingCoupons = Coupon::whereIn('id', $existingCouponIds)->get();

        $compatible = [];
        $incompatible = [];

        foreach ($existingCoupons as $existing) {
            if ($coupon->canBeStackedWith($existing)) {
                $compatible[] = [
                    'id' => $existing->id,
                    'code' => $existing->code,
                    'priority' => $existing->stack_priority
                ];
            } else {
                $incompatible[] = [
                    'id' => $existing->id,
                    'code' => $existing->code,
                    'reason' => $this->getStackingIncompatibilityReason($coupon, $existing)
                ];
            }
        }

        return [
            'can_stack' => $coupon->is_stackable,
            'priority' => $coupon->stack_priority,
            'compatible' => $compatible,
            'incompatible' => $incompatible
        ];
    }

    /**
     * Check coupon compatibility for stacking
     */
    public function checkStacking(Request $request)
    {
        $coupon = Coupon::findOrFail($request->coupon_id);
        $result = $this->checkStackingCompatibility(
            $coupon,
            $request->existing_coupon_ids ?? []
        );

        return response()->json($result);
    }

    /**
     * Get coupon statistics and analytics
     */
    public function getCouponStatistics(int $period = 30): array
    {
        $startDate = Carbon::now()->subDays($period);

        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('is_active', true)->count(),
            'expired' => Coupon::where('date_fin', '<', Carbon::now())->count(),
            'stackable' => Coupon::where('is_stackable', true)->count(),
            'auto_apply' => Coupon::where('auto_apply', true)->count(),
            'with_affiliate' => Coupon::whereNotNull('affiliate_partner_id')->count(),

            // Usage stats for period
            'total_uses' => CouponUsageLog::where('created_at', '>=', $startDate)->count(),
            'unique_users' => CouponUsageLog::where('created_at', '>=', $startDate)
                ->distinct('student_id')->count('student_id'),
            'total_discount_given' => CouponUsageLog::where('created_at', '>=', $startDate)
                ->sum('discount_amount'),
            'total_revenue' => CouponUsageLog::where('created_at', '>=', $startDate)
                ->sum('final_price'),
            'stacked_usage' => CouponUsageLog::where('created_at', '>=', $startDate)
                ->where('was_stacked', true)->count(),
            'auto_applied' => 0, // Column was_auto_applied doesn't exist in table

            // Top performing coupons
            'top_coupons' => Coupon::withCount(['usageLogs' => function($q) use ($startDate) {
                    $q->where('created_at', '>=', $startDate);
                }])
                ->with('usageLogs')
                ->having('usage_logs_count', '>', 0)
                ->orderByDesc('usage_logs_count')
                ->limit(10)
                ->get()
                ->map(function (Coupon $coupon) use ($startDate) {
                    return [
                        'id' => $coupon->id,
                        'code' => $coupon->code,
                        'name' => $coupon->nom,
                        'uses' => $coupon->usage_logs_count,
                        'revenue' => $coupon->usageLogs()
                            ->where('created_at', '>=', $startDate)
                            ->sum('final_price'),
                        'discount_given' => $coupon->usageLogs()
                            ->where('created_at', '>=', $startDate)
                            ->sum('discount_amount')
                    ];
                }),

            // Device breakdown
            'by_device' => CouponUsageLog::where('created_at', '>=', $startDate)
                ->selectRaw('device_type, COUNT(*) as count, SUM(final_price) as revenue')
                ->groupBy('device_type')
                ->get(),

            // Customer type breakdown
            'by_customer_type' => Coupon::selectRaw('customer_type, COUNT(*) as count')
                ->groupBy('customer_type')
                ->get(),

            // Daily usage trend
            'daily_trend' => CouponUsageLog::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as uses, SUM(discount_amount) as discount, SUM(final_price) as revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        ];

        return $stats;
    }

    /**
     * Get coupon statistics and analytics
     */
    public function statistics(Request $request)
    {
        $period = (int) $request->get('period', '30');
        $stats = $this->getCouponStatistics($period);

        return view('admin.coupons.statistics', compact('stats', 'period'));
    }

    /**
     * Export coupon usage data
     */
    public function exportCouponUsageData(int $period = 30): array
    {
        $startDate = Carbon::now()->subDays($period);

        return CouponUsageLog::with(['coupon', 'student', 'order'])
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($log) {
                return [
                    'date' => $log->created_at->format('Y-m-d H:i:s'),
                    'coupon_code' => $log->coupon->code ?? 'N/A',
                    'student' => $log->student->name ?? 'N/A',
                    'order_id' => $log->order_id ?? 'N/A',
                    'original_price' => $log->original_price ?? 0,
                    'discount' => $log->discount_amount ?? 0,
                    'final_price' => $log->final_price ?? 0,
                    'device' => $log->device_type ?? 'unknown',
                    'stacked' => $log->was_stacked ? 'Yes' : 'No',
                    'auto_applied' => 'N/A', // Column doesn't exist
                    'utm_source' => $log->utm_source ?? '',
                    'utm_campaign' => $log->utm_campaign ?? ''
                ];
            })
            ->toArray();
    }

    /**
     * Export coupon usage data
     */
    public function export(Request $request)
    {
        $period = (int) $request->get('period', '30');
        $exportData = $this->exportCouponUsageData($period);

        $filename = 'coupon_usage_' . Carbon::now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($exportData) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Date',
                'Coupon Code',
                'Student',
                'Order ID',
                'Original Price',
                'Discount',
                'Final Price',
                'Device',
                'Stacked',
                'Auto Applied',
                'UTM Source',
                'UTM Campaign'
            ]);

            foreach ($exportData as $row) {
                fputcsv($file, [
                    $row['date'],
                    $row['coupon_code'],
                    $row['student'],
                    $row['order_id'],
                    $row['original_price'],
                    $row['discount'],
                    $row['final_price'],
                    $row['device'],
                    $row['stacked'],
                    $row['auto_applied'],
                    $row['utm_source'],
                    $row['utm_campaign']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get active coupons for customer type
     */
    public function getActiveCouponsForCustomerType(string $customerType = 'all'): \Illuminate\Database\Eloquent\Collection
    {
        return Coupon::active()
            ->where(function($q) use ($customerType) {
                $q->where('customer_type', 'all')
                  ->orWhere('customer_type', $customerType);
            })
            ->where('is_public', true)
            ->get();
    }

    /**
     * Determine product application type for editing
     */
    public function determineProductApplicationType(Coupon $coupon): array
    {
        $selectedProducts = $coupon->detailles->pluck('product_id')->toArray();
        $totalProducts = Product::where('statut', 1)->count();
        $couponProductsCount = count($selectedProducts);

        $applicationType = 'selected'; // default
        $selectedCategories = [];

        if ($couponProductsCount == $totalProducts) {
            $applicationType = 'all';
        } else {
            // Check if coupon has associated categories
            $couponCategories = $coupon->categories->pluck('id')->toArray();

            if (!empty($couponCategories)) {
                // Verify that all products from these categories are included
                $categoryProductsCount = Product::where('statut', 1)
                    ->whereIn('categories_id', $couponCategories)
                    ->count();

                if ($categoryProductsCount == $couponProductsCount) {
                    $applicationType = 'category';
                    $selectedCategories = $couponCategories;
                }
            }
        }

        return [
            'application_type' => $applicationType,
            'selected_categories' => $selectedCategories,
            'selected_products' => $selectedProducts
        ];
    }

    /**
     * Attach products to coupon based on application type
     */
    private function attachProductsToCoupon(Coupon $coupon, array $data): void
    {
        if ($data['product_application'] === 'all') {
            // Apply to all active products
            $allProducts = Product::where('statut', 1)->get();
            foreach ($allProducts as $product) {
                CouponDetaille::create([
                    'coupon_id' => $coupon->id,
                    'product_id' => $product->id
                ]);
            }
        } elseif ($data['product_application'] === 'selected' && !empty($data['products'])) {
            // Apply to selected products only
            foreach ($data['products'] as $productId) {
                CouponDetaille::create([
                    'coupon_id' => $coupon->id,
                    'product_id' => $productId
                ]);
            }
        } elseif ($data['product_application'] === 'category' && !empty($data['categories'])) {
            // First, attach categories to coupon (many-to-many)
            $coupon->categories()->sync($data['categories']);

            // Then apply to all products in the selected categories
            $categoryProducts = Product::where('statut', 1)
                ->whereIn('categories_id', $data['categories'])
                ->get();

            foreach ($categoryProducts as $product) {
                CouponDetaille::create([
                    'coupon_id' => $coupon->id,
                    'product_id' => $product->id
                ]);
            }

            Log::info('Applied coupon to multiple categories', [
                'coupon_id' => $coupon->id,
                'categories' => $data['categories'],
                'products_count' => $categoryProducts->count()
            ]);
        }
    }

    /**
     * Generate a unique coupon code
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Coupon::where('code', $code)->exists());

        return $code;
    }

    /**
     * Build auto-apply conditions from request data
     */
    private function buildAutoApplyConditions(array $data): ?array
    {
        $conditions = [];

        if (!empty($data['auto_apply_min_cart_amount'])) {
            $conditions['min_cart_amount'] = $data['auto_apply_min_cart_amount'];
        }

        if (!empty($data['auto_apply_course_types'])) {
            $conditions['course_types'] = is_array($data['auto_apply_course_types'])
                ? $data['auto_apply_course_types']
                : json_decode($data['auto_apply_course_types'], true);
        }

        if (!empty($data['auto_apply_min_items'])) {
            $conditions['min_items'] = $data['auto_apply_min_items'];
        }

        if (!empty($data['auto_apply_specific_products'])) {
            $conditions['specific_products'] = is_array($data['auto_apply_specific_products'])
                ? $data['auto_apply_specific_products']
                : json_decode($data['auto_apply_specific_products'], true);
        }

        return !empty($conditions) ? $conditions : null;
    }

    /**
     * Get reason why two coupons cannot be stacked
     */
    private function getStackingIncompatibilityReason(Coupon $coupon1, Coupon $coupon2): string
    {
        if (!$coupon1->is_stackable) {
            return "Coupon '{$coupon1->code}' is not stackable";
        }

        if (!$coupon2->is_stackable) {
            return "Coupon '{$coupon2->code}' is not stackable";
        }

        if ($coupon1->affiliate_partner_id && $coupon2->affiliate_partner_id) {
            return 'Affiliate coupons cannot be stacked together';
        }

        return 'Unknown incompatibility';
    }

    /**
     * Get all valid coupons for a specific user
     */
    public function getValidCouponsForUser(User $user, bool $publicOnly = true): \Illuminate\Database\Eloquent\Collection
    {
        $query = Coupon::active()
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now());

        if ($publicOnly) {
            $query->where('is_public', true);
        }

        return $query->get()->filter(function ($coupon) use ($user) {
            return $coupon->canBeUsedByUser($user);
        });
    }

    /**
     * Validate coupon for specific user with detailed feedback
     */
    public function validateCouponForUser(string $code, User $user): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'Coupon code not found.',
                'coupon' => null
            ];
        }

        $validation = $coupon->getValidationResult($user);

        return [
            'valid' => $validation['can_use'],
            'message' => $validation['can_use']
                ? 'Coupon is valid and ready to use!'
                : 'Coupon cannot be used: ' . implode(' ', $validation['reasons']),
            'reasons' => $validation['reasons'],
            'coupon' => $coupon,
            'user_type' => $validation['user_type'],
            'user_stats' => $validation['user_stats']
        ];
    }

    /**
     * Get customer type statistics for dashboard
     */
    public function getCustomerTypeStats(): array
    {
        $stats = [
            'new' => User::whereHas('orders', function($q) {
                $q->where('payment_success', true);
            }, '=', 0)->count(),
            'returning' => 0,
            'vip' => 0
        ];

        // Get all users with successful orders
        $usersWithOrders = User::withCount(['orders as successful_orders_count' => function($q) {
            $q->where('payment_success', true);
        }])
        ->with(['orders' => function($q) {
            $q->where('payment_success', true);
        }])
        ->having('successful_orders_count', '>', 0)
        ->get();

        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $usersWithOrders */
        foreach ($usersWithOrders as $user) {
            $customerType = $user->getCustomerType();
            if (isset($stats[$customerType])) {
                $stats[$customerType]++;
            }
        }

        return $stats;
    }

    /**
     * Get coupons suitable for user's customer type
     */
    public function getCouponsForCustomerType(User $user): \Illuminate\Database\Eloquent\Collection
    {
        $customerType = $user->getCustomerType();

        return Coupon::active()
            ->where('is_public', true)
            ->where(function($q) use ($customerType) {
                $q->where('customer_type', 'all')
                  ->orWhere('customer_type', $customerType);
            })
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->get()
            ->filter(function ($coupon) use ($user) {
                return $coupon->canBeUsedByUser($user);
            });
    }

    /**
     * Apply coupon with automatic customer type validation
     */
    public function applyCouponToCart(string $code, User $user, array $cartData): array
    {
        $validation = $this->validateCouponForUser($code, $user);

        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'details' => $validation
            ];
        }

        $coupon = $validation['coupon'];

        // Additional cart validation (minimum amount, items, etc.)
        if ($coupon->min_purchase_amount && $cartData['total'] < $coupon->min_purchase_amount) {
            return [
                'success' => false,
                'message' => "Minimum purchase amount of {$coupon->min_purchase_amount} required."
            ];
        }

        if ($coupon->min_items && $cartData['item_count'] < $coupon->min_items) {
            return [
                'success' => false,
                'message' => "Minimum {$coupon->min_items} items required in cart."
            ];
        }

        // Calculate discount
        $discountAmount = $this->calculateDiscount($coupon, $cartData);

        return [
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'coupon' => $coupon,
            'discount_amount' => $discountAmount,
            'user_type' => $validation['user_type'],
            'final_total' => $cartData['total'] - $discountAmount
        ];
    }

    /**
     * Calculate discount amount based on coupon type
     */
    private function calculateDiscount(Coupon $coupon, array $cartData): float
    {
        $discountAmount = 0;

        if ($coupon->type === 'percentage') {
            $discountAmount = ($cartData['total'] * $coupon->valeur) / 100;

            // Apply maximum discount limit if set
            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } else {
            // Fixed amount discount
            $discountAmount = min($coupon->valeur, $cartData['total']);
        }

        return round($discountAmount, 2);
    }
}
