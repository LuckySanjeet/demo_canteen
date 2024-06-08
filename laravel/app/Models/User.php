<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username', 'first_name', 'last_name', 'email', 'role', 'password', 'mobile', 'profile_image',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

         // Accessor for created_at
         public function getCreatedAtAttribute($value)
         {
             return Carbon::parse($value)->format('d-m-Y H:i:s');
         }

         // Accessor for updated_at
         public function getUpdatedAtAttribute($value)
         {
             return Carbon::parse($value)->format('d-m-Y H:i:s');
         }
         // Accessor for images
         public function getProfileImageAttribute($value)
        {
            $v = 'localhost/demo_canteen/laravel/public/profile_images/'.$value;
            return $value ? $v : null;
        }


}
