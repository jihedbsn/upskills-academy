<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    protected $fillable = [
        'product_id',
        'book_id',
        'student_id',
        'price',
        'quantity',
        'payment_success',
        'payment_method',
        'payment_description',
        'payment_receipt',
        'payment_status',
        'rejection_comment',
        'transaction_id',
        'notes',
        'first_name',
        'last_name',
        'email',
        'phone',
        'country',
        'city',
        'address',
        'zip_code',
        'stripe_session_id',
        'stripe_payment_intent',
        'points_used',
        'points_discount',
        'points_processed',
    ];

    protected $casts = [
        'payment_success' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('payment_success', true);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByBook($query, $bookId)
    {
        return $query->where('book_id', $bookId);
    }

    // Méthodes
    public function isForCourse()
    {
        return !is_null($this->product_id);
    }

    public function isForBook()
    {
        return !is_null($this->book_id);
    }

    public function getItemName()
    {
        if ($this->isForCourse()) {
            return $this->product->titre ?? 'Course';
        }
        if ($this->isForBook()) {
            return $this->book->titre ?? 'Book';
        }
        return 'Unknown Item';
    }

    public function getItemType()
    {
        if ($this->isForCourse()) {
            return 'course';
        }
        if ($this->isForBook()) {
            return 'book';
        }
        return 'unknown';
    }
}
