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
     * 
     * @return \App\Models\User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The articles that belong to the role.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    /**
     * The creators that belong to the role.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function creators()
    {
        return $this->belongsToMany(Creator::class);
    }

    /**
     * Return a string representation of the Source
     * 
     * @return string
     */
    public function render(): string
    {
        return $this->sourceManager->render($this);
    }

    /**
     * Return a readable name of the type of the source
     * 
     * @return string
     */
    public function name(): string
    {
        return $this->sourceManager->name($this);
    }
}
