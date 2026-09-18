<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\User;
use App\Card;
use App\CardStatus;

use Session;

class CardController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission.check:rfid-kartice');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cards = Card::with('user')
                    ->get();
        return view('app.cards.index', compact('cards'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::withCount('cards')
                    ->get();
        return view('app.cards.create', compact('users'));
    } 

    /**
     * Show the form for creating a new resource to specific user.
     *
     * @return \Illuminate\Http\Response
     */
    public function createToUser($user)
    {
        $user = User::find($user);
        return view('app.cards.create-to-user', compact('user'));
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
            'uuid' => 'required|string|unique:cards',
        ]);

        $user = User::find(request('user_id'));

        $card = new Card;
        $card->uuid = request('uuid');
        $card->user_id = request('user_id');
        $card->card_status_id = 1;
        $card->save();

        session()->flash('success', 'RFID kartica ' . $card->uuid . ' za zaposlenika ' . $user->name . ' uspješno dodana.');
        return redirect()->route('cards.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeToUser(Request $request, User $user)
    {
        $this->validate($request, [
            'uuid' => 'required|string|unique:cards',
        ]);

        $user = User::find(request('user_id'));

        $card = new Card;
        $card->uuid = request('uuid');
        $card->user_id = request('user_id');
        $card->card_status_id = 1;
        $card->save();

        session()->flash('success', 'RFID kartica ' . $card->uuid . ' za zaposlenika ' . $user->name . ' uspješno dodana.');
        return redirect()->route('cards.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Card $card)
    {
        return view('app.cards.show', compact('card'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Card $card)
    {
        $users = User::withCount('cards')
                    ->get();
        $card_statuses = CardStatus::all();

        return view('app.cards.edit', compact([
            'users',
            'card',
            'card_statuses'
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Card $card)
    {
        $this->validate($request, [
            'uuid' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('cards')->ignore($card->id),
                ],
        ]);

        $user = User::find(request('user_id'));

        $card->uuid = request('uuid');
        $card->user_id = request('user_id');
        $card->card_status_id = request('card_status_id');
        $card->save();

        session()->flash('success', 'RFID kartica ' . $card->uuid . ' za zaposlenika ' . $user->name . ' uspješno uređena.');
        return redirect()->route('cards.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Card $card)
    {
        //
    }

    /**
     * Activate specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activate(Card $card)
    {
        $card->card_status_id = 1;
        $card->save();
        
        session()->flash('success', 'RFID kartica ' . $card->uuid . ' za zaposlenika ' . $card->user->name . ' uspješno aktivirana.');
        return redirect()->route('cards.index');
    }

    /**
     * Cancel specific resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Card $card)
    {
        $card->card_status_id = 2;
        $card->save();
        
        session()->flash('success', 'RFID kartica ' . $card->uuid . ' za zaposlenika ' . $card->user->name . ' uspješno otkazana.');
        return redirect()->route('cards.index');
    }
}
