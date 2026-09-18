<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Department;
use App\User;

use Session;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::withCount('users')
                                ->get();
        return view('app.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app.departments.create');
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

        $department = new Department;
        $department->name = request('name');
        $department->slug = str_slug(request('name'), '-');
        $department->save();

        session()->flash('success', 'Odjel ' . $department->name . ' uspješno dodana.');
        return redirect()->route('departments.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {  
        return view('app.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        return view('app.departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        $this->validate($request, [
            'name' => 'required|string'
        ]);

        $department->name = request('name');
        $department->slug = str_slug(request('name'), '-');
        $department->save();

        session()->flash('success', 'Odjel ' . $department->name . ' uspješno uređena.');
        return redirect()->route('departments.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        $department->delete();

        session()->flash('success', 'Odjel ' . $department->name . ' uspješno obrisana');
        return redirect()->route('departments.index');
    }

    /**
     * Detach user from department 
     *
    */
    public function detachUser(Request $request, Department $department, User $user)
    {
        $user->department()->dissociate();
        $user->save();

        session()->flash('success', 'Zaposlenik ' . $user->name . ' uspješno izbrisan iz odjela ' . $department->name);
        return back();
    }
}
