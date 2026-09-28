<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'metakeyword',
        'content',
        'metadescription',
        'type',
        'products_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }
}
