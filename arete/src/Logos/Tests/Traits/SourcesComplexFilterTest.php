<?php

declare(strict_types=1);

namespace Arete\Logos\Tests\Traits;

use Arete\Logos\Application\Ports\Interfaces\ComplexSourcesRepository;
use DateTime;

trait SourcesComplexFilterTest
{
    /**
     * @param ComplexSourcesRepository $sources
     *
     * @depends testComplexSourcesRepositoryIsBinded
     * @return
     */
    public function testComplexFilterTestRecivesComplexSourcesRepo(
        ComplexSourcesRepository $sources
    ): ComplexSourcesRepository {
        $this->assertInstanceOf(ComplexSourcesRepository::class, $sources);
        $this->seedSources($sources);
        return $sources;
    }

    /**
     * @param ComplexSourcesRepository $filter
     *
     * @depends testComplexFilterTestRecivesComplexSourcesRepo
     * @return ComplexSourcesRepository
     */
    public function testFilterByAttributes(ComplexSourcesRepository $sources): ComplexSourcesRepository
    {
        $params = [
            'attributes' => ['title' => 'gatos', 'abstractNote' => 'Dios']
        ];
        $godSource = $sources->complexFilter($params)[0];
        $this->assertEquals("Todos los gatos van al Cielo.", $godSource->title);
        return $sources;
    }

    /**
     * @param ComplexSourcesRepository $sources
     *
     * @depends testFilterByAttributes
     * @return ComplexSourcesRepository
     */
    public function testFilterByAuthor(ComplexSourcesRepository $sources): ComplexSourcesRepository
    {
        $result = $sources->complexFilter([
                'attributes' => [
                    'title' => 'gatos'
                ],
                'participations' => [
                    'author' => [
                        'attributes' => [
                            'name' => 'Magdalena Tamara'
                        ]
                    ]
                ],
            ]);

        $source = $result[0];

        $this->assertEquals("Todos los gatos van al Cielo.", $source->title);
        return $sources;
    }

    /**
     * @param ComplexSourcesRepository $sources
     *
     * @depends testFilterByAuthor
     * @return ComplexSourcesRepository
     */
    public function testFilterByRole(ComplexSourcesRepository $sources): ComplexSourcesRepository
    {
        $result = $sources->complexFilter([
                'attributes' => [
                    'title' => 'gatos'
                ],
                'participations' => [
                    'reviewedAuthor' => []
                ],
            ]);

        $source = $result[0];

        $this->assertEquals(
            "Roberto Miguel",
            $source->participations()->byRelevance('reviewedAuthor')[0]->name
        );
        return $sources;
    }

    /**
     * @param ComplexSourcesRepository $sources
     *
     * @depends testFilterByRole
     * @return ComplexSourcesRepository
     */
    public function testFilterByOwner(ComplexSourcesRepository $sources): ComplexSourcesRepository
    {
        $results = $sources->complexFilter([
            'ownerID' => '1',
            'participations' => [
                'author' => [
                    'attributes' => [
                        'name' => 'Magdalena Tamara'
                    ]
                ]
            ]
        ]);
        $this->assertGreaterThan(0, count($results));
        $source = $results[0];
        $this->assertEquals('Animal Metaphysics Handbook', $source->title);
        return $sources;
    }

    /**
     * @param ComplexSourcesRepository $sources
     *
     * @depends testFilterByOwner
     * @return ComplexSourcesRepository
     */
    public function testFilterByKey(ComplexSourcesRepository $sources): ComplexSourcesRepository
    {
        $results = $sources->complexFilter([
            'key' => 'guinazu19',
            'attributes' => [
                'title' => 'gato'
            ]
        ]);

        $this->assertGreaterThan(0, count($results));
        $source = $results[0];
        $this->assertEquals('guinazu1988', $source->key());
        $this->assertEquals('Todos los gatos van al Cielo.', $source->title);
        $this->assertEquals('Journal of Trans-Species Metaphysics.', $source->publicationTitle);
        return $sources;
    }


    public function seedIndexTestData(ComplexSourcesRepository $sources, string $uid)
    {
        $prototypeSource = [
            'type' => 'journalArticle',
            'attributes' => [
                'title' => "Las palomas con DNI '{$uid}' de ",
                'volume' => 4,
                'issue' => 3
            ],
            'participations' => [
                [
                    'role' => 'author',
                    'relevance' => 2,
                    'creator' => [
                        'type' => 'person',
                        'attributes' => [
                            'name' => "Peteco Mario",
                            'lastName' => "Carabajal"
                        ]
                    ]
                ],[
                    'role' => 'author',
                    'relevance' => 3,
                    'creator' => [
                        'type' => 'person',
                        'attributes' => [
                            'name' => "Carlos Saul",
                            'lastName' => "Abad"
                        ]
                    ]
                ]
            ]
        ];

        $varianceData = [
            ['Marta', '1978', 'Cruz', 5,3,'Abad'],
            ['Samanta', '1959', 'Cirigliano', 2,3,'Abad'],
            ['Pablo', '1997', 'Saucedo', 22,3,'Abad'],
            ['Don Ramón', '1967', 'Simplicio', 12,1,'Zalazar'],
            ['María', '2005', 'Cibrian', 32,3,'Abad'],
            ['Claudia', '2006', 'Leuco', 35,3,'Abad'],
            ['Petiza Violenta', '1998', 'Cruz', 21,3,'Abad'],
            ['Petiza Embaucadora', '1999', 'Cruz', 22,3,'Abad'],
            ['Cintia', '2007', 'Milei', 38,3,'Abad'],
            ['Roberto', '1988', 'Etanislao', 19,1,'Zalazar'],
            ['Shakira', '2008', 'Sanmartin', 38,1,'Abciso'],
            ['Luigi', '2018', 'Lopez', 45,3,'Abad'],
        ];

        foreach ($varianceData as $data) {
            $sourceParams = $prototypeSource;
            $sourceParams['attributes']['title'] .= $data[0];
            $sourceParams['attributes']['date'] = new DateTime('01-01-' . $data[1]);
            $sourceParams['participations'][0]['creator']['attributes']['lastName'] = $data[2];
            $sourceParams['attributes']['volume'] = $data[3];
            // This alternates the 'first' -most relevant- author to sort with.
            $sourceParams['participations'][1]['relevance'] = $data[4];
            $sourceParams['participations'][1]['creator']['attributes']['lastName'] = $data[5];
            $sources->createFromArray($sourceParams);
        }
    }

    /**
     * @param ComplexSourcesRepository $sources
     *
     * @depends testFilterByKey
     * @return array
     */
    public function testLimitResults(ComplexSourcesRepository $sources): array
    {
        $uid = uniqid();
        $this->seedIndexTestData($sources, $uid);

        $mySources = $sources->limit(3)
                             ->offset(2)
                             ->orderBy('date', 'attributes')
                             ->complexFilter(['attributes' => ['title' => $uid]]);

        $this->assertEquals(3, count($mySources));
        $this->assertStringContainsString(
            'Marta',
            $mySources[0]->title,
            'No obtiene el nombre esperado en el título si los resultados se ordenaran por fecha'
        );

        return [$sources, $uid];
    }

    /**
     * @param array $data
     *
     * @depends testLimitResults
     * @return ComplexSourcesRepository
     */
    public function testOrderByKey(array $data)
    {
        /** @var ComplexSourcesRepository */
        $sources = $data[0];
        $uid = $data[1];
        $mySources = $sources->orderBy('key', 'source')
                             ->offset(0)
                             ->limit(3)
                             ->complexFilter(['attributes' => ['title' => $uid]]);

        $this->assertEquals(3, count($mySources));
        $testSource = $mySources[0];
        $this->assertStringContainsString('cibrian', $mySources[0]->key());
        return [$sources, $uid];
    }

    /**
     * @param mixed $data
     *
     * @depends testOrderByKey
     * @return void
     */
    public function testOrderByNumberAttribute($data)
    {
        /** @var ComplexSourcesRepository */
        $sources = $data[0];
        $uid = $data[1];

        $mySources = $sources->orderBy('volume', 'attributes')
                             ->offset(0)
                             ->limit(4)
                             ->complexFilter(['attributes' => ['title' => $uid]]);

        $this->assertEquals(4, count($mySources));
        $this->assertStringContainsString('cirigliano1959', $mySources[0]->key());

        return $data;
    }

    /**
     * @param array $data
     *
     * @depends testOrderByNumberAttribute
     * @return array
     */
    public function testOrderByCreatorAttribute(array $data): array
    {
        /** @var ComplexSourcesRepository */
        $sources = $data[0];
        $uid = $data[1];

        $mySources = $sources->orderBy('lastName', 'creator')
                             ->offset(0)
                             ->limit(3)
                             ->complexFilter(['attributes' => ['title' => $uid]]);

        $this->assertEquals(3, count($mySources));

        $this->assertEquals('Abciso', $mySources[0]->participations()->byRelevance('author')[0]->lastName);

        return $data;
    }

    public static function seedSources(ComplexSourcesRepository $sources)
    {
        $sources->flush();

        $sources->createFromArray([
            'key' => 'carabajal1988',
            'type' => 'journalArticle',
            'attributes' => [
                'title' => "Todos los gatos van al infierno.",
                'abstractNote' =>   "La historia del trato secreto de Lucifer con una especie diseñada " .
                                    "para dominar humanos.",
                'date' => new DateTime('01-01-1988'),
                'volume' => 4,
                'issue' => 3
            ],
            'participations' => [
                [
                    'role' => 'author',
                    'relevance' => 2,
                    'creator' => [
                        'type' => 'person',
                        'attributes' => [
                            'name' => "Peteco Mario",
                            'lastName' => "Carabajal"
                        ]
                    ]
                ]
            ]
        ], 3);

        $sources->createFromArray([
            'key' => 'guinazu1988',
            'type' => 'journalArticle',
            'attributes' => [
                'title' => "Todos los gatos van al Cielo.",
                'abstractNote' =>   "La historia del trato secreto de Dios con una especie diseñada " .
                                    "para llevar a los humanos a ejercitar su paciencia.",
                'publicationTitle' => 'Journal of Trans-Species Metaphysics.',
                'date' => new DateTime('01-01-1988'),
                'volume' => 4,
                'issue' => 3
            ],
            'participations' => [
                [
                    'role' => 'author',
                    'relevance' => 2,
                    'creator' => [
                        'type' => 'person',
                        'attributes' => [
                            'name' => "Magdalena Tamara",
                            'lastName' => "Guiñazú"
                        ]
                    ]
                ], [
                    'role' => 'reviewedAuthor',
                    'relevance' => 4,
                    'creator' => [
                        'type' => 'person',
                        'attributes' => [
                            'name' => "Roberto Miguel",
                            'lastName' => "García"
                        ]
                    ]
                ]
            ]
        ], 3);

        $sources->createFromArray(
            [
                'key' => 'guinazubook',
                'type' => 'book',
                'attributes' => [
                    'title' => "Animal Metaphysics Handbook",
                    'publisher' => 'Gomez e Hijos Inc.',
                    'place' => 'Argentina'
                ],
                'participations' => [
                    [
                        'role' => 'author',
                        'relevance' => 2,
                        'creator' => [
                            'type' => 'person',
                            'attributes' => [
                                'name' => "Magdalena Tamara",
                                'lastName' => "Guiñazú"
                            ]
                        ]
                    ]
                ]
            ],
            1
        );
    }
}
