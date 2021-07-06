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
            'type' => 'nullable|string',
            'ownerID' => 'nullable|integer',
            'attribute.*.name' => 'nullable|string',
            'attribute.*.value' => 'nullable|string',
            'participations' => 'nullable|array',
        ];
    }
}
