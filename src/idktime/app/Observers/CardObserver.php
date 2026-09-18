<?php

namespace App\Observers;

use App\Card;

class CardObserver
{
    /**
     * Handle the card "created" event.
     *
     * @param  \App\Card  $card
     * @return void
     */
    public function created(Card $card)
    {
        info('Korisnik ' . auth()->user()->name . ' dodao karticu ' . $card->uuid . '.');
    }

    /**
     * Handle the card "updated" event.
     *
     * @param  \App\Card  $card
     * @return void
     */
    public function updated(Card $card)
    {
        info('Korisnik ' . auth()->user()->name . ' uredio karticu ' . $card->uuid . '.');
    }

    /**
     * Handle the card "deleted" event.
     *
     * @param  \App\Card  $card
     * @return void
     */
    public function deleted(Card $card)
    {
        info('Korisnik ' . auth()->user()->name . ' obrisao karticu ' . $card->uuid . '.');
    }

    /**
     * Handle the card "restored" event.
     *
     * @param  \App\Card  $card
     * @return void
     */
    public function restored(Card $card)
    {
        //
    }

    /**
     * Handle the card "force deleted" event.
     *
     * @param  \App\Card  $card
     * @return void
     */
    public function forceDeleted(Card $card)
    {
        //
    }
}
