<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Traits\HasArticleTrait;
use Livewire\Component;

class DocumentCitations extends Component
{
    use HasArticleTrait;

    public $articleId = null;

    public $liClass = '';

    protected $listeners = [
        'sourcesUpdated' => '$refresh'
    ];

    public function render()
    {
        return view('livewire.document-citations');
    }
}
