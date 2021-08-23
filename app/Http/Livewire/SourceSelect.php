<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Arete\Logos\Application\Ports\Interfaces\FilteredIndexUseCase;
use Arete\Logos\Domain\Source;

class SourceSelect extends Component
{

    public $listen = 'source-get';

    public $asc = true;

    public $searchFields = [
        'key' => '',
        'title' => ''
    ];

    protected $maxRows = 10;

    public $sources = [];

    public function mount()
    {
        //
    }

    protected function sourcesToArray(array $sources): array
    {
        return array_map(function (Source $source) {
            $sourceData = $source->toArray('relevance');
            return (object) $sourceData;
        }, $sources);
    }

    public function render(FilteredIndexUseCase $filter)
    {
        /**
         * @todo ver como evitar que se cargue esto si la vista no es
         * 'utilizada' por el usuario y solo permanece en segundo plano
         * en la página.
         */
        $params = [
            'orderBy' => [
                'group' => 'source',
                'field' => 'key',
                'asc' => $this->asc
            ],
            'offset' => 0,
            'limit' => $this->maxRows
        ];
        foreach ($this->searchFields as $field => $value) {
            if ($field == 'title' && !($value == '' || $value == null)) {
                $params['attributes'] = ['title' => $value];
            }
            if ($field == 'key'  && !($value == '' || $value == null)) {
                $params['key'] = $value;
            }
        }



        $results = $filter->filter($params);
        $this->sources = $this->sourcesToArray($results);

        return view('livewire.source-select');
    }

    public function flush()
    {
        $this->reset();
    }
}
