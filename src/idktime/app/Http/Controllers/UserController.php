<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use App\User;
use App\UserRole;
use App\UserStatus;
use App\Permission;
use App\Position;
use App\Department;
use App\Timelog;

use Hash;
use Session;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('userRole')
                    ->get();

        return view('app.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_roles = UserRole::all();
        $permissions = Permission::all();
        $positions = Position::all();
        $departments = Department::all();

        return view('app.users.create', compact([
            'user_roles',
            'permissions',
            'positions',
            'departments',
        ]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,
            [
                'name' => 'required|string|min:4',
                'email' => 'nullable|string|email|max:255|unique:users',
                'password' => 'nullable|string|min:6|confirmed',
                'user_role_id' => 'required|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
                'phone' => 'nullable|string|min:3',
                'country' => 'nullable|string|min:3',
                'city' => 'nullable|string|min:3',
                'address' => 'nullable|string|min:3',
                'jmbg' => 'nullable|string|min:3',
           ]
        );

        if($request->hasFile('image')){
            $cover_image = $request->file('image');
            $file_name_with_ext = $cover_image->getClientOriginalName();
            $file_name = pathinfo($file_name_with_ext , PATHINFO_FILENAME);
            $file_ext = $cover_image->getClientOriginalExtension();
            $file_name_to_save = str_slug(request('name'), '-') . '_' . uniqid() . '.' . $file_ext;
            $path = $cover_image->storeAs('public/users' , $file_name_to_save);
        }else{
            $file_name_to_save = null;
        }

        $user = new User;
        $user->user_role_id = request('user_role_id');
        $user->user_status_id = 1;
        $user->position_id = request('position_id');
        $user->department_id = request('department_id');
        $user->name = request('name');
        $user->email = request('email');
        if (request('user_role_id') == 1 || request('user_role_id') == 2)
            $user->password = Hash::make(request('password'));
        $user->image = $file_name_to_save;
        if (!is_null(request('birthday')))
            $user->birthday = Carbon::parse(request('birthday'))->format('Y-m-d');
        $user->phone = request('phone');
        $user->country = request('country');
        $user->city = request('city');
        $user->address = request('address');
        $user->jmbg = request('jmbg');
        $user->save();

        if (request('user_role_id') == 1 || request('user_role_id') == 2)
            $user->permissions()->attach(request('permissions'));

        session()->flash('success', 'Zaposlenik ' . $user->name . ' uspješno dodan.');
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $timelogs = Timelog::whereUserId($user->id)
                        ->with([
                            'user',
                            'action',
                            'card',
                            'device'
                        ])
                        ->get();
        return view('app.users.show', compact([
            'user',
            'timelogs'
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if ($user->id == auth()->user()->id)
            return redirect()->route('my-profile');

        $user_roles = UserRole::all();
        $user_statuses = UserStatus::all();
        $permissions = Permission::all();
        $positions = Position::all();
        $departments = Department::all();

        $user_related_permissions = $user->permissions->map(function ($item, $key) {
            return $item->id;
        })->toArray();

        return view('app.users.edit', compact([
            'user',
            'user_roles',
            'user_statuses',
            'permissions',
            'positions',
            'departments',
            'user_related_permissions'
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request,
            [
                'name' => 'required|string|min:4',
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'password' => 'nullable|string|min:6|confirmed',
                'user_role_id' => 'required|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
                'phone' => 'nullable|string|min:3',
                'country' => 'nullable|string|min:3',
                'city' => 'nullable|string|min:3',
                'address' => 'nullable|string|min:3',
                'jmbg' => 'nullable|string|min:3',
           ]
        );

        if($request->hasFile('image')){
            if (!is_null($user->image))
                Storage::delete('public/users/' . $user->image);

            $cover_image = $request->file('image');
            $file_name_with_ext = $cover_image->getClientOriginalName();
            $file_name = pathinfo($file_name_with_ext , PATHINFO_FILENAME);
            $file_ext = $cover_image->getClientOriginalExtension();
            $file_name_to_save = str_slug(request('name'), '-') . '_' . uniqid() . '.' . $file_ext;
            $path = $cover_image->storeAs('public/users' , $file_name_to_save);
        }else{
            $file_name_to_save = $user->image;
        }

        $user->user_role_id = request('user_role_id');
        $user->user_status_id = request('user_status_id');
        $user->position_id = request('position_id');
        $user->department_id = request('department_id');
        $user->name = request('name');
        $user->email = request('email');
        if (request('user_role_id') == 1 || request('user_role_id') == 2)
            if (!is_null(request('password')))
                $user->password = Hash::make(request('password'));
            else
                $user->password = $user->password;
        else
            $user->password = null;
        $user->image = $file_name_to_save;
        if (!is_null(request('birthday')))
            $user->birthday = Carbon::parse(request('birthday'))->format('Y-m-d');
        $user->phone = request('phone');
        $user->country = request('country');
        $user->city = request('city');
        $user->address = request('address');
        $user->jmbg = request('jmbg');
        $user->save();

        if (request('user_role_id') == 1 || request('user_role_id') == 2)
            if ($user->hasRelatedPermissions())
                $user->permissions()->sync(request('permissions'));
            else
                $user->permissions()->attach(request('permissions'));
        else
            $user->permissions()->detach();

        session()->flash('success', 'Zaposlenik ' . $user->name . ' uspješno uređen.');
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->id == auth()->user()->id) {
            session()->flash('error', 'Nije vam dopušteno obrisati vlastiti profil.');
            return redirect()->route('users.index');
        }

        if (!is_null($user->image))
            Storage::delete('public/users/' . $user->image);

        $user->delete();

        session()->flash('success', 'Zaposlenik ' . $user->name . ' uspješno obrisan.');
        return redirect()->route('users.index');
    }
}
