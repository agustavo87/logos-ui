<?php

namespace App\Http\Livewire;

use App\Models\Source;
use Livewire\Component;
use Livewire\WithPagination;

class SourceSelect extends Component
{
    use WithPagination;

    public $max_rows = 8;
    public $listen = 'source-get';

    public $searchFields = [
        'key' => '',
        'title' => ''
    ];

    public function render()
    {
        $sources = Source::select('key', 'data');

        $sources->where('user_id', auth()->user()->id);

        foreach ($this->searchFields as $field => $value) {
            if(!empty($value)) {
                if($field == 'key') {
                    $sources->where($field, 'LIKE', "%{$value}%");
                } elseif ($field == 'title') {
                    $sources->whereRaw('LCASE(data->"$.title") LIKE "%' . strtolower($value) . '%"');
                }
            }
        }

        return view('livewire.source-select', [
            'sources' => $sources->latest()->paginate( $this->max_rows )
        ]);
    }

    public function updatingSearchFields() {
        $this->resetPage();
    }

    public function reiniciarFields() {
        $this->reset(['searchFields']);
    }
}
