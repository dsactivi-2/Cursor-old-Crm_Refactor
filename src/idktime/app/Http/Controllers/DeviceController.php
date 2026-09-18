<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Device;

use Session;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $devices = Device::all();

        return view('app.devices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app.devices.create');
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
            'name' => 'required|string',
            'link' => 'nullable|string'
        ]);

        $device = new Device;
        $device->name = request('name');
        $device->slug = str_slug(request('name'), '-');
        $device->link = request('link');
        $device->save();

        session()->flash('success', 'Novi uređaj ' . $device->name . ' uspješno dodan.');
        return redirect()->route('devices.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Device $device)
    {
        return view('app.devices.show', compact('device'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Device $device)
    {
        return view('app.devices.edit', compact('device'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Device $device)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'link' => 'nullable|string'
        ]);

        $device->name = request('name');
        $device->slug = str_slug(request('name'), '-');
        $device->link = request('link');
        $device->save();

        session()->flash('success', 'Uređaj ' . $device->name . ' uspješno uređen.');
        return redirect()->route('devices.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Device $device)
    {
        $device->delete();

        session()->flash('success', 'Uređaj ' . $device->name . ' uspješno obrisan.');
        return redirect()->route('devices.index');
    }
}
