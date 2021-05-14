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

        $this->source = new Source([
            'type' => 'citation.article',
            'schema' => '0.0.1',
            'data' => []
        ]);
        $this->fillSourceData();
    }

    public function render()
    {
        return view('livewire.source-edit', [
            'supportedSchemas' => self::SUPPORTED_SCHEMAS,
        ]);
    }

    public function setSource(int $id)
    {
        $this->source_id = $id;
        $this->source = Source::findOrFail($id);
        $this->fillSourceData();
    }

    protected function fillSourceData()
    {
        $this->sourceSchema = "{$this->source->type}:{$this->source->schema}";
        $this->data = $this->source->data;
        $this->creators = $this->source->creators;
    }

    public function save()
    {
        $faker = app('\Faker\Generator');
        list($type, $schema) = explode(':',$this->sourceSchema);
        $this->source->type = $type;
        $this->source->schema = $schema;
        $this->source->data = $this->data;
        if (!$this->key) {
            $this->source->key = $faker->firstName . $this->data['year'];
        }
        if (!$this->source->user) {
            auth()->user()->sources()->save($this->source);
            $this->source_id = $this->source_id->id;
            return;
        }
        $this->source->save();
        dd($this->source);
    }
}
