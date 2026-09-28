<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TachePlanifie extends Model
{
    protected $fillable = [
        'course_id',
        'student_id',
        'message',
        'date_time',
        'status',
        'priority',
        'notes',
        'is_send'
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relation avec le cours (Product)
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'course_id');
    }

    /**
     * Relation avec l'étudiant
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Scope pour les tâches en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les tâches complétées
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les tâches annulées
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope pour les tâches par priorité
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope pour les tâches non envoyées
     */
    public function scopeNotSent($query)
    {
        return $query->whereNull('is_send');
    }

    /**
     * Scope pour les tâches envoyées
     */
    public function scopeSent($query)
    {
        return $query->where('is_send', 'sent');
    }

    /**
     * Scope pour les tâches à venir
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date_time', '>', now());
    }

    /**
     * Scope pour les tâches passées
     */
    public function scopePast($query)
    {
        return $query->where('date_time', '<', now());
    }

    /**
     * Accessor pour formater la date
     */
    public function getFormattedDateTimeAttribute()
    {
        return $this->date_time ? $this->date_time->format('d/m/Y H:i') : null;
    }

    /**
     * Accessor pour obtenir le statut en français
     */
    public function getStatusFrAttribute()
    {
        $statuses = [
            'pending' => 'En attente',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Accessor pour obtenir la priorité en français
     */
    public function getPriorityFrAttribute()
    {
        $priorities = [
            'low' => 'Faible',
            'medium' => 'Moyenne',
            'high' => 'Élevée'
        ];

        return $priorities[$this->priority] ?? $this->priority;
    }

    /**
     * Méthode pour marquer la tâche comme terminée
     */
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Méthode pour annuler la tâche
     */
    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Méthode pour marquer l'email comme envoyé
     */
    public function markAsSent()
    {
        $this->update(['is_send' => 'sent']);
    }

    /**
     * Vérifier si la tâche est en retard
     */
    public function isOverdue()
    {
        return $this->status === 'pending' && $this->date_time < now();
    }

    /**
     * Obtenir la couleur de la priorité pour l'affichage
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Obtenir la couleur du statut pour l'affichage
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'success',
            'cancelled' => 'danger',
            'pending' => $this->isOverdue() ? 'danger' : 'warning',
            default => 'secondary'
        };
    }
}
