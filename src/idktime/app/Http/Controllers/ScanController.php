<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Card;
use App\Action;
use App\Timelog;

use Session;
use Carbon\Carbon;

class ScanController extends Controller
{
    public function index()
    {
    	return view('public.scan');
    }

    public function scan(Request $request)
    {
    	$this->validate($request, [
    		'rfid' => 'required',
    	]);

    	$card = Card::whereUuid(request('rfid'))
    					->first();

        if ($card) {
            if ($card->card_status_id == 2) {
                session()->flash('error', 'Vaša RFID kartica je poništena.');
                return redirect()->route('scan.index');
            } else {
                return redirect()->route('scan.welcome', $card);
            }
        } else {
            session()->flash('error', 'RFID kartica nije pronađena u brazi. Molimo pokušajte ponovo.');
            return redirect()->route('scan.index');
        }
    }

    public function welcome($card)
    {
    	$actions = Action::suggest(1)
    					->get();
    	$card = Card::find($card);

        $latest_timelog = Timelog::whereDate('created_at', Carbon::today())
                                ->whereCardId($card->id)
                                ->orderBy('id', 'desc')
                                ->first();

        if (is_null($latest_timelog)){
            $timelog_action = true;
        } else {
            switch ($latest_timelog->action_id){
                case '1':
                    $timelog_action = false;
                    break;
                case '2':
                    $timelog_action = true;
                    break;
                case '3':
                    $timelog_action = true;
                    break;
                case '4':
                    $timelog_action = true;
                    break;
                case '5':
                    $timelog_action = false;
                    break;
                
                default:
                    $timelog_action = false;
                    break;
            }
        }

    	return view('public.welcome', compact([
    		'card',
    		'actions',
            'latest_timelog',
            'timelog_action'
    	]));
    }

    public function action(Request $request)
    {
    	$latest_timelog = Timelog::whereDate('created_at', Carbon::today())
                                ->whereCardId(request('card_id'))
    							->orderBy('id', 'desc')
    							->first();

        $card = Card::find(request('card_id'));

        switch (request('action_id')) {
            case '1':
                $status = 1;
                break;
            case '2':
                $status = 2;
                break;
            case '3':
                $status = 3;
                break;
            case '4':
                $status = 4;
                break;
            case '5':
                $status = 1;
                break;
            
            default:
                # code...
                break;
        }

        if (is_null($latest_timelog)) {
            $timelog = new Timelog;
            $timelog->card_id = request('card_id');
            $timelog->action_id = 1;
            $timelog->device_id = 1;
            $timelog->user_id = $card->user_id;
            $timelog->status_id = $status;
            $timelog->start = Carbon::now();
            $timelog->end = null;
            $timelog->save();
        } else {
            if ($latest_timelog->action_id == 1 || $latest_timelog->action_id == 5) {
                $latest_timelog->end = Carbon::now();
                $latest_timelog->updated_at = Carbon::now();
                $latest_timelog->save();

                $timelog = new Timelog;
                $timelog->card_id = request('card_id');
                $timelog->action_id = request('action_id');
                $timelog->device_id = 1;
                $timelog->status_id = $status;
                $timelog->user_id = $card->user_id;
                $timelog->start = Carbon::now();
                $timelog->end = null;
                $timelog->save();
            } elseif ($latest_timelog->action_id == 2 || $latest_timelog->action_id == 3) {
                $latest_timelog->end = Carbon::now();
                $latest_timelog->updated_at = Carbon::now();
                $latest_timelog->save();

                $timelog = new Timelog;
                $timelog->card_id = request('card_id');
                $timelog->action_id = 5;
                $timelog->device_id = 1;
                $timelog->status_id = 1;
                $timelog->user_id = $card->user_id;
                $timelog->start = Carbon::now();
                $timelog->end = null;
                $timelog->save();
            } elseif ($latest_timelog->action_id == 4) {
                $timelog = new Timelog;
                $timelog->card_id = request('card_id');
                $timelog->action_id = 1;
                $timelog->device_id = 1;
                $timelog->status_id = 1;
                $timelog->user_id = $card->user_id;
                $timelog->start = Carbon::now();
                $timelog->end = null;
                $timelog->save();
            }
        }

        return redirect()->route('scan.index');
    }
}
