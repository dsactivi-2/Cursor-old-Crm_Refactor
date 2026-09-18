<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Idk_kandidati extends Model
{
    protected $table = "idk_kandidati";

    protected $primaryKey = 'kandidat_id';

    protected $fillable = [
        'kandidat_id', 'kandidat_ime', 'kandidat_prezime', 'kandidat_drzavljanstvo_vrsta', 'kandidat_status_messenger',
    ];

    public function user(){
        return $this->hasOne('App\User', 'kandidat_id');
    }
}
