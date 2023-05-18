<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'product_id'
    ];

    /**
     * Get the products for the store.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,
            'store_product', 'store_id', 'product_id');
    }

    /**
     * Get the user that own the store
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
