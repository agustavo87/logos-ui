<?php

namespace App\Logos;

use App\Models\Source;

class Sources 
{
    /**
     * Devuelve una representación estilo APA de una fuente
     * según su tipo
     * 
     * @param \App\Models\Source $source
     * @return string
     */
    public function render(Source $source): string
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

    /**
     * Devuelve un render estilo APA fuente tipo book 
     * esquema
     * 
     * @param \App\Models\Source $book
     * @return string
     */
    public function renderBook($book)
    {
        list('year' => $year, 'title' => $title, 'editorial' => $editorial, 'city' => $city) = $book->data;
        return "{$this->renderCreatorsAPA($book->creators)} ({$year}). {$title}. {$editorial}: {$city}.";
    }

    /**
     * Devuelve un render estilo APA fuente tipo article 
     * esquema
     * 
     * @param \App\Models\Source $article
     * @return string
     */    
    public function renderArticle($article)
    {
        list('year' => $year, 'title' => $title, 'journal' => $journal, 'volume' => $vol, 
            'issue' => $issue, 'firstPage' => $pageInit, 'lastPage' => $pageEnd) = $article->data;

        return "{$this->renderCreatorsAPA($article->creators)} ({$year}). {$title}. {$journal}, vol. {$vol}({$issue}), {$pageInit}-{$pageEnd}.";
    }

    /**
     * Render creators in standard APA style
     * 
     * @param \Illuminate\Database\Eloquent\Collection  $creators The \App\Models\Creator collection
     * @return string
     */
    public function renderCreatorsAPA($creators): string
    {
        $i = -1;
        $creatorsAPA = '';
        $creatorsLength = $creators->count();
        foreach ($creators as $creator) {
            if(++$i) {
                $creatorsAPA .= $i + 1 == $creatorsLength ? ' & ': ', ';
            }
            $creatorsAPA .= $creator->data['last_name'] . ', ' . strtoupper($creator->data['name'][0]) . '.';
        }
        return $creatorsAPA;
    }


    /**
     * Devuelve un render por defecto de una fuente sin depender del 
     * esquema
     * 
     * @param \App\Models\Source $source
     * @return string
     */
    public function renderDefault($source): string
    {
        $creators = $source->creators;
        $render = $creators->count() ? $this->renderCreatorsAPA($creators) : '';
        $render .= $source->data['year'] ? "({$source->data['year'] }). " : '';
        $render .= $source->data['title'] ? "{$source->data['title']}." : '';
        $render .= $render === '' ? $source->key : '';
        return $render;
    }

    /**
     * Devuelve el nombre legible del tipo de fuente
     * 
     * @param \App\Models\Source $source
     * @return string
     */
    public function name($source): string
    {
        return [
            'citation.article' => 'Artículo',
            'citation.book' => 'Libro'
            ][$source->type];
    }

}