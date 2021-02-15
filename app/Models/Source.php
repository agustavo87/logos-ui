<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Logos\Sources; // Luego implementarlo en el service provider de logos

class Source extends Model
{
    use HasFactory;


    public Sources $sourceManager;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->sourceManager = new Sources();
    }

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

    public function render()
    {
        return $this->sourceManager->render($this);
    }
}
