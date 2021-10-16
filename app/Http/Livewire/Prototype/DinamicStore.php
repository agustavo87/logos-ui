<?php

namespace App\Http\Livewire\Prototype;

use Livewire\Component;

class DinamicStore extends Component
{
    public array $data = [
        'name' => 'value',
        'name2' => 'value2'
    ];

    public function render()
    {
        return view('livewire.prototype.dinamic-store');
    }

    public function change()
    {
        $this->data['teta'] = 'loca';
    }
}
