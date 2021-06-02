<?php

return [
    'usersTable'    => 'users',
    'usersPK'       => 'id',
    'valueTypes'    => [
        'text'      => 'text',
        'number'    => 'number',
        'date'      => 'date',
        'complex'   => 'complex',
        'default'   => 'text'
    ],
    'fieldValueTypes' => [
        'default'           => 'text',
        'date'              => 'date',
        'accessDate'        => 'date',
        'numPages'          => 'text',
        'seriesNumber'      => 'number',
        'volume'            => 'number',
        'numberOfVolumes'   => 'number',
        'issue'             => 'number'
    ],
    'schemaTypes'    => [
        'source'    => 'source',
        'creator'   => 'creator'
    ],
    'creatorTypes'  => [
        'version'   => 'l.0.1',
        'data' => [
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
        ]
    ],
    'source' => [
        'typesLabels' => [
            'journalArticle'   => 'Journal Article',
            'annotation'       => 'Annotation',
            'blogPost'         => 'Blog Post',
            'book'             => 'Book',
            'bookSection'      => 'Book Section',
            'default'          => ''
        ]
    ]
];
