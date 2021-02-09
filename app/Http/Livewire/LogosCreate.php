<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Livewire\Component;

class LogosCreate extends Component
{
    public Article $article;
    public $delta;
    public $html;
    public $meta;

    protected $rules = [
        'article.title' => 'string',
        'article.html' => 'string',
        'article.delta' => 'JSON',
        'article.meta' => 'JSON'
    ];

    public function mount($id = null)
    {

        if ($id) {
            $this->article =  Article::find($id);
        } else {
            $this->article = new Article([
                'title' => '',
                'html' => '<p></p>',
                'delta' => [],
                'meta' => []
            ]);
        }
        
    }

    public function save() 
    {   

        if($this->article->user == null) {
            auth()->user()->articles()->save($this->article);
        } else {
            $this->article->save();
        }
        
        
    }


    public function render()
    {
        return view('livewire.logos-create');
    }
}
