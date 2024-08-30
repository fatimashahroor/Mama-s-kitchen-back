<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Additional_ing extends Model
{
    use HasFactory;

    protected $fillable = ['ingredient_id', 'user_id', 'cost'];

    public function order()
    {
        return $this->belongsToMany(Order::class);
    }

    public function dish()
    {
        return $this->belongsToMany(Dish::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
