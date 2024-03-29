<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'language',
        'country'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the sources of the user.
     */
    public function sources()
    {
        return $this->hasMany(Source::class);
    }

    /**
     * Get the articles of the user.
     */
    public function articles() 
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Get the creators of the user.
     */
    public function creators() 
    {
        return $this->hasMany(Creator::class);
    }


    public function isAdministrator()
    {
        return $this->email === 'agustavo87@gmail.com';
    }
}
