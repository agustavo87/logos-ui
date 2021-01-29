<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Livewire\Component;

class ShowArticle extends Component
{
    public Article $article;
    public $articleId;

    public function mount($articleId) 
    {
        // dd([$articleId, Article::find($articleId)]);
        $this->article = Article::find($articleId);
    }

    public function render()
    {
        return view('livewire.show-article', [
            'article' => $this->article
        ]);
    }
}
