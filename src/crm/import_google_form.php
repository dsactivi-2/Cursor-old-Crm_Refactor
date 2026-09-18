<?php

    $data = json_decode(file_get_contents("php://input"), true);

    $partner_token  = $data["token"];
    $urlid          = $data["url_id"];   //testni 457
    $grupa_id       = $data["group_id"];
    $nalog_id       = $data["nalog_id"]; //testni 127

    if($data["Nivo poznavanja njemačkog jezika?"] != null)
    {
        $jezik = $data["Nivo poznavanja njemačkog jezika?"];
    }
    else
    {
        $jezik = $data["Nivo poznavanja nemačkog jezika"];
    }
    
    //Formatiranje datuma
    if(strpos($data['Datum rođenja?'],".")){
        $pieces         = explode(".", $data['Datum rođenja?']);
        $day            = $pieces[0];
        $month          = $pieces[1];
        $year           = $pieces[2];
    }else if(strpos($data['Datum rođenja?'],"-")){
        $pieces         = explode("-", $data['Datum rođenja?']);
        $day            = $pieces[2];
        $month          = $pieces[1];
        $year           = $pieces[0];
    }    
    //Biljeske
    function getBiljeske($k)
    {
        $pos  = strpos($k, "Biljeska");
        $pos1 = strpos($k, "Beleska");

        if($pos !== false)
        {
            return $k;
        }
        if($pos1 !== false)
        {
            return $k;
        }
    }
    $keys           = array_keys($data);
    $biljeskeKey    = array_filter($keys, "getBiljeske");
    $biljeske       = "";
    
    $i=0;
    foreach($biljeskeKey as $biljeska)
    {
        if($i==0)
            $biljeske .= $data[$biljeska];
        else
            $biljeske .= ", ". $data[$biljeska];

        $i++;
    }

    if($partner_token == 1){
        $partner_token = "7ade2e5421a26c6d8e67bfe2";
    }else{
        $partner_token = NULL;
    }
    
    $params = array(
        "urlid"                 => $urlid,
        "kandidat_prijava_na"   => $grupa_id,
        "token"                 => $partner_token,
        "nalog_id"              => $nalog_id,
        "datum_prijave"         => date("Y-m-d H:i:s"),
        "kandidat_ime"          => $data['Ime'],
        "kandidat_prezime"      => $data['Prezime'],
        "kki_phone"             => $data['Pozivni broj'].$data['Broj telefona'],
        "datum_dan"             => $day,
        "datum_mjesec"          => $month,
        "datum_godina"          => $year,
        "nivo_jezika"           => $jezik,
        "kategorija_vozacke"    => $data['Da li imate vozačku dozvolu?'],
        "biljeske"              => $biljeske
    );

    $url = "https://crm.job-step.com/public_kandidati_import.php?page=import_google_forma";
    // $url = "https://f887-85-92-236-48.ngrok-free.app/public_kandidati_import.php?page=import_google_forma";
    $ch = curl_init();
    $data = http_build_query($params);
    
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, count($params));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    
    
    
    //close connection
    curl_close($ch);
    //STARI KOD I OBJASNJENJA

    /* include ('csv_file.php'); ovo nece trebati */
    // $partner_token = ""; moj za testiranje: 2780de2cefcf5520140e4634  dino klepo token: "7ade2e5421a26c6d8e67bfe2"; arman ce slati 0 ili 1, 
        // ako je 0 onda je vrijednost tokena == null
        

        //$novi_niz = json_decode ($json_niz, true ) ; // ovo se mijenja, $json_niz je pokupljen iz includanog filea

        //foreach( $novi_niz as $red ){  // nece trebati for petlja, jer ce ulaziti jedan po jedan
            
        //ovaj nacin kupljenja i setovanja datuma rodjenja neka ostane
    
        // $datum_rodjenja = date("Y-m-d", strtotime($day."-".$month."-".$year));

        //biljeske ce probati Harun predefinisati tamo na formi, tako da cemo ovdje samo uzimati tu gotovu
    /*  $biljeske = "";
        $biljeske = "Da li ste diplomirani inženjer građevinarstva/magistar inženjer građevinarstva, inženjer građevinarstva? - ".$red['Da li ste diplomirani inženjer građevinarstva/magistar inženjer građevinarstva, inženjer građevinarstva?']."<br>".
                    "Naziv visokoškolske ustanove: ".$red['Naziv visokoškolske ustanove:']."<br>".
                    
                    
                    "Da li imate iskustva u ovom poslu? - ".$red['Da li imate iskustva u ovom poslu?']."<br>"; */
        // "Smjer: ".$red['Smjer:']."<br>".
        // "Stečeno zvanje: ".$red['Stečeno zvanje:']."<br>".
        // "Završena visokoškolska ustanova je: ".$red['Završena visokoškolska ustanova je:']."<br>".
        
        //ovakvi parametri se trebaju slati na public_kandidati_import page: import_google_forma
    // print_r(json_encode($params)); //ove dvije linije sluzile samo za ispis i testiranje prije slanja
    // exit();

    //kod ispod treba otkomentarisati pri slanju
    
    
    

    //}
?>