<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Livewire\Component;

class ArticleShow extends Component
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
        return view('livewire.article-show', [
            'article' => $this->article
        ]);
    }
}
