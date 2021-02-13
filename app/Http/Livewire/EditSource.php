<?php

namespace App\Http\Livewire;

use Livewire\Component;

class EditSource extends Component
{
    public $listen = 'edit-source';
    public $withBg = true;

    public function render()
    {
        return view('livewire.edit-source');
    }
}
