<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    public array $countries;

    public array $languages;

    public function mount()
    {
        $this->countries = config('locale.countries');
        $this->languages = config('locale.languages');
    }


    public function render()
    {
        return view('livewire.user.index', [
            'users' => User::paginate(10)
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
    }
}
