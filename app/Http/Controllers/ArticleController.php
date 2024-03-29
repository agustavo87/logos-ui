<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Display a listing of the articles that belongs to the
     * specified user
     *
     * @return \Illuminate\Http\Response
     */
    public function indexBy($lang, User $user)
    {
        return view('articles.index_by', [
            'articles' => $user->articles,
            'user' => $user
        ]);
    }

    /**
     * Display a listing of the articles that belongs to the
     * logged user
     *
     * @return \Illuminate\Http\Response
     */
    public function mine($lang)
    {
        return view('articles.mine');
    }

    /**
     * Show the UI for creating a new Article.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('articles.edit', [
            'title' => "Crear Artículo",
        ]);
    }

    /**
     * Show the UI for editing the existing article
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit($lang)
    {
        return view('articles.edit', [
            'title' => "Editar artículo"
        ]);
    }

    /**
     * Store a newly created article
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
     * Display the article.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show($lang, $articleID)
    {
        return view('articles.show', [
            'article' => Article::find($articleID)
        ]);
    }

    // public function search(Request $request)
    // {
    //     if (!$request->has('q')) abort(Response::HTTP_BAD_REQUEST, "No query");
    //     if((int) $request->input('exact', 0 ) ) {
    //         return Article::whereJsonContains('meta->sources', $request->q)->get();
    //     }
    //     return Article::where('meta->sources', 'like', "%{$request->q}%")
    //         ->get();
    // }

    public function sources(Request $request)
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
