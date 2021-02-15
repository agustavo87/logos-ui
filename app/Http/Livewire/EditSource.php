<?php

namespace App\Http\Livewire;

use App\Models\Source;
use Livewire\Component;

class EditSource extends Component
{
    public $listen = 'edit-source';
    public $withBg = true;
    public $data;

    public Source $source;
    public $sourceSchema; // schema tag = type:version

    protected const SUPPORTED_SCHEMAS = [
        // schema tag => name
        'citation.book:0.0.1' => "Libro",
        'citation.article:0.0.1' => "Artículo de Revista Académica"
    ];

    public function mount($sourceId = null)
    {
        if ($sourceId) {
            $this->source = Source::findOrFail($sourceId);
            $this->sourceSchema = "{$this->source->type}:{$this->source->schema}";
            $this->data = $this->source->data;
        } else {
            $this->source = new Source();
        }
    }

    public function render()
    {
        return view('livewire.edit-source', [
            'supportedSchemas' => self::SUPPORTED_SCHEMAS
        ]);
    }

    public function save()
    {
        list($type, $schema) = explode(':',$this->sourceSchema);
        $this->source->type = $type;
        $this->source->schema = $schema;
        $this->source->data = $this->data;
        // dd($this->source);
        $this->source->save();
    }
}
