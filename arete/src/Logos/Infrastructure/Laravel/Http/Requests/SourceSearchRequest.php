<?php

namespace Arete\Logos\Infrastructure\Laravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SourceSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'string',
            'ownerID' => 'integer',
            'attributes' => 'array',
            'participations' => 'array',
        ];
    }
}
