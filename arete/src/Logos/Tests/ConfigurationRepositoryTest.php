<?php

declare(strict_types=1);

namespace Arete\Logos\Tests;

use Arete\Logos\Application\LogosContainer;
use Arete\Logos\Application\Ports\Abstracts\ConfigurationRepository;
use PHPUnit\Framework\TestCase;
use Arete\Logos\Application\TestSourcesProvider;

class ConfigurationRepositoryTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // boot container
        LogosContainer::pushProvider(TestSourcesProvider::class);
        LogosContainer::load();
    }

    public function testTestBindingsWorking(): ConfigurationRepository
    {
        $config = LogosContainer::get('config');
        $this->assertInstanceOf(ConfigurationRepository::class, $config);
        return $config;
    }

    /**
     * @param ConfigurationRepository $config
     *
     * @depends testTestBindingsWorking
     * @return void
     */
    public function testGetDefaultConfigKey(ConfigurationRepository $config)
    {
        $this->assertEquals('users', $config->get('ownersTable'));
    }
}
