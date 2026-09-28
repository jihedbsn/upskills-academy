<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'nom',
        'description',
        'valeur',
        'montant_minimum',
        'min_purchase_amount', // Alias for montant_minimum
        'date_debut',
        'date_fin',
        'usage_limit',
        'usage_count',
        'limit_utilise',
        'is_active',
        'type',
        // New fields from migration
        'is_stackable',
        'stack_priority',
        'customer_type',
        'auto_apply',
        'auto_apply_conditions',
        'max_discount_amount',
        'min_items',
        'min_cart_items',
        'course_types',
        'max_uses_per_user',
        'affiliate_partner_id',
        'commission_rate',
        'is_public',
        'first_purchase_only',
        'cumulative_enabled',
        'allow_multiple_uses'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'valeur' => 'decimal:2',
        'montant_minimum' => 'decimal:2',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'usage_limit' => 'integer',
        'limit_utilise' => 'integer',
        // New casts
        'is_stackable' => 'boolean',
        'stack_priority' => 'integer',
        'auto_apply' => 'boolean',
        'auto_apply_conditions' => 'array',
        'max_discount_amount' => 'decimal:2',
        'min_items' => 'integer',
        'course_types' => 'array',
        'max_uses_per_user' => 'integer',
        'commission_rate' => 'decimal:2',
        'is_public' => 'boolean',
        'first_purchase_only' => 'boolean',
        'cumulative_enabled' => 'boolean',
        'allow_multiple_uses' => 'boolean'
    ];

    /**
     * Relation avec les détails du coupon
     */
    public function detailles(): HasMany
    {
        return $this->hasMany(CouponDetaille::class);
    }

    /**
     * Relation avec les produits via les détails
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_detailles', 'coupon_id', 'product_id');
    }

    /**
     * Relation avec le partenaire affilié
     */
    public function affiliatePartner()
    {
        return $this->belongsTo(AffiliatePartner::class);
    }

    /**
     * Get the categories associated with the coupon (many-to-many).
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'coupon_category', 'coupon_id', 'category_id')
                    ->withTimestamps();
    }

    /**
     * Relation avec les coupons actifs dans les paniers
     */
    public function activeCartCoupons(): HasMany
    {
        return $this->hasMany(ActiveCartCoupon::class);
    }

    /**
     * Relation avec les logs d'utilisation
     */
    public function usageLogs(): HasMany
    {
        return $this->hasMany(CouponUsageLog::class);
    }

    /**
     * Relation avec l'utilisation par commande
     */
    public function orderUsages(): HasMany
    {
        return $this->hasMany(CouponOrderUsage::class);
    }

    /**
     * Scope pour les coupons actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les coupons valides (dans la période)
     */
    public function scopeValid($query)
    {
        $today = Carbon::today();
        return $query->where('date_debut', '<=', $today)
                    ->where('date_fin', '>=', $today);
    }

    /**
     * Scope pour les coupons disponibles (pas de limite d'usage atteinte)
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('limit_utilise')
              ->orWhereColumn('usage_count', '<', 'limit_utilise');
        });
    }

    /**
     * Vérifier si le coupon est valide
     */
    public function isValid(): bool
    {
        $today = Carbon::today();
        return $this->is_active
            && $this->date_debut <= $today
            && $this->date_fin >= $today;
    }

    /**
     * Vérifier si le coupon peut être utilisé pour un produit
     */
    public function canBeUsedForProduct(int $productId): bool
    {
        return $this->detailles()->where('product_id', $productId)->exists();
    }

    /**
     * Vérifier si le coupon peut être stacké avec un autre coupon
     */
    public function canBeStackedWith(Coupon $coupon): bool
    {
        if (!$this->is_stackable || !$coupon->is_stackable) {
            return false;
        }

        return true;
    }

    /**
     * Vérifier si le coupon est éligible pour un type de client
     */
    public function isEligibleForCustomer(string $customerType): bool
    {
        if ($this->customer_type === 'all') {
            return true;
        }

        return $this->customer_type === $customerType;
    }

    /**
     * Check if coupon can be used by a specific user (with automatic type detection)
     */
    public function canBeUsedByUser(User $user): bool
    {
        // Check customer type eligibility
        $userCustomerType = $user->getCustomerType();
        if (!$this->isEligibleForCustomer($userCustomerType)) {
            return false;
        }

        // Check first purchase only restriction
        if ($this->first_purchase_only && !$user->isNewCustomer()) {
            return false;
        }

        // Check if coupon is active and within validity period
        if (!$this->is_active || !$this->isValid()) {
            return false;
        }

        // Check usage limits
        if ($this->hasReachedUsageLimit()) {
            return false;
        }

        // Check per-user usage limit
        if ($this->max_uses_per_user) {
            $userUsageCount = $this->usageLogs()
                ->where('user_id', $user->id)
                ->count();

            if ($userUsageCount >= $this->max_uses_per_user) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get detailed validation result with reasons
     */
    public function getValidationResult(User $user): array
    {
        $reasons = [];
        $canUse = true;

        // Check customer type
        $userCustomerType = $user->getCustomerType();
        if (!$this->isEligibleForCustomer($userCustomerType)) {
            $canUse = false;
            $reasons[] = "This coupon is only for {$this->customer_type} customers. You are a {$userCustomerType} customer.";
        }

        // Check first purchase only
        if ($this->first_purchase_only && !$user->isNewCustomer()) {
            $canUse = false;
            $reasons[] = "This coupon is only valid for first-time customers.";
        }

        // Check if active
        if (!$this->is_active) {
            $canUse = false;
            $reasons[] = "This coupon is currently inactive.";
        }

        // Check validity period
        if (!$this->isValid()) {
            $canUse = false;
            if ($this->date_debut > now()) {
                $reasons[] = "This coupon is not yet active. It will be available from {$this->date_debut->format('Y-m-d')}.";
            } else {
                $reasons[] = "This coupon has expired on {$this->date_fin->format('Y-m-d')}.";
            }
        }

        // Check usage limits
        if ($this->hasReachedUsageLimit()) {
            $canUse = false;
            $reasons[] = "This coupon has reached its maximum usage limit ({$this->usage_limit} uses).";
        }

        // Check per-user limit
        if ($this->max_uses_per_user) {
            $userUsageCount = $this->usageLogs()
                ->where('user_id', $user->id)
                ->count();

            if ($userUsageCount >= $this->max_uses_per_user) {
                $canUse = false;
                $reasons[] = "You have already used this coupon the maximum number of times ({$this->max_uses_per_user}).";
            }
        }

        // Check allow_multiple_uses - if false, user can only use the coupon once
        if (!$this->allow_multiple_uses) {
            $hasUsedBefore = $this->usageLogs()
                ->where('student_id', $user->id)
                ->exists();

            if ($hasUsedBefore) {
                $canUse = false;
                $reasons[] = "You have already used this coupon once. This coupon can only be used one time.";
            }
        }

        return [
            'can_use' => $canUse,
            'reasons' => $reasons,
            'user_type' => $userCustomerType,
            'user_stats' => $user->getCustomerStats()
        ];
    }

    /**
     * Vérifier les conditions d'auto-application
     */
    public function checkAutoApplyConditions(array $cartData): bool
    {
        if (!$this->auto_apply || !$this->auto_apply_conditions) {
            return false;
        }

        $conditions = $this->auto_apply_conditions;

        // Check minimum amount
        if (isset($conditions['min_amount']) && $cartData['total'] < $conditions['min_amount']) {
            return false;
        }

        // Check minimum items
        if (isset($conditions['min_items']) && $cartData['item_count'] < $conditions['min_items']) {
            return false;
        }

        // Check customer type
        if (isset($conditions['customer_type']) && $cartData['customer_type'] !== $conditions['customer_type']) {
            return false;
        }

        // Check course types
        if (isset($conditions['course_types'])) {
            $hasMatchingType = false;
            foreach ($cartData['course_types'] as $type) {
                if (in_array($type, $conditions['course_types'])) {
                    $hasMatchingType = true;
                    break;
                }
            }
            if (!$hasMatchingType) {
                return false;
            }
        }

        return true;
    }

    /**
     * Vérifier si l'utilisateur peut utiliser ce coupon
     */
    public function canBeUsedByStudent(int $studentId): bool
    {
        if (!$this->max_uses_per_user) {
            return true;
        }

        $usageCount = $this->usageLogs()
            ->where('student_id', $studentId)
            ->count();

        return $usageCount < $this->max_uses_per_user;
    }

    /**
     * Vérifier si le coupon peut être utilisé pour cette commande
     */
    public function canBeUsedForOrder(int $userId, string $orderId): bool
    {
        // Check if coupon was already used in this specific order
        $usedInOrder = $this->orderUsages()
            ->where('user_id', $userId)
            ->where('order_id', $orderId)
            ->exists();

        if ($usedInOrder) {
            return false;
        }

        // If allow_multiple_uses is false, check if user has used this coupon before at all
        if (!$this->allow_multiple_uses) {
            $hasUsedBefore = $this->orderUsages()
                ->where('user_id', $userId)
                ->exists();

            if ($hasUsedBefore) {
                return false;
            }
        }

        return true;
    }

    /**
     * Enregistrer l'utilisation du coupon pour une commande
     */
    public function recordOrderUsage(int $userId, string $orderId, float $discountAmount): void
    {
        $this->orderUsages()->create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount
        ]);
    }

    /**
     * Calculer la réduction pour un montant
     */
    public function calculateDiscount(float $amount): float
    {
        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = ($amount * $this->valeur) / 100;
        } else {
            $discount = min($this->valeur, $amount);
        }

        // Apply maximum discount cap if set
        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return $discount;
    }

    /**
     * Obtenir le montant final après réduction
     */
    public function getFinalAmount(float $amount): float
    {
        $discount = $this->calculateDiscount($amount);
        return max(0, $amount - $discount);
    }

    /**
     * Incrémenter le compteur d'utilisation
     */
    public function incrementUsage(): void
    {
        if ($this->limit_utilise !== null && $this->usage_count >= $this->limit_utilise) {
            return;
        }

        $this->increment('usage_count');
    }

    /**
     * Obtenir le statut du coupon
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        $today = Carbon::today();

        if ($this->date_debut > $today) {
            return 'Not yet active';
        }

        if ($this->date_fin < $today) {
            return 'Expired';
        }

        return 'Active';
    }

    /**
     * Obtenir la couleur du statut
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Active' => 'success',
            'Inactive' => 'secondary',
            'Not yet active' => 'info',
            'Expired' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Obtenir le format de la valeur
     */
    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'percentage') {
            return number_format($this->valeur, 0) . '%';
        } else {
            return number_format($this->valeur, 2) . '$';
        }
    }

    /**
     * Obtenir la période de validité
     */
    public function getValidityPeriodAttribute(): string
    {
        return $this->date_debut->format('d/m/Y') . ' - ' . $this->date_fin->format('d/m/Y');
    }

    public function getLimitUtiliseAttribute($value)
    {
        if (!is_null($value)) {
            return $value;
        }

        if (array_key_exists('usage_limit', $this->attributes)) {
            return $this->attributes['usage_limit'];
        }

        return null;
    }

    /**
     * Accessor for min_purchase_amount to map to montant_minimum
     */
    public function getMinPurchaseAmountAttribute()
    {
        return $this->montant_minimum;
    }

    /**
     * Mutator for min_purchase_amount to map to montant_minimum
     */
    public function setMinPurchaseAmountAttribute($value)
    {
        $this->attributes['montant_minimum'] = $value;
    }
}
