<?php

namespace App\Http\Livewire;

use App\Models\Source;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @todo Hacer que muestre solo las fuentes del usuario
 */
class SourceSelect extends Component
{
    use WithPagination;

    public $listen = 'source-get';

    public $searchFields = [
        'key' => '',
        'title' => ''
    ];

    public function render()
    {
        $sources = Source::select('key', 'data');

        foreach ($this->searchFields as $field => $value) {
            if(!empty($value)) {
                if($field == 'key') {
                    $sources->where($field, 'LIKE', "%{$value}%");
                } elseif ($field == 'title') {
                    $sources->whereRaw('LCASE(data->"$.title") LIKE "%' . strtolower($value) . '%"');
                    // $sources->where('data->title', 'LIKE', "%{$value}%");
                }
            }
        }

        return view('livewire.source-select', [
            'sources' => $sources->latest()->paginate(5)
        ]);
    }

    public function updatingSearchFields() {
        $this->resetPage();
    }

    public function reiniciarFields() {
        $this->reset(['searchFields']);
    }
}
