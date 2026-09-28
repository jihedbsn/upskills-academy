<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AnisTest extends Model
{
    protected $table = 'anis_test';
    
    protected $fillable = [
        'task_name',
        'task_description',
        'scheduled_time',
        'actual_created_time',
        'status',
        'email_sent',
        'notes'
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'actual_created_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scope pour les tâches en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les tâches d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_time', Carbon::today());
    }

    /**
     * Scope pour les tâches de demain
     */
    public function scopeTomorrow($query)
    {
        return $query->whereDate('scheduled_time', Carbon::tomorrow());
    }

    /**
     * Marquer comme traité
     */
    public function markAsProcessed()
    {
        $this->update(['status' => 'processed']);
    }

    /**
     * Marquer l'email comme envoyé
     */
    public function markEmailSent()
    {
        $this->update(['email_sent' => 'yes']);
    }

    /**
     * Obtenir le temps d'attente
     */
    public function getWaitTimeAttribute()
    {
        return $this->actual_created_time->diffInMinutes($this->scheduled_time);
    }
}