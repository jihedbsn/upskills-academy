<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LateFeeLog Model
 * 
 * Audit trail for daily late fee charges applied by the cron job.
 * Each record represents a $5 daily penalty for an overdue installment.
 *
 * @property int $id
 * @property int $order_specifique_id
 * @property int $installment_id
 * @property float $fee_amount
 * @property string $charged_date
 * @property string|null $notes
 */
class LateFeeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_specifique_id',
        'installment_id',
        'fee_amount',
        'charged_date',
        'notes',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'charged_date' => 'date',
    ];

    /**
     * Get the installment order this fee belongs to.
     */
    public function orderSpecifique(): BelongsTo
    {
        return $this->belongsTo(OrderSpecifique::class);
    }

    /**
     * Get the specific installment this fee was charged against.
     */
    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class);
    }

    /**
     * Scope: Filter by order.
     */
    public function scopeByOrder($query, int $orderId)
    {
        return $query->where('order_specifique_id', $orderId);
    }

    /**
     * Scope: Filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('charged_date', [$startDate, $endDate]);
    }
}
