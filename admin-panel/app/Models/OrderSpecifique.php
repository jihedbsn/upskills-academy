<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Get the student that owns the order.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the product that owns the order.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product variation that owns the order.
     */
    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class);
    }

    /**
     * Get the installments for the order.
     */
    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by student.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to filter by product.
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Get the product title from variation or fallback to product's first variation name.
     */
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

    /**
     * Get the payment progress percentage.
     */
    public function getPaymentProgressAttribute(): float
    {
        if ($this->total_price <= 0) {
            return 0;
        }

        return round(($this->paid_amount / $this->total_price) * 100, 2);
    }

    /**
     * Check if the order is fully paid.
     */
    public function isFullyPaid(): bool
    {
        return $this->status === 'paid' || $this->remaining_amount <= 0;
    }

    /**
     * Check if the order is partially paid.
     */
    public function isPartiallyPaid(): bool
    {
        return $this->status === 'partial' || ($this->paid_amount > 0 && $this->remaining_amount > 0);
    }

    /**
     * Update order status based on payment progress.
     */
    public function updateStatus(): void
    {
        if ($this->remaining_amount <= 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'pending';
        }

        $this->save();
    }

    /**
     * Get late fee log entries.
     */
    public function lateFees(): HasMany
    {
        return $this->hasMany(LateFeeLog::class);
    }

    /**
     * Add a new installment payment.
     */
    public function addInstallmentPayment(float $amount, ?string $paymentMethod = null, ?string $notes = null): Installment
    {
        $installment = $this->installments()->create([
            'amount' => $amount,
            'paid_date' => now(),
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'notes' => $notes,
        ]);

        // Update order amounts
        $this->paid_amount += $amount;
        $this->remaining_amount = max(0, $this->total_price - $this->paid_amount);
        $this->paid_installments = $this->installments()->where('status', 'paid')->count();

        $this->updateStatus();

        // Lift suspension if payment was made
        if ($this->is_suspended) {
            $this->liftSuspension();
        }

        return $installment;
    }

    /**
     * Suspend the student's access due to overdue payment.
     */
    public function suspend(): void
    {
        $this->update([
            'is_suspended' => true,
            'suspended_at' => now(),
        ]);

        // Revoke course access
        $productStudent = ProductStudent::where('student_id', $this->student_id)
            ->where('product_id', $this->product_id)
            ->first();

        if ($productStudent) {
            $productStudent->revokeAccess();
        }
    }

    /**
     * Lift suspension and restore access after payment.
     */
    public function liftSuspension(): void
    {
        $this->update([
            'is_suspended' => false,
            'suspended_at' => null,
        ]);

        // Restore course access
        $productStudent = ProductStudent::where('student_id', $this->student_id)
            ->where('product_id', $this->product_id)
            ->first();

        if ($productStudent) {
            $productStudent->grantAccess();
        } else {
            ProductStudent::create([
                'product_id' => $this->product_id,
                'student_id' => $this->student_id,
                'date' => now()->toDateString(),
                'is_active' => true,
                'access_granted_at' => now(),
            ]);
        }
    }

    /**
     * Check if the student has any overdue installments.
     */
    public function hasOverdueInstallments(): bool
    {
        return $this->installments()
            ->where('status', 'overdue')
            ->exists();
    }

    /**
     * Get the total outstanding amount including late fees.
     */
    public function getTotalDueAttribute(): float
    {
        return (float) $this->remaining_amount + (float) $this->late_fee_total;
    }

    /**
     * Check if all payments are complete (Total_Due = 0).
     */
    public function isAllPaid(): bool
    {
        return $this->total_due <= 0;
    }

    /**
     * Get the number of content months the student has unlocked.
     * Equal to the number of paid installments.
     */
    public function getUnlockedMonthsAttribute(): int
    {
        return $this->paid_installments;
    }

    /**
     * Check if the student can access the final exam.
     * Requires Total_Due = 0 (all installments fully paid).
     */
    public function canAccessFinalExam(): bool
    {
        return $this->isAllPaid();
    }

    /**
     * Check if the student can receive a certificate.
     * Requires all installments paid AND course content completed.
     */
    public function canReceiveCertificate(): bool
    {
        return $this->isAllPaid();
    }
}
