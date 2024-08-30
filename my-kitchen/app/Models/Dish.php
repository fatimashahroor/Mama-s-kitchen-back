<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'steps', 'price', 'image_path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function additional_ingredients()
    {
        return $this->belongsToMany(Additional_ing::class);
    }
}
