<?php

declare(strict_types=1);

namespace Arete\Logos\Domain;

trait IndexResults
{
    protected int $limit = 10;
    protected int $offset = 0;
    protected array $orderBy = ['group' => 'source', 'field' => 'key', 'asc' => true];

    public function limit(int $n): self
    {
        $this->limit = $n;
        return $this;
    }

    public function offset(int $i): self
    {
        $this->offset = $i;
        return $this;
    }

    public function orderBy(string $field, $group = 'source', bool $asc = true): self
    {
        $this->orderBy = [
            'group' => $group,
            'field' => $field,
            'asc' => $asc
        ];
        return $this;
    }
}
