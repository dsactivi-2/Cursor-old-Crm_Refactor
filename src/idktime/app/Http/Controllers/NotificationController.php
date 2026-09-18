<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Notification;
use App\User;

use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = User::where('user_role_id', '!=', 1)
                        ->get();

        return view('app.notifications.index', compact('employees'));
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

    public function enableNotification(User $user, Request $request)
    {
        $user = User::find(request('employee_id'));
        $user->notification = 1;
        $user->save();

        session()->flash('success', 'Notifikacije za zaposlenika ' . $user->name . ' uspješno uključena.');
        return redirect()->route('notifications.index');
    }

    public function disableNotification(User $user, Request $request)
    {
        $user = User::find(request('employee_id'));
        $user->notification = 0;
        $user->save();

        session()->flash('success', 'Notifikacije za zaposlenika ' . $user->name . ' uspješno isključena.');
        return redirect()->route('notifications.index');
    }

    public function fetchNotifications()
    {
        $notifications = Notification::with([
                                        'timelog:id,user_id,start',
                                        'status',
                                        'timelog.user:id,name'
                                    ])
                                    ->orderBy('created_at', 'DESC')
                                    ->limit(20)
                                    ->get();

        $notifications_array = array();
        $notifications_unread_count = 0;

        foreach ($notifications as $key => $notification){
            if ($notification->is_seen == 0)
                $notifications_unread_count++;

            $notifications_array[] = array(
                'id' => $notification->id,
                'name' => $notification->timelog->user->name,
                'start' => Carbon::parse($notification->timelog->start)->format('d/m/y, H:i'),
                'status' => $notification->status->name,
                'is_seen' => $notification->is_seen
            );
        }

        return response()->json([
            $notifications_array,
            $notifications_unread_count
        ]);
    }

    public function markNotificationsAsRead()
    {
        $notifications = Notification::where('is_seen', 0)
                                    ->update(['is_seen' => 1]);

        return response()->json();
    }
}
