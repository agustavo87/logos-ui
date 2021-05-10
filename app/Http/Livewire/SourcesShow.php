<?php

namespace App\Http\Livewire;

use App\Models\Source;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class SourcesShow extends Component
{
    use WithPagination;

    public $userId;

    public function render()
    {
        return view('livewire.sources-show', [
            'sources' =>  Source::where('user_id', $this->userId)->latest()->paginate(10),
            'user' => User::find($this->userId)
            ]
        );
    }

    public function destroy(Source $source)
    {
        $source->delete();
    }

}
