<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

use Arete\Common\FillableProperties;
use Arete\Logos\Domain\Comparators\ComparatorFactory;
use Arete\Logos\Domain\Comparators\ComparatorInterface;

class Attribute extends FillableProperties
{
    /**
     * @var string attribute code name
     */
    public string $code = '';

    /**
     * @var string|null base attribute code name
     */
    public ?string $base  = null;

    /**
     * @var string|null optional label
     */
    public ?string $label = null;

    /**
     * @var string value type
     */
    public string $type = '';

    /**
     * @var int order of presentation
     */
    public int $order = 0;

    protected ComparatorInterface $comparator;

    public function __construct($params)
    {
        parent::__construct($params);
        $this->comparator = (new ComparatorFactory())->get($params['type']);
    }

    public function compare($a, $b): int
    {
        return $this->comparator->compare($a, $b);
    }

    protected function fillDefaultsAttributes()
    {
        //
    }
}
