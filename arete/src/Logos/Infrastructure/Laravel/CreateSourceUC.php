<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\DTO\AttributePresentation;
use Arete\Logos\Application\DTO\SourceTypePresentation;
use Arete\Logos\Application\Ports\Interfaces\CreateSourceUC as ICreateSourceUC;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Arete\Logos\Application\Ports\Interfaces\SourcesTranslator;
use Arete\Logos\Infrastructure\Laravel\Common\DB;
use DateTime;
use Illuminate\Support\Facades\Log;

class CreateSourceUC implements ICreateSourceUC
{
    protected DB $db;
    protected SourcesRepository $sources;
    protected SourcesTranslator $translator;

    public function __construct(DB $db, SourcesRepository $sources, SourcesTranslator $translator)
    {
        $this->db = $db;
        $this->sources = $sources;
        $this->translator = $translator;
    }

    public function presentSourceTypes(): array
    {
        /**
         * @todo mover la lógica de DB al repositorio de tipo de fuentes
         * y extraer la clase UC hacia la aplicación.
         */
        $types = $this->db->getSourceTypeData(['code_name']);
        $data = [];
        foreach ($types as $type) {
            $label = $this->translator->translate($type->code_name, 'types');
            $attributes = $this->getAttributePresentations($type->code_name);
            $data[$type->code_name] = new SourceTypePresentation($type->code_name, $label, $attributes);
        }

        return $data;
    }

    /**
     * @param string[] $attrCodes
     *
     * @return \Arete\Logos\Application\DTO\AttributePresentation[] keyed by attribute code
     */
    public function getAttributePresentations(string $typeCode): array
    {
        $attributesData = $this->db->getSourceTypeAttributes(
            $typeCode,
            null,
            true,
            ['*']
        );
        $presentations = [];
        foreach ($attributesData as $attributeData) {
            $label = $this->translator->translate($attributeData->attribute_type_code_name, 'attributes') ??
                        ($attributeData->label ?? $attributeData->attribute_type_code_name);
            $presentations[$attributeData->attribute_type_code_name] = new AttributePresentation(
                $attributeData->attribute_type_code_name,
                $attributeData->base_attribute_type_code_name,
                $label,
                $attributeData->value_type,
                (int) $attributeData->order
            );
        }
        return $presentations;
    }

    public function create($ownerID, string $type, array $attributes, ?string $key = null): string
    {
        // should be validated on adapter, but just to be sure.
        if (isset($attributes['date'])) {
            $attributes['date'] = $this->datesize($attributes['date']);
        }
        $params = [
            'type' => $type,
            'attributes' => $attributes
        ];
        if ($key != null || $key != '') {
            $params['key'] = $key;
        }
        $source = $this->sources->createFromArray($params, $ownerID);
        Log::info('source creado', ['source', $source->toArray()]);
        return $source->key();
    }

    protected function datesize($date): DateTime
    {
        return $date instanceof DateTime ? $date : new DateTime($date);
    }

    public function sugestKey($params): string
    {
        return $this->sources->getKey($params);
    }
}
