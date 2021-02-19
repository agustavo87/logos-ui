<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class ArticlesShow extends Component
{
    use WithPagination;

    public $userId;
   
    public function render()
    {
        return view('livewire.articles-show', [
            'articles' =>  Article::where('user_id', $this->userId)->latest()->paginate(8)
        ]);
    }

    public function destroy(Article $article)
    {
        $article->delete();
    }
}
