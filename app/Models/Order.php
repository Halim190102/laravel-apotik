<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $hidden = ['cartItems'];

    protected $appends = ['pivot'];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function cartItems()
    {
        return $this->belongsToMany(CartItem::class, 'order_detail', 'order_id', 'cart_item_id')
            ->withTimestamps();
    }

    public function getPivotAttribute()
    {
        return $this->cartItems->map(function ($item) {
            return [
                'order_id' => $item->pivot->order_id,
                'cart_item_id' => $item->pivot->cart_item_id,
                'created_at' => $item->pivot->created_at,
                'updated_at' => $item->pivot->updated_at,
            ];
        });
    }
}
