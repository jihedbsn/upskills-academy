<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponDetaille extends Model
{
    protected $table = 'coupon_detailles';
    
    protected $fillable = [
        'coupon_id',
        'product_id'
    ];

    /**
     * Relation avec le coupon
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Relation avec le produit
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}