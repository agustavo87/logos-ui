<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Source,
    User
};
use App\Http\Resources\SourceCollection;
use App\Http\Resources\SourceResource;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        $perPage = $request->input('perpage',2);
        $sources_paginated = DB::table('sources')->where('user_id', $user->id)->paginate($perPage);
        return new SourceCollection($sources_paginated, $user);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $data = $request->validate([
            'key' => [
                'required', 'max:25',
                Rule::unique('sources')->where('user_id', $user->id)
            ],
            'data' => 'required|max:500',
        ]);

        return $user->sources()->create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Source  $source
     * @return \Illuminate\Http\Response
     */
    public function show(User $user, Source $source)
    {
        return new SourceResource($source);;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @param  \App\Models\Source  $source
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user, Source $source)
    {
        $data = $request->validate([
            'key' => [
                'max:25',
                Rule::unique('sources')
                    ->where('user_id', Auth::user()->id)
                    ->ignore($source),
            ],
            'data' => 'max:500',
        ]);

        $source->update($data);

        return new SourceResource($source);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Source  $source
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user, Source $source)
    {
        return $source->delete();
    }
}
