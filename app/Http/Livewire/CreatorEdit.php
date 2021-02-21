<?php

namespace App\Http\Livewire;

use App\Models\Creator;
use Livewire\Component;

class CreatorEdit extends Component
{
    public $name;
    public $lastName;
    public $type; // tiene que pasarse el rol también más que solo el tipo.
    public $role;
    public $creatorId;

    public function mount($creator)
    {
        // dd($rol);
        $this->name = $creator->data['name'];
        $this->lastName = $creator->data['last_name'];
        $this->type = $creator->type;
        $this->role = $creator->role;
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
