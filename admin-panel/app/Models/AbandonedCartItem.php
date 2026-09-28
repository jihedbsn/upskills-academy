<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbandonedCartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'abandoned_cart_id',
        'product_id',
        'package_id',
        'book_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the abandoned cart that owns the item.
     */
    public function abandonedCart()
    {
        return $this->belongsTo(AbandonedCart::class);
    }

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the package.
     */
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    /**
     * Get the book.
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    /**
     * Get the item name (product, package, or book).
     */
    public function getItemNameAttribute()
    {
        if ($this->product) {
            return $this->product->titre ?? $this->product->title ?? 'Product';
        } elseif ($this->package) {
            return $this->package->name ?? 'Package';
        } elseif ($this->book) {
            return $this->book->title ?? 'Book';
        }
        return 'Unknown Item';
    }
}
