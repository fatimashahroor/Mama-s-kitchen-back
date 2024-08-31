<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function dishes()
    {
        return $this->belongsToMany(Dish::class);
    }

    public function additional_ing()
    {
        return $this->hasOne(Additional_ing::class);
    }
}