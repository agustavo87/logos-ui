<?php

declare(strict_types=1);

namespace Arete\Logos\Domain\Zotero;

use Arete\Common\FillableProperties;

class ItemType extends FillableProperties implements \Stringable
{
    public string $itemType;
    /**
     * @var Field[]
     */
    public array $fields;

    /**
     * @var CreatorType[]
     */
    public array $creatorTypes;

    protected function fillDefaultsAttributes()
    {
        $this->defaultProperties = [
            'itemType' => '',
            'fields' => [],
            'creatorTypes' => []
        ];
    }

    /**
     * Add fields
     *
     * E.g. [['field' => 'title']]
     * Merge values with defaults
     *
     * @param   array $data of item $attributes
     *
     * @return  self
     */
    public function addFields(array $data): self
    {
        foreach ($data as $attributes) {
            $this->fields[] = new Field($attributes);
        }
        return $this;
    }

    /**
     * Add Creator Types
     *
     * * E.g. [['creatorType' => 'author']]
     * Merge values with defaults
     *
     * @param   array $data of item $attributes
     *
     * @return  self
     */
    public function addCreatorTypes(array $data): self
    {
        foreach ($data as $attributes) {
            $this->creatorTypes[] = new CreatorType($attributes);
        }
        return $this;
    }

    /**
     * Get the first Field that match $name.
     *
     * @param string $name
     *
     * @return Field|null
     */
    public function getField(string $name): ?Field
    {
        $results = array_filter($this->fields, fn ($field) => $field->field == $name);
        // error_log('resultados: ' . count($results));
        return count($results) ? array_shift($results) : null;
    }

    public function __toString()
    {
        return $this->itemType;
    }
}
