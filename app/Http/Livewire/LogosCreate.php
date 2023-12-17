<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Livewire\Component;

class LogosCreate extends Component
{
    protected $queryString = ['articleID'];

    public $articleID = null;

    /** @todo sacar article como propiedad pública */
    public Article $article;

    protected $rules = [
        'article.title' => 'string',
        'article.html' => 'string',
        'article.delta' => 'JSON',
        'article.meta' => 'JSON'
    ];

    public function mount()
    {
        if ($this->articleID) {
            $this->article =  Article::find($this->articleID);
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
        if ($this->article->user == null) {
            auth()->user()->articles()->save($this->article);
            $this->articleID = $this->article->id;
        } else {
            $this->article->save();
            $this->articleID = $this->article->id;
        }
    }

    public function render()
    {
        return view('livewire.logos-create');
    }

    public function saveList($list)
    {
        $this->article->source_list = $list;
        $this->article->save();
        $this->emit('sourcesUpdated');
    }
}
