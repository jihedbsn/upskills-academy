<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ActiveCartCoupon extends Model
{
    protected $fillable = [
        'session_id',
        'student_id',
        'coupon_id',
        'cart_total',
        'discount_amount',
        'original_cart_total',
        'applied_at',
        'expires_at'
    ];

    protected $casts = [
        'cart_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'original_cart_total' => 'decimal:2',
        'applied_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    /**
     * Relation avec le coupon
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Relation avec l'étudiant (user database)
     * Note: This references students table in user database
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class);
    }

    /**
     * Vérifier si le coupon a expiré
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Scope pour les coupons actifs (non expirés)
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', Carbon::now());
        });
    }

    /**
     * Scope pour une session spécifique
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope pour un étudiant spécifique
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Calculer le discount actuel basé sur le nouveau total du panier
     */
    public function calculateCurrentDiscount(float $newCartTotal): float
    {
        return $this->coupon->calculateDiscount($newCartTotal);
    }
}
