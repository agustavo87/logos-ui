<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Arete\Logos\Application\Ports\Interfaces\FilteredIndexUseCase;
use Arete\Logos\Domain\Source;

// use Livewire\WithPagination;

class SourceSelect extends Component
{

    public $listen = 'source-get';

    public $searchFields = [
        'key' => '',
        'title' => ''
    ];

    public $sources = [];

    public function mount()
    {
        //
    }

    protected function sourcesToArray(array $sources): array
    {
        return array_map(function (Source $source) {
            $sourceData = $source->toArray('relevance');
            $sourceData['render'] = $source->render();
            return (object) $sourceData;
        }, $sources);
    }

    public function render(FilteredIndexUseCase $filter)
    {
        $params = [];
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

    public function refreshSources()
    {
        $this->searchFields['title'] = 'infierno';
    }
}
