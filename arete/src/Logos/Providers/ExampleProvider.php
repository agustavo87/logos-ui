<?php

declare(strict_types=1);

namespace Arete\Logos\Providers;

use Arete\Common\Provider;

class ExampleProvider extends Provider
{
    public function register()
    {
        $this->container::register('example', function ($container) {
            return 'hola';
        });

        $this->container::register('object', function ($container) {
            return new class ($container) {
                public $name;
                public $greet;
                public function __construct($container)
                {
                    $this->greet = $container::get('example');
                }
                public function __invoke($name)
                {
                    $this->name = $name;
                    return $this;
                }
                public function greet()
                {
                    echo $this->greet . ' ' . $this->name;
                }
            };
        });
    }
}
