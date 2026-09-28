<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'student_id',
        'sujet',
        'ticket_iscomplet'
    ];

    protected $casts = [
        'ticket_iscomplet' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ResponseTicket::class);
    }
}
