<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','cook_id', 'location_id', 'order_price', 'order_date' ,'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'orders_dishes', 'order_id', 'dish_id')
                ->using(OrderDish::class)->withPivot('quantity', 'comment');
    }

    public function additional_ingredient()
    {
        return $this->belongsToMany(Additional_ing::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
