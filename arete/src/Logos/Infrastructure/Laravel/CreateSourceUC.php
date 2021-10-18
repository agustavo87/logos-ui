<?php

declare(strict_types=1);

namespace Arete\Logos\Infrastructure\Laravel;

use Arete\Logos\Application\DTO\AttributePresentation;
use Arete\Logos\Application\DTO\RolePresentation;
use Arete\Logos\Application\DTO\SourceTypePresentation;
use Arete\Logos\Application\Ports\Interfaces\CreateSourceUC as ICreateSourceUC;
use Arete\Logos\Application\Ports\Interfaces\CreatorsRepository;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Arete\Logos\Application\Ports\Interfaces\SourcesTranslator;
use Arete\Logos\Infrastructure\Laravel\Common\DB;
use DateTime;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CreateSourceUC implements ICreateSourceUC
{
    protected DB $db;
    protected SourcesRepository $sources;
    protected SourcesTranslator $translator;
    protected CreatorsRepository $creators;

    public function __construct(
        DB $db,
        SourcesRepository $sources,
        CreatorsRepository $creators,
        SourcesTranslator $translator
    ) {
        $this->db = $db;
        $this->sources = $sources;
        $this->creators = $creators;
        $this->translator = $translator;
    }

    public function publishSourceTypesPresentationScript()
    {
        $stubPath = realpath(__DIR__ . '/stubs/lgassets.js.stub');
        $targetPath = public_path('js/' . basename($stubPath, '.stub'));

        $presentations = json_encode($this->getSourceTypesPresentations());
        $stub = File::get($stubPath);
        $stub = str_replace('{{ sourceTypes }}', $presentations, $stub);
        return File::put($targetPath, $stub);
    }

    public function getSourceTypesPresentations(): array
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
            $roles = $this->getRolesPresentations($type->code_name);
            $data[$type->code_name] = new SourceTypePresentation(
                $type->code_name,
                $label,
                $attributes,
                $roles
            );
        }

        return $data;
    }

    /**
     * @param string $typeCode
     *
     * @return \Arete\Logos\Application\DTO\RolePresentation[]
     */
    protected function getRolesPresentations(string $typeCode): array
    {
        /**
         * @todo retornar los roles del tipo correspondiente con RolePresentation
         */
        return $this->db->getRoles($typeCode)
                ->map(function ($item, $key) {
                    return new RolePresentation(
                        $item->code_name,
                        $this->translator->translate($item->code_name, 'roles') ??
                                    ($item->label ?? $item->code_name),
                        (bool) $item->primary
                    );
                })->toArray();
    }

    /**
     * Returns a source type attribute presentations
     *
     * @param string $typeCode
     *
     * @return \Arete\Logos\Application\DTO\AttributePresentation[] keyed by attribute code
     */
    protected function getAttributePresentations(string $typeCode): array
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

    public function create($ownerID, string $type, array $attributes, array $creators, ?string $key = null): string
    {
        // should be validated on adapter, but just to be sure.
        if (isset($attributes['date'])) {
            $attributes['date'] = $this->datesize($attributes['date']);
        }
        $creators = $this->processCreators($creators);
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

    protected function processCreators(array $creators): array
    {
        return $creators;
        // foreach ($creators as $creator) {

        // }
    }

    protected function datesize($date): DateTime
    {
        return $date instanceof DateTime ? $date : new DateTime($date);
    }

    public function sugestKey($params): string
    {
        return $this->sources->getKey($params);
    }

    public function suggestCreators(
        $owner,
        string $hint,
        string $attribute = 'lastName',
        string $type = 'person',
        string $orderBy = 'lastName',
        bool $asc = true,
        int $limit = 5
    ): array {
        return $this->creators->suggestCreators(
            $owner,
            $hint,
            $attribute,
            $type,
            $orderBy,
            $asc,
            $limit
        );
    }
}
