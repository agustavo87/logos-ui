<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;

use function Arete\Common\simplifyWord;

class SourcesKeyGenerator
{
    public SourcesRepository $sources;
    public CreatorsRepository $creators;

    public static array $diferenciators = [
        'a','b','c','d','e','f','g','h','i','j','k','m','n','o','p',
        'q', 'r', 's', 't', 'u', 'b', 'w', 'x', 'y', 'z'
    ];

    public function __construct(
        SourcesRepository $sources,
        CreatorsRepository $creators
    ) {
        $this->sources = $sources;
        $this->creators = $creators;
    }

    public function getKey(array $params): string
    {
        if (isset($params['key'])) {
            $keyWord = $params['key'];
        } else {
            $keyWord = $this->generateKeyWord($params);
        }
        $i = 1;
        $baseKeyWord = $keyWord;
        while ($this->sources->keyExist($keyWord)) {
            $keyWord = $baseKeyWord . $this->getDiferenciator(++$i);
        }
        return $keyWord;
    }

    protected function generateKeyWord(array $params): string
    {
        $keyWord = $this->getCreatorKeyWord($params);

        if ($keyWord == '') {
            if (isset($params['title'])) {
                $keyWord = explode(' ', $params['title'])[0];
            } else {
                $keyWord = 'anon';
            }
        }

        $keyWord = simplifyWord($keyWord);
        if (isset($params['attributes']['date'])) {
            $keyWord .= $params['attributes']['date']->format('Y');
        }

        return $keyWord;
    }

    protected function getCreatorKeyWord(array $params): string
    {
        if (!isset($params['participations'])) {
            return '';
        }

        // look for valid relevant participation
        $authors = array_filter(
            $params['participations'],
            /** @todo seleccionar creador primario */
            fn ($part) => $part['role'] == 'author'
        );
        $authors = array_values($authors);
        if (count($authors)) {
            /** @todo seleccionar el más relevante */
            $participation = $authors[0];
        } else {
            $participation = $params['participations'][0];
        }

        $creator = [];
        if (isset($participation['creator']['creatorID'])) {
            $creator = $this->creators->get(
                $participation['creator']['creatorID']
            )->toArray();
        } else {
            $creator = $participation['creator'];
        }

        // get some relevant attribute
        if ($creator['type'] == 'person') {
            return $creator['attributes']['lastName'];
        }

        return array_values($creator['attributes'])[0];
    }

    protected function getDiferenciator(int $i): string
    {
        if ($i < count(self::$diferenciators)) {
            return self::$diferenciators[$i - 1];
        }
        return '-' . $i;
    }
}
