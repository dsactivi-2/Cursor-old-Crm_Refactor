<?php
class Gateway
{
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getLead($lead_id){ 
        $env = parse_ini_file('.env');
        $access_token = $env["ACCESS_TOKEN"];
        $url = "https://graph.facebook.com/v17.0/$lead_id/?access_token=$access_token";
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function saveLead($kamp, $first_name, $last_name, $phone_number){
        
        $params = array(
            'kandidat_ime_dipl' => $first_name,
            'kandidat_prezime_dipl' => $last_name,
            'kandidat_telefon_dipl' => $phone_number,
            'kamp' => $kamp
        );

        $url = "https://crm.job-step.com/public_kandidati.php?page=prijavaDIPLK";
        $ch = curl_init();
        $data = http_build_query($params);
        
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($params));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function saveLeadForm($urlid, $lead_result){
        $token = null;
        $lg_language = 'bs';
        $kandidat_prijava_na = '';
        $kandidat_ime = '';
        $kandidat_prezime = '';
        $kandidat_telefon = '';
        $kandidat_email = '';
        $datum_dan = '';
        $datum_mjesec = '';
        $datum_godina = '';
        $nivo_jezika = '';
        $nivo_engleskog_jezika = '';
        $smjer_naziv = '';
        $skola_rucno = '';
        $smjer_rucno = '';
        $radno_iskustvo = '';   
        $kandidat_vozacka_dozvola = '';
        $kandidat_drzavljanstvo_vrsta = '';

        foreach($lead_result['field_data'] as $field){
            switch($field['name']){
                case 'grupa':
                    $kandidat_prijava_na = $field['values'][0];
                    break;
                case 'first_name':
                    $kandidat_ime = $field['values'][0];
                    break;
                case 'last_name':
                    $kandidat_prezime = $field['values'][0];
                    break;
                case 'phone_number':
                    $kandidat_telefon = $field['values'][0];
                    break;
                case 'email':
                    $kandidat_email = $field['values'][0];
                    break;
                case 'date_of_birth':
                    $datum_explode = explode('/', $field['values'][0]);
                    $datum_dan = $datum_explode[1];
                    $datum_mjesec = $datum_explode[0];
                    $datum_godina = $datum_explode[2];
                    break;
                case 'nivo_njemackog':
                    $nivo_jezika = $field['values'][0];
                    break;
                case 'nivo_engleskog':
                    $nivo_engleskog_jezika = $field['values'][0];
                    break;
                case 'nalog_smjer':
                    $smjer_naziv = $field['values'][0];
                    break;
                case 'radno_iskustvo':
                    $radno_iskustvo = $field['values'][0];
                    break;
                case 'vozacka_dozvola':
                    $kandidat_vozacka_dozvola = $field['values'][0];
                    break;
                case 'drzavljanstvo':
                    $kandidat_drzavljanstvo_vrsta = $field['values'][0];
                    break;
                case 'skola_rucno':
                    $skola_rucno = $field['values'][0];
                    break;
                case 'smjer_rucno':
                    $smjer_rucno = $field['values'][0];
                    break;
            }
        }

        $params = array(
            "urlid"                        => $urlid, //moguc problem
            "token"                        => $token, //ne traziti
            "lg_language"                  => $lg_language, //ne traziti
            "kandidat_prijava_na"          => $kandidat_prijava_na, //moguc problem
            "kandidat_email"               => $kandidat_email, //ne traziti
            "kandidat_ime"                 => $kandidat_ime,
            "kandidat_prezime"             => $kandidat_prezime,
            "datum_dan"                    => $datum_dan,
            "datum_mjesec"                 => $datum_mjesec,
            "datum_godina"                 => $datum_godina,
            "kki_phone"                    => $kandidat_telefon,
            "smjer_naziv"                  => $smjer_naziv,
            "skola_naziv_ru"               => $skola_rucno,
            "smjer_naziv_ru"               => $smjer_rucno,
            "nivo_jezika"                  => $nivo_jezika,
            "nivo_engleskog_jezika"        => $nivo_engleskog_jezika,
            "kandidat_iskustvo_u_struci"   => $radno_iskustvo,
            "kandidat_drzavljanstvo_vrsta" => $kandidat_drzavljanstvo_vrsta,
            "kandidat_kategorija_vozacke"  => $kandidat_vozacka_dozvola,
        ); 

        $url = "https://crm.job-step.com/public_kandidati.php?page=add_kandidat_new";
        $ch = curl_init();
        $data = http_build_query($params);
        
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($params));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);
    }
}