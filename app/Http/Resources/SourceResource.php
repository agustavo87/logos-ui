<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'owner_id' => $this->user_id,
            'key' => $this->key,
            'data' => $this->data,
            'created' => $this->created_at,
            'updated' => $this->updated_at
        ];

    }
}
