<?php

namespace App\Http\Livewire;

use App\Models\Creator;
use Livewire\Component;

class CreatorEdit extends Component
{
    public $name;
    public $lastName;
    public $type;
    public $creatorId;

    public function mount($creator)
    {
        $this->name = $creator->data['name'];
        $this->lastName = $creator->data['last_name'];
        $this->type = $creator->type;
        $this->creatorId = $creator->id;
    }

    public function render()
    {
        return view('livewire.creator-edit');
    }

    public function delete()
    {
        // $ok = Creator::findOrFail($this->creatorId)->delete();

        $this->emitUp('creatorDetach', $this->creatorId);
    }
}
