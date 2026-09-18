<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use App\Setting;

use Session;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission.check:podesavanja');
    }
	 /**
     * Display website settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$settings = Setting::first();
    	return view('app.settings.index', compact('settings'));
    }

    /**
     * Show the form for edditing the website settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
    	$settings = Setting::first();
    	return view('app.settings.edit', compact('settings'));
    } 

    /**
     * Update the website settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    	$settings = Setting::first();

        if($request->hasFile('image')){
            if (!is_null($settings->image))
                Storage::delete('public/settings/' . $settings->image);
            
            $cover_image = $request->file('image');
            $file_name_with_ext = $cover_image->getClientOriginalName();
            $file_name = pathinfo($file_name_with_ext , PATHINFO_FILENAME);
            $file_ext = $cover_image->getClientOriginalExtension();
            $file_name_to_save = uniqid() . '.' . $file_ext;
            $path = $cover_image->storeAs('public/settings' , $file_name_to_save);
        }else{
            $file_name_to_save = $settings->image;
        }

    	$settings->email = request('email');
        $settings->image = $file_name_to_save;
    	$settings->country = request('country');
    	$settings->city = request('city');
    	$settings->postcode = request('postcode');
    	$settings->address = request('address');
    	$settings->fax = request('fax');
    	$settings->primary_phone = request('primary_phone');
    	$settings->secondary_phone = request('secondary_phone');
    	$settings->primary_mobile = request('primary_mobile');
    	$settings->secondary_mobile = request('secondary_mobile');
    	$settings->facebook = request('facebook');
    	$settings->youtube = request('youtube');
    	$settings->twitter = request('twitter');
    	$settings->save();

    	session()->flash('success', 'Podešavanja uspješno uređena.');
    	return redirect()->route('settings.index');
    }
}
