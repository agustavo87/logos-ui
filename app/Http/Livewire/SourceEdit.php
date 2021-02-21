<?php

namespace App\Http\Livewire;

use App\Models\Source;
use Livewire\Component;
use Faker\Generator;

class SourceEdit extends Component
{
    public $listen = 'source-edit';
    public $key;
    public $data;
    public $sourceSchema; // schema tag = type:version
    public Source $source;
    public $withBg = true;


    protected const SUPPORTED_SCHEMAS = [
        // schema tag => name
        'citation.book:0.0.1' => "Libro",
        'citation.article:0.0.1' => "Artículo de Revista Académica"
    ];

    public function mount($sourceId = 130)
    {
        if ($sourceId) {
            $this->source = Source::findOrFail($sourceId);
        } else {
            $this->source = new Source([
                'type' => 'citation.article',
                'schema' => '0.0.1',
                'data' => []
            ]);
        }
        $this->sourceSchema = "{$this->source->type}:{$this->source->schema}";
        $this->data = $this->source->data;
        $this->creators = $this->source->creators;
    }

    public function render()
    {
        return view('livewire.source-edit', [
            'supportedSchemas' => self::SUPPORTED_SCHEMAS,
        ]);
    }

    public function save()
    {
        $faker = app(Generator::class);
        list($type, $schema) = explode(':',$this->sourceSchema);
        $this->source->type = $type;
        $this->source->schema = $schema;
        $this->source->data = $this->data;
        if (!$this->key) {
            $this->source->key = $faker->firstName . $this->data['year'];
        }
        if (!$this->source->user) {
            auth()->user()->sources()->save($this->source);
            return;
        }
        $this->source->save();
        dd($this->source);
    }
}
