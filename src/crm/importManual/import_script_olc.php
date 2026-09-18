<?php
include ('json_file_old_lilium.php');
include ('../includes/functions.php');
$from = $_GET['from'] ?? null; 
$to = $_GET['to'] ?? null; 
$novi_niz = json_decode ($json_niz, true ) ;

for ($i = $from; $i <= $to; $i++) {

    if ($i < 0 OR $i > count($novi_niz) - 1) {
        break;
    }

    // foreach( $novi_niz as $novi_niz[$i] ){

    //Za unos
    $ime                = trim($novi_niz[$i]['Ime:']);
    $prezime            = trim($novi_niz[$i]['Prezime:']);
    $datum_rodjenja     = $novi_niz[$i]['Datum rođenja:'];
    $telefon            = "+".$novi_niz[$i]['Telefon:'];
    $drzavljanstvo      = $novi_niz[$i]['Državljanstvo:'];
    $njem_jezik         = $novi_niz[$i]['Nivo poznavanja njemačkog jezika'];
    $naziv_smjera       = $novi_niz[$i]['Zvanje/Smjer*:'];
    $naziv_skole        = $novi_niz[$i]['Naziv škole:'];
    $vozacka_format     = $novi_niz[$i]['Vozačka formatirano'];

    //Za bilješku
    $datum_prijave      = $novi_niz[$i]['Submission Time:'];
    $certifikat         = $novi_niz[$i]['Certifikat DA/NE'];
    $viza_u_njem        = $novi_niz[$i]['Da li imate vizu u Njemačkoj?'];
    $istek_vize         = $novi_niz[$i]['Kad Vam ističe viza?'];
    $godine_u_njem      = $novi_niz[$i]['Koliko godina zivite u Njemackoj?'];
    $grad_boravka       = $novi_niz[$i]['Grad boravka u DE'];
    $tri_grada_zelja    = $novi_niz[$i]['3 grada želja gdje bi radili'];
    $terenski_rad       = $novi_niz[$i]['Da li odgovara terenski radi 5 dana?'];
    $vozacka_input      = $novi_niz[$i]['Vozačka - država i kategorija'];
    $koja_viza          = $novi_niz[$i]['Ako nije EU pass, koja viza'];
    $nost_diplome       = $novi_niz[$i]['nostrifikacija diplome (koje je zvanje dobio)'];

    echo 
        $datum_prijave." - ".$ime." - ".$prezime." - ".$datum_rodjenja." - ".$telefon."++ - ".$drzavljanstvo." - ".$njem_jezik." - ".$certifikat." - ".$naziv_skole." - ".
        $naziv_smjera." - ".$viza_u_njem." - ".$istek_vize." - ".$godine_u_njem." - ".$grad_boravka." - ".$tri_grada_zelja." - ".$terenski_rad." - ".$vozacka_format." - ".
        $vozacka_input." - ".$koja_viza." - ".$nost_diplome.""
    ;
    
    //Check if candidate exists
    $query_check = $db->prepare("SELECT kandidat_id FROM idk_kandidati 
        WHERE
        (TRIM(kandidat_ime) = :kandidat_ime AND TRIM(kandidat_prezime) = :kandidat_prezime AND kandidat_mobitel = :kandidat_mobitel)");
    $query_check->execute(array(
        ':kandidat_ime' => $ime,
        ':kandidat_prezime' => $prezime,
        // ':kandidat_datumrodjenja' => $datum_rodjenja,
        ':kandidat_mobitel' => $telefon
    ));

    $number_of_users = $query_check->rowCount();
    if($number_of_users > 0){
        echo " IMA DUPLIH ".$number_of_users."<hr>";
        continue;
    }

    if($vozacka_format != ""){
        $vozacka_dozvola = "Da";
        $vozacka_kategorija = $vozacka_format;
        // echo "imavozacku<br>";
    }else{
        $vozacka_dozvola = null;
        $vozacka_kategorija = null;
        // echo "nemavozacku<br>";
    }

    $datum_rodjenja_frmt = date('Y-m-d', strtotime($datum_rodjenja));

    //Add user to db
        $kandidat_check = md5(uniqid(rand(), true));
        $kandidat_datetime = date('Y-m-d H:i:s');
        $query = $db->prepare("
            INSERT INTO idk_kandidati
                ( kandidat_check, kandidat_ime, kandidat_prezime, kandidat_datumrodjenja, kandidat_mobitel, kandidat_visitedurl, kandidat_vozacka_dozvola, kandidat_vozacka_kategorija, kandidat_drzavljanstvo_vrsta, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_datetime, kandidat_prijava_na, kandidat_group, kandidat_status_prijave, kandidat_porijeklo)
            VALUES
                (:kandidat_check,:kandidat_ime,:kandidat_prezime,:kandidat_datumrodjenja,:kandidat_mobitel,:kandidat_visitedurl,:kandidat_vozacka_dozvola,:kandidat_vozacka_kategorija,:kandidat_drzavljanstvo_vrsta, :kandidat_slika,:kandidat_status,:kandidat_status_messenger,:kandidat_datetime,:kandidat_prijava_na,:kandidat_group,:kandidat_status_prijave,:kandidat_porijeklo)");

        $query->execute(array(
        ':kandidat_check' => $kandidat_check,
        ':kandidat_ime' => $ime,
        ':kandidat_prezime' => $prezime,
        ':kandidat_datumrodjenja' => $datum_rodjenja_frmt,
        ':kandidat_mobitel' => $telefon,
        ':kandidat_visitedurl' => 2293,
        ':kandidat_vozacka_dozvola' => $vozacka_dozvola,
        ':kandidat_vozacka_kategorija' => $vozacka_kategorija,
        ':kandidat_drzavljanstvo_vrsta' => $drzavljanstvo,
        ':kandidat_slika' => "none",
        ':kandidat_status' => 0,
        ':kandidat_status_messenger' => 0,
        ':kandidat_datetime' => $kandidat_datetime,
        ':kandidat_prijava_na' => "Ostalo",
        ':kandidat_group' => 7,
        ':kandidat_status_prijave' => 1,
        ':kandidat_porijeklo' => 0
        ));

        $kandidat_id = $db->lastInsertId();
    //Add user to db

    // INSERT LOG STATUSA
        $query_log_status = $db->prepare("
            INSERT INTO idk_log_kandidat_statusi
                (lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
            VALUES
                (:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
        ");

        $query_log_status->execute(array(
            ':lks_kandidat_id' => $kandidat_id,
            ':lks_status_obrade' => 0,
            ':lks_status_messenger' => 0,
            ':lks_datetime' => $kandidat_datetime
        ));
    // INSERT LOG STATUSA

    //INSERT TELEFONA
        $query_mob = $db->prepare("
                        INSERT INTO idk_kandidat_kontakt_info
                            (kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
                        VALUES
                            (:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

        $query_mob->execute(array(
                    ':kki_grupa' => 1,
                    ':kki_naziv' => "Mobilni",
                    ':kki_podatak' => $telefon,
                    ':kki_kandidat_id' => $kandidat_id));
        
    //INSERT TELEFONA

    //INSERT JEZIKA
        if($njem_jezik != ""){
                                            
            $kj_naziv_njemacki = "Njemački";
            $kj_znanje_njemacki = $njem_jezik;
            // Add language knowlege
            $query_njem = $db->prepare("
                            INSERT INTO idk_kandidat_jezici
                                (kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
                            VALUES
                                (:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");

            $query_njem->execute(array(
                        ':kj_naziv' => $kj_naziv_njemacki,
                        ':kj_slusanje' => $kj_znanje_njemacki,
                        ':kj_citanje' => $kj_znanje_njemacki,
                        ':kj_govorna_interakcija' => $kj_znanje_njemacki,
                        ':kj_govorna_produkcija' => $kj_znanje_njemacki,
                        ':kj_pisanje' => $kj_znanje_njemacki,
                        ':kj_kandidatid' => $kandidat_id));
        }
    //INSERT JEZIKA

    //TRAŽENJE SMJERA NA OSNOVU NAZIVA
        if($naziv_smjera != "" ){
            
            //provjeri da li smjer vec postoji
            $query_smjer_naziv = $db->prepare("SELECT 
                                                ss_id,
                                                skola_id,
                                                ss_naziv,
                                                skola_naziv
                                            FROM 
                                                idk_skole_smjerovi 
                                            JOIN 
                                                idk_skole 
                                            ON 
                                                idk_skole_smjerovi.ss_skola_id = idk_skole.skola_id 
                                            WHERE 
                                                ss_naziv = '$naziv_smjera'  AND ss_naziv_de is not null AND ss_naziv_de != 'x'
                                            ORDER BY ss_naziv_de, ss_naziv_en DESC, skola_naziv_de DESC
            ");
            $query_smjer_naziv->execute();
            $count = $query_smjer_naziv->rowCount();
            $row_smjer_naziv = $query_smjer_naziv->fetch();
            if($count > 0){
                $smjer_id_post = $row_smjer_naziv['ss_id'];
                $smjer_naziv = $row_smjer_naziv['ss_naziv'];
                $skola_id_post = $row_smjer_naziv['skola_id'];
                $skola_naziv = $row_smjer_naziv['skola_naziv'];
            } else {
                $smjer_id_post = null;
                $smjer_naziv = $naziv_smjera;
                $skola_id_post = null;
                $skola_naziv = $naziv_skole;
            }
        }else{
            if($naziv_skole != "" ){
                //Provjera da li postoji smjer sa ovim nazivom skole jer su neki kandidati u polje za skolu unosili smjer
                $query_smjer_naziv = $db->prepare("SELECT 
                                                    ss_id,
                                                    skola_id,
                                                    ss_naziv,
                                                    skola_naziv
                                                FROM 
                                                    idk_skole_smjerovi 
                                                JOIN 
                                                    idk_skole 
                                                ON 
                                                    idk_skole_smjerovi.ss_skola_id = idk_skole.skola_id 
                                                WHERE 
                                                    ss_naziv = '$naziv_skole' AND ss_naziv_de is not null AND ss_naziv_de != 'x'
                                                    ORDER BY ss_naziv_de, ss_naziv_en DESC, skola_naziv_de DESC
                ");
                $query_smjer_naziv->execute();
                $count = $query_smjer_naziv->rowCount();
                $row_smjer_naziv = $query_smjer_naziv->fetch();
                if($count > 0){
                    $smjer_id_post = $row_smjer_naziv['ss_id'];
                    $smjer_naziv = $row_smjer_naziv['ss_naziv'];
                    $skola_id_post = $row_smjer_naziv['skola_id'];
                    $skola_naziv = $row_smjer_naziv['skola_naziv'];
                } else {
                    $smjer_id_post = null;
                    $smjer_naziv = $naziv_skole;
                    $skola_id_post = null;
                    $skola_naziv = $naziv_skole;
                }
            }
        }
        //INSERT SKOLE
			if($naziv_skole != "" OR $naziv_smjera != ""){
                $insert_skole = $db->prepare("
                                INSERT INTO idk_kandidat_edukacija
                                    (ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id, ke_skola_id, ke_smjer_id)
                                VALUES
                                    (:ke_naziv_kvalifikacije,:ke_naziv,:ke_vrsta_obrazovanja,:ke_kandidat_id,:ke_skola_id,:ke_smjer_id)");
        
                $insert_skole->execute(array(
                            ':ke_naziv_kvalifikacije' => $smjer_naziv,
                            ':ke_naziv' => $skola_naziv,
                            ':ke_vrsta_obrazovanja' => "srednje",
                            ':ke_skola_id' => $skola_id_post,
                            ':ke_smjer_id' => $smjer_id_post,
                            ':ke_kandidat_id' => $kandidat_id));
            }
        //INSERT SKOLE
    //TRAŽENJE SMJERA NA OSNOVU NAZIVA

    //DODAVANJE U PROJEKAT I LSP
        $query_project = $db->prepare("
            INSERT INTO idk_project_kandidati
                (pk_projectid, pk_kandidatid)
            VALUES
                (:pk_projectid, :pk_kandidatid)");

        $query_project->execute(array(
            ':pk_projectid' => 4517,
            ':pk_kandidatid' => $kandidat_id));	

        addToLogsStatusPrijave(NULL, 4517, 1, $kandidat_id, 1);
    //DODAVANJE U PROJEKAT I LSP

    $datum_prijave_date = DateTime::createFromFormat('M j, Y @ g:i A', $datum_prijave);
    $datum_prijave_formattedDate = $datum_prijave_date->format('d.m.Y H:i:s');

    //BILJESKE:
        $biljeska = "<table>";
        $biljeska .= "<tr><td>Originalni datum prijave: </td><td>".$datum_prijave_formattedDate."</td></tr>";
        $biljeska .= "<tr><td>Certifikat: </td><td>".$certifikat."</td></tr>";
        $biljeska .= "<tr><td>Da li imate vizu u Njemačkoj? </td><td>".$viza_u_njem."</td></tr>";
        $biljeska .= "<tr><td>Kad Vam ističe viza? </td><td>".$istek_vize."</td></tr>";
        $biljeska .= "<tr><td>Koliko godina zivite u Njemackoj? </td><td>".$godine_u_njem."</td></tr>";
        $biljeska .= "<tr><td>Grad boravka u DE </td><td>".$grad_boravka."</td></tr>";
        $biljeska .= "<tr><td>3 grada želja gdje bi radili </td><td>".$tri_grada_zelja."</td></tr>";
        $biljeska .= "<tr><td>Da li odgovara terenski radi 5 dana? </td><td>".$terenski_rad."</td></tr>";
        $biljeska .= "<tr><td>Vozačka - država i kategorija </td><td>".$vozacka_input."</td></tr>";
        $biljeska .= "<tr><td>Ako nije EU pass, koja viza </td><td>".$koja_viza."</td></tr>";
        $biljeska .= "<tr><td>nostrifikacija diplome (koje je zvanje dobio) </td><td>".$nost_diplome."</td></tr></table>";

        $query_biljeske = $db->prepare("
                        INSERT INTO idk_notes
                            (note_txt, note_datetime, note_group, note_dataid, note_employeeid)
                        VALUES
                            (:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
        
        $query_biljeske->execute(array(
                    ':note_txt' => $biljeska,
                    ':note_datetime' => date('Y-m-d H:i:s'),
                    ':note_group' => 2,
                    ':note_dataid' => $kandidat_id,
                    ':note_employeeid' => 67
        ));
    //BILJESKE:
    
    echo "<br/>Kandidat unešen!<br/><hr>";
    
}
?>