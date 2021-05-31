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
    ]
];
