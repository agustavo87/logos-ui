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
        list('year' => $year, 'title' => $title, 'editorial' => $editorial, 'city' => $city) = $source->data;
        return "{$this->renderCreatorsAPA($source->creators)} ({$year}). {$title}. {$editorial}: {$city}.";
    }

    public function renderArticle($source)
    {
        list('year' => $year, 'title' => $title, 'journal' => $journal, 'volume' => $vol, 
            'issue' => $issue, 'firstPage' => $pageInit, 'lastPage' => $pageEnd) = $source->data;

        return "{$this->renderCreatorsAPA($source->creators)} ({$year}). {$title}. {$journal}, vol. {$vol}({$issue}), {$pageInit}-{$pageEnd}.";
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

    public function renderDefault($source)
    {
        return $source->data['title'];
    }
}