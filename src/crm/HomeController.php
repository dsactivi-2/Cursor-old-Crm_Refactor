<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Idk_kandidati;
use DB;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $kandidat_id = auth()->user()->kandidat_id;
        $nalog_id = auth()->user()->nalog_id;
        $kandidat = Idk_kandidati::find($kandidat_id);
        $kandidatIskustva = $this->getKandidatIskustvo($kandidat_id);
        $nalogBlokovi = $this->getNalogBlokovi($nalog_id);
        $kandidatJezik = $this->getKandidatJezik($kandidat_id);
        $kandidatSrednje = $this->getKandidatSrednjeObrazovanje($kandidat_id);
        $kandidatVisoko = $this->getKandidatVisokoObrazovanje($kandidat_id);
        $kandidatDodatno = $this->getKandidatDodatnoObrazovanje($kandidat_id);
        $srednje_skole = $this->getSrednjeSkole();
        $visoke_skole = $this->getVisokeSkole();
        $smjerovi_srednje = $this->getSmjeroveSrednje();
        $smjerovi_faks = $this->getSmjeroveFaks();
        $docsSlika = $this->getDocsSlika($kandidat_id);
        $docsDiploma = $this->getDocsDiploma($kandidat_id);
        $docsPripravnicki = $this->getDocsPripravnicki($kandidat_id);
        $docsStrucni= $this->getDocsStrucni($kandidat_id);
        $docsCertJezik = $this->getDocsCertJezik($kandidat_id);
        $godinaRodjenja = $this->getGodinaRodjenja($kandidat_id);
		$kandidatNeValPodaci = $this->getValidacije($kandidat_id);
		$val_blok_viza = $this->getValidacijeBlok($kandidat_id, "blok_viza");
		$val_blok_termin = $this->getValidacijeBlok($kandidat_id, "blok_termin");
		$val_blok_apl = $this->getValidacijeBlok($kandidat_id, "blok_apl");
		$val_blok_obr = $this->getValidacijeBlok($kandidat_id, "obrazovanje");
		$val_blok_isk = $this->getValidacijeBlok($kandidat_id, "iskustvo");
		$val_blok_doc = $this->getValidacijeBlok($kandidat_id, "dokument");
		$val_blok_ime = $this->getValidacijeBlok($kandidat_id, "blok_ime");
		$val_blok_pre = $this->getValidacijeBlok($kandidat_id, "blok_pre");
		$val_blok_dtR = $this->getValidacijeBlok($kandidat_id, "blok_dtR");
		$val_blok_mjR = $this->getValidacijeBlok($kandidat_id, "blok_mjR");
		$val_blok_drR = $this->getValidacijeBlok($kandidat_id, "blok_drR");
		$val_blok_adr = $this->getValidacijeBlok($kandidat_id, "blok_adr");
		$val_blok_gra = $this->getValidacijeBlok($kandidat_id, "blok_gra");
		$val_blok_pbr = $this->getValidacijeBlok($kandidat_id, "blok_pbr");
		$val_blok_drz = $this->getValidacijeBlok($kandidat_id, "blok_drz");

        // dd($val_blok_dtR);
        // dd($srednje_skole[0]->skola_naziv);
        //dd($nalogBlokovi->nbp_vozacka_kategorija);
        
        return view('home', [
            'kandidat' => $kandidat,
            'nalogBlokovi' => $nalogBlokovi,
            'kandidatIskustva' => $kandidatIskustva,
            'kandidatJezik' => $kandidatJezik,
            'kandidatSrednje' => $kandidatSrednje,
            'kandidatVisoko' => $kandidatVisoko,
            'kandidatDodatno' => $kandidatDodatno,
            'srednje_skole' => $srednje_skole,
            'visoke_skole' => $visoke_skole,
            'smjerovi_srednje' => $smjerovi_srednje,
            'smjerovi_faks' => $smjerovi_faks,
            'docsSlika' => $docsSlika,
            'docsDiploma' => $docsDiploma,
            'docsPripravnicki' => $docsPripravnicki,
            'docsStrucni' => $docsStrucni,
            'docsCertJezik' => $docsCertJezik,
			'godinaRodjenja' => $godinaRodjenja,
			'pogresniPodaci' => $kandidatNeValPodaci,
			'val_blok_viza' => $val_blok_viza,
			'val_blok_termin' => $val_blok_termin,
			'val_blok_apl' => $val_blok_apl,
			'val_blok_obr' => $val_blok_obr,
			'val_blok_isk' => $val_blok_isk,
			'val_blok_doc' => $val_blok_doc,
			'val_blok_ime' => $val_blok_ime,
			'val_blok_pre' => $val_blok_pre,
			'val_blok_dtR' => $val_blok_dtR,
			'val_blok_mjR' => $val_blok_mjR,
			'val_blok_drR' => $val_blok_drR,
			'val_blok_adr' => $val_blok_adr,
			'val_blok_gra' => $val_blok_gra,
			'val_blok_pbr' => $val_blok_pbr,
			'val_blok_drz' => $val_blok_drz

        ]);
        //return view('home')->with($data_kandidat);
    }

    public function getNalogBlokovi($nalog_id){
        $nalogBlokovi = DB::table('idk_nalozi_blokovi_prijave')
        ->select('*')
        ->where('nbp_nalogid', '=', $nalog_id)
        ->first();

        return $nalogBlokovi;
    }

    public function getKandidatIskustvo($kandidat_id){
        $kandidatIskustva = DB::table('idk_kandidat_radno_iskustvo')
        ->select('kri_pozicija','kri_naziv', 'kri_opis')
        ->where('kri_kandidat_id', '=', $kandidat_id)
        ->get();

        return $kandidatIskustva;
    }

    public function getKandidatJezik($kandidat_id){
        $kandidatJezik = DB::table('idk_kandidat_jezici')
        ->select('kj_naziv', 'kj_slusanje')
        ->where([
            ['kj_kandidatid', '=', $kandidat_id],
            ['kj_naziv', '=', 'Njemački'],
        ])
        ->first();

        return $kandidatJezik;
    }
	public function getKandidatSrednjeObrazovanje($kandidat_id){
        $kandidatSrednje = DB::table('idk_kandidat_edukacija')
        ->select('ke_id')
        ->where([
            ['ke_kandidat_id', '=', $kandidat_id],
            ['ke_vrsta_obrazovanja', '=', 'srednje'],
        ])
        ->first();

        return $kandidatSrednje;
    }
	
	public function getKandidatVisokoObrazovanje($kandidat_id){
        $kandidatVisoko = DB::table('idk_kandidat_edukacija')
        ->select('ke_id')
        ->where([
            ['ke_kandidat_id', '=', $kandidat_id],
            ['ke_vrsta_obrazovanja', '=', 'visoko'],
        ])
        ->first();

        return $kandidatVisoko;
    }
	
	public function getKandidatDodatnoObrazovanje($kandidat_id){
        $kandidatDodatno = DB::table('idk_kandidat_edukacija')
        ->select('ke_id')
        ->where([
            ['ke_kandidat_id', '=', $kandidat_id],
            ['ke_vrsta_obrazovanja', '=', 'ostalo'],
        ])
        ->first();

        return $kandidatDodatno;
    }

    public function getSrednjeSkole(){
        $srednje_skole = DB::table('idk_skole')
        ->select('skola_id', 'skola_naziv', 'skola_tip_obrazovanja')
        ->where('skola_tip_obrazovanja', '=', 'srednje')
        ->get();

        return $srednje_skole;
    }

    public function getVisokeSkole(){
        $visoke_skole = DB::table('idk_skole')
        ->select('skola_id', 'skola_naziv', 'skola_tip_obrazovanja')
        ->where('skola_tip_obrazovanja', '=', 'visoko')
        ->get();

        return $visoke_skole;
    }

    public function getSmjeroveSrednje(){
        $smjerovi_srednje = DB::table('idk_skole_smjerovi')
        ->join('idk_skole', 'ss_skola_id', '=', 'skola_id')
        ->select('idk_skole_smjerovi.ss_id', 'idk_skole_smjerovi.ss_naziv', 'idk_skole_smjerovi.ss_skola_id', 'idk_skole.skola_tip_obrazovanja')
        ->where('idk_skole.skola_tip_obrazovanja', '=', 'srednje')
        ->get();

        return $smjerovi_srednje;
    }
    public function getSmjeroveFaks(){
        $smjerovi_faks = DB::table('idk_skole_smjerovi')
        ->join('idk_skole', 'ss_skola_id', '=', 'skola_id')
        ->select('idk_skole_smjerovi.ss_id', 'idk_skole_smjerovi.ss_naziv', 'idk_skole_smjerovi.ss_skola_id', 'idk_skole.skola_tip_obrazovanja')
        ->where('idk_skole.skola_tip_obrazovanja', '=', 'visoko')
        ->get();

        return $smjerovi_faks;
    }
	public function getDocsSlika($kandidat_id){
        $docsSlika = DB::table('idk_documents')
        ->select('document_id')
        ->where([
            ['document_dataid', '=', $kandidat_id],
            ['document_name', '=', 'Slika'],
        ])
        ->first();

        return $docsSlika;
    }
	public function getDocsDiploma($kandidat_id){
        $docsDiploma = DB::table('idk_documents')
        ->select('document_id')
        ->where([
            ['document_dataid', '=', $kandidat_id],
            ['document_name', '=', 'Diploma završene škole'],
        ])
        ->first();

        return $docsDiploma;
    }
	public function getDocsPripravnicki($kandidat_id){
        $docsPripravnicki = DB::table('idk_documents')
        ->select('document_id')
        ->where([
            ['document_dataid', '=', $kandidat_id],
            ['document_name', '=', 'Uvjerenje o pripravničkom stažu'],
        ])
        ->first();

        return $docsPripravnicki;
    }
	public function getDocsStrucni($kandidat_id){
        $docsStrucni = DB::table('idk_documents')
        ->select('document_id')
        ->where([
            ['document_dataid', '=', $kandidat_id],
            ['document_name', '=', 'Uvjerenje o položenom stručnom ispitu'],
        ])
        ->first();

        return $docsStrucni;
    }
	public function getDocsCertJezik($kandidat_id){
        $docsCertJezik = DB::table('idk_documents')
        ->select('document_id')
        ->where([
            ['document_dataid', '=', $kandidat_id],
            ['document_name', '=', 'Certifikati o poznavanju jezika'],
        ])
        ->first();

        return $docsCertJezik;
    }
	
	public function getGodinaRodjenja($kandidat_id){
        $datum = DB::table('idk_kandidati')
        ->select('kandidat_datumrodjenja')
        ->where([
            ['kandidat_id', '=', $kandidat_id]
        ])
        ->first();
		$godina = date('Y', strtotime($datum->kandidat_datumrodjenja));
        return $godina;
    }
	
	
	//UPDATES AND INSERTS
	
	public function updateNjemJezik(Request $request){
		$njem_jezik = $request->get('jezik');
		$naziv = "Njemački";
		$kandidat_id = auth()->user()->kandidat_id;
		DB::table('idk_kandidat_jezici')->insert(
			[
				'kj_naziv' => $naziv,
				'kj_slusanje' => $njem_jezik,
				'kj_citanje' => $njem_jezik,
				'kj_govorna_interakcija' => $njem_jezik,
				'kj_govorna_produkcija' => $njem_jezik,
				'kj_pisanje' => $njem_jezik,
				'kj_kandidatid' => $kandidat_id,
			]
		);	
	}
	public function addDatumApl(Request $request){
		$datum_apl = $request->get('datum_apliciranja');
		$datum_apll = date("Y-m-d", strtotime($datum_apl));
		$datum_termina = date("Y-m-d", strtotime("+20 months", strtotime($datum_apl)));
		$kandidat_id = auth()->user()->kandidat_id;
		// echo $datum_apliciranja;
		DB::table('idk_kandidati')
		->where('kandidat_id', $kandidat_id)
		->update([
			'datum_aplikacije' => $datum_apll,
			'datum_termina' => $datum_termina,
			'kandidat_procjenatermina' => 1 ]);
		DB::table('idk_validnosti_inputa')
		->where([
			['vi_kandidat_id', $kandidat_id],
			['vi_vrsta_podatka', "blok_apl"]
		])
		->update([
			'vi_status' => 3]);
		
	}
	public function addDatumTermina(Request $request){
		$datum_termina = $request->get('datum_termina');
		$datum_termina_f = date("Y-m-d", strtotime($datum_termina));
		$kandidat_id = auth()->user()->kandidat_id;
		// echo $datum_apliciranja;
		DB::table('idk_kandidati')
		->where('kandidat_id', $kandidat_id)
		->update(['datum_termina' => $datum_termina_f]);
		DB::table('idk_validnosti_inputa')
		->where([
			['vi_kandidat_id', $kandidat_id],
			['vi_vrsta_podatka', "blok_termin"]
		])
		->update([
			'vi_status' => 3]);
		
	}
	public function addViza(Request $request){
		$datum_vize = $request->get('datum_vize');
		$datum_vize_f = date("Y-m-d", strtotime($datum_vize));
		$kandidat_id = auth()->user()->kandidat_id;
		DB::table('idk_kandidati')
		->where('kandidat_id', $kandidat_id)
		->update([
			'kandidat_viza' => 1,
			'kandidat_viza_vrijedi_do' => $datum_vize_f]);
		DB::table('idk_validnosti_inputa')
		->where([
			['vi_kandidat_id', $kandidat_id],
			['vi_vrsta_podatka', "blok_viza"]
		])
		->update([
			'vi_status' => 3]);
	}
	/*public function addProcjenaTermina(Request $request){
		$datum_apl = $request->get('datum_apliciranja');
		$datum_termina = date("Y-m-d", strtotime("+20 months", strtotime($datum_apl)));
		$kandidat_id = auth()->user()->kandidat_id;
		// echo $datum_apliciranja;
		DB::table('idk_kandidati')
		->where('kandidat_id', $kandidat_id)
		->update([
			'datum_termina' => $datum_termina,
			'kandidat_procjenatermina' => 1 ]);
		
	}*/
	public function updateVozacka(Request $request){
		$kandidat_id = auth()->user()->kandidat_id;
		$odg = $request->get('odg');
		DB::table('idk_kandidati')
		->where('kandidat_id', $kandidat_id)
		->update(['kandidat_vozacka_dozvola' => $odg]);
	}
	public function addKategorijeVozacke(Request $request){
		$kandidat_id = auth()->user()->kandidat_id;
		$kategorije = $request->get('polozene_kategorije');
		// dd($kategorije);
		if($kategorije != null)
			$kategorije_f = implode(",", $kategorije);
		else
			$kategorije_f = "B";
		
		DB::table('idk_kandidati')
		->where('kandidat_id', $kandidat_id)
		->update(['kandidat_vozacka_kategorija' => $kategorije_f]);
	}
	public function addSrednjaSkola(Request $request){
		$srednje_obr_smjer = $request->get('srednje_obr_smjer');
		$srednje_obr_skola_f = $request->get('srednje_obr_skola_f');
		$srednje_obr_zvanje_id = $request->get('srednje_obr_zvanje_id');
		//$srednje_obr_godinaOd = $request->get('srednje_obr_godinaOd');
		$srednje_obr_godinaDo = $request->get('srednje_obr_godinaDo');
		$srednje_obr_drzava = $request->get('srednje_obr_drzava');
		$srednje_obr_grad = $request->get('srednje_obr_grad');
		
		if($srednje_obr_smjer == "ostalo"){
			$srednje_obr_zvanje = $srednje_obr_zvanje_id;
			$srednje_obr_godinaOd = $srednje_obr_godinaDo - 3;
		}else{
			//SELECT TRAJANJE SKOLOVANJE RADI GODINE POCETKA
			$smjerovi_srednje = DB::table('idk_skole_smjerovi')
			->select('ss_naziv', 'ss_trajanje_skole')
			->where('ss_id', '=', $srednje_obr_zvanje_id)
			->first();
			//dd($smjerovi_srednje);
			$srednje_obr_zvanje = $smjerovi_srednje->ss_naziv;
			$srednje_obr_trajanje = $smjerovi_srednje->ss_trajanje_skole;
			$srednje_obr_godinaOd = $srednje_obr_godinaDo - $srednje_obr_trajanje;
		}
		$datum_od = "01-09-".$srednje_obr_godinaOd;
		$datum_do = "01-06-".$srednje_obr_godinaDo;
		$ke_datumod	 = date("Y-m-d", strtotime($datum_od));
		$ke_datumdo	 = date("Y-m-d", strtotime($datum_do));
		
		$kandidat_id = auth()->user()->kandidat_id; 
		DB::table('idk_kandidat_edukacija')->insert(
			[
				'ke_naziv' => $srednje_obr_skola_f,
				'ke_naziv_kvalifikacije' => $srednje_obr_zvanje,
				'ke_datumod' => $ke_datumod,
				'ke_datumdo' => $ke_datumdo,
				'ke_drzava' => $srednje_obr_drzava,
				'ke_grad' => $srednje_obr_grad,
				'ke_vrsta_obrazovanja' => "srednje",
				'ke_opis' => "",
				'ke_kandidat_id' => $kandidat_id,
			]
		);	
	}
	public function addVisokaSkola(Request $request){
		$visoko_obr_smjer = $request->get('visoko_obr_smjer');
		$visoko_obr_skola_f = $request->get('visoko_obr_skola_f');
		$visoko_obr_zvanje_id = $request->get('visoko_obr_zvanje_id');
		$visoko_obr_godinaDo = $request->get('visoko_obr_godinaDo');
		$visoko_obr_drzava = $request->get('visoko_obr_drzava');
		$visoko_obr_grad = $request->get('visoko_obr_grad');
		
		if($visoko_obr_smjer == "ostalo"){
			$visoko_obr_zvanje = $visoko_obr_zvanje_id;
			$visoko_obr_godinaOd = $visoko_obr_godinaDo - 4;
		}else{
			//SELECT TRAJANJE SKOLOVANJE RADI GODINE POCETKA
			$smjerovi_srednje = DB::table('idk_skole_smjerovi')
			->select('ss_naziv', 'ss_trajanje_skole')
			->where('ss_id', '=', $visoko_obr_zvanje_id)
			->first();
			//dd($smjerovi_srednje);
			$visoko_obr_zvanje = $smjerovi_srednje->ss_naziv;
			$srednje_obr_trajanje = $smjerovi_srednje->ss_trajanje_skole;
			$visoko_obr_godinaOd = $visoko_obr_godinaDo - $srednje_obr_trajanje;
		}
		$datum_od = "01-09-".$visoko_obr_godinaOd;
		$datum_do = "01-06-".$visoko_obr_godinaDo;
		$ke_datumod	 = date("Y-m-d", strtotime($datum_od));
		$ke_datumdo	 = date("Y-m-d", strtotime($datum_do));
		
		$kandidat_id = auth()->user()->kandidat_id; 
		DB::table('idk_kandidat_edukacija')->insert(
			[
				'ke_naziv' => $visoko_obr_skola_f,
				'ke_naziv_kvalifikacije' => $visoko_obr_zvanje,
				'ke_datumod' => $ke_datumod,
				'ke_datumdo' => $ke_datumdo,
				'ke_drzava' => $visoko_obr_drzava,
				'ke_grad' => $visoko_obr_grad,
				'ke_vrsta_obrazovanja' => "visoko",
				'ke_opis' => "",
				'ke_kandidat_id' => $kandidat_id,
			]
		);	
	}
	public function addDodatnaEdukacija(Request $request){
		$dod_obr_vrsta = $request->get('dod_obr_vrsta');
		$dod_obr_naziv = $request->get('dod_obr_naziv');
		$opisDiv_dod = $request->get('opisDiv_dod');
		$dod_obr_godinaDo = $request->get('dod_obr_godinaDo');
		$dod_obr_grad = $request->get('dod_obr_grad');
		
		$datum_od = "01-01-".$dod_obr_godinaDo;
		$datum_do = "01-01-".$dod_obr_godinaDo;
		$ke_datumod	 = date("Y-m-d", strtotime($datum_od));
		$ke_datumdo	 = date("Y-m-d", strtotime($datum_do));
		
		$kandidat_id = auth()->user()->kandidat_id; 
		DB::table('idk_kandidat_edukacija')->insert(
			[
				'ke_naziv' => $dod_obr_vrsta,
				'ke_naziv_kvalifikacije' => $dod_obr_naziv,
				'ke_datumod' => $ke_datumod,
				'ke_datumdo' => $ke_datumdo,
				'ke_drzava' => "",
				'ke_grad' => $dod_obr_grad,
				'ke_vrsta_obrazovanja' => "ostalo",
				'ke_opis' => $opisDiv_dod,
				'ke_kandidat_id' => $kandidat_id,
			]
		);	
	}
	
	public function addIskustvo(Request $request){
		$iskustvo_pozicija = $request->get('iskustvo_pozicija');
		$iskustvo_poslodavac = $request->get('iskustvo_poslodavac');
		$datum_iskustvo_od = $request->get('datum_iskustvo_od');
		$datum_iskustvo_do = $request->get('datum_iskustvo_do');
		$iskustvo_grad = $request->get('iskustvo_grad');
		$aktuelno = $request->get('aktuelno');
		
		if($aktuelno == 1){
			$ke_datumdo = "";
		}else{
			$ke_datumdo	 = date("Y-m-d", strtotime($datum_iskustvo_do));
		}
		$ke_datumod	 = date("Y-m-d", strtotime($datum_iskustvo_od));
		//dd($ke_datumod);
		$kandidat_id = auth()->user()->kandidat_id; 
		DB::table('idk_kandidat_radno_iskustvo')->insert(
			[
				'kri_darum_od' => $ke_datumod,
				'kri_datum_do' => $ke_datumdo,
				'kri_pozicija' => $iskustvo_pozicija,
				'kri_naziv' => $iskustvo_poslodavac,
				'kri_grad' => $iskustvo_grad,
				'kri_kandidat_id' => $kandidat_id,
			]
		);	
	}
	
	public function updateLastOnline(Request $request){
		$kandidat_id = auth()->user()->kandidat_id;
		$last_online = date('Y-m-d H:i:s');
		DB::table('users')
		->where('kandidat_id', $kandidat_id)
		->update(
			[
				'last_online' => $last_online
			]
		);
	}
	
	public function updateStatusMessenger(Request $request){
		$kandidat_id = auth()->user()->kandidat_id;
		$novi_status_mess = $request->get('novi_status_mess');
		DB::table('idk_kandidati')
		->where('kandidat_id', $kandidat_id)
		->update(
			[
				'kandidat_status_messenger' => $novi_status_mess,
				'kandidat_status' => 1
			]
		);
	}
	
	public function updateStatusObrade(Request $request){
		$kandidat_id = auth()->user()->kandidat_id;
		$novi_status_obrade = $request->get('status');
		$stari_status_obrade = $this->getStatusObrade($kandidat_id);
		//dd($stari_status_obrade->kandidat_status);
		if($stari_status_obrade->kandidat_status == "1" or $stari_status_obrade->kandidat_status == "5" or $stari_status_obrade->kandidat_status == "0" ){
			DB::table('idk_kandidati')
			->where('kandidat_id', $kandidat_id)
			->update(
				[
					'kandidat_status' => $novi_status_obrade
				]
			);
		}
	}
	
	public function editUserAnswers(Request $request){
		$kandidat_id = auth()->user()->kandidat_id;
		$answer = $request->get('answer');
		$answer_f = "'".$answer."'";
		DB::table('users')
		->where('kandidat_id', $kandidat_id)
		->update(
			[
				$answer => 1
			]
		);
	}
	
	public function getValidacije($kandidat_id){
		$nevalidni = DB::table('idk_validnosti_inputa')
		->join('idk_razlozi_odbijanja', 'ro_id', '=', 'vi_razlog_id')
        ->select('idk_razlozi_odbijanja.ro_naziv', 'idk_razlozi_odbijanja.ro_dio_bloka','idk_validnosti_inputa.vi_status', 'idk_validnosti_inputa.vi_podatak_id', 'idk_validnosti_inputa.vi_vrsta_podatka')
        ->where([
            ['idk_validnosti_inputa.vi_kandidat_id', '=', $kandidat_id],
            ['idk_validnosti_inputa.vi_status', '=', 2]
        ])
        ->get();
		
        return $nevalidni;
	}
	public function getValidacijeBlok($kandidat_id, $blok){
		$nevalidni = DB::table('idk_validnosti_inputa')
		->join('idk_razlozi_odbijanja', 'ro_id', '=', 'vi_razlog_id')
        ->select('idk_razlozi_odbijanja.ro_naziv', 'idk_razlozi_odbijanja.ro_dio_bloka','idk_validnosti_inputa.vi_status', 'idk_validnosti_inputa.vi_podatak_id', 'idk_validnosti_inputa.vi_vrsta_podatka', 'idk_validnosti_inputa.vi_id', 'idk_validnosti_inputa.vi_kandidat_id')
        ->where([
            ['idk_validnosti_inputa.vi_kandidat_id', '=', $kandidat_id],
            ['idk_validnosti_inputa.vi_vrsta_podatka', '=', $blok],
            ['idk_validnosti_inputa.vi_status', '=', 2]
        ])
		->orderBy('idk_validnosti_inputa.vi_podatak_id', 'desc')
        ->get();
		
        return $nevalidni;
	}
	public static function getSkolaInfo($skola_id){
		$skola = DB::table('idk_kandidat_edukacija')
        ->select('ke_naziv', 'ke_naziv_kvalifikacije')
        ->where([
            ['ke_id', '=', $skola_id]
        ])
        ->first();

        return $skola;
	}
	public function editSkole(Request $request){
		$kandidat_id = auth()->user()->kandidat_id;
		$id_skole = $request->get('id_skole');
		$promijenjen_podatak = $request->get('promijenjen_podatak');
		$naziv_podatka = $request->get('naziv_podatka');
		$kontrola_id_val = $request->get('kontrola_id_val');
		if (is_numeric($promijenjen_podatak )) {
			$datum_do = "01-06-".$promijenjen_podatak;
			$promijenjen_podatak = date("Y-m-d", strtotime($datum_do));
		}

		DB::table('idk_kandidat_edukacija')
		->where('ke_id', $id_skole)
		->update(
			[
				$naziv_podatka => $promijenjen_podatak
			]
		);
		DB::table('idk_validnosti_inputa')
		->where([
			['vi_id', $kontrola_id_val]
		])
		->update([
			'vi_status' => 3]);
	}
	public static function getIskustvoInfo($iskustvo_id){
		$iskustvo = DB::table('idk_kandidat_radno_iskustvo')
        ->select('kri_pozicija', 'kri_naziv')
        ->where([
            ['kri_id', '=', $iskustvo_id]
        ])
        ->first();

        return $iskustvo;
	}
	public function editIskustva(Request $request){
		$kandidat_id = auth()->user()->kandidat_id;
		$id_iskustva = $request->get('id_iskustva');
		$promijenjen_podatak = $request->get('promijenjen_podatak');
		$naziv_podatka = $request->get('naziv_podatka');
		$kontrola_id_val = $request->get('kontrola_id_val');
		if ($naziv_podatka == "kri_datum_do" or $naziv_podatka == "kri_darum_od") {
			$promijenjen_podatak = date("Y-m-d", strtotime($promijenjen_podatak));
		}

		DB::table('idk_kandidat_radno_iskustvo')
		->where('kri_id', $id_iskustva)
		->update(
			[
				$naziv_podatka => $promijenjen_podatak
			]
		);
		DB::table('idk_validnosti_inputa')
		->where([
			['vi_id', $kontrola_id_val]
		])
		->update([
			'vi_status' => 3]);
	}
	public static function getDiplomaInfo($dokument_id){
		$dokument = DB::table('idk_documents')
        ->select('document_name')
        ->where([
            ['document_id', '=', $dokument_id]
        ])
        ->first();

        return $dokument;
	}
	public function deleteDoc(Request $request){
		$dokument_id = $request->get('kontrola_doc_id');
		DB::table('idk_documents')->where('document_id', '=', $dokument_id)->delete();
		DB::table('idk_validnosti_inputa')->where('vi_podatak_id', '=', $dokument_id)->delete();
	}
	public function updateAdresa(Request $request){
		$podatak = $request->get('podatak');
		$kolona = $request->get('kolona');
		$kandidat_id = auth()->user()->kandidat_id;
		DB::table('idk_kandidati')
		->where('kandidat_id', $kandidat_id)
		->update([
			$kolona => $podatak]);
			
	}
	
	public function editInformacije(Request $request){
		$kandidat_id = auth()->user()->kandidat_id;
		$podatak = $request->get('podatak');
		$kolona = $request->get('kolona');
		$vi_id = $request->get('vi_id');
		
		if($kolona == "kandidat_datumrodjenja"){
			$podatak = date("Y-m-d", strtotime($podatak));
		}
		
		DB::table('idk_kandidati')
		->where('kandidat_id', $kandidat_id)
		->update(
			[
				$kolona => $podatak
			]
		);
		DB::table('idk_validnosti_inputa')
		->where([
			['vi_id', $vi_id]
		])
		->update([
			'vi_status' => 3]);
	}
	public function getStatusObrade($kandidat_id){
		$kandidat_status = DB::table('idk_kandidati')
		->select('kandidat_status')
        ->where('kandidat_id', '=', $kandidat_id)
        ->first();

        return $kandidat_status;
	}
}
