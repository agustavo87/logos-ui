<?php

namespace App\Http\Controllers;

use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Arete\Logos\Application\Ports\Logos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $results = array_map(function ($source) {
            $sourceData = $source->toArray();
            $sourceData['render'] = $source->render();
            return $sourceData;
        }, $results);

        return $results;
    }

    public function filter(Request $request)
    {
        return response()->json(
            Logos::filteredIndex([
                'title' => 'a'
            ])
        );
    }
}
