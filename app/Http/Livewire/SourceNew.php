<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Arete\Logos\Application\DTO\SourceTypePresentation;
use Arete\Logos\Application\Ports\Interfaces\CreateSourceUC;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SourceNew extends Component
{
    public array $sourceTypes;

    public $selectedType = "journalArticle";

    /**
     * Source attributes
     *
     * @var array
     */
    public array $attributes = [];

    public string $sourceKey = '';

    protected array $rules = [
        'attributes.title' => ['required']
    ];

    protected array $validationAttributes = [];

    /**
     * A map of the laravel rules tags in relation to the
     * source attribute data types
     *
     * @var array
     */
    protected static array $typeRules = [
        'text' => ['string'],
        'number' => ['numeric'],
        'date'  => ['date'],
        'complex' => []
    ];

    public array $creators = [
        [
            'id'    => 23,
            'role' => 'author',
            'relevance' => 1,
            'type' => 'person',
            'attributes' => [
                'name' => 'Pedro',
                'lastName' => "Saucedo"
            ]
        ], [
            'id' => 32,
            'type' => 'person',
            'role' => 'editor',
            'relevance' => 2,
            'attributes' => [
                'name' => 'Juan',
                'lastName' => "Ramirez"
            ]
        ],
    ];

    /**
     * Creators suggestions to user input
     *
     * @var array
     */
    public $creatorSuggestions = [];

    /**
     * Parameters for the suggestion of creators
     *
     * @var array
     */
    public $creatorSuggestionParams = [
        'hint' => 'ar',
        'attribute' => 'lastName',
        'type' => 'person',
        'orderBy' => 'created_at', // attribute, 'created_at', 'updated_at'
        'asc'  => false,
        'limit' => 7
    ];

    public function mount(CreateSourceUC $createSource)
    {
        $types = $createSource->presentSourceTypes();
        $this->sourceTypes = array_map(
            fn (SourceTypePresentation $typePresentation) => $typePresentation->toArray(),
            $types
        );
        $this->mapSourceAttributesFields();
        $this->myUpdateCreatorSuggestions($createSource);
    }

    public function render()
    {
        return view('livewire.source-new');
    }

    public function hydrate()
    {
        $this->validationAttributes['sourceKey'] = strtolower(__('sources.key'));
    }

    public function updated($propertyName)
    {
        // Validates the current updated property if it is an attribute
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

    protected function myUpdateCreatorSuggestions(?CreateSourceUC $createUC = null)
    {
        /** @var \Arete\Logos\Application\Ports\Interfaces\CreateSourceUC */
        $createUC = $createUC ?? app(CreateSourceUC::class);

        $this->creatorSuggestions = $createUC->suggestCreators(
            Auth::user()->id,
            $this->creatorSuggestionParams['hint'],
            $this->creatorSuggestionParams['attribute'],
            $this->creatorSuggestionParams['type'],
            $this->creatorSuggestionParams['orderBy'],
            $this->creatorSuggestionParams['asc'],
            $this->creatorSuggestionParams['limit']
        );
    }

    public function creatorInput($type, $attribute, $value)
    {
        $data = [
            'type' => $type,
            'attribute' => $attribute,
            'hint' => $value
        ];
        $this->creatorSuggestionParams = array_merge($this->creatorSuggestionParams, $data);
        $this->myUpdateCreatorSuggestions();
    }

    public function updatedCreatorSuggestionParamsHint($value)
    {
        $this->myUpdateCreatorSuggestions();
    }

    public function computeKey(CreateSourceUC $createSource, $value)
    {
        $this->sourceKey = $createSource->sugestKey([
            'ownerID'   => Auth::user()->id,
            'key'       => $value
        ]);
    }

    public function updatedSelectedType($type)
    {
        $this->mapSourceAttributesFields();
    }

    public function save(CreateSourceUC $createSource, $data)
    {
        $this->attributes = $data['attributes'];
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

    /**
     * Updates the public attributes fields to match to the selected source type attributes.
     *
     * Also recicles the available data according to attribute type and base type.
     * It don't deletes the attributes that don't belong to the current type to be available
     * for an eventual change of source type
     *
     * @return void
     */
    protected function mapSourceAttributesFields()
    {
        $this->deleteEmptyAttributes();
        foreach ($this->currentSourceTypeAttributes() as $attr) {
            list('code' => $code, 'base' => $base) = $attr;
            if (!isset($this->attributes[$code])) {
                $this->attributes[$code] = $this->attributes[$base] ?? null;
            }
        }
    }

    /**
     * Deletes the attributes that are empty
     *
     * @return void
     */
    protected function deleteEmptyAttributes()
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
        $this->deleteEmptyAttributes();
        $typeAttrCodes = array_map(fn ($attr) => $attr['code'], $this->currentSourceTypeAttributes());
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
        foreach ($this->currentSourceTypeAttributes() as $order => $attr) {
            list(
                'path' => $path,
                'rules' => $rules,
                'label' => $label
            ) = $this->getAttributeRuleData($attr);
            $this->rules[$path] = $rules;
            $this->validationAttributes[$path] = $label;
        }
    }

    protected function currentSourceTypeAttributes()
    {
        return $this->sourceTypes[$this->selectedType]['attributes'];
    }

    /**
     * Returns the data of a particular attribute of the current
     * selected type
     *
     * @param mixed $code
     *
     * @return array
     */
    protected function attributeData($code): array
    {
        return array_values(
            array_filter($this->currentSourceTypeAttributes(), fn ($attr) => $attr['code'] == $code)
        )[0];
    }

    /**
     * Returns an array of rule data corresponding
     * to a particular attribute type
     *
     * @param array $attributeData
     *
     * @return void
     */
    protected function getAttributeRuleData(array $attributeData)
    {
        $path = 'attributes.' . $attributeData['code'];
        $rules = $this->rules[$path] ?? [];
        if (isset(self::$typeRules[$attributeData['type']])) {
            $rules = array_merge($rules, self::$typeRules[$attributeData['type']]);
        }
        return [
            'path' => $path,
            'rules' => $rules,
            'label' => strtolower($attributeData['label'])
        ];
    }

    public function addCreator()
    {
        $this->creators[] = [
            'type' => 'person',
            'attributes' => [
                'name'  => '',
                'lastName'  => ''
            ]
        ];
    }

    public function removeCreator($i)
    {
        unset($this->creators[$i]);
    }

    public function changeCreator()
    {
        Log::info('change-creator', $this->creators);
    }
}
