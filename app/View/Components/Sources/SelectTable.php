<?php

namespace App\View\Components\Sources;

use Illuminate\View\Component;

class SelectTable extends Component
{

    public $maxRows = 8;

    public string $initialTitle;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($initialTitle = '')
    {
        $this->initialTitle = $initialTitle;
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
