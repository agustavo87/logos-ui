<?php

namespace App\Http\Livewire;

use App\Models\Creator;
use App\Models\Source;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class CreatorsEdit extends Component
{
    public Source $source;
    protected $rules = [
        'creators.*.data.name' => 'string',
        'creators.*.data.last_name' => 'string',
        'creators.*.type' => 'string',
        'creators.*.key' => 'string'
    ];
    public $creators;

    public function mount($source = null) {
        if ($source) {
            $this->creators = $this->source->creators;
        } else {
            $this->creators = new EloquentCollection([
                new Creator([
                    'id' => 0,
                    'user_id' => null,
                    'key' => null,
                    'type' => null,
                    'schema' => null,
                    'data' => [
                        'name' => 'prueba',
                        'last_name' => null
                    ]
                ])
            ]);
        }
    }

    public function render()
    {
        return view('livewire.creators-edit');
    }
}
