<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
	public function timelogs()
    {
    	return $this->hasMany(Timelog::class);
    }
}
