<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'student_id',
        'date',
        'is_active',
        'access_granted_at',
        'expiration_date',
        'is_expired',
        'extension_count',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
        'access_granted_at' => 'datetime',
        'expiration_date' => 'datetime',
        'is_expired' => 'boolean',
        'extension_count' => 'integer',
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // Méthodes
    public function grantAccess()
    {
        $this->update([
            'is_active' => true,
            'access_granted_at' => now()
        ]);
    }

    public function revokeAccess()
    {
        $this->update([
            'is_active' => false,
            'access_granted_at' => null
        ]);
    }

    public function hasAccess()
    {
        return $this->is_active;
    }
}
