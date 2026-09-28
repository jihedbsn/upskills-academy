<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Installment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_specifique_id',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'payment_method',
        'notes',
        'content_milestone_id',
        'late_fee',
        'installment_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'late_fee' => 'decimal:2',
        'installment_number' => 'integer',
    ];

    /**
     * Get the order specifique that owns the installment.
     */
    public function orderSpecifique(): BelongsTo
    {
        return $this->belongsTo(OrderSpecifique::class);
    }

    /**
     * Get the content milestone linked to this installment.
     */
    public function contentMilestone(): BelongsTo
    {
        return $this->belongsTo(ContentMilestone::class);
    }

    /**
     * Get late fee log entries for this installment.
     */
    public function lateFees()
    {
        return $this->hasMany(LateFeeLog::class);
    }

    /**
     * Get the total amount due including late fees.
     */
    public function getTotalDueAttribute(): float
    {
        return (float) $this->amount + (float) $this->late_fee;
    }

    /**
     * Get the number of overdue days.
     */
    public function getOverdueDaysAttribute(): int
    {
        if (!$this->due_date || !$this->due_date->isPast() || $this->status === 'paid') {
            return 0;
        }
        return (int) $this->due_date->diffInDays(now());
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter paid installments.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to filter pending installments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter overdue installments.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope to filter by due date range.
     */
    public function scopeDueBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('due_date', [$startDate, $endDate]);
    }

    /**
     * Check if the installment is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending' && 
               $this->due_date && 
               $this->due_date->isPast();
    }

    /**
     * Mark the installment as paid.
     */
    public function markAsPaid(string $paymentMethod = null, string $notes = null, $paidDate = null): void
    {
        $resolvedPaidDate = $paidDate ? Carbon::parse($paidDate)->startOfDay() : now();

        $this->update([
            'status' => 'paid',
            'paid_date' => $resolvedPaidDate,
            'payment_method' => $paymentMethod,
            'notes' => $notes,
        ]);
    }

    /**
     * Mark the installment as overdue.
     */
    public function markAsOverdue(): void
    {
        if ($this->status === 'pending' && $this->isOverdue()) {
            $this->update(['status' => 'overdue']);
        }
    }

    /**
     * Get the days until due date (negative if overdue).
     */
    public function getDaysUntilDueAttribute(): int
    {
        if (!$this->due_date) {
            return 0;
        }
        
        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Get formatted due date.
     */
    public function getFormattedDueDateAttribute(): string
    {
        return $this->due_date ? $this->due_date->format('d/m/Y') : 'Not set';
    }

    /**
     * Get formatted paid date.
     */
    public function getFormattedPaidDateAttribute(): string
    {
        return $this->paid_date ? $this->paid_date->format('d/m/Y') : 'Not paid';
    }

    /**
     * Boot method to automatically update overdue status.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($installment) {
            if ($installment->status === 'pending' && $installment->due_date && $installment->due_date->isPast()) {
                $installment->status = 'overdue';
            }
        });
    }
}