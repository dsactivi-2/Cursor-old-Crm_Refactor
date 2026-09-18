<?php

namespace App\Observers;

use App\Position;

class PositionObserver
{
    /**
     * Handle the position "created" event.
     *
     * @param  \App\Position  $position
     * @return void
     */
    public function created(Position $position)
    {
        info('Korisnik ' . auth()->user()->name . ' dodao radnu poziciju ' . $position->name . '.');
    }

    /**
     * Handle the position "updated" event.
     *
     * @param  \App\Position  $position
     * @return void
     */
    public function updated(Position $position)
    {
        info('Korisnik ' . auth()->user()->name . ' uredio radnu poziciju ' . $position->name . '.');
    }

    /**
     * Handle the position "deleted" event.
     *
     * @param  \App\Position  $position
     * @return void
     */
    public function deleted(Position $position)
    {
        info('Korisnik ' . auth()->user()->name . ' obrisao radnu poziciju ' . $position->name . '.');
    }

    /**
     * Handle the position "restored" event.
     *
     * @param  \App\Position  $position
     * @return void
     */
    public function restored(Position $position)
    {
        //
    }

    /**
     * Handle the position "force deleted" event.
     *
     * @param  \App\Position  $position
     * @return void
     */
    public function forceDeleted(Position $position)
    {
        //
    }
}
