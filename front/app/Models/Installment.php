<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Installment Model (User Project)
 *
 * Represents a single installment payment within an installment order.
 * Tracks payment status, due dates, and late fees.
 */
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
        'payment_receipt',
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

    // ─── Relationships ─────────────────────────────────────────────

    public function orderSpecifique(): BelongsTo
    {
        return $this->belongsTo(OrderSpecifique::class);
    }

    public function contentMilestone(): BelongsTo
    {
        return $this->belongsTo(ContentMilestone::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    // ─── Accessors ─────────────────────────────────────────────────

    public function getTotalDueAttribute(): float
    {
        return (float) $this->amount + (float) $this->late_fee;
    }

    public function getDaysUntilDueAttribute(): int
    {
        if (!$this->due_date) {
            return 0;
        }

        return (int) now()->diffInDays($this->due_date, false);
    }

    public function getOverdueDaysAttribute(): int
    {
        if (!$this->due_date || !$this->due_date->isPast() || $this->status === 'paid') {
            return 0;
        }
        return (int) $this->due_date->diffInDays(now());
    }

    public function getFormattedDueDateAttribute(): string
    {
        return $this->due_date ? $this->due_date->format('d/m/Y') : 'Not set';
    }

    public function getFormattedPaidDateAttribute(): string
    {
        return $this->paid_date ? $this->paid_date->format('d/m/Y') : 'Not paid';
    }

    // ─── Status Checks ─────────────────────────────────────────────

    public function isOverdue(): bool
    {
        return $this->status !== 'paid'
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid' => __('messages.paid'),
            'overdue' => __('messages.overdue'),
            'pending' => __('messages.pending'),
            'awaiting_payment' => __('messages.awaiting_payment'),
            default => $this->status,
        };
    }

    /**
     * Get status color for display.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'success',
            'overdue' => 'danger',
            'pending' => 'warning',
            'awaiting_payment' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Check if installment is awaiting payment confirmation.
     */
    public function isAwaitingPayment(): bool
    {
        return $this->status === 'awaiting_payment';
    }
}
