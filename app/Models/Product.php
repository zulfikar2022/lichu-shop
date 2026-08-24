<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_name',
        'name',
        'description',
        'price_per_piece',
        'available_quantity',
        'is_available'
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
        ];
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orderedProducts()
    {
        return $this->hasMany(OrderedProduct::class);
    }

}
