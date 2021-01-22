<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Pagination\LengthAwarePaginator;

class SourceCollection extends ResourceCollection
{
    public $preserveKeys = true;
    public $collects = SourceResource::class;

    public ?User $user;

    /**
     * Create a new User resource collection instance.
     *
     * @param  mixed  $resource
     * @param App\Model\User|null $user
     * @return void
     */
    function __construct($resource, User $user = null) 
    {
        $this->user = $user;
        parent::__construct($resource);
       
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta' => [
                'user' => $this->when($this->user !== null, function () {
                    return Arr::only($this->user->toArray(),['id','name']);
                }),
            ],
        ];
    }
}
