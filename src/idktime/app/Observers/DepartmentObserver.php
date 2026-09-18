<?php

namespace App\Observers;

use App\Department;

class DepartmentObserver
{
    /**
     * Handle the department "created" event.
     *
     * @param  \App\Department  $department
     * @return void
     */
    public function created(Department $department)
    {
        info('Korisnik ' . auth()->user()->name . ' dodao odjel ' . $department->name . '.');
    }

    /**
     * Handle the department "updated" event.
     *
     * @param  \App\Department  $department
     * @return void
     */
    public function updated(Department $department)
    {
        info('Korisnik ' . auth()->user()->name . ' uredio odjel ' . $department->name . '.');
    }

    /**
     * Handle the department "deleted" event.
     *
     * @param  \App\Department  $department
     * @return void
     */
    public function deleted(Department $department)
    {
        info('Korisnik ' . auth()->user()->name . ' obrisao odjel ' . $department->name . '.');
    }

    /**
     * Handle the department "restored" event.
     *
     * @param  \App\Department  $department
     * @return void
     */
    public function restored(Department $department)
    {
        //
    }

    /**
     * Handle the department "force deleted" event.
     *
     * @param  \App\Department  $department
     * @return void
     */
    public function forceDeleted(Department $department)
    {
        //
    }
}
