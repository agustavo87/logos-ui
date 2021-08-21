<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Arete\Logos\Application\Ports\Interfaces\CreateSourceUC;
use Illuminate\Support\Facades\Log;

class SourceNew extends Component
{
    public $selectedType = "journalArticle";

    public array $attributes = [];

    public string $sourceKey = 'prueba1-2';

    public function render(CreateSourceUC $createSource)
    {
        /*
        $types = array_values($createSource->presentSourceTypes());
        dd("{$types[1]}");//*/
        $types = $createSource->presentSourceTypes();
        $this->updateAttributesFields($types[$this->selectedType]->attributes);
        Log::info('fields', ['fields' => $this->attributes]);
        return view(
            'livewire.source-new',
            [
                'types' => $types
            ]
        );
    }

    public function computeKey($value)
    {
        $this->sourceKey = $value . '-x';
    }

    /**
     * Create the public Attribute Fields Property to bind input data
     *
     * @param \Arete\Logos\Application\DTO\AttributePresentation[] $attributes
     *
     * @return void
     */
    protected function updateAttributesFields(array $attributes)
    {
        // limpiar campos vacíos
        foreach ($this->attributes as $code => $value) {
            if (!$value) {
                unset($this->attributes[$code]);
            }
        }

        // updates fields
        foreach ($attributes as $attr) {
            if (!isset($this->attributes[$attr->code])) {
                $this->attributes[$attr->code] = $this->attributes[$attr->baseAttributeCode] ?? null; //
            }
        }
    }

    public function save(CreateSourceUC $createSource)
    {
        $result = $createSource->create($this->selectedType, $this->attributes, $this->sourceKey);
        return $result;
    }
}
