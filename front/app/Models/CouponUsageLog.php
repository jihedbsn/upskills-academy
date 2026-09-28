<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CouponUsageLog extends Model
{
    protected $fillable = [
        'coupon_id',
        'student_id',
        'order_id',
        'discount_amount',
        'original_price',
        'final_price',
        'cart_total_before',
        'cart_total_after',
        'used_at',
        'user_type',
        'device_type',
        'browser',
        'referral_source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'ip_address',
        'country_code',
        'currency',
        'was_stacked',
        'stacked_with_coupons',
        'stack_order',
        'products_purchased',
        'user_agent',
        'session_data'
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'original_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'cart_total_before' => 'decimal:2',
        'cart_total_after' => 'decimal:2',
        'used_at' => 'datetime',
        'was_stacked' => 'boolean',
        'stacked_with_coupons' => 'array',
        'products_purchased' => 'array',
        'session_data' => 'array'
    ];

    /**
     * Relation avec le coupon
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Relation avec l'étudiant
     * Note: This references students table in user database
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class);
    }

    /**
     * Relation avec la commande
     * Note: This references orders table in user database
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    /**
     * Relation avec les commissions partenaires
     */
    public function partnerCommissions(): HasMany
    {
        return $this->hasMany(PartnerCommission::class);
    }

    /**
     * Calculer les économies réalisées
     */
    public function calculateSavings(): float
    {
        return $this->original_price - $this->final_price;
    }

    /**
     * Scope pour un coupon spécifique
     */
    public function scopeForCoupon($query, int $couponId)
    {
        return $query->where('coupon_id', $couponId);
    }

    /**
     * Scope pour un étudiant spécifique
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope pour un type d'utilisateur
     */
    public function scopeForUserType($query, string $userType)
    {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope pour une période
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('used_at', [$startDate, $endDate]);
    }

    /**
     * Scope pour les coupons stackés
     */
    public function scopeStacked($query)
    {
        return $query->where('was_stacked', true);
    }

    /**
     * Scope pour une source de référence
     */
    public function scopeFromReferral($query, string $referralSource)
    {
        return $query->where('referral_source', $referralSource);
    }
}
