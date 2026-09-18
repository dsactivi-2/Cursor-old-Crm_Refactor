<?php

namespace App\Observers;

use App\Device;

class DeviceObserver
{
    /**
     * Handle the device "created" event.
     *
     * @param  \App\Device  $device
     * @return void
     */
    public function created(Device $device)
    {
        info('Korisnik ' . auth()->user()->name . ' dodao uređaj ' . $device->name . '.');
    }

    /**
     * Handle the device "updated" event.
     *
     * @param  \App\Device  $device
     * @return void
     */
    public function updated(Device $device)
    {
        info('Korisnik ' . auth()->user()->name . ' uredio uređaj ' . $device->name . '.');
    }

    /**
     * Handle the device "deleted" event.
     *
     * @param  \App\Device  $device
     * @return void
     */
    public function deleted(Device $device)
    {
        info('Korisnik ' . auth()->user()->name . ' izbrisao uređaj ' . $device->name . '.');
    }

    /**
     * Handle the device "restored" event.
     *
     * @param  \App\Device  $device
     * @return void
     */
    public function restored(Device $device)
    {
        //
    }

    /**
     * Handle the device "force deleted" event.
     *
     * @param  \App\Device  $device
     * @return void
     */
    public function forceDeleted(Device $device)
    {
        //
    }
}
