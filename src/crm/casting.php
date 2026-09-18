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
    <title><?php getTitle(); ?></title>

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
                
                    $project_id = $_GET['id'];

					$query = $db->prepare("
									SELECT project_name, project_datetime, project_nalogid
									FROM idk_projects
									LEFT JOIN idk_employees ON idk_projects.project_pmanagerid = idk_employees.employee_id
									WHERE project_id = :project_id");

					$query->execute(array(
								':project_id' => $project_id));

					$row = $query->fetch();

					$project_name = $row['project_name'];
					$project_nalogid = $row['project_nalogid'];
					$project_datetime = date('d.m.Y.', strtotime($row['project_datetime']));

                    $query_kriterij_jezik = $db->prepare("SELECT nbp_njemacki_jezik FROM idk_nalozi_blokovi_prijave JOIN idk_projects ON nbp_nalogid = project_nalogid WHERE project_id = $project_id");
                        $query_kriterij_jezik->execute();
                        if($query_kriterij_jezik->rowCount() > 0){
                            $row_kriterij_jezik = $query_kriterij_jezik->fetch();
                            $kriterij_jezik = $row_kriterij_jezik['nbp_njemacki_jezik'];
                            switch($kriterij_jezik){
                                case "A1":
                                    $njem_uslov = "C1,C2,B2,B1,A2,A1";
                                break;
                                case "A2":
                                    $njem_uslov = "C1,C2,B2,B1,A2";
                                break;
                                case "B1":
                                    $njem_uslov = "C1,C2,B2,B1";
                                break;
                                case "B2":
                                    $njem_uslov = "C1,C2,B2";
                                break;
                                case "C1":
                                    $njem_uslov = "C1,C2";
                                break;
                                case "C2":
                                    $njem_uslov = "C2";
                                break;
                                default:
                                    $njem_uslov = "C1,C2,B2,B1,A2,A1,Bez znanja";
                            }
                        }else{
                            $njem_uslov = "C1,C2,B2,B1,A2,A1";
                        }
                        $njem_uslov_niz = explode(',',$njem_uslov);
					
                    ?>
                    <div class="row">
                        <div class="col-xs-8">
                            <h1><i class="fa fa-stack-overflow idk_color_green" aria-hidden="true"></i> Projekat: <?php echo $project_name; ?> </h1>
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
                                                
                                                responsive: true,

                                                "order": [[ 0, "desc" ]],

                                                "bAutoWidth": false,

                                                "aoColumns": [
                                                        { "width": "5%" },
                                                        { "width": "5%", "bSortable": false },
                                                        { "width": "15%" },

                                                        { "width": "10%" },
                                                        { "width": "10%" },
                                                        { "width": "15%" },

                                                        { "width": "12%" },
                                                        { "width": "12%" },
                                                        { "width": "8%" },

                                                        { "width": "8%" },
                                                    ]
                                            });
                                        } );
                                    </script>
                                    <table id="idk_table_kandidati" class="display" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Slika</th>
                                                <th>Ime i prezime</th>

                                                <th class="text-center">Iskustvo</th>
                                                <th class="text-center">Škola</th>
                                                <th class="text-center">Termin</th>

                                                <th class="text-center">Prva potvrda</th>
                                                <th class="text-center">Druga potvrda</th>
                                                <th class="text-center">Go Online</th>

                                                <th class="text-center">Njemacki</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $sql = "SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_slika, kri.kri_prikaz_pp as kri_prikaz_pp, count(ke_id) as broj_skola, kj_slusanje,
                                                            pap_date, pap_city, pca_time, pca_id, l1.link_status as status_prvog, l2.link_status as status_drugog, 
                                                    CASE 
                                                        WHEN kandidat_nalog_id = 301 
                                                            THEN (SELECT COUNT(kri_id) FROM idk_kandidat_radno_iskustvo WHERE kri_prikaz_pp = 1 AND kri_pozicija_en IS NULL AND kri_kandidat_id = kandidat_id) 
                                                            ELSE 0 
                                                        END AS broj_iskustava_za_prevesti, 
                                                    CASE 
                                                        WHEN kandidat_nalog_id = 301 
                                                            THEN (SELECT COUNT(ke_id) FROM idk_kandidat_edukacija JOIN idk_skole_smjerovi ON ke_smjer_id = ss_id WHERE ke_prikaz_pp = 1 AND ss_naziv_en IS NULL AND ke_kandidat_id = kandidat_id) 
                                                            ELSE 0 
                                                        END AS broj_skola_za_prevesti 
                                                    FROM idk_kandidati
                                                    INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid 
                                                    LEFT JOIN (
                                                                SELECT kri_id, kri_kandidat_id 
                                                                FROM idk_kandidat_radno_iskustvo 
                                                                GROUP BY kri_kandidat_id
                                                                ORDER BY kri_prikaz_pp DESC
                                                        ) kri_ord
                                                        ON kri_ord.kri_kandidat_id = kandidat_id
                                                    LEFT JOIN idk_kandidat_radno_iskustvo kri ON kri.kri_id = kri_ord.kri_id
                                                    LEFT JOIN idk_kandidat_edukacija ON kandidat_id = ke_kandidat_id AND ke_prikaz_pp = 1
                                                    LEFT JOIN idk_pp_cand_appts ON kandidat_id = pca_kandidat_id AND pca_status = 1 AND pca_appointment_id IN
                                                            ( SELECT pap_id FROM idk_pp_appointments WHERE pap_nalog_id = :pap_nalog_id)
                                                    LEFT JOIN idk_pp_appointments ON pca_appointment_id = pap_id 

                                                    LEFT JOIN idk_kandidat_jezici ON kandidat_id = kj_kandidatid AND kj_naziv = 'Njemački'
                                                    LEFT JOIN idk_appointment_invite_links l1 ON l1.counter_sent = 1 AND l1.candidate_id = kandidat_id AND l1.interview_id = pca_id
                                                    LEFT JOIN idk_appointment_invite_links l2 ON l2.counter_sent = 2 AND l2.candidate_id = kandidat_id AND l2.interview_id = pca_id
                                                    WHERE pk_projectid = :pk_projectid GROUP BY pk_kandidatid";
                                            $query_get_kandidati = $db->prepare($sql);

                                            $query_get_kandidati->execute(array(
                                                ':pk_projectid' => $project_id,
                                                ':pap_nalog_id' => $project_nalogid
                                            ));
                                            
                                            while($row_get_kandidati = $query_get_kandidati->fetch()){
                                                $go_online_checker = 0;
                                                $kandidat_id = $row_get_kandidati['kandidat_id'];
                                                $kandidat_ime = $row_get_kandidati['kandidat_ime'];
                                                $kandidat_prezime = $row_get_kandidati['kandidat_prezime'];
                                                $kandidat_slika = $row_get_kandidati['kandidat_slika'];
                                                if($kandidat_slika == "none"){$kandidat_slika = "none.jpg";}
                                                
                                                //RADNO ISKUSTVO
                                                $kri_prikaz_pp = $row_get_kandidati['kri_prikaz_pp'];
                                                $broj_iskustava_za_prevesti = $row_get_kandidati['broj_iskustava_za_prevesti'];
                                                if(is_null($kri_prikaz_pp))
                                                $casting_column1 = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nema</span>';
                                                elseif($kri_prikaz_pp == 0)
                                                $casting_column1 = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Neprovjeren</span>';
                                                elseif($kri_prikaz_pp == 1){
                                                    if($project_nalogid == 301){
                                                        if($broj_iskustava_za_prevesti == 0){

                                                            $casting_column1 = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Provjeren</span>';
                                                        }else{
                                                            $casting_column1 = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">NEPREVEDENO <b>'.$broj_iskustava_za_prevesti.'</b></span>';
                                                        }
                                                    }else{

                                                        $casting_column1 = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Provjeren</span>';
                                                    }
                                                }
                                                
                                                //SKOLE
                                                $broj_skola = $row_get_kandidati['broj_skola'];
                                                $broj_skola_za_prevesti = $row_get_kandidati['broj_skola_za_prevesti'];
                                                if($broj_skola > 0){
                                                    if($broj_skola_za_prevesti > 0){
                                                        $casting_column2 = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">NEPREVEDNO '.$broj_skola_za_prevesti.'</span>';
                                                    }else{

                                                        $casting_column2 = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Provjeren</span>';
                                                        $go_online_checker++;
                                                    }
                                                }else{
                                                    $casting_column2 = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Neprovjeren</span>';
                                                }

                                                //TERMINI
                                                $pca_time = $row_get_kandidati['pca_time'];
                                                $pap_date = $row_get_kandidati['pap_date'];
                                                $pap_city = $row_get_kandidati['pap_city'];
                                                $pca_id = $row_get_kandidati['pca_id'];
                                                $status_prvog = $row_get_kandidati['status_prvog'];
                                                $status_drugog = $row_get_kandidati['status_drugog'];
                                                if($pca_id != null){
                                                    $full_termin = $pap_city.' '.date('d.m.y H:i', strtotime($pap_date.' '.$pca_time));
                                                    $casting_column3 = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$full_termin.'</span>';
                                                    $sorting_column3 = ' data-order="'.date('y.m.d H:i', strtotime($pap_date.' '.$pca_time)).'" ';
                                                    $go_online_checker++;

                                                    //POTVRDE DOLASKA
                                                    [$confirmation_link_name1, $confirmation_color1, $confirmation_link_name2, $confirmation_color2] = getCastingConfirmationOutputByStatus($status_prvog, $status_drugog);
                                                    $casting_column4 = '<span class= "label label-'.$confirmation_color1.' material-label material-label_'.$confirmation_color1.' material-label_xs main-container__column" style="width: 100%">'.$confirmation_link_name1.'</span>';
                                                    $casting_column5 = '<span class= "label label-'.$confirmation_color2.' material-label material-label_'.$confirmation_color2.' material-label_xs main-container__column" style="width: 100%">'.$confirmation_link_name2.'</span>';
                                                }else{
                                                    $casting_column3 = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nema</span>';
                                                    $sorting_column3 = '';
                                                    $casting_column4 = '<span class= "label label-secondary material-label material-label_secondary material-label_xs main-container__column" style="width: 100%">Nepoznato</span>';
                                                    $casting_column5 = '<span class= "label label-secondary material-label material-label_secondary material-label_xs main-container__column" style="width: 100%">Nepoznato</span>';
                                                }

                                                
                                                //GO ONLINE 
                                                if($go_online_checker == 2){
                                                    if(getModulePermission(8)){
                                                        $casting_column6 = '<a href="" data-toggle="modal" data-target="#modalGoOnline" data-kandidat_id="'.$kandidat_id.'" class="material-dropdown-menu__link idk_candidate_action_button idk_candidate_action_button_green goOnlineBtn" id="goOnlineBtn"><i class="fa fa-check" aria-hidden="true"></i></a>';
                                                        $sorting_column6 = 'data-order=1';
                                                    }else{
                                                        $casting_column6 = '';
                                                    }
                                                }else{
                                                    $casting_column6 = '<span title="Potrebno je provjeriti školu i dodati termin!"><a href="#" style="pointer-events: none;" data-kandidat_id="'.$kandidat_id.'" class="material-dropdown-menu__link idk_candidate_action_button idk_candidate_action_button_red " ><i class="fa fa-minus" aria-hidden="true"></i></a></span>';
                                                    $sorting_column6 = 'data-order=2';
                                                }

                                                //NJEMACKI JEZIK
                                                $njemacki = $row_get_kandidati['kj_slusanje'];
                                                if($njemacki == "")
                                                    $njemacki = "Nije unešeno";
                                                
                                                if(in_array($njemacki, $njem_uslov_niz)){
                                                    $njem_ispis = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$njemacki.'</span>';
                                                }else{
                                                    $njem_ispis = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">'.$njemacki.'</span>';
                                                }
                                                $casting_column7 = $njem_ispis;
                                                
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $kandidat_id; ?></td>
                                                    <td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
                                                    <td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime." ".$kandidat_prezime; ?></a></td>
                                                    
                                                    <td class="text-center"><?php echo $casting_column1; ?></td>
                                                    <td class="text-center"><?php echo $casting_column2; ?></td>
                                                    <td class="text-center" <?php echo $sorting_column3; ?>><?php echo $casting_column3; ?></td>
                                                    
                                                    <td class="text-center"><?php echo $casting_column4; ?></td>
                                                    <td class="text-center"><?php echo $casting_column5; ?></td>
                                                    <td class="text-center" <?php echo $sorting_column6; ?>><?php echo $casting_column6; ?></td>
                                                    
                                                    <td class="text-center"><?php echo $casting_column7; ?></td>
                                                    
                                                </tr>
                                                <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        $(".goOnlineBtn").click(function(){
                            var kandidat_id = $(this).data("kandidat_id");
                            $("#kandidat_id_go").val(kandidat_id);
                            $(this).css("pointer-events", "none");
                        });
                        
                    </script>
                    <!-- MODAL GO ONLINE -->
                    <div class="modal material-modal material-modal_success fade" id="modalGoOnline">
                        <div class="modal-dialog">
                            <div class="modal-content material-modal__content">
                                <div class="modal-header material-modal__header">
                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title material-modal__title">GO ONLINE</h4>
                                </div>
                                <div class="modal-body material-modal__body">
                                <form action="<?php getSiteURL(); ?>do?form=kandidat_go_online" method="POST">
                                        <div class="form-group">
                                            <div class="col-md-offset-2 col-sm-8">
                                                Ovom akcijom omogućujete prikaz ovog kandidata poslodavcu?
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="kandidat_id_go" id="kandidat_id_go">
                                    <input type="hidden" name="stari_project_go" id="stari_project_go" value="<?php echo $project_id; ?>">
                                    <input type="hidden" name="nalog_id_go" id="nalog_id_go" value="<?php echo $project_nalogid; ?>">
                                    <div class="modal-footer material-modal__footer">
                                        <button class="btn material-btn material-btn" data-dismiss="modal" id="zatvoriModal">Odustani</button>
                                        <button type="submit" class="btn btn-primary material-btn material-btn_success val_isk_da">Potvrdi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <script>
                        $("#modalGoOnline").on('hide.bs.modal', function () {
                            var kandidat_id = $("#kandidat_id_go").val()
                            $(".goOnlineBtn").each(function(){
                                var btn_kandidat_id = $(this).data("kandidat_id");
                                if(kandidat_id == btn_kandidat_id){
                                    $(this).css("pointer-events", "");
                                }
                            });
                        });
                    </script>


                <?php 
                break;

                case "intervju": 
                
                    $project_id = $_GET['id'];


					$query = $db->prepare("
									SELECT project_name, project_datetime, project_nalogid
									FROM idk_projects
									LEFT JOIN idk_employees ON idk_projects.project_pmanagerid = idk_employees.employee_id
									WHERE project_id IN (2610,2612)");

					$query->execute();

					$row = $query->fetch();

					$project_name = $row['project_name'];
					$project_nalogid = $row['project_nalogid'];
					$project_datetime = date('d.m.Y.', strtotime($row['project_datetime']));

                    $query_kriterij_jezik = $db->prepare("SELECT nbp_njemacki_jezik FROM idk_nalozi_blokovi_prijave JOIN idk_projects ON nbp_nalogid = project_nalogid WHERE project_id = $project_id");
                        $query_kriterij_jezik->execute();
                        if($query_kriterij_jezik->rowCount() > 0){
                            $row_kriterij_jezik = $query_kriterij_jezik->fetch();
                            $kriterij_jezik = $row_kriterij_jezik['nbp_njemacki_jezik'];
                            switch($kriterij_jezik){
                                case "A1":
                                    $njem_uslov = "C1,C2,B2,B1,A2,A1";
                                break;
                                case "A2":
                                    $njem_uslov = "C1,C2,B2,B1,A2";
                                break;
                                case "B1":
                                    $njem_uslov = "C1,C2,B2,B1";
                                break;
                                case "B2":
                                    $njem_uslov = "C1,C2,B2";
                                break;
                                case "C1":
                                    $njem_uslov = "C1,C2";
                                break;
                                case "C2":
                                    $njem_uslov = "C2";
                                break;
                                default:
                                    $njem_uslov = "C1,C2,B2,B1,A2,A1,Bez znanja";
                            }
                        }else{
                            $njem_uslov = "C1,C2,B2,B1,A2,A1";
                        }
                        $njem_uslov_niz = explode(',',$njem_uslov);
					
                    ?>
                    <div class="row">
                        <div class="col-xs-8">
                            <h1><i class="fa fa-stack-overflow idk_color_green" aria-hidden="true"></i> Projekat: <?php echo $project_name; ?> </h1>
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
                                                
                                                responsive: true,

                                                "order": [[ 0, "desc" ]],

                                                "bAutoWidth": false,

                                                "aoColumns": [
                                                        { "width": "5%" },
                                                        { "width": "5%", "bSortable": false },
                                                        { "width": "15%" },

                                                        { "width": "10%" },
                                                        { "width": "10%" },
                                                        { "width": "15%" },

                                                        { "width": "12%" },
                                                        { "width": "12%" },
                                                        { "width": "8%" },

                                                        { "width": "8%" },
                                                    ]
                                            });
                                        } );
                                    </script>
                                    <table id="idk_table_kandidati" class="display" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Slika</th>
                                                <th>Ime i prezime</th>

                                                <th class="text-center">Iskustvo</th>
                                                <th class="text-center">Škola</th>
                                                <th class="text-center">Termin</th>

                                                <th class="text-center">Prva potvrda</th>
                                                <th class="text-center">Druga potvrda</th>
                                                <th class="text-center">Projekt</th>

                                                <th class="text-center">Njemacki</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $sql = "SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_slika, kri.kri_prikaz_pp as kri_prikaz_pp, count(ke_id) as broj_skola, kj_slusanje,
                                                            pap_date, pap_city, pca_time, pca_id, l1.link_status as status_prvog, l2.link_status as status_drugog, pk_projectid 
                                                    FROM idk_kandidati
                                                    INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid 
                                                    LEFT JOIN (
                                                                SELECT kri_id, kri_kandidat_id 
                                                                FROM idk_kandidat_radno_iskustvo 
                                                                GROUP BY kri_kandidat_id
                                                                ORDER BY kri_prikaz_pp DESC
                                                        ) kri_ord
                                                        ON kri_ord.kri_kandidat_id = kandidat_id
                                                    LEFT JOIN idk_kandidat_radno_iskustvo kri ON kri.kri_id = kri_ord.kri_id
                                                    LEFT JOIN idk_kandidat_edukacija ON kandidat_id = ke_kandidat_id AND ke_prikaz_pp = 1
                                                    LEFT JOIN idk_pp_cand_appts ON kandidat_id = pca_kandidat_id AND pca_status = 1 AND pca_appointment_id IN
                                                            ( SELECT pap_id FROM idk_pp_appointments WHERE pap_nalog_id = :pap_nalog_id)
                                                    LEFT JOIN idk_pp_appointments ON pca_appointment_id = pap_id 

                                                    LEFT JOIN idk_kandidat_jezici ON kandidat_id = kj_kandidatid AND kj_naziv = 'Njemački'
                                                    LEFT JOIN idk_appointment_invite_links l1 ON l1.counter_sent = 1 AND l1.candidate_id = kandidat_id AND l1.interview_id = pca_id
                                                    LEFT JOIN idk_appointment_invite_links l2 ON l2.counter_sent = 2 AND l2.candidate_id = kandidat_id AND l2.interview_id = pca_id
                                                    WHERE pk_projectid IN(2610,2612) GROUP BY pk_kandidatid";
                                            $query_get_kandidati = $db->prepare($sql);

                                            $query_get_kandidati->execute(array(
                                                // ':pk_projectid' => $project_id,
                                                ':pap_nalog_id' => $project_nalogid
                                            ));
                                            
                                            while($row_get_kandidati = $query_get_kandidati->fetch()){
                                                $go_online_checker = 0;
                                                $kandidat_id = $row_get_kandidati['kandidat_id'];
                                                $kandidat_ime = $row_get_kandidati['kandidat_ime'];
                                                $kandidat_prezime = $row_get_kandidati['kandidat_prezime'];
                                                $kandidat_slika = $row_get_kandidati['kandidat_slika'];
                                                if($kandidat_slika == "none"){$kandidat_slika = "none.jpg";}
                                                
                                                //RADNO ISKUSTVO
                                                $kri_prikaz_pp = $row_get_kandidati['kri_prikaz_pp'];
                                                if(is_null($kri_prikaz_pp))
                                                $casting_column1 = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nema</span>';
                                                elseif($kri_prikaz_pp == 0)
                                                $casting_column1 = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Neprovjeren</span>';
                                                elseif($kri_prikaz_pp == 1)
                                                $casting_column1 = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Provjeren</span>';
                                                
                                                //SKOLE
                                                $broj_skola = $row_get_kandidati['broj_skola'];
                                                if($broj_skola > 0){
                                                    $casting_column2 = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Provjeren</span>';
                                                    $go_online_checker++;
                                                }else{
                                                    $casting_column2 = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Neprovjeren</span>';
                                                }

                                                //TERMINI
                                                $pca_time = $row_get_kandidati['pca_time'];
                                                $pap_date = $row_get_kandidati['pap_date'];
                                                $pap_city = $row_get_kandidati['pap_city'];
                                                $pca_id = $row_get_kandidati['pca_id'];
                                                $status_prvog = $row_get_kandidati['status_prvog'];
                                                $status_drugog = $row_get_kandidati['status_drugog'];
                                                if($pca_id != null){
                                                    $full_termin = $pap_city.' '.date('d.m.y H:i', strtotime($pap_date.' '.$pca_time));
                                                    $casting_column3 = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$full_termin.'</span>';
                                                    $sorting_column3 = ' data-order="'.date('y.m.d H:i', strtotime($pap_date.' '.$pca_time)).'" ';
                                                    $go_online_checker++;

                                                    //POTVRDE DOLASKA
                                                    [$confirmation_link_name1, $confirmation_color1, $confirmation_link_name2, $confirmation_color2] = getCastingConfirmationOutputByStatus($status_prvog, $status_drugog);
                                                    $casting_column4 = '<span class= "label label-'.$confirmation_color1.' material-label material-label_'.$confirmation_color1.' material-label_xs main-container__column" style="width: 100%">'.$confirmation_link_name1.'</span>';
                                                    $casting_column5 = '<span class= "label label-'.$confirmation_color2.' material-label material-label_'.$confirmation_color2.' material-label_xs main-container__column" style="width: 100%">'.$confirmation_link_name2.'</span>';
                                                }else{
                                                    $casting_column3 = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nema</span>';
                                                    $sorting_column3 = '';
                                                    $casting_column4 = '<span class= "label label-secondary material-label material-label_secondary material-label_xs main-container__column" style="width: 100%">Nepoznato</span>';
                                                    $casting_column5 = '<span class= "label label-secondary material-label material-label_secondary material-label_xs main-container__column" style="width: 100%">Nepoznato</span>';
                                                }

                                                $pk_projectid = $row_get_kandidati['pk_projectid'];
                                                if($pk_projectid == 2610)
                                                    $casting_column6 = "CASTING";
                                                else
                                                    $casting_column6 = "INTERVJU";

                                                //NJEMACKI JEZIK
                                                $njemacki = $row_get_kandidati['kj_slusanje'];
                                                if($njemacki == "")
                                                $njemacki = "Nije unešeno";
                                                
                                                if(in_array($njemacki, $njem_uslov_niz)){
                                                    $njem_ispis = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$njemacki.'</span>';
                                                }else{
                                                    $njem_ispis = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">'.$njemacki.'</span>';
                                                }
                                                $casting_column7 = $njem_ispis;
                                                
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $kandidat_id; ?></td>
                                                    <td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
                                                    <td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime." ".$kandidat_prezime; ?></a></td>
                                                    
                                                    <td class="text-center"><?php echo $casting_column1; ?></td>
                                                    <td class="text-center"><?php echo $casting_column2; ?></td>
                                                    <td class="text-center" <?php echo $sorting_column3; ?>><?php echo $casting_column3; ?></td>
                                                    
                                                    <td class="text-center"><?php echo $casting_column4; ?></td>
                                                    <td class="text-center"><?php echo $casting_column5; ?></td>
                                                    <td class="text-center"><?php echo $casting_column6; ?></td>
                                                    
                                                    <td class="text-center"><?php echo $casting_column7; ?></td>
                                                    
                                                </tr>
                                                <?php
                                            } ?>
                                        </tbody>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <?php 
                break;
            } ?>

        </div>
    </div>
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