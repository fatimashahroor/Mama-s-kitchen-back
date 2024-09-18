<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'steps', 'price', 'image_path', 'available_on', 'duration', 'diet_type', 
    'main_ingredients'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'orders_dishes', 'dish_id',
         'order_id')->withPivot('quantity', 'comment');
    }

    public function additional_ingredients()
{
    return $this->belongsToMany(Additional_ing::class, 'dishes_additional_ings', 'dish_id', 'additional_ing_id'); 
}

}
