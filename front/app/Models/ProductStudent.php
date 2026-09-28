<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'student_id',
        'date',
        'is_active',
        'access_granted_at',
        'expiration_date',
        'is_expired',
        'extension_count',
        'last_expiration_reminder',
        'last_reminder_interval'
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
        'access_granted_at' => 'datetime',
        'expiration_date' => 'datetime',
        'is_expired' => 'boolean',
        'last_expiration_reminder' => 'datetime',
    ];

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // Méthodes
    public function grantAccess()
    {
        $this->update([
            'is_active' => true,
            'access_granted_at' => now()
        ]);
    }

    public function revokeAccess()
    {
        $this->update([
            'is_active' => false,
            'access_granted_at' => null
        ]);
    }

    public function hasAccess()
    {
        return $this->is_active && !$this->is_expired;
    }

    /**
     * Get days remaining until expiration
     * Returns null for lifetime access
     */
    public function daysRemaining()
    {
        if (!$this->expiration_date) {
            return null; // Lifetime access
        }

        $now = now();
        if ($now->greaterThan($this->expiration_date)) {
            return 0; // Expired
        }

        return (int) $now->diffInDays($this->expiration_date, false);
    }

    /**
     * Get formatted access left message
     */
    public function getAccessLeftAttribute()
    {
        if (!$this->expiration_date) {
            return 'Lifetime Access';
        }

        $daysLeft = $this->daysRemaining();

        if ($daysLeft === 0 || $this->is_expired) {
            return 'Expired';
        }

        if ($daysLeft === 1) {
            return '1 day left';
        }

        if ($daysLeft <= 30) {
            return $daysLeft . ' days left';
        }

        $monthsLeft = floor($daysLeft / 30);
        if ($monthsLeft === 1) {
            return '1 month left';
        }

        return $monthsLeft . ' months left';
    }

    /**
     * Get days remaining as integer
     */
    public function getDaysLeftAttribute()
    {
        return $this->daysRemaining();
    }

    /**
     * Check if course is expiring critically (within 5 days)
     */
    public function isExpiringCritically()
    {
        if (!$this->expiration_date) {
            return false;
        }

        $daysLeft = $this->daysRemaining();
        return $daysLeft !== null && $daysLeft <= 5 && $daysLeft > 0;
    }

    /**
     * Check if course is about to expire (within 30 days)
     */
    public function isExpiringSoon()
    {
        if (!$this->expiration_date) {
            return false;
        }

        $daysLeft = $this->daysRemaining();
        return $daysLeft !== null && $daysLeft <= 30 && $daysLeft > 0;
    }
}
