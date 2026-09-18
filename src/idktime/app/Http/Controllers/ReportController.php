<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Report;
use App\Timelog;
use App\User;
use PDF;
use DB;

use Carbon\Carbon;


class ReportController extends Controller
{
    private function generateDateRange(Carbon $start_date, Carbon $end_date)
    {
        $dates = [];

        for($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    private function convertMinutesToHoursAndMinutes($time, $format = '%02d:%02d')
    {
        if ($time < 1)
            return;

        $hours = floor($time / 60);
        $minutes = ($time % 60);

        return sprintf($format, $hours, $minutes);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = User::where('user_role_id', '=', 3)
                        ->select('id', 'name')
                        ->get();

        return view('app.reports.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getEmployees()
    {
        $employees = User::where('user_role_id', '!=', 1)->get();

        foreach($employees as $employee){
            $data[] = array(
                'id' => $employee->id,
                'title' => $employee->name
            );
        }

        return response()->json($data);
    }

    public function getReports()
    {
        $timelogs = Timelog::with([
                                'user',
                                'device',
                                'card',
                                'action',
                            ])
                            ->where('action_id', '!=', 4)
                            ->get();

        $data[] = array();
 
        foreach ($timelogs as $timelog){

            if ($timelog->end == null) {
                $end = Carbon::now()->toDateTimeString();
                $departure_status = 0;
            } else {
                $end = $timelog->end;
                $departure_status = 1;
            }

            $data[] = array(
                "id" => $timelog->id,
                "departure_status" => $departure_status,
                "status_name" => $timelog->status->name,
                "status_id" => $timelog->status->id,
                "title" => $timelog->status->name . ' / ' . Carbon::parse($timelog->start)->format('H:i') . ' - ' . Carbon::parse($timelog->end)->format('H:i') ,
                "start" => $timelog->start,
                "end" => $end,
                "resourceId" => $timelog->user_id,
                'backgroundColor' => $timelog->status->color,
                'borderColor' => $timelog->status->color,
            );
        }

        return response()->json($data);
    }

    public function getEmployee($user_id)
    {
        $employee = User::find($user_id);

        $data[] = array(
            'id' => $employee->id,
            'title' => $employee->name
        );

        return response()->json($data);
    }

    public function getReport($user_id)
    {
        $timelogs = Timelog::with([
                                'device',
                                'card',
                                'action',
                            ])
                            ->whereUserId($user_id)
                            ->where('action_id', '!=', 4)
                            ->get();

        $data[] = array();

        foreach ($timelogs as $timelog){

            if ($timelog->end == null) {
                $end = Carbon::now()->toDateTimeString();
                $departure_status = 0;
            } else {
                $end = $timelog->end;
                $departure_status = 1;
            }

            $data[] = array(
                "id" => $timelog->id,
                "departure_status" => $departure_status,
                "status_name" => $timelog->status->name,
                "status_id" => $timelog->status->id,
                "title" => $timelog->status->name . ' / ' . Carbon::parse($timelog->start)->format('H:i') . ' - ' . Carbon::parse($timelog->end)->format('H:i') ,
                "start" => $timelog->start,
                "end" => $end,
                "resourceId" => $timelog->user_id,
                'backgroundColor' => $timelog->status->color,
                'borderColor' => $timelog->status->color,
            );
        }

        return response()->json($data);
    }

    public function reportFilter(Request $request)
    {
        $current_time = Carbon::now();
        $status = request('status');

        $timelogs = Timelog::whereDate('created_at', Carbon::today())
                            ->where([
                                ['start' , '<=', $current_time],
                                ['end', null],
                                ['status_id', '=', $status]
                            ])
                            ->with([
                                'user',
                                'user.position:id,name',
                                'user.department:id,name',
                            ])
                            ->limit(5)
                            ->get();

        return response()->json($timelogs);
    }

    public function getByDate($date)
    {
        return view('app.reports.filter', compact('date'));
    }
    
    public function generateReport(Request $request)
    {
        $from = Carbon::parse(request('from'));
        $to = Carbon::parse(request('to'));

        $dates_range = $this->generateDateRange($from, $to);
        $employees = request('employees');

        $dates = "('" . implode ( "', '", $dates_range ) . "')";
        $users = '(' . implode(',', $employees) .')';

        //Loop through every user selected and generate reports by date
        foreach ($employees as $key => $employee) {
            $user = User::find($employee);
            foreach ($dates_range as $key => $date) {
                
                $day = Carbon::parse($date)->format('d');
                $month = Carbon::parse($date)->format('m');
                $year = Carbon::parse($date)->format('Y');
                
                $first_log = Timelog::where('user_id', '=', $employee)
                                    ->whereDay('created_at', '=', $day)
                                    ->whereMonth('created_at', '=', $month)
                                    ->first();
                                    
                $last_log = Timelog::where('user_id', '=', $employee)
                                    ->whereDay('created_at', '=', $day)
                                    ->whereMonth('created_at', '=', $month)
                                    ->latest('created_at')
                                    ->first();
                                    

                // Add single quotes around date for query usage
                $formated_date = "'" . $date . "'";
                $report = DB::select(DB::raw(
                    "
                        SELECT timelogs.user_id, users.name, timelogs.created_at, timelogs.updated_at,
                            sum(case when status_id = 1 then TIMESTAMPDIFF(MINUTE, start, end) else 0 end) AS 'rad',
                            sum(case when status_id = 2 then TIMESTAMPDIFF(MINUTE, start, end) else 0 end) AS 'pauza',
                            sum(case when status_id = 3 then TIMESTAMPDIFF(MINUTE, start, end) else 0 end) AS 'teren'
                        FROM timelogs
                        LEFT JOIN users ON timelogs.user_id = users.id
                        WHERE timelogs.user_id = $employee
                        AND CAST(start AS DATE) = $formated_date
                        GROUP BY timelogs.user_id, users.name, timelogs.created_at, timelogs.updated_at
                    "
                ));
                
                
                
                // Check if there is a report generated
                // If there is a report for selected day format it
                    if($report) {
                        
                        $work_time = 0;
                        $work_time_in_mins = 0;
                        $break_time = 0;
                        $break_time_in_mins = 0;
                        $field_time = 0;
                        $field_time_in_mins = 0;
                        
                        foreach($report as $report_result){
                          $work_time += $report_result->rad;
                          $work_time_in_mins += $report_result->rad;
                          
                          $break_time += $report_result->pauza;
                          $break_time_in_mins += $report_result->pauza;
                          
                          $field_time += $report_result->teren;
                          $field_time_in_mins += $report_result->teren;
                        }
                        
                        
                        $work_time = $this->convertMinutesToHoursAndMinutes($work_time);
                        $field_time = $this->convertMinutesToHoursAndMinutes($field_time);
                        $break_time = $this->convertMinutesToHoursAndMinutes($break_time);
                        
                        if(is_null($work_time))
                            $wokr_time = '00:00';
                        if(is_null($field_time))
                            $field_time = '00:00';
                        if(is_null($break_time))
                            $break_time = '00:00';
                            
                        

                        $result["{$user->name}"][$date] = array(
                            'type' => 1,
                            'date' => Carbon::parse($date)->format('d.m.Y'),
                            'start' => Carbon::parse($first_log->created_at)->format('H:i'),
                            'end' => Carbon::parse($last_log->updated_at)->format('H:i'),
                            'break' => $break_time,
                            'field' => $field_time,
                            'standby' => '-',
                            'other' => '-',
                            'absence' => '-',
                            'total_daily_work_time' => $work_time,
                            'total_work_time_in_day' => $work_time,
                            'total_work_time_in_day_in_mins' => $work_time_in_mins
                        );
                    }
                    else
                        $result["{$user->name}"][$date] = array(
                            'type' => 2,
                            'date' => Carbon::parse($date)->format('d.m.Y'),
                            'start' => '-',
                            'end' => '-',
                            'break' => '-',
                            'field' => '-',
                            'standby' => '-',
                            'other' => '-',
                            'absence' => '1 | 24',
                            'total_daily_work_time' => '-',
                            'total_work_time_in_day' => '-',
                            'total_work_time_in_day_in_mins' => 0
                        );
            }
        }
        
        
        return view('app.reports.pdf', compact([
            'result',
            'month',
            'year',
        ]));
    }
}