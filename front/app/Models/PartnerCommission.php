<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class PartnerCommission extends Model
{
    protected $fillable = [
        'partner_id',
        'coupon_usage_id',
        'order_id',
        'order_total',
        'commission_rate',
        'commission_amount',
        'discount_amount',
        'base_amount',
        'tax_amount',
        'fee_amount',
        'net_commission',
        'status',
        'status_reason',
        'calculated_at',
        'approved_at',
        'paid_at',
        'cancelled_at',
        'payment_batch_id',
        'payment_reference',
        'payment_method',
        'payment_details',
        'approved_by',
        'paid_by',
        'admin_notes',
        'currency',
        'exchange_rate',
        'original_amount',
        'dispute_opened_at',
        'dispute_reason',
        'dispute_resolved_at'
    ];

    protected $casts = [
        'order_total' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_commission' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'original_amount' => 'decimal:2',
        'payment_details' => 'array',
        'calculated_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'dispute_opened_at' => 'datetime',
        'dispute_resolved_at' => 'datetime'
    ];

    /**
     * Relation avec le partenaire
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(AffiliatePartner::class, 'partner_id');
    }

    /**
     * Relation avec le log d'utilisation du coupon
     */
    public function couponUsage(): BelongsTo
    {
        return $this->belongsTo(CouponUsageLog::class, 'coupon_usage_id');
    }

    /**
     * Relation avec le coupon via le log d'utilisation
     */
    public function coupon()
    {
        return $this->hasOneThrough(
            Coupon::class,
            CouponUsageLog::class,
            'id', // Foreign key on CouponUsageLog table
            'id', // Foreign key on Coupon table
            'coupon_usage_id', // Local key on PartnerCommission table
            'coupon_id' // Local key on CouponUsageLog table
        );
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
     * Relation avec l'admin qui a approuvé
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    /**
     * Relation avec l'admin qui a payé
     */
    public function payer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'paid_by');
    }

    /**
     * Get sale amount (alias for order_total)
     */
    public function getSaleAmountAttribute(): float
    {
        return $this->order_total ?? 0.0;
    }

    /**
     * Calculer la commission
     */
    public function calculateCommission(): float
    {
        if ($this->base_amount) {
            return ($this->base_amount * $this->commission_rate) / 100;
        }

        return ($this->order_total * $this->commission_rate) / 100;
    }

    /**
     * Marquer comme payé
     */
    public function markAsPaid(int $adminId, string $paymentReference = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_by' => $adminId,
            'payment_reference' => $paymentReference
        ]);

        // Update partner's totals
        $partner = $this->partner;
        $partner->increment('total_paid', $this->net_commission);
        $partner->decrement('pending_earnings', $this->net_commission);
    }

    /**
     * Approuver la commission
     */
    public function approve(int $adminId): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $adminId
        ]);
    }

    /**
     * Annuler la commission
     */
    public function cancel(string $reason, int $adminId): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'status_reason' => $reason,
            'approved_by' => $adminId
        ]);

        // Update partner's pending earnings
        $partner = $this->partner;
        $partner->decrement('pending_earnings', $this->net_commission);
    }

    /**
     * Ouvrir un litige
     */
    public function openDispute(string $reason): void
    {
        $this->update([
            'status' => 'disputed',
            'dispute_opened_at' => now(),
            'dispute_reason' => $reason
        ]);
    }

    /**
     * Résoudre un litige
     */
    public function resolveDispute(string $resolution): void
    {
        $this->update([
            'dispute_resolved_at' => now(),
            'admin_notes' => ($this->admin_notes ?? '') . "\n\nDispute Resolution: " . $resolution
        ]);
    }

    /**
     * Scope pour les commissions en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les commissions approuvées
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope pour les commissions payées
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope pour un partenaire spécifique
     */
    public function scopeForPartner($query, int $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    /**
     * Scope pour un lot de paiement
     */
    public function scopeForBatch($query, string $batchId)
    {
        return $query->where('payment_batch_id', $batchId);
    }

    /**
     * Scope pour une période
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('calculated_at', [$startDate, $endDate]);
    }
}
