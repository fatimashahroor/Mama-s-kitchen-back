<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ai_suggestion extends Model
{
    use HasFactory;

    protected $fillable = ['suggestion', 'user_id', 'suggestion_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
