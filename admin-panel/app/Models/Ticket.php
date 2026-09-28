<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $connection = 'mysql';
    
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

    /**
     * Scope for open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('ticket_iscomplet', false);
    }

    /**
     * Scope for completed tickets
     */
    public function scopeCompleted($query)
    {
        return $query->where('ticket_iscomplet', true);
    }

    /**
     * Get formatted ticket ID
     */
    public function getFormattedIdAttribute()
    {
        return '#TKT-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}
