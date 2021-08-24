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

    /**
     * @todo agregar regla de que siempre el título o atributo similar
     * sea requerido.
     */
    protected static array $attributeRules = [
        '*' => [
            'title' => ["required", 'filled']
        ]
    ];

    protected static array $typeRules = [
        'text' => 'string',
        'number' => 'numeric',
        'date'  => 'date',
        'complex' => ''
    ];

    public array $rules;

    public function render(CreateSourceUC $createSource)
    {
        $types = $createSource->presentSourceTypes();
        $this->updateAttributesFields($types[$this->selectedType]->attributes);
        // Log::info('fields', ['fields' => $this->attributes]);
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
        $this->cleanEmptyAttributes();

        foreach ($attributes as $attr) {
            if (!isset($this->attributes[$attr->code])) {
                $this->attributes[$attr->code] = $this->attributes[$attr->baseAttributeCode] ?? null; //
            }
        }
    }

    protected function cleanEmptyAttributes()
    {
        foreach ($this->attributes as $code => $value) {
            if (!$value) {
                unset($this->attributes[$code]);
            }
        }
    }

    public function save(CreateSourceUC $createSource)
    {
        $typeAttributes = $createSource->presentSourceTypes()[$this->selectedType]->attributes;
        $this->filterTypeAttributes($typeAttributes);
        $this->updateValidationRules($typeAttributes);
        Log::info('validation rules', ['rules' => $this->rules]);
        $this->validate();
        $this->sourceKey = $createSource->create($this->selectedType, $this->attributes, $this->sourceKey);
        return $this->sourceKey;
    }

    /**
     * @param \Arete\Logos\Application\DTO\AttributePresentation[] $attributes
     *
     * @return void
     */
    protected function filterTypeAttributes(array $attributes)
    {
        $this->cleanEmptyAttributes();
        $typeAttrCodes = array_map(fn ($attr) => $attr->code, $attributes);
        foreach ($this->attributes as $code => $value) {
            if (!in_array($code, $typeAttrCodes)) {
                unset($this->attributes[$code]);
            }
        }
    }

    /**
     * @param \Arete\Logos\Application\DTO\AttributePresentation[] $attributes
     *
     * @return void
     */
    public function updateValidationRules(array $attributes)
    {
        foreach ($attributes as $attr) {
            if (isset(self::$typeRules[$attr->type])) {
                $this->rules['attributes.' . $attr->code] = self::$typeRules[$attr->type];
            }
        }
    }
}
