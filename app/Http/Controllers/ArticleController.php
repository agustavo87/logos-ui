<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Article::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|between:10,250',
            'delta' => 'required|JSON',
            'html' => 'required|between:10,65530',
            'meta' => 'required|JSON',
        ]);
        return Auth::user()
            ->articles()
            ->create([
                'title' => $data['title'],
                'html' => $data['html'],
                'delta' => json_decode($data['delta']),
                'meta' => json_decode($data['meta'])
            ]);
            
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show($lang, Article $article)
    {
        return $article;
    }

    public function search(Request $request)
    {
        if (!$request->has('q')) abort(Response::HTTP_BAD_REQUEST, "No query");
        if((int) $request->input('exact', 0 ) ) {
            return Article::whereJsonContains('meta->sources', $request->q)->get();
        }
        return Article::where('meta->sources', 'like', "%{$request->q}%")
            ->get();
    }

    public function sources(Request $request)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        //
    }
}