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
    public $suggestedCreators = [];
    
    public ?Creator $creator = null;
    public $name = '';
    public $last_name = '';
    public $type = 'author'; // default

    protected const SCHEMA = "0.0.1";

    protected $listeners = [
        // 'creatorDeleted' => 'handleCreatorDeleted'
        'creatorDetach' => 'handleCreatorDetach'
    ];

    public $rules = [
        'name' => 'required',
        'last_name' => 'required',
        'type'=> 'required'
    ];

    public function logea()
    {
        dd('hola');
    }

    public function mount($source = null) 
    {
        $this->loadCreators($source);
        $this->loadSuggestedCreators(); 
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

    public function save($isDirty = true)
    {
        if ($isDirty) {
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

        } elseif ($this->creator) {

            $creator = $this->creator;

        } else {

            return 'error no hay creador';

        }

       
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
        $this->reset(['name', 'last_name', 'type', 'creator']);
        $this->source->creators()->attach($creator);
        $this->source->refresh();
        $this->loadCreators();
    }

    /**
     * Actualiza la vista de los creadores luego de que se borró un creador.
     */
    public function handleCreatorDetach($id)
    {
        // dd('creator deleted');
        $this->source->creators()->detach($id);
        $this->source->refresh();  // it's neccesary?
        $this->loadCreators();
    }

    /**
     * Updates the suggestion options
     */
    public function handleInput()
    {
        $this->loadSuggestedCreators();
    }


    public function loadSuggestedCreators()
    {
        $query = Creator::where('user_id', auth()->user()->id);
        if ($this->name !== '') {
            $query = $query->where('data->name', 'like', "%{$this->name}%");
        };
        if ($this->last_name !== '') {
            $query = $query->where('data->last_name', 'like', "%{$this->last_name}%");
        };
        $this->suggestedCreators = $query->get();
    }


    public function select($id)
    {
        $this->creator = Creator::findOrFail($id);
        $this->name = $this->creator->data['name'];
        $this->last_name = $this->creator->data['last_name'];
    }

}
