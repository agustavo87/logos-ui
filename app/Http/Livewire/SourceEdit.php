<?php

namespace App\Http\Livewire;

use App\Models\Source;
use Livewire\Component;

class SourceEdit extends Component
{
    public $listen = 'source-edit';
    public $key;
    public $data;
    public $sourceSchema; // schema tag = type:version
    public Source $source;
    public $withBg = true;
    public $source_id;

    protected const SUPPORTED_SCHEMAS = [
        // schema tag => name
        'citation.book:0.0.1' => "Libro",
        'citation.article:0.0.1' => "Artículo de Revista Académica"
    ];

    public function mount($source_id = null)
    {
        if ($source_id) {
            $this->setSource($source_id);
            return;
        } 
        $this->setNewSource();
    }

    public function render()
    {
        return view('livewire.source-edit', [
            'supportedSchemas' => self::SUPPORTED_SCHEMAS,
        ]);
    }

    /**
     * Set the state of the controller to a 
     * certain source alredy known (has id & key)
     * 
     * @param   int     $id
     * 
     * @return  void
     */
    public function setSource(int $id)
    {
        $this->source_id = $this->source->id;
        $this->source = Source::findOrFail($id);
        $this->key = $this->source->key;
        $this->fillSourceData();
    }
    
    /**
     * Fill controller cache of model attributes.
     * 
     * Except id & key
     * 
     * @return  void
     */
    protected function fillSourceData()
    {
        $this->sourceSchema = "{$this->source->type}:{$this->source->schema}";
        $this->data = $this->source->data;
        $this->creators = $this->source->creators;
    }

    /**
     * Sets the state of the controller to a new source
     * 
     * But not yet stored.
     * 
     * @return void
     */
    public function setNewSource()
    {
        $this->source = new Source([
            'key' => '',
            'type' => 'citation.article',
            'schema' => '0.0.1',
            'data' => []
        ]);
        $this->source_id = null;
        $this->key = null;
        $this->fillSourceData();
    }

    public function save()
    {
        list($type, $schema) = explode(':',$this->sourceSchema);
        $this->source->type = $type;
        $this->source->schema = $schema;
        $this->source->data = $this->data;
        if (!$this->key) {
            /** @todo use the creators last_names */
            $this->source->key = $this->source->generateKey();
            $this->key = $this->source->key;
        }
        if (!$this->source->user) {
            auth()->user()->sources()->save($this->source);
            $this->source_id = $this->source->id;
            return;
        }
        $this->source->save();
        dd($this->source);
    }
}
