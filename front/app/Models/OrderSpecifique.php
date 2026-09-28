<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * OrderSpecifique Model (User Project)
 *
 * Represents an installment-based order for a course.
 * Tracks payment progress, suspension status, and content access.
 */
class OrderSpecifique extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'product_id',
        'product_variation_id',
        'total_price',
        'paid_amount',
        'remaining_amount',
        'status',
        'total_installments',
        'paid_installments',
        'notes',
        'is_suspended',
        'suspended_at',
        'late_fee_total',
        'payment_type',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'total_installments' => 'integer',
        'paid_installments' => 'integer',
        'is_suspended' => 'boolean',
        'suspended_at' => 'datetime',
        'late_fee_total' => 'decimal:2',
    ];

    // ─── Relationships ─────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class)->orderBy('installment_number');
    }

    public function lateFees(): HasMany
    {
        return $this->hasMany(LateFeeLog::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'partial']);
    }

    public function scopeSuspended($query)
    {
        return $query->where('is_suspended', true);
    }

    // ─── Accessors ─────────────────────────────────────────────────

    public function getProductTitleAttribute(): string
    {
        // 1. Direct variation link
        if ($this->productVariation && $this->productVariation->name) {
            return $this->productVariation->name;
        }

        // 2. Fallback: get name from product's first variation (prefer English)
        if ($this->product) {
            $variation = $this->product->variations()
                ->orderByRaw("FIELD(langue, 'en', 'sv', 'ar') ASC")
                ->first();

            if ($variation && $variation->name) {
                return $variation->name;
            }

            return "Product #{$this->product->id} - {$this->product->period}";
        }

        return 'Unknown Product';
    }

    public function getPaymentProgressAttribute(): float
    {
        if ($this->total_price <= 0) {
            return 0;
        }

        return round(($this->paid_amount / $this->total_price) * 100, 2);
    }

    public function getTotalDueAttribute(): float
    {
        return (float) $this->remaining_amount + (float) $this->late_fee_total;
    }

    public function getUnlockedMonthsAttribute(): int
    {
        return $this->paid_installments;
    }

    public function getNextInstallmentAttribute()
    {
        return $this->installments()
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('installment_number')
            ->first();
    }

    // ─── Status Checks ─────────────────────────────────────────────

    public function isFullyPaid(): bool
    {
        return $this->status === 'paid' || $this->remaining_amount <= 0;
    }

    public function isPartiallyPaid(): bool
    {
        return $this->status === 'partial' || ($this->paid_amount > 0 && $this->remaining_amount > 0);
    }

    public function isAllPaid(): bool
    {
        return $this->total_due <= 0;
    }

    public function hasOverdueInstallments(): bool
    {
        return $this->installments()
            ->where('status', 'overdue')
            ->exists();
    }

    public function canAccessFinalExam(): bool
    {
        return $this->isAllPaid();
    }

    public function canReceiveCertificate(): bool
    {
        return $this->isAllPaid();
    }
}
