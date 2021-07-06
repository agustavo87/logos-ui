<?php

namespace Arete\Logos\Infrastructure\Laravel\Http\Controllers;

use Arete\Logos\Infrastructure\Laravel\Http\Requests\SourceSearchRequest;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Arete\Logos\Application\Ports\Logos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Arete\Common\Laravel\Controller;
use Arete\Logos\Domain\Source;

class SourceController extends Controller
{
    public const DEF_ATTR = 'title';
    public const DEF_Q = '%';
    public static $defUserID = null;

    protected SourcesRepository $sources;

    public function __construct(SourcesRepository $sources)
    {
        $this->sources = $sources;
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

    public function showSearch(SourceSearchRequest $request)
    {
        return view('logos::sources.search');
    }
}
