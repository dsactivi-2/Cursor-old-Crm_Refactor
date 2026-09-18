<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timelog extends Model
{
    public function card()
    {
    	return $this->belongsTo(Card::class);
    }

    public function action()
    {
    	return $this->belongsTo(Action::class);
    }

    public function device()
    {
    	return $this->belongsTo(Device::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
