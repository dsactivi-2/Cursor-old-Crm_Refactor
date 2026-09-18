<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kandidat;
use DB;
class HomeController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }
	
    public function index()
    { 		if(!isset($_COOKIE['jobstep_confirmer'])){
			return view('home');
		}else{
			// AKO KANDIDAT IMA COOKIE ZNACI DA JE UNESEN U BAZU I POTVRDIO BROJ PA SE PREUSMJERAVA ILI NA FORMU ILI 4 KORAK
			$kandidat_info = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->get();
			$status = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('status_dak_kandidat');
			// AKO JE STATUS 1 ZNACI DA JE NA 4 KORAK
			if($status == 1){
				// PROVJERI CONTRACT
				$contract = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('contract_dak_kandidat'); 
				if($contract != NULL){
					$return_contract = 1;
				}else{
					$return_contract = 0;
				}
				//PROVJERI PASSPORT
				$passport = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('passport_dak_kandidat');
				if($passport != NULL){
					$return_passport = 1;
				}else{
					$return_passport = 0;
				}
				
				$osiguranje = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('osiguranje_dak_kandidat');
				 
				return redirect('thanksmessage')->with(['return_contract'=>$return_contract, 'return_passport'=>$return_passport, 'osiguranje'=>$osiguranje]);
				
			}else{
				return view('/form', ['kandidat_info'=>$kandidat_info, 'hashed_codetel'=>$_COOKIE['jobstep_confirmer']]);
			}
		}
    }
}
 