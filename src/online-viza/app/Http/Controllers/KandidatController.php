<?php

namespace App\Http\Controllers;
use Validator,Redirect,Response,File;
use Illuminate\Http\Request;
use DB;
use Session;
use Illuminate\Support\Facades\Storage;

class KandidatController extends Controller
{

	public function __construct()
    {
        //$this->middleware('auth');
    }
	// OVO JE FUNKCIJA ZA SPREMANJE KANDIDATA SA PRVE FORME
	// AKO POSTOJI KANDIDAT ONDA NE SALJE SMS VEC SAMO VODI NA FORMU
    public function store(Request $request)
    {

		$code = encrypt(mt_rand(100000,199999));
		$telefon = $request->post('telefon');
		$ime = $request->post('ime');
		$prezime = $request->post('prezime');
		$counter = $this->kandidatExistance($telefon);
		$hashed_tel = encrypt($request->post('telefon'));
		if($counter == 0){
			DB::table('idk_dak_kandidati')->insert(
					[
						'tel_dak_kandidat' => $request->post('telefon'),
						'name_dak_kandidat' => $request->post('ime'),
						'lastname_dak_kandidat' => $request->post('prezime'),
						'hashedtel_idk_dak_kandidat' => $hashed_tel,
						'telconfirm_dak_kandidat' => 0,
						'pin_dak_kandidat' => $code,
						'status_dak_kandidat' => 0,
						'tipunosa_dak_kandidat' => 0,
						'manager_dak_kandidat' => 48, 
						'datumunosa_dak_kandidat' => date("Y-m-d H:i:s"),
					]
				);
			$this->sendSmsToCandidateInfobip2($telefon, decrypt($code));
			return view('/confirm', ['code' => decrypt($code), 'hashed_codetel'=>$hashed_tel, 'mess'=>'1']);
		}else{
			$hashed_tel = DB::Table('idk_dak_kandidati')->where('tel_dak_kandidat', $request->get('telefon'))->value('hashedtel_idk_dak_kandidat');
			$code = DB::Table('idk_dak_kandidati')->where('tel_dak_kandidat', $request->get('telefon'))->value('pin_dak_kandidat');
			return view('/confirm', ['code' => decrypt($code), 'hashed_codetel'=>$hashed_tel, 'mess'=>'2']);
		}
    }
	// POSTOJANJE KANDIDATA
	public function kandidatExistance($telefon)
	{
		$kandidat_info = DB::Table('idk_dak_kandidati')->where('tel_dak_kandidat', $telefon)->get();
		$countNumber = $kandidat_info->count();

		return $countNumber;
	}


	// SLANJE PINA NA KLIK RESEND
	public function resend_pin(Request $request){

		$code = encrypt(mt_rand(100000,199999));
		$telefon = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $request->get('hashed_codetel'))->value('tel_dak_kandidat');

		$this->sendSmsToCandidateInfobip2($telefon, decrypt($code));
		DB::table('idk_dak_kandidati')
            ->where('hashedtel_idk_dak_kandidat', $request->get('hashed_codetel'))
            ->update(['pin_dak_kandidat' => $code]);

		return view('/confirm', ['code' => decrypt($code), 'hashed_codetel'=>$request->get('hashed_codetel'), 'mess'=>'3']);
	}

	public function sendToForm(Request $request)
    {
		$code = $request->post('phone_confirm');
		$hashed_codetel = $request->post('hashed_codetel');

		$id_kandidata = DB::Table('idk_dak_kandidati')->select('id_dak_kandidat')->where('hashedtel_idk_dak_kandidat', $hashed_codetel)->first();

		DB::table('idk_dak_kandidati')->updateOrInsert(
			[
				'id_dak_kandidat' => $id_kandidata->id_dak_kandidat,
			],
			[
				'telconfirm_dak_kandidat' => 1,
			]
		);

		setcookie("jobstep_confirmer", $hashed_codetel,  time() + (20 * 365 * 24 * 60 * 60));
		$kandidat_info = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $hashed_codetel)->get();
		return view('/form', ['kandidat_info'=>$kandidat_info, 'hashed_codetel'=>$hashed_codetel]);
    }

	// FUNKCIJA ZA SPREMANJE INFORMACIJA KAD DODJE NA VELIKU FORMU
	// AKO JE KANDIDAT VEC UNESEN I U OBRADI TJ. AKO MU JE STATUS 1 ONDA NE UPDATA DATUM UNOSA I OBRADE
	// AKO JE ODABRAO DIPL ONDA UPDATE DIPLA RADI
	public function save_info(Request $request)
    {
		$id_kandidata = DB::Table('idk_dak_kandidati')->select('id_dak_kandidat')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->first();
		$kandidatExistanceObrada = $this->kandidatExistanceObrada($id_kandidata->id_dak_kandidat);

		$contract = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('contract_dak_kandidat');
		if($contract != NULL){
			$return_contract = 1;
		}else{
			$return_contract = 0;
		}

		$passport = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('passport_dak_kandidat');
		if($passport != NULL){
			$return_passport = 1;
		}else{
			$return_passport = 0;
		}

		if($request->get('osiguranje')){
			$osiguranje = 1;
		}else{
			$osiguranje = 0;
		}

		if($request->get('dipl')){
			$dipl = 1;
		}else{
			$dipl = 0;
		}

	if($kandidatExistanceObrada == 0){
		DB::table('idk_dak_kandidati')->updateOrInsert(
			[
				'id_dak_kandidat' => $id_kandidata->id_dak_kandidat,
			],
			[
				'telconfirm_dak_kandidat' => 1,
				'name_dak_kandidat' => $request->get('name'),
				'lastname_dak_kandidat' => $request->get('lastname'),
				'email_dak_kandidat' => $request->get('email'),
				'daypart_dak_kandidat' => $request->get('daypart'),
				'comment_dak_kandidat' => $request->get('comment'),
				'status_dak_kandidat' => 1,
				'datumobrade_dak_kandidat' => date("Y-m-d H:i:s"),
				'osiguranje_dak_kandidat' => $osiguranje,
				'dipl_dak_kandidat' => $dipl,
			]
		);



	}else{
		DB::table('idk_dak_kandidati')->updateOrInsert(
			[
				'id_dak_kandidat' => $id_kandidata->id_dak_kandidat,
			],
			[
				'name_dak_kandidat' => $request->get('name'),
				'lastname_dak_kandidat' => $request->get('lastname'),
				'email_dak_kandidat' => $request->get('email'),
				'daypart_dak_kandidat' => $request->get('daypart'),
				'comment_dak_kandidat' => $request->get('comment'),
				'osiguranje_dak_kandidat' => $osiguranje,
				'dipl_dak_kandidat' => $dipl,
			]
		);
	}

	if($request->get('dipl')){
			// AKO JE OVDJE JEDINICA ODABRANA POSALJI NA DIPL, A DIPL TREBA PAZITI
			// DA NE UNOSI DUPLU PRIJAVU TAKO STO CE GLEDATI IMA LI TAJ BROJ VEC U DIPL BAZI

			// set post fields
			$post = [
				'id' => $id_kandidata->id_dak_kandidat,
				'dipl' => 1,
			];

			$ch = curl_init('https://jobstep-app.com/public_kandidati?page=sendToDiplFromSvezaVizu');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

			// execute!
			$response = curl_exec($ch);

			// close the connection, release resources used
			curl_close($ch);

		}else{
			// AKO JE OVDJE JEDINICA ODABRANA POSALJI NA DIPL, A DIPL TREBA PAZITI
			// DA NE UNOSI DUPLU PRIJAVU TAKO STO CE GLEDATI IMA LI TAJ BROJ VEC U DIPL BAZI

			// set post fields
			$post = [
				'id' => $id_kandidata->id_dak_kandidat,
				'dipl' => 0,
			];

			$ch = curl_init('https://jobstep-app.com/public_kandidati?page=sendToDiplFromSvezaVizu');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

			// execute!
			$response = curl_exec($ch);

			// close the connection, release resources used
			curl_close($ch);
		}

		return view('/thanksmessage', ['return_contract'=>$return_contract, 'return_passport'=>$return_passport, 'osiguranje'=>$osiguranje]);
    }

	// POSTOJANJE KANDIDATA U OBRADI
	public function kandidatExistanceObrada($id_kandidata)
	{
		$kandidat_info = DB::Table('idk_dak_kandidati')->where([['id_dak_kandidat', '=', $id_kandidata],['status_dak_kandidat', '>', 0],])->get();

		$countNumber = $kandidat_info->count();

		return $countNumber;
	}
	public function save_contract($file_name)
    {

		//POKUPI ID KANDIDATA
		$id_kandidata = DB::Table('idk_dak_kandidati')->select('id_dak_kandidat')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->first();

		// UPDATE TABELE
		DB::table('idk_dak_kandidati')->updateOrInsert(
			[
				'id_dak_kandidat' => $id_kandidata->id_dak_kandidat,
			],
			[
				'contract_dak_kandidat' => $file_name,
				'contracttime_dak_kandidat' => date("Y-m-d H:i:s"),
			]
		);

		$contract = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('contract_dak_kandidat');
		if($contract != NULL){
			$return_contract = 1;
		}else{
			$return_contract = 0;
		}

		$passport = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('passport_dak_kandidat');
		if($passport != NULL){
			$return_passport = 1;
		}else{
			$return_passport = 0;
		}

		$osiguranje = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('osiguranje_dak_kandidat');

		return redirect('thanksmessage')->with(['return_contract'=>$return_contract, 'return_passport'=>$return_passport, 'osiguranje'=>$osiguranje]);
    }

	public function save_passport($file_name)
	{

		$id_kandidata = DB::Table('idk_dak_kandidati')->select('id_dak_kandidat')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->first();

		DB::table('idk_dak_kandidati')->updateOrInsert(
			[
				'id_dak_kandidat' => $id_kandidata->id_dak_kandidat,
			],
			[
				'passport_dak_kandidat' => $file_name,
				'passporttime_dak_kandidat' => date("Y-m-d H:i:s"),
			]
		);

		$contract = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('contract_dak_kandidat');
		if($contract != NULL){
			$return_contract = 1;
		}else{
			$return_contract = 0;
		}

		$passport = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('passport_dak_kandidat');
		if($passport != NULL){
			$return_passport = 1;
		}else{
			$return_passport = 0;
		}

		$osiguranje = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('osiguranje_dak_kandidat');

		return redirect('thanksmessage')->with(['return_contract'=>$return_contract, 'return_passport'=>$return_passport, 'osiguranje'=>$osiguranje]);
    }

	public function thanksmessage(){

		$contract = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('contract_dak_kandidat');
		if($contract != NULL){
			$return_contract = 1;
		}else{
			$return_contract = 0;
		}

		$passport = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('passport_dak_kandidat');
		if($passport != NULL){
			$return_passport = 1;
		}else{
			$return_passport = 0;
		}

		$osiguranje = DB::Table('idk_dak_kandidati')->where('hashedtel_idk_dak_kandidat', $_COOKIE['jobstep_confirmer'])->value('osiguranje_dak_kandidat');

		return view('/thanksmessage', ['return_contract'=>$return_contract, 'return_passport'=>$return_passport, 'osiguranje'=>$osiguranje]);
	}
	public function sendSMS($number, $pin){
		$secret_token = 'aebf56deb2ef2ff5ccfd8f48456f8bb4';
		$start_of_number = "00";
		$telefon = $start_of_number.trim($number,"+");
		$received_message_origin = "IDK_STUDIO";
		$received_message_content = "Thank you for downloading our app.Your PIN: ".$pin."";
		$received_message_number = "".$telefon."";

		$message_content_formatted = str_replace("@","(at)", $received_message_content);
		$message_content_formatted = str_replace("đ","dj", $message_content_formatted);
		$message_content_formatted = str_replace("Đ","DJ", $message_content_formatted);
		$message_content_formatted = rawurlencode(iconv("UTF-8", "ASCII//TRANSLIT", $message_content_formatted) );

		/*
		 * NTH SMS Gateway INFO
		 */
		$nth_gateway_endpoint = 'http://bulk.mobile-gw.com';
		$nth_gateway_endpoint_port = '9000';
		$nth_customer_username = 'IDK_Acc';
		$nth_customer_password = 'GadOSYY2';

		// Additional gateway options

		$nth_gateway_message_allow_adaption = 1;

		/*
		 * Building URL
		 */
		$gateway_url = $nth_gateway_endpoint . ':' . $nth_gateway_endpoint_port . '/?username=' . $nth_customer_username . '&password=' . $nth_customer_password;
		// Adding options
		$gateway_url .= '&allow_adaption=' . $nth_gateway_message_allow_adaption;
		// Adding notification info
		$gateway_url .= '&status_report=' . 7;
		$gateway_url .= '&status_url=' . urlencode('https://www.idksms.com/sms/get_sms_notification.php');
		// Adding SMS message info
		$gateway_url .= '&origin=' . $received_message_origin;
		$gateway_url .= '&call-number=' . $received_message_number;
		$gateway_url .= '&text=' . $message_content_formatted;
		//$gateway_url .= '&messageid=' . $message_id;

		$headers = [];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $gateway_url);
		//Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		//Get headers
		curl_setopt($ch, CURLOPT_HEADERFUNCTION,
		  function($curl, $header) use (&$headers)
		  {
			$len = strlen($header);
			$header = explode(':', $header, 2);
			if (count($header) < 2) // ignore invalid headers
			  return $len;

			$name = strtolower(trim($header[0]));
			if (!array_key_exists($name, $headers))
			  $headers[$name] = [trim($header[1])];
			else
			  $headers[$name][] = trim($header[1]);

			return $len;
		  }
		);
		//Execute the request.
		$body = curl_exec($ch);
		$info = curl_getinfo($ch);

		// echo '<pre>';
		// if($info === FALSE)
		// {
		   // echo 'Curl Failed: ' . curl_error($ch);
		// }
		// var_dump($info);
		// var_dump($body);
		// print_r($headers);

		//Close the cURL handle.
		curl_close($ch);
	}
	public function sendSmsToCandidateInfobip2($telefon, $pin){

		$sender = "JOBSTEP";
		// $broj = "38761938892";
		$text = "Hvala na instalaciji aplikacije.Vaš PIN: ".$pin."";


		$broj = str_replace("+","",$telefon);
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://ej8w3r.api.infobip.com/sms/1/text/single",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{ \"from\":\"".$sender."\", \"to\":\"".$broj."\", \"text\":\"".$text."\" }",
			CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			"content-type: application/json"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		// if ($err) {
			// echo "cURL Error #:" . $err;
		// } else {
			// echo $response;
		// }
	}
}
