<?php

return [
    'usersTable'    => 'users',
    'usersPK'       => 'id',
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
