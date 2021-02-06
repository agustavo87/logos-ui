<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Livewire\Component;

class LogosCreate extends Component
{
    public Article $article;
    public $delta;
    public $html;

    protected $rules = [
        'article.title' => 'string',
        'article.html' => 'string',
        'article.delta' => 'JSON'
    ];

    public function mount($id = null)
    {
        // if ($id) {
        //     $this->article = Article::find($id);
        // } else {
        //     $this->article = auth()->user()->articles()->create();
        // }

        $this->article = $id ? Article::find($id) : new Article();
    }

    public function save() 
    {   
        $this->article->delta = $this->delta;
        $this->article->html = $this->html;
        $this->article->save();
    }

    public function sync($delta, $html)
    {
        $this->article->delta = $delta;
        $this->article->html = $html;
        
    }

    public function render()
    {
        return view('livewire.logos-create');
    }
}
