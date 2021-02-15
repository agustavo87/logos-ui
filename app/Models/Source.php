<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
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
     * The articles that belong to the role.
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    /**
     * The creators that belong to the role.
     */
    public function creators()
    {
        return $this->belongsToMany(Creator::class);
    }
}
