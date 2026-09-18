<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Position;
use App\User;

use Session;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $positions = Position::withCount('users')
                                ->get();
        return view('app.positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app.positions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string'
        ]);

        $position = New Position;
        $position->name = request('name');
        $position->slug = str_slug(request('name'), '-');
        $position->save();

        session()->flash('success', 'Radna pozicija ' . $position->name . ' uspješno dodana.');
        return redirect()->route('positions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Position $position)
    {  
        return view('app.positions.show', compact('position'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Position $position)
    {
        return view('app.positions.edit', compact('position'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Position $position)
    {
        $this->validate($request, [
            'name' => 'required|string'
        ]);

        $position->name = request('name');
        $position->slug = str_slug(request('name'), '-');
        $position->save();

        session()->flash('success', 'Radna pozicija ' . $position->name . ' uspješno uređena.');
        return redirect()->route('positions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Position $position)
    {
        $position->delete();

        session()->flash('success', 'Radna pozicija ' . $position->name . ' uspješno obrisana');
        return redirect()->route('positions.index');
    }

    /**
     * Detach user from position 
     *
    */
    public function detachUser(Request $request, Position $position, User $user)
    {
        $user->position()->dissociate();
        $user->save();

        session()->flash('success', 'Zaposlenik ' . $user->name . ' uspješno izbrisan sa pozicije ' . $position->name);
        return back();
    }
}
