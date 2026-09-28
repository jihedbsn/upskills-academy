<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentVideoExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'product_id',
        'lien',
        'video_description',
        'is_valid',
        'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'is_valid' => 'integer'
    ];

    /**
     * Relation avec le modèle Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Relation avec le modèle Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Vérifier si la soumission est validée
     */
    public function isValidated()
    {
        return $this->is_valid === 1;
    }

    /**
     * Vérifier si la soumission est rejetée
     */
    public function isRejected()
    {
        return $this->is_valid === -1;
    }

    /**
     * Vérifier si la soumission est en attente
     */
    public function isPending()
    {
        return $this->is_valid === 0;
    }

    /**
     * Obtenir le statut textuel
     */
    public function getStatusTextAttribute()
    {
        switch ($this->is_valid) {
            case 1:
                return 'Validated';
            case -1:
                return 'Rejected';
            default:
                return 'Pending';
        }
    }

    /**
     * Obtenir la classe CSS pour le statut
     */
    public function getStatusClassAttribute()
    {
        switch ($this->is_valid) {
            case 1:
                return 'success';
            case -1:
                return 'danger';
            default:
                return 'warning';
        }
    }
}
