<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Logos\Domain\Contracts\Formatter;

class SimpleFormatter implements Formatter
{
    public function format(Source $source): string
    {
        $creators = $this->getCreators($source);
        $date = $this->getDate($source);
        $sourceFormat = $this->getSourceFormat($source);
        return "$creators ($date).  $sourceFormat.";
    }

    public function getCreators(Source $source): string
    {
        $participations = $source->participations();
        $authors = '';
        if ($participations->has('author')) {
            $n = 1 ;
            $authorsList = $participations->byRelevance('author');
            $authorCount = count($authorsList);
            foreach ($authorsList as $relevance => $participation) {
                if ($n > 1) {
                    if ($n >= $authorCount) {
                        $authors .= " & ";
                    } else {
                        $authors .= ", ";
                    }
                }
                $n++;

                if ($participation->creatorType()->code() === 'person') {
                    $firstLastName = explode(' ', $participation->lastName)[0];
                    $firstNameInitials = implode(' ', array_map(
                        fn($x) => substr($x, 0, 1) . '. ',
                        explode(' ', $participation->name)
                    ));

                    $authors .= "{$firstLastName}, {$firstNameInitials}";
                } elseif ($participation->creatorType()->code() == 'organization') {
                    $authors .= "{$participation->fullName}";
                }
            }
        }
        return $authors;
    }

    public function getDate(Source $source): string
    {
        $date = '';
        if ($source->has('date')) {
            // /** @var \DateTime */
            $dateObj = $this->datesize($source->date);
            $date = $dateObj->format('Y');
        }
        return $date;
    }

    protected function datesize($date)
    {
        return $date instanceof \DateTime ? $date : new \DateTime((string) $date);
    }

    public function getSourceFormat(Source $source): string
    {
        $txt = '';
        if ($source->has('title')) {
            $title = trim($source->title, ". ");
            $txt .= $title;
        }
        if ($source->has('publicationTitle')) {
            $title = trim($source->publicationTitle, ". ");
            $txt .= ". $title";
        }

        if ($source->has('publisher')) {
            $txt .= ". {$source->publisher}";
        }

        if ($source->has('place')) {
            $txt .= ": {$source->place}";
        }
        return $txt;
    }
}
