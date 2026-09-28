<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'student_id',
        'book_id',
        'product_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer'
    ];

    /**
     * Relation avec le Student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relation avec le Book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Relation avec le Product (Course)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relation avec le Package
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Accesseur pour le prix total de l'item
     */
    public function getTotalPriceAttribute()
    {
        return $this->price * $this->quantity;
    }

    /**
     * Accesseur pour le prix formaté
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Accesseur pour le prix total formaté
     */
    public function getFormattedTotalPriceAttribute()
    {
        return '$' . number_format($this->total_price, 2);
    }

    /**
     * Accesseur pour le titre de l'item (livre ou cours)
     */
    public function getItemTitleAttribute()
    {
        if ($this->book) {
            return $this->book->titre;
        }
        if ($this->product) {
            return $this->product->titre;
        }
        return 'Item inconnu';
    }

    /**
     * Accesseur pour le type d'item
     */
    public function getItemTypeAttribute()
    {
        if ($this->book) {
            return 'book';
        }
        if ($this->product) {
            return 'course';
        }
        return 'unknown';
    }

    /**
     * Scope pour les livres
     */
    public function scopeBooks($query)
    {
        return $query->whereNotNull('book_id');
    }

    /**
     * Scope pour les cours
     */
    public function scopeCourses($query)
    {
        return $query->whereNotNull('product_id');
    }
}
