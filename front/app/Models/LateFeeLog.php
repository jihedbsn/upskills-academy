<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LateFeeLog Model (User Project)
 * 
 * Audit trail for daily late fee charges.
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

    public function orderSpecifique(): BelongsTo
    {
        return $this->belongsTo(OrderSpecifique::class);
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class);
    }
}
