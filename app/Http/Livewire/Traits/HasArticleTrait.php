<?php

namespace App\Http\Livewire\Traits;

use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use App\Models\Article;

trait HasArticleTrait
{
    protected ?Article $_article = null;

    protected ?SourcesRepository $_sources = null;

    protected ?array $_sourceList = null;

    public function getArticleProperty()
    {
        if (!$this->_article) {
            $this->_article = Article::find($this->articleId);
        }
        return $this->_article;
    }

    public function getSourcesProperty()
    {
        if (!isset($this->_sources)) {
            $this->_sources = app(SourcesRepository::class);
        }
        return  $this->_sources;
    }

    public function getSourceListProperty()
    {
        if(!$this->_sourceList) {
            $this->_sourceList = [];
            if($this->article && !is_null($this->article->source_list)) {
                foreach ($this->article->source_list as $sourceKey) {
                    if($sourceKey && $sourceKey !== 'null') {
                        try {
                            $this->_sourceList[$sourceKey] = $this->sources->getByKey($sourceKey);
                        } catch (\Throwable $th) {
                            dd($sourceKey);
                        }
                    }
                }
            } 
        }
        return $this->_sourceList;
    }
}
