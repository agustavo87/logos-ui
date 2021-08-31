<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Arete\Logos\Application\Ports\Interfaces\CreateSourceUC;

class SourceNew extends Component
{
    public $selectedType = "journalArticle";

    public array $attributes = [];

    public string $sourceKey = '';

    /** @todo evitar que se pueda crear una fuente sin al menos un atributo
     * por ej 'title'
     */
    protected static array $typeRules = [
        'text' => 'string',
        'number' => 'numeric',
        'date'  => 'date',
        'complex' => ''
    ];

    public array $creators = [
        [
            'type' => 'person',
            'attributes' => [
                'name' => 'Martinez',
                'lastName' => "Mario"
            ]
        ]
    ];

    public array $rules = [];

    protected array $validationAttributes = [];

    public function render(CreateSourceUC $createSource)
    {
        $types = $createSource->presentSourceTypes();
        $this->updateAttributesFields($types[$this->selectedType]->attributes);
        return view(
            'livewire.source-new',
            [
                'types' => $types
            ]
        );
    }

    public function save(CreateSourceUC $createSource)
    {
        $typeAttributes = $createSource->presentSourceTypes()[$this->selectedType]->attributes;
        $this->filterTypeAttributes($typeAttributes);
        $this->updateValidationRules($typeAttributes);
        $this->filterTypeAttributes($typeAttributes);
        $this->validate();
        $this->sourceKey = $createSource->create($this->selectedType, $this->attributes, $this->sourceKey);
        return $this->sourceKey;
    }

    public function computeKey(CreateSourceUC $createSource, $value)
    {
        $this->sourceKey = $createSource->sugestKey($value);
    }

    public function hydrate()
    {
        $this->validationAttributes['sourceKey'] = strtolower(__('sources.key'));
    }


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
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
            $path = 'attributes.' . $attr->code;
            $this->rules[$path] = [];
            if (isset(self::$typeRules[$attr->type])) {
                $this->rules[$path][] = self::$typeRules[$attr->type];
            }
            $this->validationAttributes[$path] = strtolower($attr->label);
        }
        $this->rules['attributes.title'][] = 'required';
    }
}
