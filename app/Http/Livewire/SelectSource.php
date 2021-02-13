<?php

namespace App\Http\Livewire;

use App\Models\Source;
use Livewire\Component;
use Livewire\WithPagination;

class SelectSource extends Component
{
    use WithPagination;

    public $listen = 'get-source';

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

        return view('livewire.select-source', [
            'sources' => $sources->latest()->paginate(5)
        ]);
    }

    public function updatingSearchFields() {
        $this->resetPage();
    }
}
