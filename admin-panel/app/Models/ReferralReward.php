<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralReward extends Model
{
    protected $fillable = [
        'user_id',
        'referral_id',
        'type',
        'amount',
        'role',
        'claimed_at',
        'spent_at',
        'spent_order_id',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'claimed_at' => 'datetime',
        'spent_at'   => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'user_id');
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    public function isClaimed(): bool
    {
        return $this->claimed_at !== null;
    }
}
