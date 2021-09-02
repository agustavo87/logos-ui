<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Arete\Logos\Application\DTO\SourceTypePresentation;
use Arete\Logos\Application\Ports\Interfaces\CreateSourceUC;
use Illuminate\Support\Facades\Auth;

class SourceNew extends Component
{
    public array $sourceTypes;

    public $selectedType = "journalArticle";

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
        $types = $createSource->presentSourceTypes();
        $this->sourceTypes = array_map(
            fn (SourceTypePresentation $typePresentation) => $typePresentation->toArray(),
            $types
        );
        $this->updateAttributesFields();
    }

    public function render()
    {
        return view('livewire.source-new');
    }

    public function save(CreateSourceUC $createSource)
    {
        $this->filterTypeAttributes();
        $this->updateValidationRules();
        $this->validate();
        $this->sourceKey = $createSource->create(
            Auth::user()->id,
            $this->selectedType,
            $this->attributes,
            $this->sourceKey
        );
        return $this->sourceKey;
    }

    public function computeKey(CreateSourceUC $createSource, $value)
    {
        $this->sourceKey = $createSource->sugestKey([
            'ownerID'   => Auth::user()->id,
            'key'       => $value
        ]);
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
        $this->updateAttributesFields();
    }

    /**
     * Update the public attributes fields to bind the source data
     *
     * @return void
     */
    protected function updateAttributesFields()
    {
        $this->cleanEmptyAttributes();
        foreach ($this->attributesData() as $attr) {
            list('code' => $code, 'base' => $base) = $attr;
            if (!isset($this->attributes[$code])) {
                $this->attributes[$code] = $this->attributes[$base] ?? null;
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
     * Clean empty attributes and the ones that not belong to current type.
     *
     * @return void
     */
    protected function filterTypeAttributes()
    {
        $this->cleanEmptyAttributes();
        $typeAttrCodes = array_map(fn ($attr) => $attr['code'], $this->attributesData());
        foreach ($this->attributes as $code => $value) {
            if (!in_array($code, $typeAttrCodes)) {
                unset($this->attributes[$code]);
            }
        }
    }

    /**
     * Updates the rules for validation of current attributes.
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
