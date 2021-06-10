<?php

declare(strict_types=1);

namespace Arete\Logos\Repositories\Conceptual;

use Arete\Logos\Repositories\SourceTypeRepositoryInterface;
use Arete\Logos\Repositories\CreatorTypeRepositoryInterface;
use Arete\Logos\Models\SourceInterface;

abstract class SourceRepository
{
    protected SourceTypeRepositoryInterface $sourceTypes;
    protected CreatorTypeRepositoryInterface $creatorTypes;

    public function __construct(
        SourceTypeRepositoryInterface $sourceTypes,
        CreatorTypeRepositoryInterface $creatorTypes
    ) {
        $this->sourceTypes = $sourceTypes;
        $this->creatorTypes = $creatorTypes;
    }

    public const CREATE_KEYWORD = 'create';

    public function __call($name, $arguments)
    {
        if (self::forwardIf($name, $arguments)) {
            $methodName = self::getForwardMethodName($name, $arguments);
            if (method_exists($this, $methodName)) {
                return $this->$methodName(...$arguments);
            }
        }
        throw new \BadMethodCallException("Method don't exist", 1);
    }

    protected static function forwardIf($method, $arguments)
    {
        return str_starts_with($method, self::CREATE_KEYWORD);
    }

    protected static function getForwardMethodName($method, $arguments)
    {
        return substr($method, strlen(self::CREATE_KEYWORD));
    }

    /**
     * createFromArray
     *
     * @param array $params
     *
     * @return SourceInterface
     */
    abstract protected function FromArray(array $params): SourceInterface;
}
