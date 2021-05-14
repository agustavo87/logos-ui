<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Arete\Logos\Services\Sources; // Luego implementarlo en el service provider de logos
use Illuminate\Support\Facades\DB;

class Source extends Model
{
    use HasFactory;

    public Sources $sourceManager;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array'
    ];

    /**
     * Faker instance if it is required it i has to be generated.
     * 
     * @var null
     */
    protected $faker = null;

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
        return $this->belongsToMany(Creator::class)
            ->withPivot('type','relevance')
            ->using(CreatorSource::class)
            ->as('role');
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

    /**
     * Generate key 
     * 
     * If data suplied is in DB or absent, generates random.
     * 
     * @param null $last_name
     * @param null $year
     * 
     * @return [type]
     */
    public function generateKey($last_name = null, $year = null): string
    {
        
        $faker = \Faker\Factory::create();
        if (!$last_name) {
            $last_name = $faker->lastName();
        }
        
        $year = $this->data['year'] ? $this->data['year'] : $faker->numberBetween(1950,2019);
        $key = strtolower("{$last_name}{$year}");
        
        while ($this->keyExist($key)) {
            $last_name = $faker->lastName();
            $key = strtolower("{$last_name}{$year}");
        }

        return  $key;
    }

    /**
     * If the key already exists in the user sources
     * 
     * @param mixed $key
     * 
     * @return [type]
     */
    public function keyExist($key)
    {
        return DB::table('sources')->where('user_id', $this->user->id)
                                   ->where('key', $key)->exists();
    }

}
