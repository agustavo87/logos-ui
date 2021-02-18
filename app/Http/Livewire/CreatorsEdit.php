<?php

namespace App\Http\Livewire;

use App\Models\Creator;
use App\Models\Source;
use Livewire\Component;

class CreatorsEdit extends Component
{
    public Source $source;
    protected $creators;
    public $arrCreators;

    public function mount() {
        if ($this->source) {
            $this->creators = $this->source->creators;
        } else {
            $this->creators = collect([
                new Creator([
                    'user_id' => null,
                    'key' => null,
                    'type' => null,
                    'schema' => null,
                    'data' => [
                        'name' => null,
                        'last_name' => null
                    ]
                ])
            ]);
        }
        $this->arrCreators = $this->creators->toArray();
    }

    public function render()
    {
        return view('livewire.creators-edit');
    }
}
