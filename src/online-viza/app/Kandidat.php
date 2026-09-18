<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Kandidat extends Model
{
    protected $table = 'idk_dak_kandidati';
    protected $primaryKey = 'id_dak_kandidat';


    protected $fillable = [
        'tel_dak_kandidat',
        'telconfirm_dak_kandidat',
        'hashedtel_idk_dak_kandidat',
        'pin_dak_kandidat '
        ];
}
