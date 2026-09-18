<?php
include("includes/functions.php");
include("includes/common.php");
// Turn off all error reporting
error_reporting(0);
$getEmployeeStatus = getEmployeeStatus();

if(isset($_REQUEST["page"])) {
    $page = $_REQUEST["page"];
}else{
    header("Location: projects?page=list");
}
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Casting</title>

    <?php include('includes/head.php'); 
if (in_array($getUserIp, $getIpWhiteList)){ ?>

    <script src="<?php getSiteURL(); ?>js/sortable.min.js"></script>

</head>
<body>
	<header>
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('menu.php'); ?>
	</div>
	<div id="content">
		<div class="container-fluid">
            <?php
			switch ($page){

				case "open": 
                
                    $casting_id = $_GET['id'];
                    $query = $db->prepare("
									SELECT pap_city, pap_date, CONCAT(nalog_broj, ' - ', nalog_naziv) as nalog, pap_group_id
									FROM idk_pp_appointments
									JOIN idk_nalozi ON pap_nalog_id = nalog_id
									WHERE pap_id = :pap_id");

					$query->execute(array(
								':pap_id' => $casting_id));

					$row = $query->fetch();

					$nalog_name = $row['nalog'];
					$pap_city = $row['pap_city'];
					$pap_group_id = $row['pap_group_id'];
					$pap_date = date('d.m.Y', strtotime($row['pap_date'] ));
                    ?>
                    <div class="row">
                        <div class="col-xs-8">
                            <h1><i class="fa fa-stack-overflow idk_color_green" aria-hidden="true"></i> Casting: <b><?php echo $nalog_name.":</b>  ".$pap_date." - ".$pap_city; ?> </h1>
                        </div>
                        <div class="col-xs-12">
                            <hr />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="content_box">
                                <div class="row">
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            var table = $('#idk_table_kandidati').DataTable({
                                                
                                                dom: 'Qfrtip',

                                                // searchBuilder:{
                                                //     conditions:{
                                                //         num:{
                                                //             asdf
                                                //             }
                                                //         }
                                                //     }
                                                // ,

                                                

                                                responsive: true,

                                                "order": [[ 0, "desc" ]],

                                                "bAutoWidth": false,

                                                "aoColumns": [
                                                        { "width": "10%" },
                                                        { "width": "10%" },
                                                        { "width": "9%"  },

                                                        { "width": "5%", "bSortable": false },
                                                        { "width": "10%"/*,
                                                        
                                                        "render": function(data, type, row, meta) {
                                                                // return "<b>asdfsd"+ data +"</b>";
                                                                console.log(row);
                                                                // if(data == "" ){
                                                                //     data = 'nema';
                                                                //     return '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nema</span>';
                                                                //     // return "<b>null"+ data +"</b>";
                                                                    
                                                                // }
                                                                // else if(data == 0){
                                                                //     data = 'Neprovjeren';
                                                                //     console.log(row);
                                                                //     return '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Neprovjeren</span>';
                                                                //     // return "<b>nula"+ data +"</b>";,
                                                                // }
                                                                // else if(data == 1){
                                                                //     data = 'Provjeren';
                                                                    
                                                                //     return '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Provjeren</span>';
                                                                //     // return "<b>kec"+ data +"</b>";
                                                                // }
                                                            },*/
                                                        
                                                        },
                                                        { "width": "10%" },
                                                        { "width": "10%" },

                                                        { "width": "7%" },
                                                        { "width": "5%" },
                                                        { "width": "8%" },
                                                        { "width": "8%" },

                                                        { "width": "8%" },
                                                    ]
                                            });
                                        } );
                                    </script>
                                    <table id="idk_table_kandidati" class="display" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Agent</th>
                                                <th>Kandidat</th>
                                                <th style="border-right: solid 2px;" >Telefon</th>
                                                
                                                <th>Slika</th>
                                                <th class="text-center">Iskustvo</th>
                                                <th class="text-center">Škola</th>
                                                <th class="text-center" style="border-right: solid 2px;" >Jezik</th>

                                                <th class="text-center">TF status</th>
                                                <th class="text-center">Termin</th>
                                                <th class="text-center">Prva potvrda</th>
                                                <th class="text-center">Druga potvrda</th>

                                                <th class="text-center">Status dolaska</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $sql = "SELECT 
                                                        kandidat_id, 
                                                        CONCAT(employee_firstname, ' ', employee_lastname) as agent_full_name,
                                                        CONCAT(kandidat_ime, ' ', kandidat_prezime) as kandidat_full_name, kandidat_mobitel, kandidat_slika, 
                                                        kri.kri_prikaz_pp as kri_prikaz_pp, count(ke_id) as broj_skola, kj_slusanje,
                                                        tfs_name, pca_time, pca_id, l1.link_status as status_prvog, l2.link_status as status_drugog, tsr_status

                                                    FROM idk_kandidati
                                                    INNER JOIN idk_pp_cand_appts pca ON idk_kandidati.kandidat_id = pca.pca_kandidat_id
                                                    LEFT JOIN (
                                                                SELECT kri_id, kri_kandidat_id 
                                                                FROM idk_kandidat_radno_iskustvo 
                                                                GROUP BY kri_kandidat_id
                                                                ORDER BY kri_prikaz_pp DESC
                                                        ) kri_ord
                                                        ON kri_ord.kri_kandidat_id = kandidat_id
                                                    LEFT JOIN idk_kandidat_radno_iskustvo kri ON kri.kri_id = kri_ord.kri_id
                                                    LEFT JOIN idk_kandidat_edukacija ON kandidat_id = ke_kandidat_id AND ke_prikaz_pp = 1
                                                    LEFT JOIN idk_kandidat_jezici ON kandidat_id = kj_kandidatid AND kj_naziv = 'Njemački'
                                                    LEFT JOIN idk_appointment_invite_links l1 ON l1.counter_sent = 1 AND l1.candidate_id = kandidat_id AND l1.interview_id = pca_id
                                                    LEFT JOIN idk_appointment_invite_links l2 ON l2.counter_sent = 2 AND l2.candidate_id = kandidat_id AND l2.interview_id = pca_id
                                                    LEFT JOIN idk_employees ON employee_id = tf_reserved_agent
                                                    LEFT JOIN idk_tf_statusi ON tfs_id = kandidat_tf_status
                                                    LEFT JOIN idk_tf_stats_reservations ON tsr_candidate_id = kandidat_id AND tsr_interview_id = :pap_group_id
                                                    WHERE pca.pca_appointment_id = :casting_id 
                                                    GROUP BY pca.pca_kandidat_id";
                                            $query_get_kandidati = $db->prepare($sql);

                                            $query_get_kandidati->execute(array(
                                                ':casting_id' => $casting_id,
                                                ':pap_group_id' => $pap_group_id
                                            ));
                                            while($row_get_kandidati = $query_get_kandidati->fetch()){
                                                
                                                //CHECKED
                                                $kandidat_id = $row_get_kandidati['kandidat_id'];
                                                $agent_full_name = $row_get_kandidati['agent_full_name'];
                                                $kandidat_full_name = $row_get_kandidati['kandidat_full_name'];
                                                
                                                $kandidat_slika = $row_get_kandidati['kandidat_slika'];
                                                if($kandidat_slika == "none"){$kandidat_slika = "none.jpg";}
                                                
                                                $kandidat_mobitel = $row_get_kandidati['kandidat_mobitel'];
                                                
                                                //RADNO ISKUSTVO
                                                $kri_prikaz_pp = $row_get_kandidati['kri_prikaz_pp'];
                                                if(is_null($kri_prikaz_pp)){
                                                    $radno_iskustvo = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nema</span>';
                                                    $iskustvo_sorting = ' data-order="Nema"';
                                                }
                                                elseif($kri_prikaz_pp == 0){
                                                    $radno_iskustvo = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Neprovjeren</span>';
                                                    $iskustvo_sorting = ' data-order="Neprovjeren"';
                                                }
                                                elseif($kri_prikaz_pp == 1){
                                                    $radno_iskustvo = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Provjeren</span>';
                                                    $iskustvo_sorting = ' data-order="Provjeren"';
                                                }
                                                
                                                //SKOLE
                                                $broj_skola = $row_get_kandidati['broj_skola'];
                                                if($broj_skola > 0){
                                                    $skola = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Provjeren</span>';
                                                    $skola_sorting = ' data-order="Provjeren"';
                                                }else{
                                                    $skola = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Neprovjeren</span>';
                                                    $skola_sorting = ' data-order="Neprovjeren"';
                                                }
                                                
                                                //NJEMACKI JEZIK
                                                $njemacki = $row_get_kandidati['kj_slusanje'];
                                                if($njemacki == ""){
                                                    $njem_ispis = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nije unešeno</span>';
                                                    $jezik_sorting = ' data-order="Nije unešeno"';
                                                }else{
                                                    $njem_ispis = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$njemacki.'</span>';
                                                    $jezik_sorting = ' data-order="'.$njemacki.'"';
                                                }

                                                //TERMINI
                                                $tfs_name = $row_get_kandidati['tfs_name'];
                                                $pca_time = $row_get_kandidati['pca_time'];
                                                $pca_id = $row_get_kandidati['pca_id'];
                                                $status_prvog = $row_get_kandidati['status_prvog'];
                                                $status_drugog = $row_get_kandidati['status_drugog'];
                                                if($pca_id != null){
                                                    $full_termin = date('H:i', strtotime($pca_time));
                                                    $termin_ispis = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$full_termin.'</span>';
                                                    $termin_sorting = ' data-order="'.date('H:i', strtotime($pca_time)).'" ';
                                                    
                                                    //POTVRDE DOLASKA
                                                    [$confirmation_link_name1, $confirmation_color1, $confirmation_link_name2, $confirmation_color2] = getCastingConfirmationOutputByStatus($status_prvog, $status_drugog);
                                                    $potvrda_1 = '<span class= "label label-'.$confirmation_color1.' material-label material-label_'.$confirmation_color1.' material-label_xs main-container__column" style="width: 100%">'.$confirmation_link_name1.'</span>';
                                                    $potvrda_2 = '<span class= "label label-'.$confirmation_color2.' material-label material-label_'.$confirmation_color2.' material-label_xs main-container__column" style="width: 100%">'.$confirmation_link_name2.'</span>';
                                                    $potvrda1_sorting = ' data-order="'.$confirmation_link_name1.'"';
                                                    $potvrda2_sorting = ' data-order="'.$confirmation_link_name2.'"';
                                                }else{
                                                    $termin_ispis = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nema</span>';
                                                    $termin_sorting = '';
                                                    $potvrda_1 = '<span class= "label label-secondary material-label material-label_secondary material-label_xs main-container__column" style="width: 100%">Nepoznato</span>';
                                                    $potvrda_2 = '<span class= "label label-secondary material-label material-label_secondary material-label_xs main-container__column" style="width: 100%">Nepoznato</span>';
                                                    $potvrda1_sorting = ' data-order="Nepoznato"';
                                                    $potvrda2_sorting = ' data-order="Nepoznato"';
                                                }
                                                
                                                $tsr_status = $row_get_kandidati['tsr_status'];

                                                switch($tsr_status){
                                                    case 1:
                                                        $status_dolaska = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Došao</span>';
                                                        $status_dolaska_sorting = ' data-order="Došao"';
                                                    break;
                                                    case 2:
                                                        $status_dolaska = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nije Došao</span>';
                                                        $status_dolaska_sorting = ' data-order="Nije Došao"';
                                                    break;
                                                    case 3:
                                                        $status_dolaska = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Odustao</span>';
                                                        $status_dolaska_sorting = ' data-order="Odustao"';
                                                    break;
                                                    default:
                                                        $status_dolaska = '<span class="label label-secondary material-label material-label_secondary material-label_xs main-container__column">Nepoznato</span>';
                                                        $status_dolaska_sorting = ' data-order="Nepoznato"';
                                                }
                                                
                                                
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $agent_full_name; ?></td>
                                                    <td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_full_name; ?></a></td>
                                                    <td style="border-right: solid 2px;" ><?php echo $kandidat_mobitel; ?></td>
                                                    
                                                    <td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
                                                    <td class="text-center" <?php echo $iskustvo_sorting; ?>><?php echo $radno_iskustvo; ?></td>
                                                    <td class="text-center" <?php echo $skola_sorting; ?>><?php echo $skola; ?></td>
                                                    <td class="text-center" <?php echo $jezik_sorting; ?> style="border-right: solid 2px;" ><?php echo $njem_ispis; ?></td>

                                                    
                                                    <td class="text-center"><?php echo $tfs_name; ?></td>
                                                    <td class="text-center" <?php echo $termin_sorting; ?>><?php echo $termin_ispis; ?></td>
                                                    <td class="text-center" <?php echo $potvrda1_sorting; ?>><?php echo $potvrda_1; ?></td>
                                                    <td class="text-center" <?php echo $potvrda2_sorting; ?>><?php echo $potvrda_2; ?></td>
                                                    <td class="text-center" <?php echo $status_dolaska_sorting; ?>><?php echo $status_dolaska; ?></td>
                                                    
                                                    
                                                </tr>
                                                <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php

                break;

                case "openAll":
                    if (isset($_REQUEST["papGroupId"])) {
                        $papGroupId = $_REQUEST["papGroupId"];
                    }else{
                        $papGroupId = "";
                    }
                    ?>
                    <div class="row">
                        <div class="col-xs-2">
                            <h1><i class="fa fa-stack-overflow idk_color_green" aria-hidden="true"></i> Tekući Castinzi: </h1>
                        </div>
                        <div class="col-xs-10">
                            <div style="width:30%; border: 3px solid green;">
                                <select class="selectpicker" data-live-search="true" data-actions-box="true" id="select_casting">
                                    <option value = "" selected disabled hidden><b>Odaberite Casting</b></option>
                                    <?php 
                                        $query_get_castings = $db->prepare("
                                            SELECT ppaq_id, CONCAT(DATE_FORMAT(ppaq_start_date, '%d.%m.'),' - ', DATE_FORMAT(ppaq_end_date, '%d.%m'), DATE_FORMAT(ppaq_end_date, '.%Y'),'  ',cmp.company_name) as naziv_castinga 
                                            FROM idk_pp_appointment_groups 
                                            JOIN idk_nalozi ON idk_nalozi.nalog_id = papq_nalog_id
                                            JOIN idk_companies cmp ON cmp.company_id = idk_nalozi.kompanija_id
                                            WHERE papq_name IS NOT NULL AND papq_name != 'TEST' ORDER BY ppaq_id DESC;
                                        ");
                                        $query_get_castings->execute();
                                        while($row_casting = $query_get_castings->fetch()){
                                            ?>
                                                <option value="<?php echo $row_casting['ppaq_id']; ?>" <?php if($row_casting['ppaq_id'] == $papGroupId){echo "selected";} ?> ><?php echo $row_casting['naziv_castinga']; ?></option> 
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <hr />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="content_box">
                                <div class="row">
                                    <script>
                                        $(document).ready(function() {
                                            $('#select_casting').change(function(){
                                                location.href = "/casting_appointments?page=openAll&papGroupId=" + $('#select_casting').val();
                                            });
                                            var table = $('#idk_table_kandidati').DataTable({
                                                
                                                dom: 'Qfrtip',
                                                responsive: true,
                                                "order": [[ 0, "desc" ]],
                                                "bAutoWidth": false,
                                                "aoColumns": [
                                                    { "width": "8%" },
                                                    { "width": "8%" },
                                                    { "width": "8%"  },

                                                    { "width": "5%", "bSortable": false },
                                                    { "width": "9%" },
                                                    { "width": "9%" },
                                                    { "width": "9%" },

                                                    { "width": "7%" },
                                                    { "width": "8%" },
                                                    { "width": "5%" },
                                                    { "width": "8%" },
                                                    { "width": "8%" },

                                                    { "width": "8%" },
                                                ]
                                            });
                                        });
                                    </script>
                                    <table id="idk_table_kandidati" class="display" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Agent</th>
                                                <th>Kandidat</th>
                                                <th style="border-right: solid 2px;" >Telefon</th>
                                                
                                                <th>Slika</th>
                                                <th class="text-center">Iskustvo</th>
                                                <th class="text-center">Škola</th>
                                                <th class="text-center" style="border-right: solid 2px;" >Jezik</th>

                                                <th class="text-center">TF status</th>
                                                <th class="text-center">Dan</th>
                                                <th class="text-center">Termin</th>
                                                <th class="text-center">Prva potvrda</th>
                                                <th class="text-center">Druga potvrda</th>

                                                <th class="text-center">Status dolaska</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $sql = "SELECT 
                                                        kandidat_id, 
                                                        IF(kandidat_tf_status is null, CONCAT(e2.employee_firstname, ' ', e2.employee_lastname) , CONCAT(e1.employee_firstname, ' ', e1.employee_lastname)) as agent_full_name,
                                                        CONCAT(kandidat_ime, ' ', kandidat_prezime) as kandidat_full_name, kandidat_mobitel, kandidat_slika, 
                                                        kri.kri_prikaz_pp as kri_prikaz_pp, count(ke_id) as broj_skola, kj_slusanje,
                                                        IF(
                                                            kandidat_tf_status is null, 
                                                            (CASE 
                                                                WHEN tsr_status = 1 THEN 'Došao' 
                                                                WHEN tsr_status = 2 THEN 'Nije došao' 
                                                                WHEN tsr_status = 3 THEN 'Odustao' 
                                                                ELSE 'Nepoznato'
                                                            END), 
                                                            tfs1.tfs_name
                                                        ) as tf_status_name, 
                                                        pap_date, pca_time, pca_id, l1.link_status as status_prvog, l2.link_status as status_drugog, tsr_status

                                                    FROM idk_kandidati
                                                    INNER JOIN idk_pp_cand_appts pca ON idk_kandidati.kandidat_id = pca.pca_kandidat_id
                                                    LEFT JOIN (
                                                                SELECT kri_id, kri_kandidat_id 
                                                                FROM idk_kandidat_radno_iskustvo 
                                                                GROUP BY kri_kandidat_id
                                                                ORDER BY kri_prikaz_pp DESC
                                                        ) kri_ord
                                                        ON kri_ord.kri_kandidat_id = kandidat_id
                                                    LEFT JOIN idk_kandidat_radno_iskustvo kri ON kri.kri_id = kri_ord.kri_id
                                                    LEFT JOIN idk_kandidat_edukacija ON kandidat_id = ke_kandidat_id AND ke_prikaz_pp = 1
                                                    LEFT JOIN idk_kandidat_jezici ON kandidat_id = kj_kandidatid AND kj_naziv = 'Njemački'
                                                    LEFT JOIN idk_appointment_invite_links l1 ON l1.counter_sent = 1 AND l1.candidate_id = kandidat_id AND l1.interview_id = pca_id
                                                    LEFT JOIN idk_appointment_invite_links l2 ON l2.counter_sent = 2 AND l2.candidate_id = kandidat_id AND l2.interview_id = pca_id
                                                    LEFT JOIN idk_employees e1 ON e1.employee_id = tf_reserved_agent
                                                    LEFT JOIN idk_tf_statusi tfs1 ON tfs1.tfs_id = kandidat_tf_status
                                                    LEFT JOIN idk_tf_stats_reservations ON tsr_candidate_id = kandidat_id AND tsr_interview_id = :pap_group_id
                                                    LEFT JOIN idk_employees e2 ON e2.employee_id = tsr_agent_id
                                                    JOIN idk_pp_appointments pap on pap.pap_id = pca.pca_appointment_id
                                                    WHERE pap.pap_group_id = :pap_group_id 
                                                    GROUP BY pca.pca_kandidat_id";
                                            $query_get_kandidati = $db->prepare($sql);

                                            $query_get_kandidati->execute(array(
                                                ':pap_group_id' => $papGroupId
                                            ));
                                            while($row_get_kandidati = $query_get_kandidati->fetch()){
                                                
                                                //CHECKED
                                                $kandidat_id = $row_get_kandidati['kandidat_id'];
                                                $agent_full_name = $row_get_kandidati['agent_full_name'];
                                                $kandidat_full_name = $row_get_kandidati['kandidat_full_name'];
                                                
                                                $kandidat_slika = $row_get_kandidati['kandidat_slika'];
                                                if($kandidat_slika == "none"){$kandidat_slika = "none.jpg";}
                                                
                                                $kandidat_mobitel = $row_get_kandidati['kandidat_mobitel'];
                                                
                                                //RADNO ISKUSTVO
                                                $kri_prikaz_pp = $row_get_kandidati['kri_prikaz_pp'];
                                                if(is_null($kri_prikaz_pp)){
                                                    $radno_iskustvo = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nema</span>';
                                                    $iskustvo_sorting = ' data-order="Nema"';
                                                }
                                                elseif($kri_prikaz_pp == 0){
                                                    $radno_iskustvo = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Neprovjeren</span>';
                                                    $iskustvo_sorting = ' data-order="Neprovjeren"';
                                                }
                                                elseif($kri_prikaz_pp == 1){
                                                    $radno_iskustvo = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Provjeren</span>';
                                                    $iskustvo_sorting = ' data-order="Provjeren"';
                                                }
                                                
                                                //SKOLE
                                                $broj_skola = $row_get_kandidati['broj_skola'];
                                                if($broj_skola > 0){
                                                    $skola = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Provjeren</span>';
                                                    $skola_sorting = ' data-order="Provjeren"';
                                                }else{
                                                    $skola = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Neprovjeren</span>';
                                                    $skola_sorting = ' data-order="Neprovjeren"';
                                                }
                                                
                                                //NJEMACKI JEZIK
                                                $njemacki = $row_get_kandidati['kj_slusanje'];
                                                if($njemacki == ""){
                                                    $njem_ispis = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nije unešeno</span>';
                                                    $jezik_sorting = ' data-order="Nije unešeno"';
                                                }else{
                                                    $njem_ispis = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$njemacki.'</span>';
                                                    $jezik_sorting = ' data-order="'.$njemacki.'"';
                                                }

                                                //TERMINI
                                                $tfs_name = $row_get_kandidati['tf_status_name'];
                                                $pca_time = $row_get_kandidati['pca_time'];
                                                $pap_date = $row_get_kandidati['pap_date'];
                                                $pca_id = $row_get_kandidati['pca_id'];
                                                $status_prvog = $row_get_kandidati['status_prvog'];
                                                $status_drugog = $row_get_kandidati['status_drugog'];
                                                if($pca_id != null){
                                                    $date_formated = date('d.m.Y', strtotime($pap_date))." ";
                                                    $full_termin = date('H:i', strtotime($pca_time));
                                                    $date_ispis = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$date_formated.'</span>';
                                                    $date_sorting = ' data-order="'.$date_formated.'" ';
                                                    $termin_ispis = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$full_termin.'</span>';
                                                    $termin_sorting = ' data-order="'.$full_termin.'" ';
                                                    
                                                    //POTVRDE DOLASKA
                                                    [$confirmation_link_name1, $confirmation_color1, $confirmation_link_name2, $confirmation_color2] = getCastingConfirmationOutputByStatus($status_prvog, $status_drugog);
                                                    $potvrda_1 = '<span class= "label label-'.$confirmation_color1.' material-label material-label_'.$confirmation_color1.' material-label_xs main-container__column" style="width: 100%">'.$confirmation_link_name1.'</span>';
                                                    $potvrda_2 = '<span class= "label label-'.$confirmation_color2.' material-label material-label_'.$confirmation_color2.' material-label_xs main-container__column" style="width: 100%">'.$confirmation_link_name2.'</span>';
                                                    $potvrda1_sorting = ' data-order="'.$confirmation_link_name1.'"';
                                                    $potvrda2_sorting = ' data-order="'.$confirmation_link_name2.'"';
                                                }else{
                                                    $date_ispis = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nema</span>';
                                                    $date_sorting = '';
                                                    $termin_ispis = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nema</span>';
                                                    $termin_sorting = '';
                                                    $potvrda_1 = '<span class= "label label-secondary material-label material-label_secondary material-label_xs main-container__column" style="width: 100%">Nepoznato</span>';
                                                    $potvrda_2 = '<span class= "label label-secondary material-label material-label_secondary material-label_xs main-container__column" style="width: 100%">Nepoznato</span>';
                                                    $potvrda1_sorting = ' data-order="Nepoznato"';
                                                    $potvrda2_sorting = ' data-order="Nepoznato"';
                                                }
                                                
                                                $tsr_status = $row_get_kandidati['tsr_status'];

                                                switch($tsr_status){
                                                    case 1:
                                                        $status_dolaska = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Došao</span>';
                                                        $status_dolaska_sorting = ' data-order="Došao"';
                                                    break;
                                                    case 2:
                                                        $status_dolaska = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nije Došao</span>';
                                                        $status_dolaska_sorting = ' data-order="Nije Došao"';
                                                    break;
                                                    case 3:
                                                        $status_dolaska = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Odustao</span>';
                                                        $status_dolaska_sorting = ' data-order="Odustao"';
                                                    break;
                                                    default:
                                                        $status_dolaska = '<span class="label label-secondary material-label material-label_secondary material-label_xs main-container__column">Nepoznato</span>';
                                                        $status_dolaska_sorting = ' data-order="Nepoznato"';
                                                }
                                                
                                                
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $agent_full_name; ?></td>
                                                    <td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_full_name; ?></a></td>
                                                    <td style="border-right: solid 2px;" ><?php echo $kandidat_mobitel; ?></td>
                                                    
                                                    <td class="text-center"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></td>
                                                    <td class="text-center" <?php echo $iskustvo_sorting; ?>><?php echo $radno_iskustvo; ?></td>
                                                    <td class="text-center" <?php echo $skola_sorting; ?>><?php echo $skola; ?></td>
                                                    <td class="text-center" <?php echo $jezik_sorting; ?> style="border-right: solid 2px;" ><?php echo $njem_ispis; ?></td>

                                                    
                                                    <td class="text-center"><?php echo $tfs_name; ?></td>
                                                    <td class="text-center" <?php echo $date_sorting; ?>><?php echo $date_ispis; ?></td>
                                                    <td class="text-center" <?php echo $termin_sorting; ?>><?php echo $termin_ispis; ?></td>
                                                    <td class="text-center" <?php echo $potvrda1_sorting; ?>><?php echo $potvrda_1; ?></td>
                                                    <td class="text-center" <?php echo $potvrda2_sorting; ?>><?php echo $potvrda_2; ?></td>
                                                    <td class="text-center" <?php echo $status_dolaska_sorting; ?>><?php echo $status_dolaska; ?></td>
                                                    
                                                    
                                                </tr>
                                                <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php
                break;
            }
            ?>
        </div>
    </div>
</body>
</html>
<?php 
}else{			
    echo '
        <br/>
        <div class="alert material-alert material-alert_danger">
            <h4>NEMATE PRIVILEGIJE!</h4>
            <p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
            <br />
        </div>
';} ?>