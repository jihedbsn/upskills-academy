<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliatePartner extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'website',
        'contact_person',
        'social_media_handle',
        'social_links',
        'bio',
        'commission_rate',
        'total_earnings',
        'total_paid',
        'pending_earnings',
        'total_referrals',
        'successful_conversions',
        'conversion_rate',
        'total_revenue_generated',
        'is_active',
        'auto_approve_coupons',
        'max_coupon_uses',
        'max_commission_per_month',
        'partnership_start_date',
        'partnership_end_date',
        'terms_agreed',
        'terms_agreed_at',
        'payment_details',
        'payment_frequency',
        'payment_threshold',
        'referral_code',
        'utm_source',
        'tags',
        'admin_notes',
        'status',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'social_links' => 'array',
        'commission_rate' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'pending_earnings' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'total_revenue_generated' => 'decimal:2',
        'is_active' => 'boolean',
        'auto_approve_coupons' => 'boolean',
        'max_commission_per_month' => 'decimal:2',
        'partnership_start_date' => 'date',
        'partnership_end_date' => 'date',
        'terms_agreed_at' => 'datetime',
        'payment_details' => 'array',
        'tags' => 'array',
        'approved_at' => 'datetime'
    ];

    /**
     * Relation avec les coupons
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Relation avec les commissions
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(PartnerCommission::class, 'partner_id');
    }

    /**
     * Relation avec l'admin qui a approuvé
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    /**
     * Calculer le total des gains
     */
    public function calculateTotalEarnings(): float
    {
        return $this->commissions()
            ->whereIn('status', ['approved', 'paid'])
            ->sum('commission_amount');
    }

    /**
     * Calculer le total payé
     */
    public function calculateTotalPaid(): float
    {
        return $this->commissions()
            ->where('status', 'paid')
            ->sum('commission_amount');
    }

    /**
     * Calculer les gains en attente
     */
    public function calculatePendingEarnings(): float
    {
        return $this->commissions()
            ->where('status', 'pending')
            ->sum('commission_amount');
    }

    /**
     * Obtenir le rapport mensuel
     */
    public function getMonthlyReport(int $year, int $month): array
    {
        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = now()->setYear($year)->setMonth($month)->endOfMonth();

        $commissions = $this->commissions()
            ->whereBetween('calculated_at', [$startDate, $endDate])
            ->get();

        return [
            'total_commissions' => $commissions->count(),
            'total_earned' => $commissions->sum('commission_amount'),
            'total_paid' => $commissions->where('status', 'paid')->sum('commission_amount'),
            'pending' => $commissions->where('status', 'pending')->sum('commission_amount'),
            'average_commission' => $commissions->avg('commission_amount'),
        ];
    }

    /**
     * Scope pour les partenaires actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('status', 'approved');
    }

    /**
     * Scope pour les partenaires en attente d'approbation
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les partenaires par statut
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Vérifier si le partenaire est actif et approuvé
     */
    public function isActiveAndApproved(): bool
    {
        return $this->is_active && $this->status === 'approved';
    }

    /**
     * Vérifier si le seuil de paiement est atteint
     */
    public function hasReachedPaymentThreshold(): bool
    {
        return $this->pending_earnings >= $this->payment_threshold;
    }
}
