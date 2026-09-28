<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSuccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'student_id',
        'lien_video',
        'success',
        'admin_notes',
        'submitted_at',
        'validated_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'validated_at' => 'datetime',
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

    public function certificates()
    {
        return $this->hasMany(CertifStudent::class, 'student_success_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('success', 0);
    }

    public function scopeApproved($query)
    {
        return $query->where('success', 1);
    }

    public function scopeRejected($query)
    {
        return $query->where('success', -1);
    }

    // Accesseurs
    public function getStatusAttribute()
    {
        switch ($this->success) {
            case 1:
                return 'approved';
            case -1:
                return 'rejected';
            default:
                return 'pending';
        }
    }

    public function getStatusTextAttribute()
    {
        switch ($this->success) {
            case 1:
                return __('messages.approved');
            case -1:
                return __('messages.rejected');
            default:
                return __('messages.pending');
        }
    }

    public function getStatusColorAttribute()
    {
        switch ($this->success) {
            case 1:
                return 'success';
            case -1:
                return 'danger';
            default:
                return 'warning';
        }
    }

    // Méthodes
    public function isPending()
    {
        return $this->success === 0;
    }

    public function isApproved()
    {
        return $this->success === 1;
    }

    public function isRejected()
    {
        return $this->success === -1;
    }

    public function approve($adminNotes = null)
    {
        $this->update([
            'success' => 1,
            'admin_notes' => $adminNotes,
            'validated_at' => now()
        ]);
    }

    public function reject($adminNotes = null)
    {
        $this->update([
            'success' => -1,
            'admin_notes' => $adminNotes,
            'validated_at' => now()
        ]);
    }

    public function submit($lienVideo = null)
    {
        $this->update([
            'lien_video' => $lienVideo,
            'submitted_at' => now()
        ]);
    }
}
