<?php

namespace Arete\Logos\Infrastructure\Laravel\Http\Controllers;

use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Arete\Logos\Application\Ports\Interfaces\SourceTypeRepository;
use Arete\Logos\Application\Ports\Logos;
use Arete\Logos\Domain\Source;
use Arete\Logos\Infrastructure\Laravel\Http\Requests\SourceSearchRequest;
use Arete\Common\Laravel\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SourceController extends Controller
{
    public const DEF_ATTR = 'title';
    public const DEF_Q = '%';
    public static $defUserID = null;

    protected SourcesRepository $sources;
    protected SourceTypeRepository $sourceTypes;

    public function __construct(
        SourcesRepository $sources,
        SourceTypeRepository $sourceTypes
    ) {
        $this->sources = $sources;
        $this->sourceTypes = $sourceTypes;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::check()) {
            $userID = Auth::user()->id;
        } else {
            $userID = $request->has('userid') ? $request->userid : self::$defUserID;
        }
        $attr = $request->has('attr') ? $request->attr : self::DEF_ATTR;
        $q = $request->has('q') ? $request->q : self::DEF_Q;

        $results = $this->sources
                        ->getLike(
                            $attr,
                            $q,
                            $userID
                        );

        if (!count($results)) {
            return response()->json('no results');
        }
        $results = $this->sourcesToArray($results);

        return $results;
    }

    /**
     * @param \Arete\Logos\Domain\Source[] $sources
     *
     * @return array
     */
    protected function sourcesToArray(array $sources): array
    {
        return array_map(function (Source $source) {
            $sourceData = $source->toArray('relevance');
            $sourceData['render'] = $source->render();
            return $sourceData;
        }, $sources);
    }

    public function filter(Request $request)
    {
        $data = Logos::filteredIndex([
            'attributes' => [
                'title' => 'gato'
            ]
        ]);

        $data = $this->sourcesToArray($data);
        return response()->json($data);
    }

    public function showSearch(Request $request)
    {
        $user = auth()->check() ? auth()->user()->id : 0;
        return view('logos::sources.search', [
            'userID' => $user,
            'sourceTypes' => $this->getSourceTypes(),
            'attributes' => $this->sourceTypes->attributes()
        ]);
    }

    protected function getSourceTypes(): array
    {
        $sourceTypes = $this->sourceTypes->types();
        $sourceTypes = array_map(function ($sType) {
            $path = "logos::sources.types.{$sType}";
            return (object) [
                'code' => $sType,
                'label' => trans()->has($path) ? trans($path) : null
            ];
        }, $sourceTypes);
        array_unshift($sourceTypes, (object) [
            'code' => null,
            'label' => trans('logos::sources.types.any')
        ]);
        return $sourceTypes;
    }

    public function typeAttributes(Request $request)
    {
        $type = $request->has('type') ? $request->type : null;
        return response()->json($this->sourceTypes->attributes($type));
    }

    public function search(SourceSearchRequest $request)
    {
        $query = [];
        if (
            $request->has('type') &&
            !($request->type !== null || $request->type !== "null")
        ) {
            $query['type'] = $request->type;
        }
        if ($request->has('ownerID')) {
            $query['ownerID'] = (int) $request->ownerID == 0 ? null : $request->ownerID;
        }
        if ($request->has('attribute')) {
            $query['attributes'] = [];
            foreach ($request->attribute as $attribute) {
                $name = $attribute['name'];
                $value = $attribute['value'];
                if ((bool) $name  && (bool) $value) {
                    $query['attributes'][$name] = $value;
                }
            }
        }
        // dd($query);

        $result = Logos::filteredIndex($query);
        $result = $this->sourcesToArray($result);
        return response()->json($result);
    }
}
