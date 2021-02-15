<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creator extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array'
    ];

    /**
     * Get the user that owns the source.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The sources that belong to the role.
     */
    public function sources()
    {
        return $this->belongsToMany(Source::class);
    }

    
}
