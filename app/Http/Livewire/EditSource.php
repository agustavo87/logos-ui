<?php

namespace App\Http\Livewire;

use App\Models\Source;
use Livewire\Component;

class EditSource extends Component
{
    public $listen = 'edit-source';
    public $withBg = true;
    public $data;

    public Source $source;

    public function mount($sourceId = null)
    {
        if ($sourceId) {
            $this->source = Source::findOrFail($sourceId);
        } else {
            $this->source = new Source();
        }
    }

    public function render()
    {
        return view('livewire.edit-source');
    }
}
