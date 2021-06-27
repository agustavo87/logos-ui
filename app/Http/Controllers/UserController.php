<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     * Solo accesible por el admin
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $lang)
    {
        $this->authorize('viewAny', User::class);
        return view('users.index', [
            'users' => User::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create', [
            'locale' => config('locale')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $lang)
    {
        // $data = $request->only('email', 'name', 'country', 'locale');
        $this->authorize('create', User::class);
        $data = $request->validate([
            'email' => 'required|email|unique:users',
            'name' => 'required|min:2|max:255',
            'country' => 'required|size:2',
            ]);

        $data['password'] = Hash::make($request->password);


        $user = User::create($data);
        Auth::login($user, true);

        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($lang, User $user)
    {
        // Gate::authorize('view-user', $user);

        return view('users.show', [
            'user' => $user,
            'country' => config('locale.countries')[$user->country],
            'language' => config('locale.languages.names')[$user->language]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($lang, User $user)
    {
        // Gate::authorize('update-user', $user);

        return view('users.edit', [
            'user' => $user,
            'locale' => config('locale')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $lang, User $user)
    {
        // if (! Gate::allows('update-user', $user)) {
        //     abort(403);
        // }
        // Gate::authorize('update-user', $user);

        $this->authorize('update', $user);
        $data = $request->validate([
            'name' =>  [ 'min:2', 'max:255'],
            'country' => ['size:2'],
            ]);

        $user->update($data);

        return redirect(route('home'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($lang, User $user)
    {
        $this->authorize('delete', $user);
    }
}
