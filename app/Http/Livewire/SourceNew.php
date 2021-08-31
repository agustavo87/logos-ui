<?php

namespace App\Http\Livewire;

use Arete\Logos\Application\DTO\SourceTypePresentation;
use Livewire\Component;
use Arete\Logos\Application\Ports\Interfaces\CreateSourceUC;
use Illuminate\Support\Facades\App;

class SourceNew extends Component
{
    public $selectedType = "journalArticle";

    public array $sourceTypes;

    public array $attributes = [];

    public string $sourceKey = '';

    protected array $rules = [
        'attributes.title' => ['required']
    ];

    protected array $validationAttributes = [];

    /** @todo evitar que se pueda crear una fuente sin al menos un atributo
     * por ej 'title'
     */
    protected static array $typeRules = [
        'text' => ['string'],
        'number' => ['numeric'],
        'date'  => ['date'],
        'complex' => []
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

    public function mount(CreateSourceUC $createSource)
    {
        $types = $createSource->presentSourceTypes($this->selectedType);
        $this->sourceTypes = array_map(
            fn (SourceTypePresentation $typePresentation) => $typePresentation->toArray(),
            $types
        );
        $typeAttributes = $types[$this->selectedType]->attributes;
        $this->updateAttributesFields($typeAttributes); /** @todo esto se puede hacer con el array */
    }

    public function render()
    {
        return view('livewire.source-new');
    }

    public function save(CreateSourceUC $createSource)
    {
        $typeAttributes = $createSource->presentSourceTypes()[$this->selectedType]->attributes;
        $this->filterTypeAttributes($typeAttributes);
        $this->updateValidationRules();
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
        $props = explode('.', $propertyName);
        if ($props[0] == 'attributes') {
            list(
                'rules' => $rules,
                'label' => $label
            ) = $this->getAttributeRuleData($this->attributeData($props[1]));
            $this->validateOnly(
                $propertyName,
                [$propertyName => $rules],
                [],
                [$propertyName => $label]
            );
        } else {
            $this->validateOnly($propertyName);
        }
    }

    public function updatedSelectedType($type)
    {
        /** @var CreateSourceUC */
        $createSource = App::make(CreateSourceUC::class);
        $typeAttributes = $createSource->presentSourceTypes()[$type]->attributes;
        $this->updateAttributesFields($typeAttributes);
        $this->updateValidationRules();
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
    protected function updateValidationRules()
    {
        foreach ($this->attributesData() as $order => $attr) {
            list(
                'path' => $path,
                'rules' => $rules,
                'label' => $label
            ) = $this->getAttributeRuleData($attr);
            $this->rules[$path] = $rules;
            $this->validationAttributes[$path] = $label;
        }
    }

    protected function attributesData()
    {
        return $this->sourceTypes[$this->selectedType]['attributes'];
    }

    protected function attributeData($code)
    {
        return array_values(
            array_filter($this->attributesData(), fn ($attr) => $attr['code'] == $code)
        )[0];
    }

    protected function getAttributeRuleData(array $attr)
    {
        $path = 'attributes.' . $attr['code'];
        $rules = $this->rules[$path] ?? [];
        if (isset(self::$typeRules[$attr['type']])) {
            $rules = array_merge($rules, self::$typeRules[$attr['type']]);
        }
        return [
            'path' => $path,
            'rules' => $rules,
            'label' => strtolower($attr['label'])
        ];
    }
}
