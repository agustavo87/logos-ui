<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'data'
    ];

    /**
     * Get the user that owns the source.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
