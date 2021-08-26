<?php

namespace App\View\Components\Sources;

use Illuminate\View\Component;

class SelectTable extends Component
{

    public $maxRows = 10;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($initialTitle = '', $maxRows = 10)
    {
        $this->initialTitle = $initialTitle;
        $this->maxRows = $maxRows;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        // dd($this->sources);
        return view('components.sources.select-table');
    }
}
