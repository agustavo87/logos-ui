<?php

namespace App\Http\Livewire;

use App\Models\Creator;
use App\Models\Source;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class CreatorsEdit extends Component
{
    public ?Source $source = null;
    public $creators = [];
    
    public $name;
    public $last_name;
    public $type = "author"; // default

    protected const SCHEMA = "0.0.1";

    public $rules = [
        'name' => 'required',
        'last_name' => 'required',
        'type'=> 'required'
    ];

    public function mount($source = null) 
    {
        $this->loadCreators($source); 
    }

    protected function loadCreators($source = null)
    {
        if ($source) {
            $this->creators = $source->creators;
        } elseif ($this->source) {
            $this->creators = $this->source->creators;
        }
    }

    public function render()
    {
        return view('livewire.creators-edit');
    }

    public function save()
    {
        $validatedData = $this->validate();
        $creatorData = [
            'name' => $validatedData['name'],
            'last_name' => $validatedData['last_name']
        ];
        $creator = new Creator([
            'key' => Creator::factory()->getKey($creatorData['name'], $creatorData['last_name']),
            'type' => $validatedData['type'],
            'schema' => self::SCHEMA,
            'data' => $creatorData
        ]);

        $user = auth()->user();

        $creator = $user->creators()->save($creator);
        // dd($this->source);
        if (!$this->source) {
            $this->source = $user->sources()->create([
                'key' => Source::factory()->getKey($creatorData['last_name'], 0000),
                'type' => 'citation.book',
                'schema' => '0.0.1',
                'data' => [
                    'year' => 0000,
                    'title' => '',
                    'editorial' => '',
                    'city' => ''
                ]
            ]);
        }
        $this->source->creators()->attach($creator);
        $this->source->refresh();
        $this->loadCreators();
    }
}
