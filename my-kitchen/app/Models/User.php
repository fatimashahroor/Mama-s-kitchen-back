<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'bio',
        'image_path',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
           'role' => $this->getRoleNames()
        ];
    }

    public function review()
    {
        return $this->hasMany(Review::class);
    }

    public function location()
    {
        return $this->hasMany(Location::class);
    }

    public function dish()
    {
        return $this->hasMany(Dish::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function additional_ingredient()
    {
        return $this->hasMany(Additional_ing::class);
    }
    public function ai_marketing()
    {
        return $this->hasMany(Ai_marketing::class);
    }

    public function ai_suggestion()
    {
        return $this->hasMany(Ai_suggestion::class);
    }
}