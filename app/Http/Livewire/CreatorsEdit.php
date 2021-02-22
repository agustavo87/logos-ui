<?php

namespace App\Http\Livewire;

use App\Models\Creator;
use App\Models\Source;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class CreatorsEdit extends Component
{
    /**
     * The source than has the creators
     * 
     * @var Source|null
     */
    public ?Source $source = null;
    
    /**
     * The creators of the source
     * 
     * @var array
     */
    public $creators = [];


    /**
     * The suggested creators when the user inputs
     * in the new creator fields.
     * 
     * @var array 
     */
    public $suggestedCreators = [];
    
    /**
     * The current editing creator
     * 
     * @var Creator|null
     */
    public ?Creator $creator = null;

    /**
     * The name of the creator
     * @var string
     */
    public $name = '';

    /**
     * The last name of the creator
     * 
     * @var string
     */
    public $last_name = '';
    
    /**
     * The type of the creator
     * 
     * @var string
     */
    public $type = 'person';    // default
                                // faltaría además la especificación del rol.

    /**
     * Defines the schema of the the creator.
     * 
     * @var string 
     */
    protected const SCHEMA = "0.0.1";

    /**
     * Defines the rules of the specified fields.
     * 
     * @todo esto se podría enlazar directamente al modelo
     * 
     * @var string 
     */
    public $rules = [
        'name' => 'required',
        'last_name' => 'required',
        'type'=> 'required'
    ];

    /**
     * Defines the events and its listeners
     * 
     * @var string 
     */
    protected $listeners = [
        // 'creatorDeleted' => 'handleCreatorDeleted'
        'creatorDetach' => 'handleCreatorDetach'
    ];


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
    

    /**
     * Saves the creator
     * 
     * @todo agregar un chequeo de si el creador ya existe
     * 
     * @param bool $isDirty
     * @return void|string
     */
    public function save(bool $isDirty = true)
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
        $this->source->creators()->attach($creator, [
            'type' => 'author', 
            'relevance' =>  $this->source->creators->count()
        ]);
        $this->source->refresh();
        $this->loadCreators();
    }
   

    /**
     * Manages the detaching of a creator
     *  
     * @param int $id The id of the detached creator.
     * @return void
     */
    public function handleCreatorDetach(int $id): void
    {
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
