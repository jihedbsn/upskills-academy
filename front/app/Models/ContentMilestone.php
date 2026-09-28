<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ContentMilestone Model (User Project)
 * 
 * Maps course content (ProductStudy/lectures) to installment months.
 * Used by the Drip Content system to control content access.
 */
class ContentMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_study_id',
        'milestone_month',
    ];

    protected $casts = [
        'milestone_month' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productStudy(): BelongsTo
    {
        return $this->belongsTo(ProductStudy::class);
    }

    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeUpToMonth($query, int $month)
    {
        return $query->where('milestone_month', '<=', $month);
    }

    /**
     * Check if this milestone's content should be accessible.
     */
    public function isUnlocked(int $paidInstallments): bool
    {
        return $this->milestone_month <= $paidInstallments;
    }
}
