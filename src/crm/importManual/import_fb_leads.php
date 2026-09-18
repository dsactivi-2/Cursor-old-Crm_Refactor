<?php
include ('fb_leads_srb.php');
include ('fb_leads_bih.php');
include ('../includes/functions.php');
$from = $_GET['from'] ?? null; 
$to = $_GET['to'] ?? null;
$visited_url = $_GET['link'] ?? null;
$country = $_GET['country'] ?? null;

// /importManual/import_fb_leads.php?from=0&to=1&link=2265&country=srb
if($country == "srb"){
    $json_niz = $json_niz_srb;
}elseif($country == "bih"){
    $json_niz = $json_niz_bih;
}else{
    echo "nema drzave";
    exit();
}
$novi_niz = json_decode ($json_niz, true ) ;

echo "<table>";

for ($i = $from; $i <= $to; $i++) {

    if ($i < 0 OR $i > count($novi_niz) - 1) {
        break;
    }

    //Za unos
    $ime                = trim($novi_niz[$i]['first_name']);
    $prezime            = trim($novi_niz[$i]['last_name']);
    $datum_rodjenja     = $novi_niz[$i]['date_of_birth'];
    $njem_jezik         = $novi_niz[$i]['nivo_njemackog'];
    $vozacka_format     = $novi_niz[$i]['vozacka_dozvola'];
    $radno_iskustvo_us  = $novi_niz[$i]['radno_iskustvo'];
    $drzavljanstvo      = $novi_niz[$i]['drzavljanstvo'];
    $telefon            = "+".$novi_niz[$i]['phone_number'];
    $naziv_smjera       = $novi_niz[$i]['završeno_obrazovanje/smjer:'];
    $smjer_ostalo       = $novi_niz[$i]['smjer_ostalo'];

    
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
        echo " IMA DUPLIH ".$number_of_users." ".$ime." ".$prezime."<hr>";
        continue;
    }
    
    if($vozacka_format != ""){
        if($vozacka_format != "ne"){
            $vozacka_dozvola = "Da";
            $vozacka_kategorija = $vozacka_format;
        }else{
            $vozacka_dozvola = "Ne";
            $vozacka_kategorija = null;
        }
    }else{
        $vozacka_dozvola = null;
        $vozacka_kategorija = null;
    }

    $datum_rodjenja_frmt = date('Y-m-d', strtotime($datum_rodjenja));

    //ISKUSTVO U STRUCI	
        if($radno_iskustvo_us != "ne"){
            $kandidat_iskustvo_u_struci = 1;
            $kandidat_iskustvo_u_struci_trajanje = $_POST['iskustvo_u_struci_trajanje'];
            switch($radno_iskustvo_us){
                case "da_-_manje_od_1_godine": 
                    $kandidat_iskustvo_u_struci_trajanje = 1;
                    break;
                case "da_-_više_od_1_godine":
                    $kandidat_iskustvo_u_struci_trajanje = 2;
                    break;
                case "da_-_više_od_2_godine":
                    $kandidat_iskustvo_u_struci_trajanje = 3;
                    break;
                case "da_-_više_od_3_godine":
                    $kandidat_iskustvo_u_struci_trajanje = 4;
                    break;
                case "da_-_više_od_4_godine":
                    $kandidat_iskustvo_u_struci_trajanje = 5;
                    break;
                case "da_-_više_od_5_godina":
                    $kandidat_iskustvo_u_struci_trajanje = 6;
                    break;
                default:
                    $kandidat_iskustvo_u_struci_trajanje = "nepoznato";
            }
        }else{
            $kandidat_iskustvo_u_struci = 0;
            $kandidat_iskustvo_u_struci_trajanje = null;
        }
    //ISKUSTVO U STRUCI

    
    //Add user to db
        $kandidat_check = md5(uniqid(rand(), true));
        $kandidat_datetime = date('Y-m-d H:i:s');
        $query = $db->prepare("
            INSERT INTO idk_kandidati
                ( kandidat_check, kandidat_ime, kandidat_prezime, kandidat_datumrodjenja, kandidat_mobitel, kandidat_visitedurl, kandidat_vozacka_dozvola, kandidat_vozacka_kategorija, kandidat_drzavljanstvo_vrsta, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_datetime, kandidat_prijava_na, kandidat_group, kandidat_status_prijave, kandidat_porijeklo, kandidat_iskustvo_u_struci, kandidat_iskustvo_u_struci_trajanje)
            VALUES
                (:kandidat_check,:kandidat_ime,:kandidat_prezime,:kandidat_datumrodjenja,:kandidat_mobitel,:kandidat_visitedurl,:kandidat_vozacka_dozvola,:kandidat_vozacka_kategorija,:kandidat_drzavljanstvo_vrsta,:kandidat_slika,:kandidat_status,:kandidat_status_messenger,:kandidat_datetime,:kandidat_prijava_na,:kandidat_group,:kandidat_status_prijave,:kandidat_porijeklo,:kandidat_iskustvo_u_struci,:kandidat_iskustvo_u_struci_trajanje)");

        $query->execute(array(
        ':kandidat_check' => $kandidat_check,
        ':kandidat_ime' => $ime,
        ':kandidat_prezime' => $prezime,
        ':kandidat_datumrodjenja' => $datum_rodjenja_frmt,
        ':kandidat_mobitel' => $telefon,
        ':kandidat_visitedurl' => $visited_url,
        ':kandidat_vozacka_dozvola' => $vozacka_dozvola,
        ':kandidat_vozacka_kategorija' => $vozacka_kategorija,
        ':kandidat_drzavljanstvo_vrsta' => $drzavljanstvo,
        ':kandidat_slika' => "none",
        ':kandidat_status' => 0,
        ':kandidat_status_messenger' => 0,
        ':kandidat_datetime' => $kandidat_datetime,
        ':kandidat_prijava_na' => "Automehanicar",
        ':kandidat_group' => 22,
        ':kandidat_status_prijave' => 6,
        ':kandidat_porijeklo' => 0,
        ':kandidat_iskustvo_u_struci' => $kandidat_iskustvo_u_struci,
		':kandidat_iskustvo_u_struci_trajanje' => $kandidat_iskustvo_u_struci_trajanje,
        ));

        $kandidat_id = $db->lastInsertId();
    //Add user to db

    // INSERT LOG STATUSA
        $query_log_status = $db->prepare("
            INSERT INTO idk_log_kandidat_statusi
                (lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_link_id)
            VALUES
                (:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_link_id)
        ");

        $query_log_status->execute(array(
            ':lks_kandidat_id' => $kandidat_id,
            ':lks_status_obrade' => 0,
            ':lks_status_messenger' => 0,
            ':lks_datetime' => $kandidat_datetime,
            ':lks_link_id' => $visited_url
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
            
            if($naziv_smjera == "ostalo"){
                $naziv_smjera = $smjer_ostalo;
            }
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
                
            } else {
                $smjer_id_post = null;
                $smjer_naziv = $naziv_smjera;
                $skola_id_post = null;
                $skola_naziv = "nije pronađeno";

                $biljeska = "Kandidat na prijavi unio završeno obrazovanje: ".$naziv_smjera." .";

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
            }
        }else{
            $smjer_naziv = "nista nije uneseno";
            $skola_naziv = "nista nije uneseno";
        }
        
    //TRAŽENJE SMJERA NA OSNOVU NAZIVA

    
    //DODAVANJE U PROJEKAT I LSP
        $query_project = $db->prepare("
            INSERT INTO idk_project_kandidati
                (pk_projectid, pk_kandidatid)
            VALUES
                (:pk_projectid, :pk_kandidatid)");

        //RANDSTAD BOT - ispunjava uslove 4647
        $query_project->execute(array(
            ':pk_projectid' => 4647,
            ':pk_kandidatid' => $kandidat_id));	

        addToLogsStatusPrijave(NULL, 4647, 6, $kandidat_id, 1);
    //DODAVANJE U PROJEKAT I LSP
    
    // echo "<br/>Kandidat unešen!<br/><hr>";

    echo 
    "<tr>
        <td>".$i."</td>
        <td>".$ime."</td>
        <td>".$prezime."</td>
        <td>".$datum_rodjenja_frmt."</td>
        <td>".$vozacka_dozvola."</td>
        <td>".$vozacka_kategorija."</td>
        <td>".$radno_iskustvo_us."</td>
        <td>".$kandidat_iskustvo_u_struci."</td>
        <td>".$kandidat_iskustvo_u_struci_trajanje."</td>
        <td>".$drzavljanstvo."</td>
        <td>".$telefon."</td>
        <td>".$njem_jezik."</td>
        <td>".$skola_naziv."</td>
        <td>".$smjer_naziv."</td>
    
    </tr>";
    
}

echo "</table>";
?>