<?php

namespace App\Http\Livewire;

use Livewire\Component;

class SelectSource extends Component
{
    public $listen = 'get-source';

    public function render()
    {
        return view('livewire.select-source');
    }
}
