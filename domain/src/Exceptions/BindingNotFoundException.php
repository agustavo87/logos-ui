<?php

declare(strict_types=1);

namespace Arete\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class BindingNotFoundException extends \Exception implements NotFoundExceptionInterface
{
   //
}
