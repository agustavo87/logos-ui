<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Defaults;

use Arete\Logos\Application\Ports\Abstracts\ConfigurationRepository as ConfigurationRepositoryPort;

class ConfigurationRepository extends ConfigurationRepositoryPort
{
    public array $config = [];

    public function __construct(?array $config = null)
    {
        if ($config) {
            $this->config = $config;
            return;
        }
        $this->config = [
            'ownersTable'    => 'users',
            'ownersPK'       => 'id',
            'defaultOwner'  => 1,
            'valueTypes'    => [
                'text'      => 'text',
                'number'    => 'number',
                'date'      => 'date',
                'complex'   => 'complex',
                'default'   => 'text'
            ],
            'attributesValueTypes'   => [
                'default'       => 'text',
                'map' => [
                    'date'              => 'date',
                    'accessDate'        => 'date',
                    'numPages'          => 'text',
                    'seriesNumber'      => 'number',
                    'volume'            => 'number',
                    'numberOfVolumes'   => 'number',
                    'issue'             => 'number'
                ]
            ],
            'source' => [
                'typesLabels' => [
                    'default' => '',
                    'map' => [
                        'journalArticle'   => 'Journal Article',
                        'annotation'       => 'Annotation',
                        'blogPost'         => 'Blog Post',
                        'book'             => 'Book',
                        'bookSection'      => 'Book Section'
                    ]
                ]
            ]
                    ];
    }

    public function get(string $key)
    {
        return $this->config[$key] ?? null;
    }
}
