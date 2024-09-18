<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ai_marketing extends Model
{
    use HasFactory;

    protected $fillable= ['user_id','image_path','caption'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
