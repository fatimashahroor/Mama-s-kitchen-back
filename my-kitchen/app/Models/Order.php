<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'location_id', 'order_price', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function dish()
    {
        return $this->belongsToMany(Dish::class);
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
