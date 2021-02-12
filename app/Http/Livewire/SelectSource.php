<?php

namespace App\Http\Livewire;

use App\Models\Source;
use Livewire\Component;
use Livewire\WithPagination;

class SelectSource extends Component
{
    use WithPagination;

    public $listen = 'get-source';

    public function render()
    {
        return view('livewire.select-source', [
            'sources' => Source::paginate(5)
        ]);
    }
}