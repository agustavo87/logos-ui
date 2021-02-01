<?php

namespace App\Http\Livewire;

use App\Models\Source;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ShowSources extends Component
{
    use WithPagination;

    public $userId;

    public function render()
    {
        return view('livewire.show-sources', [
            'sources' =>  Source::where('user_id', $this->userId)->paginate(2),
            'user' => User::find($this->userId)
            ]
        );
    }

    public function destroy(Source $source)
    {
        $source->delete();
    }

}
