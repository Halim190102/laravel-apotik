<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'category_id' => 'integer',
        'price' => 'integer',
        'stock' => 'integer',
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id', 'id');
    }
}
