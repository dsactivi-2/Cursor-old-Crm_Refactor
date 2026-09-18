<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Timelog;

use Session;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $current_time = Carbon::now();

        $timelogs = Timelog::whereDate('created_at', Carbon::today())
                            ->where([
                                ['start' , '<=', $current_time],
                                ['end', null],
                                ['status_id', '=', 1]
                            ])
                            ->with([
                                'user:id,name,image,position_id,department_id',
                                'user.position:id,name',
                                'user.department:id,name',
                            ])
                            ->limit(5)
                            ->get();

        $working =  Timelog::whereDate('created_at', Carbon::today())
                            ->where([
                                ['start' , '<=', $current_time],
                                ['end', null],
                                ['status_id', '=', 1]
                            ])
                            ->count();

        $pause =  Timelog::whereDate('created_at', Carbon::today())
                            ->where([
                                ['start' , '<=', $current_time],
                                ['end', null],
                                ['status_id', '=', 2]
                            ])
                            ->count(); 

        $on_the_field =  Timelog::whereDate('created_at', Carbon::today())
                            ->where([
                                ['start' , '<=', $current_time],
                                ['end', null],
                                ['status_id', '=', 3]
                            ])
                            ->count();

        $finished =  Timelog::whereDate('created_at', Carbon::today())
                            ->where([
                                ['start' , '<=', $current_time],
                                ['end', null],
                                ['status_id', '=', 4]
                            ])
                            ->count();

        return view('app.dashboard', compact([
            'timelogs',
            'working',
            'pause',
            'on_the_field',
            'finished'
        ]));
    }
}
