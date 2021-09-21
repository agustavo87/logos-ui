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

    public array $attributes = [];

    public string $sourceKey = '';

    protected array $rules = [
        'attributes.title' => ['required']
    ];

    protected array $validationAttributes = [];

    protected static array $typeRules = [
        'text' => ['string'],
        'number' => ['numeric'],
        'date'  => ['date'],
        'complex' => []
    ];

    public array $logosRoles = [
        'person' => ['author', 'contributor', 'editor', 'translator', 'reviewedAuthor']
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
        // [
        //     'type' => 'organization',
        //     'attributes' => [
        //         'name' => 'American Psychological Association',
        //         'acronym' => "APA"
        //     ]
        // ]
    ];

    public $creatorSuggestions = [];

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
        $this->updateAttributesFields();
        $this->iupdateCreatorSuggestion($createSource);
    }

    protected function iupdateCreatorSuggestion(?CreateSourceUC $createUC = null)
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
        $this->iupdateCreatorSuggestion();
    }

    public function updatedCreatorSuggestionParamsHint($value)
    {
        $this->iupdateCreatorSuggestion();
    }

    public function render()
    {
        return view('livewire.source-new');
    }

    public function save(CreateSourceUC $createSource, $data)
    {
        // dd($this->selectedType);

        // dd($data);
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

    public function computeKey(CreateSourceUC $createSource, $value)
    {
        $this->sourceKey = $createSource->sugestKey([
            'ownerID'   => Auth::user()->id,
            'key'       => $value
        ]);
    }

    public function hydrate()
    {
        // Log::info('[hydrate] creators', $this->creators);
        $this->validationAttributes['sourceKey'] = strtolower(__('sources.key'));
    }

    public function updated($propertyName)
    {
        Log::info('[updated] creators', $this->creators);
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

    public function addCreator()
    {
        $this->creators[] = [
            'type' => 'person',
            'attributes' => [
                'name'  => '',
                'lastName'  => ''
            ]
        ];
        Log::info('agregando creador');
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
