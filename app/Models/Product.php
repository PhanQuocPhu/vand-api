<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'user_id',
        'store_id'
    ];

    /**
     * Get the store that owns the product.
     */
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class,
            'store_product', 'product_id', 'store_id');
    }

    /**
     * Get the user that sell the product.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
