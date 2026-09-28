<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponseTicket extends Model
{
    protected $connection = 'mysql';
    
    protected $fillable = [
        'ticket_id',
        'student_id',
        'message',
        'isAdmin'
    ];

    protected $casts = [
        'isAdmin' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Scope for admin responses
     */
    public function scopeAdminResponses($query)
    {
        return $query->where('isAdmin', true);
    }

    /**
     * Scope for student responses
     */
    public function scopeStudentResponses($query)
    {
        return $query->where('isAdmin', false);
    }
}
