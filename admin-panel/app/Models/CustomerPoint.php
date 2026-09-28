<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'total_points',
        'available_points',
        'used_points',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'available_points' => 'integer',
        'used_points' => 'integer',
    ];

    /**
     * Points to dollar conversion rate
     * 20 points = $1 discount
     */
    const POINTS_PER_DOLLAR = 20;

    /**
     * Dollars to points earning rate
     * $1 spent = 1 point earned
     */
    const POINTS_EARNED_PER_DOLLAR = 1;

    /**
     * Get the student that owns the points.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get all transactions for this customer's points.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PointsTransaction::class, 'student_id', 'student_id');
    }

    /**
     * Get the dollar value of available points.
     */
    public function getAvailableDiscountAttribute(): float
    {
        return round($this->available_points / self::POINTS_PER_DOLLAR, 2);
    }

    /**
     * Calculate how many points are needed for a specific discount.
     */
    public static function pointsNeededForDiscount(float $discount): int
    {
        return (int) ceil($discount * self::POINTS_PER_DOLLAR);
    }

    /**
     * Calculate the discount amount for given points.
     */
    public static function calculateDiscount(int $points): float
    {
        return round($points / self::POINTS_PER_DOLLAR, 2);
    }

    /**
     * Calculate points earned for a purchase amount.
     */
    public static function calculatePointsEarned(float $amount): int
    {
        return (int) floor($amount * self::POINTS_EARNED_PER_DOLLAR);
    }

    /**
     * Add points to customer balance.
     */
    public function addPoints(int $points): void
    {
        $this->total_points += $points;
        $this->available_points += $points;
        $this->save();
    }

    /**
     * Use points from customer balance.
     */
    public function usePoints(int $points): bool
    {
        if ($points > $this->available_points) {
            return false;
        }

        $this->available_points -= $points;
        $this->used_points += $points;
        $this->save();

        return true;
    }

    /**
     * Get or create customer points record.
     */
    public static function getOrCreate(int $studentId): self
    {
        return self::firstOrCreate(
            ['student_id' => $studentId],
            [
                'total_points' => 0,
                'available_points' => 0,
                'used_points' => 0,
            ]
        );
    }
}
