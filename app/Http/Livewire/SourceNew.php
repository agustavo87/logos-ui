<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Arete\Logos\Application\DTO\SourceTypePresentation;
use Arete\Logos\Application\Ports\Interfaces\CreateSourceUC;
use Arete\Logos\Application\Ports\Interfaces\FilteredIndexUseCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @property \Arete\Logos\Application\Ports\Interfaces\CreateSourceUC $caseOperations
 * @property \Arete\Logos\Application\Ports\Interfaces\FilteredIndexUseCase $sourcesFilter
 */
class SourceNew extends Component
{
    public $sourceID = null;

    /**
     * Current source key
     *
     * @var string
     */
    public string $key = '';

    public $ownerID = null;

    /**
     * Current source type
     *
     * @var string
     */
    public $type = "journalArticle";

    /**
     * Current Source attributes
     *
     * @var array
     */
    public array $attributes = [
        'title' => 'attributo inicial'
    ];

    /**
     * Current Source attributes validation rules
     *
     * @var array
     */
    protected array $rules = [
        'attributes.title' => ['required']
    ];

    protected array $validationAttributes = [];

    /**
     * A map of the laravel rules tags in relation to
     * source attributes data types
     *
     * @var array
     */
    protected static array $typeRules = [
        'text' => ['string'],
        'number' => ['numeric'],
        'date'  => ['date'],
        'complex' => []
    ];

    public array $participations = [];

    /**
     * Creators data
     *
     * @var array
     */
    public array $creators = [];
    //     [
    //         'id'    => 23,
    //         'role' => 'author',
    //         'relevance' => 1,
    //         'type' => 'person',
    //         'attributes' => [
    //             'name' => 'Pedro',
    //             'lastName' => "Saucedo"
    //         ]
    //     ], [
    //         'id' => 32,
    //         'type' => 'person',
    //         'role' => 'editor',
    //         'relevance' => 2,
    //         'attributes' => [
    //             'name' => 'Juan',
    //             'lastName' => "Ramirez"
    //         ]
    //     ],
    // ];

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
        'hint' => '',
        'attribute' => 'lastName',
        'type' => 'person',
        'orderBy' => 'created_at', // attribute, 'created_at', 'updated_at'
        'asc'  => false,
        'limit' => 7
    ];

    /**
     * Current available source types data
     *
     * In the front end are retrieved by a js asset file that has to
     * be updated by corresponding command.
     *
     * @var array
     */
    protected array $types;

    public array $sharedErrors = [];

    protected CreateSourceUC $createSourceUseCase;

    protected FilteredIndexUseCase $filter;

    public function mount()
    {
        $testSource = $this->sourcesFilter->filter([
            'key' => 'za'
        ])[0]->toArray();
        $this->mountSource($testSource);
        $types = $this->caseOperations->getSourceTypesPresentations();
        $this->types = array_map(
            fn (SourceTypePresentation $typePresentation) => $typePresentation->toArray(),
            $types
        );
        $this->mountSourceTypeAttributesFields();
        $this->syncCreatorsSuggestions();
    }

    protected function mountSource(array $source)
    {
        $this->sourceID  = $source['id'];
        $this->key = $source['key'];
        $this->ownerID = $source['ownerID'];
        $this->type = $source['type'];
        $this->attributes = $source['attributes'];

        $participations = [];
        foreach ($source['participations'] as $role => $roleParticipations) {
            foreach ($roleParticipations as $creatorID => $participationData) {
                $participations[] = $participationData;
            }
        }
        $this->participations = $participations;
    }

    public function render()
    {
        $this->sharedErrors = $this->getShareableErrors();
        return view('livewire.source-new');
    }

    public function getShareableErrors(): array
    {
        $shareableErrors = [];
        foreach ($this->getErrorBag()->toArray() as $error => $message) {
            $shareableErrors[] = ['key' => $error, 'messages' => $message];
        }
        return $shareableErrors;
    }

    public function hydrate()
    {
        $this->validationAttributes['key'] = strtolower(__('sources.key'));
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

    protected function currentSourceTypeAttributes()
    {
        return $this->types[$this->type]['attributes'];
    }

    protected function syncCreatorsSuggestions()
    {
        $this->creatorSuggestions = $this->caseOperations->suggestCreators(
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
        $this->syncCreatorsSuggestions();
    }

    public function updatedCreatorSuggestionParamsHint($value)
    {
        $this->syncCreatorsSuggestions();
    }

    public function computeKey($value)
    {
        /** @todo
         * si la key es de una fuente ya montada no hace falta sugerir una nueva
         * solo si esta es cambiada. Probablemente, para evitar problemas
         * si la key es de una fuente existente no debería permitirse el cambio.
         */
        $this->key = $this->caseOperations->sugestKey([
            'ownerID'   => Auth::user()->id,
            'key'       => $value
        ]);
    }

    public function updatedType($type)
    {
        $this->mountSourceTypeAttributesFields();
    }

    public function save($data)
    {
        Log::info('saving source', $data);
        $creatorsData = $this->adaptCreatorsData($data);
        Log::info('creators data', $creatorsData);
        $this->attributes = $data['attributes'];
        $this->filterTypeAttributes();
        $this->updateValidationRules();
        $this->validate();
        /** @todo elegir si se actualiza o se crea una fuente */
        $this->key = $this->caseOperations->create(
            Auth::user()->id,
            $this->type,
            $this->attributes,
            $creatorsData,
            $this->key
        );
        return $this->key;
    }

    protected function adaptCreatorsData($data)
    {
          $creators = $data['creators'];
          $creators = array_map(
              fn ($creator) =>
                $creator['id']  && isset($creator['dirty']) && !$creator['dirty'] ?
                ['creatorID' => $creator['id']] :
                $creator,
              $creators
          );
          return $creators;
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
    protected function mountSourceTypeAttributesFields()
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

    public function getSourcesFilterProperty()
    {
        if (!isset($this->filter)) {
            $this->filter = app(FilteredIndexUseCase::class);
        }
        return  $this->filter;
    }

    public function getCaseOperationsProperty()
    {
        if (!isset($this->CreateSourceUseCase)) {
            $this->createSourceUseCase = app(CreateSourceUC::class);
        }
        return  $this->createSourceUseCase;
    }
}
