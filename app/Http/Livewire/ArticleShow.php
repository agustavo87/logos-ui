<?php

namespace App\Http\Livewire;

use App\Http\Livewire\Traits\HasArticleTrait;
use App\Models\Article;
use Livewire\Component;

class ArticleShow extends Component
{
    use HasArticleTrait;

    public $articleId;

    public function render()
    {
        return view('livewire.article-show', [
            'article' => $this->article
        ]);
    }

    public function getRenderedSourcesListProperty()
    {
        return array_map(fn($source) => $source->render(), $this->source_list);
    }
}
