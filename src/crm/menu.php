<?php

$employee_status = explode( ',' , getEmployeeStatus());
$employee_supervizor = explode( ',' , getEmployeeSupervizor());
$employee_warehouse = getEmployeWarehouse();
/* privremeno rješnje za automatsko prebacivanje jezika gospodinu Bongu il Bohmu il Bomu nezz */
if(in_array("11",$employee_status)) $putanja_jezik = "lang/de.php";
else $putanja_jezik = "lang/bs.php";
include($putanja_jezik);
/* privremeno rješenje ends */
// var_dump($employee_status);
// var_dump($employee_supervizor);
/*
Statusi:
	1  	Administrator
	2  	Projekt Mendadžer
	3 	Projekt Asistent
	4 	Obrada
	5  	Front Office
	6  	Tehnika
	7   Marketing
	8   Vanjski Saradnik
	9   Financije
	10 	First Call Agent
	11 	DAK
	12  Saradnik - Prevodioc
	13  Prevod
	14  Inkaso Agent
	15 	Dipl Agent
	16  Obrada Dipl
	17 	LILIUM
	18 	TASK FORCE
	19  Dysordian Dev
	20  Ama-Int Saradnik
	69 Jebena Markus Permisija
*/
//Za pravilnik da bude 8 dana START
if(isset($logged_employee_id)){
	$poslovniceOmoguceno = array(5,10);
	$pravilnikRezultat = 0;
	$queryProvjeraPrav = $db->prepare("
		SELECT 
			employee_poslovnica
		FROM 
			idk_employees
		WHERE 
			employee_id = :employee_id
	");
	$queryProvjeraPrav->execute(array(
		':employee_id' => $logged_employee_id
	));
	
	if($queryProvjeraPrav->rowCount() != 0){
		$rowProvjeraPrav = $queryProvjeraPrav->fetch();
		$loggedEmployeePoslovnica = intval($rowProvjeraPrav["employee_poslovnica"]);
		if(in_array($loggedEmployeePoslovnica, $poslovniceOmoguceno)){
			$trenDanPravilnik = date("Y-m-d");
			$pocetakPravilnik = date("Y-m-d", strtotime("2021-11-01"));
			$krajPravilnik = date("Y-m-d", strtotime($pocetakPravilnik."+8 days" ));
			if($pocetakPravilnik <= $trenDanPravilnik AND $trenDanPravilnik <= $krajPravilnik){
				$pravilnikRezultat = 1;
			}
		}
	}
}
//Za pravilnik da bude 8 dana END 

?>
<style>
	::-webkit-scrollbar {
		width: 0px;
		background: transparent;
	}
</style>
<ul>

	<?php 
	if($pravilnikRezultat == 1){
		?>
		<li style = "background-color: brown;"><a  href="<?php getSiteURL(); ?>files/pravilnik/PravilnikJobstepInternationalSrbija.pdf"><i class="fa fa-info-circle"></i> <span>Pravilnik</span></a> </li>
		<?php 
	}

	if(getEmployeeStatus() == 55 OR getEmployeeStatus() == 0){
		
	}else{
		
		
		/*** AGENT - NOVO DIPL SUČELJE ***/
		if(in_array("15", $employee_status)){
			?>
			<!-- NASLOVNICA -->
			<li><a  href="<?php echo getSiteURL(); ?>"><i class="fa fa-dashboard"></i> <span><?php echo $txt_naslovnica; ?></span></a> </li>
			<!-- MOJI KANDIDATI -->
			<li><a  href="<?php echo getSiteURL(); ?>nostrifikacija_diploma?page=menadzer_pregled_kandidata&id_m=<?php echo $logged_employee_id; ?>"><i class="fa fa-users"></i>  Moji kandidati</a></li>
			<!-- SLJEDEĆI KANDIDAT -->
			<li><a  href="<?php echo getSiteURL(); ?>ajax_data.php?page=generateNextCandidateLink"><i class="fa fa-tasks"></i>  Sljedeći kandidat</a></li>
			<!-- NOVI KANDIDAT -->
			<li><a  href="<?php echo getSiteURL(); ?>dipl?page=add_new_kandidat&type=1"><i class="fa fa-pencil-square-o"></i> Novi kandidat</a></li> 
			
			<?php 
		}
		/*** KRAJ ***/
		
		/* NASLOVNICA */
		if(in_array("11",$employee_status)){
			/* DAK zaposlenik ima drugu naslovnicu*/
			?>
			<li><a  href="<?php getSiteURL(); ?>dak?page=list_for_dak"><i class="fa fa-dashboard"></i> <span><?php echo $txt_naslovnica; ?></span></a> </li>
			<?php 
		}else{
			if(!(in_array( "15" , $employee_status))){
				/* Svi ostali imaju ovu naslovnicu sem dipl agenata koji imaju svoju */
				?>
				<li><a  href="<?php getSiteURL(); ?>"><i class="fa fa-dashboard"></i> <span><?php echo $txt_naslovnica; ?></span></a> </li>
				<?php 
			}
		}
		/* TRANSFER KANDIDATA kojeg vide samo Adil, Admira Isic, Edina Kadusic i dev (Emir i Benjo) */
		if(getModulePermission(4)){
		//if($logged_employee_id == 173 OR $logged_employee_id == 190 OR $logged_employee_id == 186 OR $logged_employee_id == 67 OR $logged_employee_id == 134 OR $logged_employee_id == 461){
			?>
			<li><a  href="<?php getSiteURL(); ?>candidateTransferToTF/candidateTransferFIlter"><i class="fa fa-exchange"></i> <span><?php echo "Transfer kandidata"; ?></span></a> </li>
			<?php 
		}

		/* MARKETING EXPORT kojeg vide samo Belmin Fajic, Arman Canic, Adil i dev (Emir i Benjo) i Faris */
		if(getModulePermission(5)){
		// if($logged_employee_id == 234 OR $logged_employee_id == 25 OR $logged_employee_id == 67 OR $logged_employee_id == 134 OR $logged_employee_id == 173 OR $logged_employee_id == 461 OR $logged_employee_id == 207){
			?>
			<li><a  href="<?php getSiteURL(); ?>marketingCandidateExport/marketingCandidateFilter"><i class="fa fa-file"></i> <span><?php echo "Marketing export"; ?></span></a> </li>
			<?php 
		}

		/* TASK FORCE */
		if(in_array( "18" , $employee_status) OR in_array( "18" , $employee_supervizor)){
			?>
			<li class= "submenu">
				<a  href="#"><i class="fa fa-volume-control-phone"></i> <span>Task Force</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<li><a  href="<?php getSiteURL(); ?>tf_naslovnica"><i class="fa fa-tty"></i> <span>Telefonija</span></a> </li>
					<?php
					if(in_array("18", $employee_supervizor)){ 
						?>
						<li><a  href="<?php getSiteURL(); ?>casting_stats"><i class="fa fa-bar-chart"></i> <span>TF statistika</span></a> </li>
						<li><a  href="<?php getSiteURL(); ?>casting_appointments?page=openAll"><i class="fa fa-users"></i> <span>Tekući castinzi</span></a> </li>
						<li><a  href="<?php getSiteURL(); ?>tf_nalog_list"><i class="fa fa-tachometer"></i> <span>Aktivacija naloga</span></a> </li>
						<li><a  href="<?php getSiteURL(); ?>tf_nalog_list?page=obrada_postavke"><i class="fa fa-tachometer"></i> <span>Postavka TF obrade</span></a> </li>
						<li><a  href="<?php getSiteURL(); ?>tf_agent_stats"><i class="fa fa-tachometer"></i> <span>Statistika agenata</span></a> </li>
						<?php 
					} ?>
				</ul>
			</li>
			<?
		}
		
		/* STATISTIKE ZA DIPL INKASO */
		if(in_array( "1" , $employee_status) OR in_array("9", $employee_status) OR in_array("14", $employee_status)){
			?>
			<li><a  href="<?php getSiteURL(); ?>modul_statistike?page=open"><i class="fa fa-bar-chart"></i></i> <span>Statistike</span></a></li>
			<?php 
		}		
		
		/* KOMPANIJE */
		if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "10" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status)) OR (in_array( "19" , $employee_status))){
			?>
			<li class = "submenu">
				<a  href="#"><i class="fa fa-briefcase"></i> <span>Kompanije</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<li><a  href="<?php getSiteURL(); ?>companies?page=list"><i class="fa fa-briefcase"></i> <span>Kompanije</span></a></li>
					<li><a  href="<?php getSiteURL(); ?>companies?page=partner_companies"><i class="fa fa-briefcase"></i> <span>Kompanije sa partnera</span></a></li>
					
				</ul>
			</li>
			<?php 
		}

		/* FIRST CALL (za kompanije) */
		if((in_array( "10" , $employee_status)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "1" , $employee_status))){
			?>
			<li class = "submenu">
				<a  href="#"><i class="fa fa-volume-control-phone"></i> <span>First Call</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<li><a  href="<?php getSiteURL(); ?>sales?page=list&type=0&status=0">Novi</a></li>
					<li><a  href="<?php getSiteURL(); ?>sales?page=list&type=0&status=1">Aktivni</a></li>
					<li><a  href="<?php getSiteURL(); ?>sales?page=list&type=0&status=2">Arhiviran</a></li>
				</ul>
			</li>
			<?php 
		}

		/* PRODAJA (za kompanije) */
		if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "1" , $employee_status))){	?>
			<li class = "submenu">
				<a  href="#"><i class="fa fa-balance-scale"></i> <span>Prodaja</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<li><a  href="<?php getSiteURL(); ?>sales?page=list&type=1&status=0">Novi</a></li>
					<li><a  href="<?php getSiteURL(); ?>sales?page=list&type=1&status=1">Aktivni</a></li>
					<li><a  href="<?php getSiteURL(); ?>sales?page=list&type=1&status=2">Arhivirani</a></li>
				</ul>
			</li>
			<?php 
		}

		/* KANDIDATI (za posredovanje)*/
		if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status)) OR (in_array( "19" , $employee_status)) OR (in_array( "20" , $employee_status))){
			?>
			<li><a  href="<?php getSiteURL(); ?>kandidati?page=list_ajax"><i class="fa fa-user"></i> <span><?php echo $txt_Kandidati; ?></span></a> </li>
			<?php 
		}
		
		/* PARTNERI (sa aplikacije JS Partner App) */
		if((in_array( "1" , $employee_status))){
			?>
			<li class = "submenu">
				<a  href="#"><i class="fa fa-users"></i> <span><?php echo $txt_Partneri; ?></span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<li><a  href="<?php getSiteURL(); ?>partners/list">Pregled</a></li>
					<li><a  href="<?php getSiteURL(); ?>partners/finance">Financije</a></li>
				</ul>
			</li>
			<?php
		}else{
			if(in_array("19",$employee_status)){
				?>
				<li><a  href="<?php getSiteURL(); ?>partners/list"><i class="fa fa-users"></i><span><?php echo $txt_Partneri; ?></span></a> </li>
				<?php
			}
		} 
		
		/* DIPL modul */
		if(
			(in_array( "1" , $employee_status)) 
			OR (in_array( "2" , $employee_status)) 
			OR (in_array( "3" , $employee_status)) 
			OR (in_array( "7" , $employee_status)) 
			OR (in_array( "9" , $employee_status)) 
			OR (in_array( "19" , $employee_status))
			OR (in_array( "2" , $employee_supervizor)) 
			OR (in_array( "3" , $employee_supervizor)) 
			OR (in_array( "15" , $employee_supervizor)) 
		){
			?>
			<li class = "submenu">
				<a  href="#"><i class="fa fa-pencil-square-o"></i> <span>DIPL</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<?php 
					
						if(getZaposlenikDiplR($logged_employee_id) == 1 OR getZaposlenikDiplR($logged_employee_id) == 0 OR $logged_employee_id == 75 OR in_array( "1" , $employee_status)){
							if(in_array("19", $employee_status)){}else{
							?>
							<li><a  href="<?php getSiteURL(); ?>dipl?page=add_new_kandidat&type=1">Novi kandidat</a></li>
							<?php }
						}
					
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status))){ //  OR (in_array( "15" , $employee_status))
						
						?>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=lista_kandidata&type=1">Lead</a></li>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=lista_kandidata&type=2">Neuspješan Lead 1</a></li>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=lista_kandidata&type=3">Neuspješan Lead 3</a></li>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=lista_kandidata&type=4">Zainteresiran Lead</a></li>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=lista_kandidata&type=5">Nezainteresiran Lead</a></li>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=lista_kandidata&type=6">U obradi Lead</a></li>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=lista_kandidata&type=7">Aktivni</a></li>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=lista_kandidata&type=8">Završeni</a></li>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=lista_kandidata&type=9">Arhivirani</a></li>
						<?php
					}
					if((in_array( "1" , $employee_status)) OR (in_array( "16" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor))){
						?>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=listaObrada">OBRADA</a></li>
						<li><a  href="<?php getSiteURL(); ?>nostrifikacija_diploma?page=ustanove">Ustanove</a></li>
						<?php
					}
					if((in_array( "1" , $employee_status)) OR (in_array( "14" , $employee_status))){
						?>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=inkaso_pocetna">Inkaso</a></li>
						<?php
					}
					if((in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "1" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "15" , $employee_supervizor)) OR (in_array( "19" , $employee_status))){
						?>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=pregledDIPL&type=1">DIPL NP</a></li>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=lista_kandidata&type=12">Prioriteti</a></li>
						<?php
					}
					if((in_array( "1" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "16" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "15" , $employee_supervizor)) OR $logged_employee_id == 206 OR (in_array( "19" , $employee_status))){
						?>
						<li><a  href="<?php getSiteURL(); ?>dipl?page=lista_kandidata&type=10">Svi kandidati</a></li>	
						<?php 
					}
					if((in_array( "1" , $employee_status)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "15" , $employee_supervizor))){
						?>
						<li><a  href="<?php getSiteURL(); ?>dipl_agenti?page=list">DIPL agenti</a></li>
						<?php
					}
					if($logged_employee_id == 67 OR $logged_employee_id == 75){
						?>
						<li><a  href="<?php getSiteURL(); ?>reminders_obrada_dipl?page=croneSettings">Cron Obrada</a></li>
						<?php
					}
					?>
				</ul>
			</li>
			<?php 
		}	
		
		/* DAK modul */
		if((in_array( "1" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "5" , $employee_status)) OR (in_array( "8" , $employee_status)) OR (in_array( "2" , $employee_supervizor)) ){
			?>
			<li class = "submenu">
				<a  href="#"><i class="fa fa-hospital-o"></i> <span>DAK</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<li><a  href="<?php getSiteURL(); ?>dak?page=list&type=0"><?php echo $dak_menu_novi; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>dak?page=list&type=1"><?php echo $dak_menu_obrada; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>dak?page=list&type=2"><?php echo $dak_menu_poslan; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>dak?page=list&type=3"><?php echo $dak_menu_aktivan; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>dak?page=list&type=4"><?php echo $dak_menu_zavrsen; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>dak?page=list&type=5"><?php echo $dak_menu_storniran_aktivirani; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>dak?page=list&type=6"><?php echo $dak_menu_storniran; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>dak?page=list&type=7"><?php echo "Arhiviran"; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>dak?page=list&type=8"><?php echo "Na čekanju"; ?></a></li>
					<?php 
					if((in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor))){	
						?>
						<li><a  href="<?php getSiteURL(); ?>dak?page=stats"><?php echo $dak_menu_statistika; ?></a></li>
						<?php 
					}	?>
				</ul>
			</li><?php
		}
		
		/* DAK modul samo za DAK permisiju */
		if((in_array( "11" , $employee_status))){
			$types = array(3,5,6);
			?>
			<li class = "submenu">
				<a  href="#"><i class="fa fa-hospital-o"></i> <span>DAK</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<li><a  href="<?php getSiteURL(); ?>dak?page=list&type=2">Anfrage</a></li>
					<li><a  href="<?php getSiteURL(); ?>dak?page=list&types[]=3&types[]=5&types[]=6">Verzeichnes</a></li>
				</ul>
			</li>
			<?php
		}

		if((in_array( "1" , $employee_status)) OR (in_array( "2" , $employee_status)) ){
			?>
			<li class = "submenu">
				<a  href="#"><i class="fa fa-building-o"></i> <span>DVAG</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<li><a  href="<?php getSiteURL(); ?>partners/listdvag"><?php echo "Partneri"; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>companies?page=partner_companies"><?php echo "Kompanije"; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>dvag_kandidati?page=list"><?php echo "Kandidati"; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>partners/eligible_leads_to_assign"><?php echo "Leads"; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>positions?page=list"><?php echo "Lista zanimanja"; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>dvag_nalozi?page=list"><?php echo "Nalozi"; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>statistics-dvag/page.php"><?php echo "Statistika"; ?></a></li>
					<li><a  href="<?php getSiteURL(); ?>dvag_notifications?page=form"><?php echo "Notifications"; ?></a></li>
					<?php
					if($logged_employee_id == 134 || $logged_employee_id == 222 || $logged_employee_id == 354 OR $logged_employee_id == 173 OR $logged_employee_id == 67 OR  $logged_employee_id == 493 OR  $logged_employee_id == 516 OR $logged_employee_id == 526){
					?>
					<li><a  href="<?php getSiteURL(); ?>partners/new_account"> Account Management</a></li>
					<?php
					}
					?>
				</ul>
			</li><?php			
		}

		
		/* NALOZI */
		if(
			(in_array( "1" , $employee_status)) 
			OR (in_array( "2" , $employee_status)) 
			OR (in_array( "3" , $employee_status)) 
			OR (in_array( "4" , $employee_status)) 
			OR (in_array( "7" , $employee_status)) 
			OR (in_array( "9" , $employee_status)) 
			OR (in_array( "19" , $employee_status))
		){
				?>
			<li class="submenu"><a  href="#"><i class="fa fa-file-o" aria-hidden="true"></i> <span>Recruiting</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<li><a  href="<?php getSiteURL(); ?>nalozi?page=list"><i class="fa fa-minus-square"></i> <span>Aktivni nalozi</span></a> </li>
					<li><a  href="<?php getSiteURL(); ?>nalozi?page=list_finish"><i class="fa fa-check-square"></i> <span>Završeni nalozi</span></a> </li>
					<?php 
					if(in_array("1", $employee_status) OR $logged_employee_id == 43){
						?>
						<li><a  href="<?php getSiteURL(); ?>nalozi?page=list_arhivirani_nalozi"><i class="fa fa-trash"></i> <span>Arhivirani nalozi</span></a> </li>
						<?php 
					} 
					if(in_array("1", $employee_status) OR in_array("2", $employee_status) OR in_array("3", $employee_status)){
						?>
						<li><a  href="<?php getSiteURL(); ?>dashboardNaloga/companies.php"><i class="fa fa-tachometer"></i> <span>Dashboard naloga</span></a> </li>
						<?php
					} 
					if(in_array("1", $employee_status) OR in_array("2", $employee_status) OR in_array("3", $employee_status)){
						?>
						<li><a  href="<?php getSiteURL(); ?>odlasci.php"><i class="fa fa-plane"></i> <span>Odlasci</span></a> </li>
						<?php
					}
					if(in_array("1", $employee_status) OR in_array("2", $employee_status) OR in_array("3", $employee_status)){
						?>
						<li><a  href="<?php getSiteURL(); ?>naloziNP.php?page=list"><i class="fa fa-tachometer"></i> <span>Nalozi - NP</span></a> </li>
						<?php
					}
					if(in_array("1", $employee_status) OR in_array("2", $employee_status) OR in_array("3", $employee_status)){
						?>
						<li><a  href="<?php getSiteURL(); ?>nalozi?page=predefinisani_dokumenti"><i class="fa fa-file-text"></i> <span>Dokumenti</span></a> </li>
						<?php 
					}
					if(in_array("1", $employee_status) OR in_array("2", $employee_status)){
						?>
						<li><a  href="<?php getSiteURL(); ?>projekcijaForm.php"><i class="fa fa-calendar" aria-hidden="true"></i> <span>Projekcije odlaska</span></a> </li>
						<?php 
					}
					if(in_array("1", $employee_status) OR in_array("2", $employee_status) OR in_array("3", $employee_status) OR $logged_employee_id == 20 OR $logged_employee_id == 173 OR $logged_employee_id == 158){
						?>
						<!-- REMINDERI -->
						<li><a  href="<?php getSiteURL(); ?>dashboardRemindera/companiesReminders"><i class="fa fa-list-alt"></i><span> Reminderi</span></a></li>
						<?php 
					}
					if(in_array("1", $employee_status) OR in_array("2", $employee_status)){
						?>
						<li><a  href="<?php getSiteURL(); ?>pregledPrijava.php?page=main_list"><i class="fa fa-calendar" aria-hidden="true"></i> <span>Pregled prijava</span></a> </li>
						<?php 
					}
					?>			
				</ul>
			</li>
			<?php
		}
		
		/* ZADACI i DNEVNI IZVJEŠTAJI (nebitne stvari) i ZAPOSLENICI - ne smiju je vidjeti specijalne role: DAK saradnik, DIPL agent, Lilium saradnik, Ama-Int Saradnik */
		if(getEmployeeStatus() != 11 AND getEmployeeStatus() != 15 AND getEmployeeStatus() != 0 AND getEmployeeStatus() != 17 AND getEmployeeStatus() != 20){
			?>
			<!-- ZADACI -->
			<li class="submenu"><a  href="#"><i class="fa fa-tasks"></i> <span>Zadaci</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<li><a  href="<?php getSiteURL(); ?>tasks?page=list&sort=new">Moji zadaci</a></li>
					<?php if(!empty($employee_supervizor[0]) OR (in_array( "1" , $employee_status))){ ?>
					<li><a  href="<?php getSiteURL(); ?>tasks?page=list_all&sort=new">Svi zadaci</a></li>
					<?php } ?>
				</ul>
			</li>
			
			<!-- DNEVNI IZVJEŠTAJI -->
			<li class="submenu"><a  href="#"><i class="fa fa-line-chart"></i> <span>Dnevni izvještaji</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<li><a  href="<?php getSiteURL(); ?>employees-reports?page=list">Moji izvještaji</a></li>
					<?php if(!empty($employee_supervizor[0]) OR (in_array( "1" , $employee_status))){ ?>
					<li><a  href="<?php getSiteURL(); ?>employees-reports?page=list_all">Izvještaji zaposlenika</a></li>
					<?php } ?>
				</ul>
			</li>

			<!-- ZAPOSLENICI -->
			<li class="submenu"><a  href="#"><i class="fa fa-users" aria-hidden="true"></i> <span>Zaposlenici</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
				<li><a  href="<?php getSiteURL(); ?>employees?page=list"><i class="fa fa-users"></i> <span>Aktivni zaposlenici</span></a></li>
					<?php if(!empty($employee_supervizor[0]) OR (in_array( "1" , $employee_status))){ ?>
					<li><a  href="<?php getSiteURL(); ?>employees?page=list_arhiva"><i class="fa fa-check-square"></i> <span>Arhivirani zaposlenici</span></a> </li>
					<?php } ?>
				</ul>
			</li>
			<?php
		}

		/* FINANCIJE (za posredovanje + provizije + dipl dugovanja + agicap konverter) */
		if((in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){
			?>
			<li class="submenu"><a  href="#"><i class="fa fa-money" aria-hidden="true"></i> <span>Financije</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
				<!-- <li><a  href="<?php getSiteURL(); ?>finances?page=info"><i class="fa fa-money"></i> <span>Financije</span></a></li> -->
				<?php 
				if((in_array( "9" , $employee_status)) OR (in_array( "9" , $employee_supervizor))){  
					?>
					<li><a  href="<?php getSiteURL(); ?>finances?page=list"><i class="fa fa-user"></i> <span>Posredovanje - kandidati</span></a></li>
					<li><a  href="<?php getSiteURL(); ?>finances?page=list_nalozi"><i class="fa fa-file-o"></i> <span>Posredovanje - nalozi</span></a></li>
					<li><a  href="<?php getSiteURL(); ?>financesProjection.php?page=list"><i class="fa fa-bar-chart"></i> <span>Projekcija financija</span></a></li>
					<?php 
				} ?>
				<li><a  href="<?php getSiteURL(); ?>finances?page=dipl_odabir_drzave"><i class="fa fa-pencil-square"></i> <span>Financije DIPL</span></a> </li>
				<?php 
				if($logged_employee_id == 67 OR $logged_employee_id == 207 OR $logged_employee_id == 20 OR $logged_employee_id == 412 OR $logged_employee_id == 63 OR $logged_employee_id == 461 OR $logged_employee_id == 173){ ?>
					<li><a  href="<?php getSiteURL(); ?>provizije?page=postavke"><i class="fa fa-cogs"></i> <span>Postavke provizija</span></a> </li>
					<?php 
				}
				if($logged_employee_id == 67 OR $logged_employee_id == 75 OR $logged_employee_id == 20 OR $logged_employee_id == 412 OR $logged_employee_id == 50 OR $logged_employee_id == 63 OR $logged_employee_id == 207 OR $logged_employee_id == 223 OR $logged_employee_id == 173 OR $logged_employee_id == 158 OR $logged_employee_id == 461){ ?>
					<li><a  href="<?php getSiteURL(); ?>provizije?page=lista"><i class="fa fa-tasks"></i> <span>Lista provizija</span></a> </li>
					<?php 
				} 
				if($logged_employee_id == 213 OR $logged_employee_id == 75 OR $logged_employee_id == 67 OR $logged_employee_id == 158 OR $logged_employee_id == 461){?>
					<li><a  href="<?php getSiteURL(); ?>finances?page=dugovanjaPoStaromSistemu"><i class="fa fa-money"></i> <span>Dugovanja Stari</span></a></li>
					<?php 
				} 
				if(in_array("9", $employee_status) or $logged_employee_id == "222"){ ?>
					<li><a  href="/bankConverter/index.php" target="_blank"><i class="fa fa-retweet"></i> <span>Agicap Converter</span></a></li>
				<?php } ?>
				</ul>
			</li>
			<?php
		}

		/* GENERATOR LINKOVA ZA LILIUM SARADNIKE */
		if(getEmployeeStatus() == 17){ ?>
			<li><a  href="<?php getSiteURL(); ?>link_generator?page=list"><i class="fa fa-retweet"></i> <span><?php echo $txt_Generatorlinkova; ?></span></a> </li>
			<?php
		}

		/* POSTAVKE (svaštara); TIKETI; TUTORIJALI - ne smiju je vidjeti specijalne role: DAK saradnik, DIPL agent, Lilium saradnik, Ama-Int Saradnik */
		if(getEmployeeStatus() != 11 AND getEmployeeStatus() != 15 AND getEmployeeStatus() != 0 AND getEmployeeStatus() != 17 AND getEmployeeStatus() != 20){
			?>
			<li class="submenu"><a  href="#"><i class="fa fa-cogs" aria-hidden="true"></i> <span>Postavke</span> <span class="label label-important"><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>
				<ul>
					<?php
					/* GENERATOR LINKOVA */
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){
						?>
						<li><a  href="<?php getSiteURL(); ?>link_generator?page=list"><i class="fa fa-retweet"></i> <span><?php echo $txt_Generatorlinkova; ?></span></a> </li>
						<?php
					}

					/* KAMPANJE (za dipl) */
					if((in_array( "7" , $employee_status)) OR (in_array( "1" , $employee_status))){
						?>
						<li><a  href="<?php getSiteURL(); ?>kampanje?page=list"><i class="fa fa-retweet"></i> <span><?php echo $txt_Kampanje; ?></span></a> </li>
						<?php
					}

					/* EMAIL PREDLOŠCI - zastarjela stvar, zakomentarisana 26.06.2023 */
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "6" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "1" , $employee_status))){
						?>
						<!-- <li><a  href="<?php /*getSiteURL()*/; ?>settings?page=email"><i class="fa fa-envelope-o"></i> <?php /*echo $txt_MenuEmail*/; ?></a></li> -->
						<?php
					}

					/* OSTATAK POSTAVKI: grupe za kandidate, pozicije, škole, struke i timovi  */
					if((in_array( "2" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "1" , $employee_status))){
						?>
						<li><a  href="<?php getSiteURL(); ?>grupe_kandidata?page=list"><i class="fa fa-check-square"></i> <span><?php echo $txt_Grupekandidata; ?></span></a> </li>
						<li><a  href="<?php getSiteURL(); ?>positions?page=list"><i class="fa fa-id-badge"></i> <span>Pozicije kandidata</span></a> </li>
						<li><a  href="<?php getSiteURL(); ?>skole?page=pregled"><i class="fa fa-paperclip"></i> <span>Škole</span></a></li>
						<?php 
						if($logged_employee_id == 67 or $logged_employee_id == 222 or $logged_employee_id == 43 OR $logged_employee_id == 207 OR $logged_employee_id == 174 OR $logged_employee_id == 189 OR $logged_employee_id == 173 OR $logged_employee_id == 390 OR $logged_employee_id == 186){ ?>
							<li><a  href="<?php getSiteURL(); ?>skole?page=pregled_struke"><i class="fa fa-paperclip"></i> <span>Struke</span></a></li><?php 
						} ?>
						<?php
						if((in_array( "1" , $employee_status))){?>
							<li><a  href="<?php getSiteURL(); ?>timovi?page=list"><i class="fa fa fa-users"></i> <span>Timovi</span></a></li><?php
						}
					}

					if(in_array("1", $employee_status)){
						if(getModulePermission(1)){
							?>
							<li><a  href="<?php getSiteURL(); ?>module_permissions?page=list_all"><i class="fa fa-wrench"></i> <span>Specijalne permisije</span></a></li>
							<?php
						}
					}

					if(in_array("1", $employee_status)){
						if(getModulePermission(6)){
							?>
							<li><a  href="<?php getSiteURL(); ?>message_providers?page=list_all"><i class="fa fa-envelope"></i> <span>Provideri poruka</span></a></li>
							<?php
						}
					}

					/* LOG KORISNIKA (supervizori i administratori vide sve, dok ostali samo svoje) */
					if(!empty($employee_supervizor[0]) OR (in_array( "1" , $employee_status))){
						?>
						<li><a  href="<?php getSiteURL(); ?>logs?page=list_all"><i class="fa fa-file-o"></i> <span>Log korisnika</span></a></li><?php
					}else{
						?>
						<li><a  href="<?php getSiteURL(); ?>logs?page=list"><i class="fa fa-file-o"></i> <span>Log korisnika</span></a></li><?php
					} ?>
				</ul>
			</li>
			<!-- TIKETI -->
			<li><a  href="<?php getSiteURL(); ?>tiketi?page=list"><i class="fa fa-ticket"></i><span>Tiketi</span></a></li>
			
			<!-- TUTORIJALI -->
			<li><a  href="<?php getSiteURL(); ?>tutorial?page=list"><i class="fa fa-info-circle"></i><span>Tutorijali</span></a></li>
			<?php
		}

		/* SKLADIŠTE (za opremu) */
		if(intval($employee_warehouse) == 1 ){ 
			?>
			<li><a  href="<?php getSiteURL(); ?>warehouse?page=list"><i class="fa fa-archive"></i> <span>Skladište</span></a></li>
			<?php
		}

		if((in_array( "15" , $employee_status))){
			/************************************************************
				LINK ZA  **TIKETE** - ZA DIPL AGENTE I NJIHOVE VOĐE
			************************************************************/
			if(getEmployeeStatus() != 11 AND getEmployeeStatus() != 0){
				?>
				<li><a  href="<?php getSiteURL(); ?>tiketi?page=list"><i class="fa fa-ticket"></i><span>Tiketi</span></a></li>
				<?php
			}
		}
		if((in_array( "1" , $employee_status)) OR (in_array( "2" , $employee_status))){
				?>
				<li><a  href="<?php getSiteURL(); ?>partner_faq_bot"><i class="fa fa-file-text-o"></i><span>FAQ</span></a></li>
				<?php
		}
	} ?>










</ul>
