<?php

namespace App\Http\Controllers;

use App\Models\Source;
use App\Models\User;
use Arete\Logos\Application\Ports\Interfaces\SourcesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SourceController extends Controller
{

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
        } elseif ($request->has('userid')) {
            $userID = $request->userid;
        } else {
            $userID = User::first('id')->id;
        }

        if ($request->has('attr')) {
            $attr = $request->attr;
        } else {
            $attr = 'title';
        }

        if ($request->has('q')) {
            $q = $request->q;
        } else {
            $q = 'a';
        }

        $results = $this->sources
                        ->getLike(
                            $attr,
                            $q,
                            $userID
                        );

        if (!count($results)) {
            return 'sin resultados';
        }
        $results = array_map(function ($source) {
            $sourceData = $source->toArray();
            $sourceData['render'] = $source->render();
            return $sourceData;
        }, $results);

        return $results;
    }
}
