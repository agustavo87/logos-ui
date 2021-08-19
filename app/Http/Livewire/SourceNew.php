<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Arete\Logos\Application\Ports\Interfaces\CreateSourceUC;

class SourceNew extends Component
{
    public $selectedType = "journalArticle";

    public function render(CreateSourceUC $createSource)
    {

        return view(
            'livewire.source-new',
            [
                'types' => $createSource->presentSourceTypes()
            ]
        );
    }
}
