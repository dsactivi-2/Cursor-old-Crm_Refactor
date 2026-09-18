<?php

include('includes/function.php');
include('includes/connect.php');

if(isset($_REQUEST["nalog_id"]))
    $nalog_id 			= $_REQUEST["nalog_id"];
if(isset($_REQUEST["bar_id"]))
    $bar_id 			= $_REQUEST["bar_id"];
if(isset($_REQUEST["type"]))
    $type				= $_REQUEST["type"];

Global $db;
$languageUser = getLanguageForUser($userId);
include("includes/language/language.php");

if($bar_id == 3){
    $search_like = "- Intervju";
    $additional_condition = "";
    $order_query = " ORDER BY pca.pap_date DESC, pca.pca_time DESC";
    $theadhs_mail_phone = "";
    $theads_school_nationality = "<td> Land </td>
                                <td> Staatsangehörigkeit </td>";
}
if($bar_id == 4){
    $search_like = "- Ugovor";
    $additional_condition = "OR pro.project_name LIKE ('%Kandidati poceli sa radom') OR pro.project_name LIKE ('%Zavrseni kandidati')";
    $order_query = " ORDER BY pca.pap_date DESC, pca.pca_time DESC";
    $theadhs_mail_phone = "<td> E-Mail </td><td> Telefon </td>";
    $theads_school_nationality = "<td> Land </td>
                                <td> Staatsangehörigkeit </td>";
}

$query_get_candidate_ids = $db -> prepare("
    SELECT kan.kandidat_id 
    FROM idk_kandidati kan
    JOIN idk_project_kandidati pk
    ON pk.pk_kandidatid = kan.kandidat_id
    JOIN idk_projects pro
    ON pro.project_id = pk.pk_projectid
    WHERE (pro.project_name LIKE ('%".$search_like."%') ".$additional_condition.")  
    AND pro.project_nalogid IN (".$nalog_id.")
    AND kan.kandidat_status != 3
");

$query_get_candidate_ids -> execute();

$candidate_ids = array();
while($row_get_candidate_ids = $query_get_candidate_ids->fetch()){
    array_push($candidate_ids, $row_get_candidate_ids['kandidat_id']);
}
// $candidate_ids = array('162451');
$candidate_ids = implode(',',$candidate_ids);

$add_to_select .= ",xml1.ke_naziv_kvalifikacije,xml1.ke_naziv_kvalifikacije_de,xml1.ss_naziv_de,xml2.ke_grad, xml2.ke_drzava_for_translate, xml2.ke_drzava";

// DODATI U QUERYU ISPOD DRZAVU ZA PREVOD
$add_to_join .= "
    LEFT JOIN (
        SELECT sqxml1.ke_kandidat_id, sqxml1.ke_naziv_kvalifikacije_de, sqxml1.ke_naziv_kvalifikacije, sqxmlss.ss_naziv_de
        FROM idk_kandidat_edukacija sqxml1
        LEFT JOIN idk_skole_smjerovi sqxmlss
        ON sqxml1.ke_smjer_id = sqxmlss.ss_id
        WHERE sqxml1.ke_id IN (
            SELECT max(ssqxml1.ke_id)
            FROM idk_kandidat_edukacija ssqxml1
            WHERE ssqxml1.ke_kandidat_id IN (
                    ".$candidate_ids."
            )
            AND ssqxml1.ke_prikaz_pp = 1
            GROUP BY(ssqxml1.ke_kandidat_id)
        )
    ) xml1
    ON xml1.ke_kandidat_id = sq_kan.kandidat_id
    LEFT JOIN (
        SELECT sqxml2.ke_kandidat_id, sqxml2.ke_grad, sqxml2.ke_drzava,
            CASE WHEN sqxml2.ke_drzava IN ('BiH', 'Bosna i Hercegovina', 'Bosna', 'Bosna i Herzegovina', 'Republika Srpska', 'B i H', 'Bosnien und Herzegowina')
                THEN 'Bosna i Hercegovina'
                WHEN sqxml2.ke_drzava IN ('Hrvatska', 'Kroatien', 'Republika Hrvatska')
                THEN 'Hrvatska'
                WHEN sqxml2.ke_drzava IN ('Njemačka', 'Deutschland', 'Nemačka')
                THEN 'Njemačka'
                WHEN sqxml2.ke_drzava IN ('Srbija', 'Republika Srbija', 'Serbia', 'R Srbija', 'Serbien')
                THEN 'Srbija'
                WHEN sqxml2.ke_drzava IN ('Crna Gora', 'Montenegro')
                THEN 'Crna Gora'
                WHEN sqxml2.ke_drzava IN ('Makedonija', 'Macedonia', 'Sjeverna Makedonija')
                THEN 'Makedonija'
                WHEN sqxml2.ke_drzava IN ('Albanija', 'Austrija', 'Bugarska', 'Danska', 'Italija', 'Kosovo', 'Mađarska', 'Slovenija', 'Švicarska')
                THEN sqxml2.ke_drzava
                ELSE 'Nije poznato'
            END AS ke_drzava_for_translate
        FROM idk_kandidat_edukacija sqxml2
        WHERE sqxml2.ke_kandidat_id IN (
            ".$candidate_ids."
        )
        AND sqxml2.ke_vrsta_obrazovanja LIKE 'srednje'
        AND sqxml2.ke_prikaz_pp = 1
        AND sqxml2.ke_grad IS NOT NULL
        GROUP BY sqxml2.ke_kandidat_id
        ORDER BY sqxml2.ke_id
    )xml2
    ON xml2.ke_kandidat_id = sq_kan.kandidat_id
";


$sql = "				
    SELECT 
        CASE 
            WHEN sq_kan.kandidat_drzavljanstvo LIKE 'Bosanskohercegovačko' THEN 'Bosnische'
            WHEN sq_kan.kandidat_drzavljanstvo LIKE 'Srpsko' THEN 'Serbische'
            WHEN sq_kan.kandidat_drzavljanstvo LIKE 'Hrvatsko' THEN 'Kroatische'
            WHEN sq_kan.kandidat_drzavljanstvo LIKE 'Njemačko' THEN 'Deutsche'
            WHEN sq_kan.kandidat_drzavljanstvo LIKE 'Kosovo' THEN 'Kosovarische'
            ELSE ''
        END AS drzavljanstvo_drzava,
        CONCAT(DATE_FORMAT(pca.pap_date, '%e.%c.%Y'), ', ', DATE_FORMAT(pca.pca_time, '%H:%i'), ' - ', pca.pap_city) AS termin_grad,
        sq_kan.kandidat_pbroj, sq_kan.kandidat_grad, sq_kan.kandidat_full_name, sq_kan.kandidat_adresa,  sq_kan.kandidat_email, sq_kan.kandidat_mobitel, sq_kan.kandidat_datumrodjenja ".$add_to_select."
    FROM (
        SELECT  kan.kandidat_pbroj, kan.kandidat_grad, CONCAT (kan.kandidat_ime, ' ', kan.kandidat_prezime) AS kandidat_full_name, kan.kandidat_adresa, kan.kandidat_email, kan.kandidat_mobitel, kan.kandidat_datumrodjenja, kan.kandidat_pp_plata, kan.kandidat_pp_razlog_odbijanja, kan.kandidat_vozacka_kategorija, kan.kandidat_id, kan.kandidat_ppa_partner_id, kan.kandidat_check, kan.kandidat_pp_datum_prihvatanja, kan.kandidat_pp_lokacija, kan.kandidat_pp_pozicija,  kan.kandidat_status_prijave, kan.kandidat_drzavljanstvo
        FROM idk_kandidati kan
        WHERE kan.kandidat_id IN (".$candidate_ids.")
    ) sq_kan
    LEFT JOIN (
        SELECT sq_kj.kj_id, sq_kj.kj_slusanje, sq_kj.kj_kandidatid
        FROM(
            SELECT ssq_kj.kj_id, ssq_kj.kj_slusanje, ssq_kj.kj_kandidatid
            FROM idk_kandidat_jezici ssq_kj
            WHERE ssq_kj.kj_kandidatid IN (".$candidate_ids.")
            AND ssq_kj.kj_naziv = 'Njemacki'
            ORDER BY ssq_kj.kj_slusanje DESC
        ) sq_kj
        GROUP BY sq_kj.kj_kandidatid
    ) kj
    ON kj.kj_kandidatid = sq_kan.kandidat_id
    LEFT JOIN (
        SELECT ke.ke_kandidat_id, ke.ke_naziv, ke.ke_naziv_de, ke.ke_vrsta_obrazovanja, ke.ss_naziv, ke.ss_naziv_de
        FROM (
            SELECT ssq_ke.ke_kandidat_id, ssq_ke.ke_naziv, ssq_ke.ke_naziv_de, sq_skole.skola_tip_obrazovanja AS ke_vrsta_obrazovanja, ssq_sm.ss_naziv, ssq_sm.ss_naziv_de
            FROM idk_kandidat_edukacija ssq_ke 
            JOIN idk_skole_smjerovi ssq_sm 
            ON ssq_sm.ss_id = ssq_ke.ke_smjer_id
            JOIN idk_skole sq_skole
            ON sq_skole.skola_id = ssq_sm.ss_skola_id
            WHERE ssq_ke.ke_skola_id IS NOT NULL
            AND ssq_ke.ke_kandidat_id IN (".$candidate_ids.")
            AND ssq_ke.ke_prikaz_pp = 1
            ORDER BY ssq_ke.ke_vrsta_obrazovanja ASC
        ) ke
        GROUP BY ke.ke_kandidat_id
    ) sq_ke
    ON sq_ke.ke_kandidat_id = sq_kan.kandidat_id
    LEFT JOIN(
        SELECT sq_pca.pca_avg_rating, sq_pca.pca_kandidat_id, sq_pca.pca_appointment_id, sq_pca.pca_time, sq_ppa.pap_date, sq_ppa.pap_city
        FROM idk_pp_cand_appts sq_pca
        JOIN idk_pp_appointments sq_ppa
        ON sq_ppa.pap_id = sq_pca.pca_appointment_id
        WHERE sq_ppa.pap_nalog_id IN (".$nalog_id.")
        AND sq_pca.pca_kandidat_id IN (".$candidate_ids.")
    ) pca
    ON pca.pca_kandidat_id = sq_kan.kandidat_id
    ".$add_to_join."
    WHERE sq_kan.kandidat_id IS NOT NULL  
    ".$order_query."      
";
// echo $sql;

$query_get_export = $db -> prepare($sql);
$query_get_export -> execute();

echo '
    <table>
        <tr>
            <td></td>
            <td> Termin </td>
            <td> Vor- und Nachname </td>
            '.$theadhs_mail_phone.'
            <td> Straße </td>
            <td> PLZ </td>
            <td> Ort </td>
            <td> Geburtsdatum  </td>
            <td> Berufsbezeichnung </td>
            <td> Ausbildung abgeschlossen in: </td>
            '.$theads_school_nationality.'
        </tr>
';

    $candidate_full_name = "";
    $candidate_address = "";
    $candidate_dob = "";
    $candidate_vocation = "";
    $candidate_city = "";
    $candidate_telefon = "";
    $x = 1;
    while($row = $query_get_export -> fetch()){
        $termin_grad                    = $row['termin_grad'];
        $candidate_full_name            = $row['kandidat_full_name'];
        $candidate_email                = $row['kandidat_email'];
        $candidate_address_street       = $row['kandidat_adresa'];
        $candidate_dob                  = $row['kandidat_datumrodjenja'];
        $candidate_vocation             = $row['ss_naziv_de'];
        $candidate_city                 = $row['ke_grad'];
        $candidate_address_city         = $row['kandidat_grad'];
        $candidate_address_postal_code  = $row['kandidat_pbroj'];
        $candidate_telefon              = $row['kandidat_mobitel'];
        $drzavljanstvo_drzava           = $row['drzavljanstvo_drzava'];
        $ke_drzava                      = $row['ke_drzava'];
        $ke_drzava_for_translate        = trim($row['ke_drzava_for_translate']);
        $ke_drzava_translated           = $txtArray[$ke_drzava_for_translate][$languageUser];
        
        if($bar_id == 4){
            $tds_school_nationality = '<td>'.$ke_drzava_translated.'</td><td>'.$drzavljanstvo_drzava.'</td>';
            $tds_mail_phone = '<td>'.$candidate_email.'</td><td>'.$candidate_telefon.'</td>';
        }else{
            $tds_school_nationality = '<td>'.$ke_drzava_translated.'</td><td>'.$drzavljanstvo_drzava.'</td>';
            $tds_mail_phone = '';
        }
        echo '
            <tr>
                <td>'.$x.'</td>
                <td>'.$termin_grad.'</td>
                <td>'.$candidate_full_name.'</td>
                '.$tds_mail_phone.'
                <td>'.$candidate_address_street.'</td>
                <td>'.$candidate_address_postal_code.'</td>
                <td>'.$candidate_address_city.'</td>
                <td>'.date('d.m.Y', strtotime($candidate_dob)).'</td>
                <td>'.$candidate_vocation.'</td>
                <td>'.$candidate_city.'</td>
                '.$tds_school_nationality.'
            </tr>
        ';
        $x++;
    }

echo '</table>';

unset($txtArray);