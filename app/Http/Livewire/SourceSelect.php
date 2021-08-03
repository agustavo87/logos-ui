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
        // 'key' => '',
        'title' => 'gato'
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
        foreach ($this->searchFields as $field => $value) {
            if ($field == 'title') {
                $results = $filter->filter([
                    'attributes' => ['title' => $value]
                ]);
                $this->sources = $this->sourcesToArray($results);
            }
        }

        return view('livewire.source-select');
    }

    public function refreshSources()
    {
        $this->searchFields['title'] = 'infierno';
    }
}
