<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertifStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'product_id',
        'certif_id',
        'student_success_id',
        'serial_number',
        'file_path',
        'generated_at',
        'certificate_date',
        'is_valid',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'certificate_date' => 'date',
        'is_valid' => 'boolean',
    ];

    // Relations
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function certif()
    {
        return $this->belongsTo(Certif::class, 'certif_id');
    }

    public function studentSuccess()
    {
        return $this->belongsTo(StudentSuccess::class, 'student_success_id');
    }

    // Accesseurs
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return asset('upload/certif-student/' . $this->file_path);
        }
        return null;
    }

    public function getSerialNumberAttribute($value)
    {
        // Si pas de serial_number, générer basé sur l'ID
        if (!$value) {
            return 'CERT-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
        }
        return $value;
    }

    // Méthodes
    public function generateSerialNumber()
    {
        $serialNumber = 'CERT-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
        return $serialNumber;
    }

    public function isValid()
    {
        return $this->is_valid;
    }

    public function markAsValid()
    {
        $this->is_valid = true;
        $this->save();
    }

    public function markAsInvalid()
    {
        $this->is_valid = false;
        $this->save();
    }
}
