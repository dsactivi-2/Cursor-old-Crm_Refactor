<?php

class Gateway
{

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getProfessions() : array
    {
        $sql = "SELECT ss_id, ss_naziv, ss_skola_id, ss_naziv_de, ss_naziv_en FROM idk_skole_smjerovi";
        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        $result = $stmt->fetchAll();

        return $result;
    }


    public function getSchools() : array
    {
        $sql = "SELECT skola_id, skola_naziv FROM idk_skole";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $results = $stmt->fetchAll();

        if ($results == NULL)
        {
            http_response_code(500);
            die("Internal server error!");
        } 
        else return $results;
    }
    
    public function getGroups($urlid) : array
    {
        $sql = "SELECT kg_id, kg_title 
                FROM idk_kandidati_grupe 
                INNER JOIN idk_link_generator_rel ON idk_kandidati_grupe.kg_id = idk_link_generator_rel.lr_groupid
                WHERE lr_lgid = $urlid";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $results = $stmt->fetchAll();

        if ($results == NULL) {
            http_response_code(500);
            die("There is no data.");
        }
        else return $results;
    }

    public function getOrderSchools($data)
    {
        $urlid = $this->checkFormParams($data["urlid"], "Nedostaje URL id kandidata.");

        $this->assertUrlID($urlid);

        $sql = "SELECT 
                    smjer_id,
                    idk_skole_smjerovi.ss_naziv,
                    idk_skole_smjerovi.ss_naziv_de,
                    idk_skole_smjerovi.ss_naziv_en
                FROM
                    idk_nalog_smjer
                JOIN
                    idk_link_generator
                ON
                    idk_nalog_smjer.nalog_id = idk_link_generator.lg_nalogid
                JOIN
                    idk_skole_smjerovi
                ON
                    idk_nalog_smjer.smjer_id = idk_skole_smjerovi.ss_id
                WHERE
                    lg_id = :urlid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":urlid", $urlid);
        $stmt->execute();

        $result = $stmt->fetchAll();

        if ($result == NULL) {
            $result = 1;
        }

        return $result;
    }

    public function getForm($data) : array
    {
        $kandidat_ime = $this -> checkFormParams($data["kandidat_ime"], "Nedostaje ime kandidata.");
        $kandidat_prezime = $this -> checkFormParams($data["kandidat_prezime"], "Nedostaje prezime kandidata.");
        $urlid = $this -> checkFormParams($data["urlid"], "Nedostaje URL id kandidata.");
        $datum_dan = $this -> checkFormParams($data["datum_dan"], "Nedostaje dan rođenja kandidata.");
        $datum_mjesec =  $this -> checkFormParams($data["datum_mjesec"], "Nedostaje mjesec rođenja kandidata.");
        $datum_godina = $this -> checkFormParams($data["datum_godina"], "Nedostaje godina rođenja kandidata.");
        $kki_phone = $this -> checkFormParams($data["kki_phone"], "Nedostaje broj telefona kandidata.");
        $nivo_jezika = $this -> checkFormParams($data["nivo_jezika"], "Nedostaje nivo jezika kandidata.");
        $kandidat_prijava_na = $this -> checkFormParams($data["kandidat_prijava_na"], "Nedostaje prijava na posao kandidata.");
        $kandidat_drzavljanstvo_vrsta = $this -> checkFormParams($data["kandidat_drzavljanstvo_vrsta"], "Nedostaje tip državljanstva kandidata.");
        $skola_naziv = $this -> checkFormParams($data["skola_naziv"],"Nedostaje naziv skole.");
        $smjer_naziv = $this -> checkFormParams($data["smjer_naziv"], "nedostaje smjer skole.");
        $kandidat_vozacka_dozvola = $data["kandidat_vozacka_dozvola"];

        // if($kandidat_drzavljanstvo_vrsta == 'NON-EU državljanin'){
        //     $skola_naziv = $this -> checkFormParams($data["skola_naziv"],"Nedostaje naziv skole.");
        //     $smjer_naziv = $this -> checkFormParams($data["smjer_naziv"], "nedostaje smjer skole.");
        //     if($skola_naziv == 'ostalo'){
        //         $skola_naziv_ru = $this -> checkFormParams($data["skola_naziv_ru"],"Nije unešena škola.");
        //         $smjer_naziv_ru = $this -> checkFormParams($data["smjer_naziv_ru"],"Nije unešen smjer škole.");
        //     }

        if($smjer_naziv == 0){
            $skola_naziv_ru = $this -> checkFormParams($data["skola_naziv_ru"],"Nije unešena škola.");
            $smjer_naziv_ru = $this -> checkFormParams($data["smjer_naziv_ru"],"Nije unešen smjer škole.");
        }
        
        if ($kandidat_drzavljanstvo_vrsta == "NON-EU državljanin") {
            $kandidat_termin =  $this -> checkFormParams($data["kandidat_termin"], "Nije unešeno posjedovanje vize");
        } else {
            $kandidat_termin = null;
        }

        //     if($kandidat_viza == '1'){
        //         $kandidat_viza_vrijedi_do = $this -> checkFormParams($data["kandidat_viza_vrijedi_do"], "Nije unešen datum do kojeg vrijedi viza");
        //     }else{
        //         $kandidat_termin = $this -> checkFormParams($data["kandidat_termin"], "Nije unešeno da li kandidat ima termin za vizu");
        //         if( $kandidat_termin == '1'){
        //             $kandidat_termin_date = $this -> checkFormParams($data["kandidat_termin_date"], "Nije unešen datum termina vize");
        //         }else{
        //             $kandidat_apliciranje = $this -> checkFormParams($data["kandidat_apliciranje"], "Nije unešeno da li je kandidat aplicirao za vizu");
        //             if($kandidat_apliciranje == '1'){
        //                 $kandidat_termin_date_app = $this -> checkFormParams($data["kandidat_termin_date_app"], "Nije unešen datum apliciranja za vizu");
        //             }
        //         }
        //     }
        // }else{
        //     $kandidat_viza = null;
        //     $kandidat_termin = null;
        //     $kandidat_apliciranje = null;
        // }

        $token = null; // njihov link nikad se neće moci shareovati preko partner appa
        //$lg_language = $data["lg_language"]; - ako oni budu imali odabir jezika na svojoj formi onda ce nam ovo trebati, za sad ide fiksno "bs"
        $lg_language = "bs";
        $kandidat_email = null; // nema ga vise na prijavnoj formi
        // $kandidat_visitedurl = $data["kandidat_visitedurl"]; - nepotreban podatak na "add_kandidat_new"

        // $params = array(
        //     "kandidat_ime"                   => $kandidat_ime,
        //     "kandidat_prezime"               => $kandidat_prezime,
        //     "urlid"                          => $urlid, //moguc problem
        //     "lg_language"                    => $lg_language, //ne traziti
        //     "datum_dan"                      => $datum_dan,
        //     "datum_mjesec"                   => $datum_mjesec,
        //     "datum_godina"                   => $datum_godina,
        //     "kki_phone"                      => $kki_phone,
        //     "kandidat_drzavljanstvo_vrsta"   => $kandidat_drzavljanstvo_vrsta,
        //     "token"                          => $token, //ne traziti
        //     "kandidat_email"                 => $kandidat_email, //ne traziti
        //     "kandidat_viza"                  => $kandidat_viza,
        //     "kandidat_termin"                => $kandidat_termin,
        //     "kandidat_apliciranje"           => $kandidat_apliciranje,
        //     "kandidat_viza_vrijedi_do"       => $kandidat_viza_vrijedi_do,
        //     "kandidat_termin_date"           => $kandidat_termin_date,
        //     "kandidat_termin_date_app"       => $kandidat_termin_date_app,
        //     "skola_naziv"                    => $skola_naziv,
        //     "smjer_naziv"                    => $smjer_naziv,
        //     "skola_naziv_ru"                 => $skola_naziv_ru,
        //     "smjer_naziv_ru"                 => $smjer_naziv_ru,
        //     "kandidat_prijava_na"            => $kandidat_prijava_na, //moguc problem
        //     "nivo_jezika"                    => $nivo_jezika,
        //     "carglass_form"                  => 1,
        // );

        $params = array(
            "kandidat_ime"                   => $kandidat_ime,
            "kandidat_prezime"               => $kandidat_prezime,
            "urlid"                          => $urlid, //moguc problem
            "lg_language"                    => $lg_language, //ne traziti
            "datum_dan"                      => $datum_dan,
            "datum_mjesec"                   => $datum_mjesec,
            "datum_godina"                   => $datum_godina,
            "kki_phone"                      => $kki_phone,
            "kandidat_drzavljanstvo_vrsta"   => $kandidat_drzavljanstvo_vrsta,
            "token"                          => $token, //ne traziti
            "kandidat_email"                 => $kandidat_email, //ne traziti
            "termin_carglass"                => $kandidat_termin,
            "skola_naziv"                    => $skola_naziv,
            "carglass_smjer"                 => $smjer_naziv,
            "skola_naziv_ru"                 => $skola_naziv_ru,
            "smjer_naziv_ru"                 => $smjer_naziv_ru,
            "kandidat_prijava_na"            => $kandidat_prijava_na, //moguc problem
            "nivo_jezika"                    => $nivo_jezika,
            "carglass_form"                  => 1,
            "kandidat_vozacka_dozvola"       => $kandidat_vozacka_dozvola   
        );

        return $params;
    }

    public function getAtuForm($data)
    {
        $kandidat_ime                   = $this->checkFormParams($data["kandidat_ime"], "Nedostaje ime kandidata.");
        $kandidat_prezime               = $this->checkFormParams($data["kandidat_prezime"], "Nedostaje prezime kandidata.");
        $urlid                          = $this->checkFormParams($data["urlid"], "Nedostaje URL id kandidata.");
        $datum_dan                      = $this->checkFormParams($data["datum_dan"], "Nedostaje dan rođenja kandidata.");
        $datum_mjesec                   = $this->checkFormParams($data["datum_mjesec"], "Nedostaje mjesec rođenja kandidata.");
        $datum_godina                   = $this->checkFormParams($data["datum_godina"], "Nedostaje godina rođenja kandidata.");
        $kki_phone                      = $this->checkFormParams($data["kki_phone"], "Nedostaje broj telefona kandidata.");
        $nivo_jezika                    = $this->checkFormParams($data["nivo_jezika"], "Nedostaje nivo jezika kandidata.");
        $kandidat_prijava_na            = $this->checkFormParams($data["kandidat_prijava_na"], "Nedostaje prijava na posao kandidata.");
        $radno_iskustvo                 = $this->checkFormParams($data["radno_iskustvo"], "Nedostaje radno iskustvo kandidata.");
        $kandidat_vozacka_dozvola       = $data["kandidat_vozacka_dozvola"];
        $pokrajina                      = $data["pokrajina"];
        
        $token = null;
        $kandidat_email = null;
        $lg_language = 'bs';

        $params = array(
            "kandidat_ime"                   => $kandidat_ime,
            "kandidat_prezime"               => $kandidat_prezime,
            "urlid"                          => $urlid, //moguc problem
            "lg_language"                    => $lg_language, //ne traziti
            "datum_dan"                      => $datum_dan,
            "datum_mjesec"                   => $datum_mjesec,
            "datum_godina"                   => $datum_godina,
            "kki_phone"                      => $kki_phone,
            "nivo_jezika"                    => $nivo_jezika,
            "kandidat_prijava_na"            => $kandidat_prijava_na, //moguc problem
            "kandidat_email"                 => $kandidat_email, //ne traziti
            "token"                          => $token, //ne traziti
            "atu_radno_iskustvo"             => $radno_iskustvo, 
            "pokrajina_rada"                 => $pokrajina, 
            "kandidat_vozacka_dozvola"       => $kandidat_vozacka_dozvola
        ); 

        return $params;
    }

    public function getDefaultForm($data)
    {
        $urlid                        = $this->checkFormParams($data["urlid"], "Nedostaje URL id kandidata.");
        $kandidat_prijava_na          = $this->checkFormParams($data["kandidat_prijava_na"], "Nedostaje prijava na posao kandidata.");
        $kandidat_ime                 = $this->checkFormParams($data["kandidat_ime"], "Nedostaje ime kandidata.");
        $kandidat_prezime             = $this->checkFormParams($data["kandidat_prezime"], "Nedostaje prezime kandidata.");
        $datum_dan                    = $this->checkFormParams($data["datum_dan"], "Nedostaje dan rođenja kandidata.");
        $datum_mjesec                 = $this->checkFormParams($data["datum_mjesec"], "Nedostaje mjesec rođenja kandidata.");
        $datum_godina                 = $this->checkFormParams($data["datum_godina"], "Nedostaje godina rođenja kandidata.");
        $kki_phone                    = $this->checkFormParams($data["kki_phone"], "Nedostaje broj telefona kandidata.");
        // Nivo jezika nije obavezan i moze se slati ili engleski ili njemacki
        $nivo_jezika                  = $data["nivo_jezika"];
        $nivo_engleskog_jezika        = $data["nivo_engleskog_jezika"];
        $smjer_naziv                  = $this->checkFormParams($data["smjer_naziv"], "nedostaje smjer skole.");
        // Radno iskustvo nije obavezno, ako je trajanje radnog iskustva je obavezno, suprotno je null
        $radno_iskustvo               = $data["radno_iskustvo"];
        if($radno_iskustvo == 1){
            $radno_iskustvo_trajanje  = $this->checkFormParams($data["radno_iskustvo_trajanje"], "Nedostaje trajanje radnog iskustva.");
        }else{
            $radno_iskustvo_trajanje      = $data["radno_iskustvo_trajanje"];
        }
        $kandidat_vozacka_dozvola     = $this->checkFormParams($data["kandidat_vozacka_dozvola"], "Nedostaje vozačka dozvola kandidata.");
        $kandidat_drzavljanstvo_vrsta = $this->checkFormParams($data["kandidat_drzavljanstvo_vrsta"], "Nedostaje tip državljanstva kandidata.");
        $skola_naziv                  = "";

        if($smjer_naziv == 0){
            $skola_naziv_ru = $this -> checkFormParams($data["skola_naziv_ru"],"Nije unešena škola.");
            $smjer_naziv_ru = $this -> checkFormParams($data["smjer_naziv_ru"],"Nije unešen smjer škole.");
        } else {
            $skola_naziv_ru = null;
            $smjer_naziv_ru = null;
        }

        $token = null;
        $kandidat_email = null;
        $lg_language = 'bs';

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
            "kki_phone"                    => $kki_phone,
            "smjer_naziv"                  => $smjer_naziv,
            "skola_naziv_ru"               => $skola_naziv_ru,
            "smjer_naziv_ru"               => $smjer_naziv_ru,
            "nivo_jezika"                  => $nivo_jezika,
            "nivo_engleskog_jezika"        => $nivo_engleskog_jezika,
            "kandidat_iskustvo_u_struci"   => $radno_iskustvo,
            "iskustvo_u_struci_trajanje"   => $radno_iskustvo_trajanje,
            "kandidat_drzavljanstvo_vrsta" => $kandidat_drzavljanstvo_vrsta,
            "kandidat_kategorija_vozacke"  => $kandidat_vozacka_dozvola,
        ); 

        return $params;
    }

    public function sendFormCurl($params)
    {
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

    public function checkFormParams($formParam, $message)
    {
        if(isset($formParam) && $formParam != "") {
            return $formParam;
        }else{
            http_response_code(400);
            die($message);
        }
    }

    public function authorizeClient($token)
    {
        $sql = "SELECT * FROM idk_api_clients WHERE client_token = :token";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":token", $token, PDO::PARAM_STR);
        $stmt->execute();

        if($stmt->rowCount() == 0)
        {
            http_response_code(401);
            die("Unauthorized!");
        }
    }

    private function assertUrlID($urlid)
    {
        $sql = "SELECT lg_id FROM idk_link_generator WHERE lg_id = :urlid";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":urlid", $urlid);
        $stmt->execute();

        if($stmt->rowCount() == 0)
        {
            http_response_code(400);
            die("Url ne postoji!");
        }
    }

}