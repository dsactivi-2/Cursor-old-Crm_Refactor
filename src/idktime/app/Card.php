<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
	public function cardStatus()
    {
    	return $this->belongsTo(CardStatus::class);
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function timelogs()
    {
    	return $this->hasMany(Timelog::class);
    }
}
