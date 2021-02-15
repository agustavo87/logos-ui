<?php

namespace App\Logos;

use App\Models\Source;

class Sources 
{
    public function render(Source $source)
    {
        
        switch ($source->type . ':' . $source->schema) {
            case 'citation.book:0.0.1':
                return $this->renderBook($source);
                break;
            case 'citation.article:0.0.1':
                return $this->renderArticle($source);
                break;
            
            default:
                return $this->renderDefault($source);
                break;
        }
    }

    public function renderBook($source)
    {
        return $source->data['title'];
    }

    public function renderArticle($source)
    {

        return $source->data['title'];
    }

    public function renderDefault($source)
    {
        return $source->data['title'];
    }
}