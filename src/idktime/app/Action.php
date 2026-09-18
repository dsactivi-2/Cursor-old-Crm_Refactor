<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
	public function timelogs()
    {
    	return $this->hasMany(Timelog::class);
    }
    
    public function scopeSuggest($query, $suggest)
    {
    	return $query->where('suggest', $suggest);
    }
}
