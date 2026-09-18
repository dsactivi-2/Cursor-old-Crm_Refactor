<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use App\User;
use App\Position;
use App\Department;

use Session;
use Hash;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function index()
    {
    	$user = User::whereId(auth()->user()->id)
    				->first();

    	return view('app.profile.index', compact('user'));
    }

    public function update(Request $request, User $user)
    {
    	$validate = $request->validate(
            [
                'name' => 'required|string|min:4',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'password' => 'nullable|string|min:6|confirmed',
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

        $user->name = request('name');
        $user->email = request('email');
        if (is_null(request('password')))
            $user->password = $user->password;
        else
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

        session()->flash('success', 'Uspješno ste uredili svoj profil.');
        return back();
    }
}
