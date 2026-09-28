<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ContentMilestone Model
 * 
 * Maps course content (ProductStudy/lectures) to installment months.
 * Used by the Drip Content system to control which content is unlocked
 * based on which installments have been paid.
 *
 * @property int $id
 * @property int $product_id
 * @property int $product_study_id
 * @property int $milestone_month
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

    /**
     * Get the course this milestone belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the study/lecture this milestone controls access to.
     */
    public function productStudy(): BelongsTo
    {
        return $this->belongsTo(ProductStudy::class);
    }

    /**
     * Scope: Filter milestones by product.
     */
    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope: Filter milestones by month.
     */
    public function scopeByMonth($query, int $month)
    {
        return $query->where('milestone_month', $month);
    }

    /**
     * Scope: Get milestones up to a given month (for unlocked content).
     */
    public function scopeUpToMonth($query, int $month)
    {
        return $query->where('milestone_month', '<=', $month);
    }

    /**
     * Check if this milestone's content should be accessible
     * based on the number of paid installments.
     */
    public function isUnlocked(int $paidInstallments): bool
    {
        return $this->milestone_month <= $paidInstallments;
    }
}
