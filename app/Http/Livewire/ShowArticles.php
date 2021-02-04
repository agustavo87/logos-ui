<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class ShowArticles extends Component
{
    use WithPagination;

    public $userId;
   
    public function render()
    {
        return view('livewire.show-articles', [
            'articles' =>  Article::where('user_id', $this->userId)->paginate(8)
        ]);
    }

    public function destroy(Article $article)
    {
        $article->delete();
    }
}
