<?php 

/**
 * Countries Codes: ISO 3166 Alpha-2 (2 characters)
 * https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes
 * 
 * TODO: Sería conveniente organizar los locales como "entidades" en una BD.
 * 
 */

return [
    'languages' => [
        'supported' => ['en', 'es'],
        'names' => [
            'en' => 'English',
            'es' => 'Español'
        ],
        'default' => 'en'
    ],    

    'countries' => [
        'AR' => [
            'name' => 'Argentina',
            'language' => 'es'
        ],
        'ES' => [
            'name' => 'España',
            'language' => 'es'
        ],
        'CO' => [
            'name' => 'Colombia',
            'language' => 'es'
        ],
        'US' => [
            'name' => 'United States',
            'language' => 'en'
        ]

    ]
];