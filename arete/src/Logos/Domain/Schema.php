<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

class Schema
{
    public const GENUS = [
        'creator' => 'creator',
        'source'  => 'source'
    ];

    public const VERSION = 'l.0.1';

    public array $creatorTypes = [
        'person'    => [
            'label'     => 'Person',
            'fields'    => [
                [ 'name', 'Name'],
                [ 'lastName', "Last Name"]
            ],
        ],
        'organization'  => [
            'label'     => 'Organization',
            'fields'    => [
                ['fullName', 'Full Name'],
                ['acronim', 'Acronim']
            ]
        ]
    ];

    public function creatorTypes(): array
    {
        return $this->creatorTypes;
    }
}
