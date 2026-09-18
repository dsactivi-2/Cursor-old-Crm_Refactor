<?php
include("includes/functions.php");


if(isset($_REQUEST["prozor"])) {
		$prozor = $_REQUEST["prozor"];
	}else{
		header("Location: statistike?prozor=pregled_racuna");
	}
$team_id = getLoggedEmployeeTeam();
switch($prozor){

case "export_kandidata_u_linku":

	$link_id = $_POST['link_id'];
	$selectedis = $_POST['selectedis'];
	$idArray = explode(",",$selectedis);
	// var_dump($idArray);
	// exit();
	
	$query_urls = $db->prepare("
					SELECT lg_url
					FROM idk_link_generator
					WHERE lg_id = $link_id
					");

	$query_urls->execute();
	$url = $query_urls->fetch();
	
	$lg_url = $url['lg_url'];
?>
<div class="" style="height:0px;overflow:hidden;">
	<table class="table" id="CASTABLEmmds" style="100%;" class="display">
		<thead>
			<tr>
				<th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
				<th style="width:70px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Kandidat ID</th>
				<th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime</th>
				<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Grad</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Državljanstvo</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Njemački jezik</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum prijave</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Link prijave</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum apliciranja</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum termina</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Telefon</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Status prijave</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">E-mail</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Vozačka dozvola</th>
				<!--<th style="width:260px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Grupa</th>-->
			</tr>
		</thead>
		<tbody>
			<?php
			$sumCount = 1;
			foreach($idArray as $kandidat_id){
				
				$query = $db->prepare("
								SELECT kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_group, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo, kandidat_bracnostanje, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_slika, kandidat_datetime,kandidat_check, kandidat_vozacka_dozvola, kandidat_prijava_na, kandidat_visitedurl, kandidat_drzavljanstvo_vrsta, datum_termina, datum_aplikacije, cv_de, profile_de, kandidat_procjenatermina, kandidat_status
								FROM idk_kandidati
								WHERE kandidat_id = :kandidat_id");

				$query->execute(array(
							':kandidat_id' => $kandidat_id));

				$row = $query->fetch();

					$kandidat_ime = $row['kandidat_ime'];
					$kandidat_prezime = $row['kandidat_prezime'];
					$kandidat_spol = $row['kandidat_spol'];
					$kandidat_check = $row['kandidat_check'];
					$kandidat_djevojackoprezime = $row['kandidat_djevojackoprezime'];
					$kandidat_jmbg = $row['kandidat_jmbg'];
					$kandidat_mjestorodjenja = $row['kandidat_mjestorodjenja'];
					$kandidat_drzavarodjenja = $row['kandidat_drzavarodjenja'];
					$kandidat_drzavljanstvo = $row['kandidat_drzavljanstvo'];
					$kandidat_drzavljanstvo_vrsta = $row['kandidat_drzavljanstvo_vrsta'];
					$kandidat_group = $row['kandidat_group'];
					$kandidat_bracnostanje = $row['kandidat_bracnostanje'];
					$kandidat_adresa = $row['kandidat_adresa'];
					$kandidat_prijava_na = $row['kandidat_prijava_na'];
					$kandidat_grad = $row['kandidat_grad'];
					$kandidat_pbroj = $row['kandidat_pbroj'];
					$kandidat_drzava = $row['kandidat_drzava'];
					$kandidat_email = $row['kandidat_email'];
					$kandidat_vozacka_dozvola = $row['kandidat_vozacka_dozvola'];
					$kandidat_status = $row['kandidat_status'];
					$kandidat_drzava = $row['kandidat_drzava'];
					$kandidat_visitedurl = $row['kandidat_visitedurl'];
					$kandidat_datumrodjenja = date('d.m.Y.', strtotime($row['kandidat_datumrodjenja']));
					$kandidat_datetime = date('d.m.Y H:i', strtotime($row['kandidat_datetime']));
					$kandidat_datum_terminacheck = $row['datum_termina'];
					$kandidat_datum_aplikacijecheck = $row['datum_aplikacije'];
					$kandidat_procjenatermina = $row['kandidat_procjenatermina'];
					$kandidat_datum_termina = date('d.m.Y', strtotime($row['datum_termina']));
					if($kandidat_procjenatermina == 0){
						$kandidat_datum_termina = '<span class="label label-success material-label material-label_success main-container__column">'.$kandidat_datum_termina.'</span>';
					}else{
						$kandidat_datum_termina = '<span class="label label-danger material-label material-label_danger main-container__column">'.$kandidat_datum_termina.'</span>';
					}
					if($kandidat_datum_aplikacijecheck == null)
						$kandidat_datum_aplikacijecheck = "-";
					if($kandidat_datum_terminacheck == null)
						$kandidat_datum_terminacheck = "-";
			
					$query_group = $db->prepare("
									SELECT kg_id, kg_title, kg_date
									FROM idk_kandidati_grupe
									WHERE kg_id = :kg_id
									ORDER BY kg_id ASC
									");

					$query_group->execute(array(
						":kg_id" => $kandidat_group
					));

					$griup = $query_group->fetch();
						$kg_title = $griup['kg_title'];

					$query_jezik = $db->prepare("
									SELECT kj_govorna_interakcija
									FROM idk_kandidat_jezici
									WHERE kj_kandidatid = '$kandidat_id'
									");
					$query_jezik->execute();
					
					$jezik_row = $query_jezik->fetch();
					if($query_jezik->rowCount() > 0)
						$govorna_interakcija = $jezik_row['kj_govorna_interakcija'];
					else
						$govorna_interakcija = "-";
					
					$query_phone = $db->prepare("
									SELECT kki_podatak
									FROM idk_kandidat_kontakt_info
									WHERE kki_kandidat_id = '$kandidat_id' AND (kki_naziv = 'Mobilni' OR kki_naziv = 'mobilni')
									");
					$query_phone->execute();
					
					$phone_row = $query_phone->fetch();
					
					if($query_phone->rowCount() > 0)
						$telefon = $phone_row['kki_podatak'];
					else
						$telefon = "-";
					
					if($kandidat_status == 0){
						$kandidat_status_p = 'Na provjeri';
					}elseif($kandidat_status == 1){
						$kandidat_status_p = 'U obradi';
					}elseif($kandidat_status == 2){
						$kandidat_status_p = 'Obrađen';
					}elseif($kandidat_status == 3){
						$kandidat_status_p = 'Arhiviran';
					}else{
						$kandidat_status_p = "-";
					}
			
			?>
			<tr>
				<td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $sumCount++; ?></td>
				<td style="width:70px; text-align:center;padding:15px 15px;"><?php echo $kandidat_id; ?></td>
				<td style="width:250px; text-align:left;padding:15px 15px;" class="text-success"><a href="<?php getSiteUrl(); ?>/kandidati?page=open&id=<?php echo $kandidat_id;?>"><?php echo $kandidat_ime; ?> <?php echo $kandidat_prezime; ?></a></td>
				<td style="width:100px; text-align:left;padding:15px 15px;"><?php echo $kandidat_grad; ?></td>
				<td style="width:150px; text-align:left;padding:15px 15px;"><?php echo $kandidat_drzavljanstvo; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $govorna_interakcija; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $kandidat_datetime; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $lg_url; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $kandidat_datum_aplikacijecheck; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $kandidat_datum_terminacheck; ?></td>
				<td style="mso-number-format:'\@'; width:150px; text-align:center;padding:15px 15px;"><?php echo $telefon; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $kandidat_status_p; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $kandidat_email; ?></td>
				<td style="width:200px; text-align:center;padding:15px 15px;"><?php echo $kandidat_vozacka_dozvola; ?></td>
				<!--<td style="width:260px; text-align:left;padding:15px 15px;"><span class="label label-success material-label material-label_success material-label_xs main-container__column"><?php echo $kg_title; ?></span></td>-->
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<script>
	$(document).ready(function(){
		$("#CASTABLEmmds").table2excel({
			exclude: ".noExl",
			name: "kandidatiExport(<?php echo date("Y-m-d");?>)",
			filename: "Link: <?php echo $lg_url; ?>",
			fileext: ".xls"
		}); 	  
	})
	</script>	
</div>

<?php
	
break;

case "export_kandidata_u_projektu":
	$selectedis = $_POST['selectedis'];
	$idArray = $selectedis;
	// $idArray = explode(",",$selectedis);
	// var_dump($idArray);
	// exit();
?>
<div class="" style="height:0px;overflow:hidden;">
	<table class="table" id="CASTABLE_project" style="100%;" class="display">
		<thead>
			<tr>
				<th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
				<th style="width:70px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Kandidat ID</th>
				<th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime</th>
				<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Grad</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Državljanstvo</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Njemački jezik</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum prijave</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum apliciranja</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum termina</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Telefon</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Status prijave</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">E-mail</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Vozačka dozvola</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Adresa</th>
				<!--<th style="width:260px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Grupa</th>-->
			</tr>
		</thead>
		<tbody>
			<?php
			$sumCount = 1;
			foreach($idArray as $kandidat_id){
			//for($i=600; $i<774;$i++){
				//$kandidat_id = $idArray[$i];
				$query = $db->prepare("
								SELECT kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_group, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo, kandidat_bracnostanje, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_slika, kandidat_datetime,kandidat_check, kandidat_vozacka_dozvola, kandidat_vozacka_kategorija, kandidat_prijava_na, kandidat_visitedurl, kandidat_drzavljanstvo_vrsta, datum_termina, datum_aplikacije, cv_de, profile_de, kandidat_procjenatermina, kandidat_status, kandidat_mobitel
								FROM idk_kandidati
								WHERE kandidat_id = :kandidat_id");

				$query->execute(array(
							':kandidat_id' => $kandidat_id));

				$row = $query->fetch();

					$kandidat_ime = $row['kandidat_ime'];
					$kandidat_prezime = $row['kandidat_prezime'];
					$kandidat_spol = $row['kandidat_spol'];
					$kandidat_check = $row['kandidat_check'];
					$kandidat_djevojackoprezime = $row['kandidat_djevojackoprezime'];
					$kandidat_jmbg = $row['kandidat_jmbg'];
					$kandidat_mjestorodjenja = $row['kandidat_mjestorodjenja'];
					$kandidat_drzavarodjenja = $row['kandidat_drzavarodjenja'];
					$kandidat_drzavljanstvo = $row['kandidat_drzavljanstvo'];
					$kandidat_drzavljanstvo_vrsta = $row['kandidat_drzavljanstvo_vrsta'];
					$kandidat_group = $row['kandidat_group'];
					$kandidat_bracnostanje = $row['kandidat_bracnostanje'];
					$kandidat_adresa = $row['kandidat_adresa'];
					$kandidat_prijava_na = $row['kandidat_prijava_na'];
					$kandidat_grad = $row['kandidat_grad'];
					$kandidat_pbroj = $row['kandidat_pbroj'];
					$kandidat_drzava = $row['kandidat_drzava'];
					$kandidat_email = $row['kandidat_email'];
					$kandidat_vozacka_dozvola = $row['kandidat_vozacka_dozvola'];
					$kandidat_vozacka_kategorija = $row['kandidat_vozacka_kategorija'];
					$kandidat_status = $row['kandidat_status'];
					$kandidat_drzava = $row['kandidat_drzava'];
					$kandidat_visitedurl = $row['kandidat_visitedurl'];
					$kandidat_datumrodjenja = date('d.m.Y.', strtotime($row['kandidat_datumrodjenja']));
					$kandidat_datetime = date('d.m.Y H:i', strtotime($row['kandidat_datetime']));
					$kandidat_datum_terminacheck = $row['datum_termina'];
					$kandidat_datum_aplikacijecheck = $row['datum_aplikacije'];
					$kandidat_procjenatermina = $row['kandidat_procjenatermina'];
					$telefon = $row['kandidat_mobitel'];
					$kandidat_datum_termina = date('d.m.Y', strtotime($row['datum_termina']));
					if($kandidat_procjenatermina == 0){
						$kandidat_datum_termina = '<span class="label label-success material-label material-label_success main-container__column">'.$kandidat_datum_termina.'</span>';
					}else{
						$kandidat_datum_termina = '<span class="label label-danger material-label material-label_danger main-container__column">'.$kandidat_datum_termina.'</span>';
					}
					if($kandidat_datum_aplikacijecheck == null)
						$kandidat_datum_aplikacijecheck = "-";
					if($kandidat_datum_terminacheck == null)
						$kandidat_datum_terminacheck = "-";
			
					$query_group = $db->prepare("
									SELECT kg_id, kg_title, kg_date
									FROM idk_kandidati_grupe
									WHERE kg_id = :kg_id
									ORDER BY kg_id ASC
									");

					$query_group->execute(array(
						":kg_id" => $kandidat_group
					));

					$griup = $query_group->fetch();
						$kg_title = $griup['kg_title'];

					$query_jezik = $db->prepare("
									SELECT kj_govorna_interakcija
									FROM idk_kandidat_jezici
									WHERE kj_kandidatid = '$kandidat_id'
									");
					$query_jezik->execute();
					
					$jezik_row = $query_jezik->fetch();
					if($query_jezik->rowCount() > 0)
						$govorna_interakcija = $jezik_row['kj_govorna_interakcija'];
					else
						$govorna_interakcija = "-";
					
					if($kandidat_status == 0){
						$kandidat_status_p = 'Na provjeri';
					}elseif($kandidat_status == 1){
						$kandidat_status_p = 'U obradi';
					}elseif($kandidat_status == 2){
						$kandidat_status_p = 'Obrađen';
					}elseif($kandidat_status == 3){
						$kandidat_status_p = 'Arhiviran';
					}else{
						$kandidat_status_p = "-";
					}
					if($kandidat_vozacka_kategorija == null){
						if($kandidat_vozacka_dozvola == "Da")
							$kandidat_vozacka = "B";
						else
							$kandidat_vozacka = "Nema";
					}
					else
						$kandidat_vozacka = $kandidat_vozacka_kategorija;
			
			?>
			<tr>
				<td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $sumCount; ?></td>
				<td style="width:70px; text-align:center;padding:15px 15px;"><?php echo $kandidat_id; ?></td>
				<td style="width:250px; text-align:left;padding:15px 15px;" class="text-success"><a href="<?php getSiteUrl(); ?>/kandidati?page=open&id=<?php echo $kandidat_id;?>"><?php echo $kandidat_ime; ?> <?php echo $kandidat_prezime; ?></a></td>
				<td style="width:100px; text-align:left;padding:15px 15px;"><?php echo $kandidat_grad; ?></td>
				<td style="width:150px; text-align:left;padding:15px 15px;"><?php echo $kandidat_drzavljanstvo; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $govorna_interakcija; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $kandidat_datetime; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $kandidat_datum_aplikacijecheck; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $kandidat_datum_terminacheck; ?></td>
				<td style="mso-number-format:'\@'; width:150px; text-align:center;padding:15px 15px;"><?php echo $telefon; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $kandidat_status_p; ?></td>
				<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $kandidat_email; ?></td>
				<td style="width:200px; text-align:center;padding:15px 15px;"><?php echo $kandidat_vozacka; ?></td>
				<td style="width:200px; text-align:center;padding:15px 15px;"><?php echo $kandidat_adresa.", ".$kandidat_pbroj." ".$kandidat_grad; ?></td>
				<!--<td style="width:260px; text-align:left;padding:15px 15px;"><span class="label label-success material-label material-label_success material-label_xs main-container__column"><?php echo $kg_title; ?></span></td>-->
			</tr>
			<?php }?>
		</tbody>
	</table>
	<script>
	$(document).ready(function(){
		$("#CASTABLE_project").table2excel({
			exclude: ".noExl",
			name: "kandidatiExport(<?php echo date("Y-m-d");?>)",
			filename: "kandidatiExport(<?php echo date("Y-m-d");?>)",
			fileext: ".xls"
		}); 	  
	})
	</script>	
</div>

<?php
	
break;

case "export_kandidata_u_filter":

	$selectedis = $_GET['selectedis'];
	$idArray = explode(",",$selectedis);
?>
<div class="" style="height:0px;overflow:hidden;">
	<table class="table" id="CASTABLEmmds" style="100%;font-weight:bold!important;" class="display">
		<thead>
			<tr>
				<th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
				<th style="width:70px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Kandidat ID</th>
				<th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">E-Mail</th>
				<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Država</th>
				<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Grad</th>
				<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Poštanski broj</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Prijava za</th>
				<th style="width:260px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Grupa</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$sumCount = 1;
			foreach($idArray as $kandidat_id){
				
				$query = $db->prepare("
								SELECT kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_group, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo, kandidat_bracnostanje, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_slika, kandidat_datetime,kandidat_check, kandidat_vozacka_dozvola, kandidat_prijava_na, kandidat_visitedurl, kandidat_drzavljanstvo_vrsta, datum_termina, datum_aplikacije, cv_de, profile_de, kandidat_procjenatermina
								FROM idk_kandidati
								WHERE kandidat_id = :kandidat_id");

				$query->execute(array(
							':kandidat_id' => $kandidat_id));

				$row = $query->fetch();

					$kandidat_ime = $row['kandidat_ime'];
					$kandidat_prezime = $row['kandidat_prezime'];
					$kandidat_spol = $row['kandidat_spol'];
					$kandidat_check = $row['kandidat_check'];
					$kandidat_djevojackoprezime = $row['kandidat_djevojackoprezime'];
					$kandidat_jmbg = $row['kandidat_jmbg'];
					$kandidat_mjestorodjenja = $row['kandidat_mjestorodjenja'];
					$kandidat_drzavarodjenja = $row['kandidat_drzavarodjenja'];
					$kandidat_drzavljanstvo = $row['kandidat_drzavljanstvo'];
					$kandidat_drzavljanstvo_vrsta = $row['kandidat_drzavljanstvo_vrsta'];
					$kandidat_group = $row['kandidat_group'];
					$kandidat_bracnostanje = $row['kandidat_bracnostanje'];
					$kandidat_adresa = $row['kandidat_adresa'];
					$kandidat_prijava_na = $row['kandidat_prijava_na'];
					$kandidat_grad = $row['kandidat_grad'];
					$kandidat_pbroj = $row['kandidat_pbroj'];
					$kandidat_drzava = $row['kandidat_drzava'];
					$kandidat_email = $row['kandidat_email'];
					$kandidat_visitedurl = $row['kandidat_visitedurl'];
					$kandidat_datumrodjenja = date('d.m.Y.', strtotime($row['kandidat_datumrodjenja']));
					$kandidat_datetime = date('d.m.Y H:i', strtotime($row['kandidat_datetime']));
					$kandidat_datum_terminacheck = $row['datum_termina'];
					$kandidat_datum_aplikacijecheck = $row['datum_aplikacije'];
					$kandidat_procjenatermina = $row['kandidat_procjenatermina'];
					$kandidat_datum_termina = date('d.m.Y', strtotime($row['datum_termina']));
					if($kandidat_procjenatermina == 0){
						$kandidat_datum_termina = '<span class="label label-success material-label material-label_success main-container__column">'.$kandidat_datum_termina.'</span>';
					}else{
						$kandidat_datum_termina = '<span class="label label-danger material-label material-label_danger main-container__column">'.$kandidat_datum_termina.'</span>';
					}
			
					$query_group = $db->prepare("
									SELECT kg_id, kg_title, kg_date
									FROM idk_kandidati_grupe
									WHERE kg_id = :kg_id
									ORDER BY kg_id ASC
									");

					$query_group->execute(array(
						":kg_id" => $kandidat_group
					));

					$griup = $query_group->fetch();
						$kg_title = $griup['kg_title'];			
			
			?>
			<tr>
				<td style="width:30px;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $sumCount++; ?></td>
				<td style="width:70px;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $kandidat_id; ?></td>
				<td style="width:250px;min-height:25px;text-align:left;padding:15px 15px;font-weight:700;" class="text-success"><?php echo $kandidat_ime; ?> <?php echo $kandidat_prezime; ?></td>
				<td style="width:200px;min-height:25px;text-align:left;padding:15px 15px;" class="text-success"><?php echo $kandidat_email; ?></td>
				<td style="width:150px;min-height:25px;text-align:left;padding:15px 15px;" class="text-success"><?php echo $kandidat_drzava; ?></td>
				<td style="width:100px;min-height:25px;text-align:left;padding:15px 15px;"><?php echo $kandidat_grad; ?></td>
				<td style="width:100px;min-height:25px;text-align:left;padding:15px 15px;"><?php echo $kandidat_pbroj; ?></td>
				<td style="width:200px;min-height:25px;text-align:left;padding:15px 15px;"><?php echo $kandidat_prijava_na; ?></td>
				<td style="width:260px;min-height:25px;text-align:left;padding:15px 15px;"><span class="label label-success material-label material-label_success material-label_xs main-container__column"><?php echo $kg_title; ?></span></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<script>
	$(document).ready(function(){
		$("#CASTABLEmmds").table2excel({
			exclude: ".noExl",
			name: "kandidatiExport(<?php echo date("Y-m-d");?>)",
			filename: "kandidatiExport(<?php echo date("Y-m-d");?>)",
			fileext: ".xls"
		}); 	  
	})
	</script>	
</div>

<?php
	
break;	

case "export_kompanija_excel":

?>
<div class="" style="height:0px;overflow:hidden;">
	<table class="table" id="CASTABLEmmds" style="100%;font-weight:bold!important;" class="display">
		<thead>
			<tr>
				<th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
				<th style="width:400px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Naziv kompanije</th>
				<th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">ID broj</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Adresa</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Grad</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Država</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Telefon</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">E-mail</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$query = $db->prepare("
								SELECT company_id, company_name, company_idnum, company_city, company_logo, company_address, company_country
								FROM idk_companies
								WHERE company_status != :company_status");

				$query->execute(array(':company_status' => 0));
				
				$count = 1;
				while($row = $query->fetch()){

					$company_id = $row['company_id'];
					$company_name = $row['company_name'];
					$company_idnum = ['company_idnum'];
					$company_city = $row['company_city'];
					$company_address = $row['company_address'];
					$company_country = $row['company_country'];
					
					$company_idnum = "<span>ID: ".$row['company_idnum']."</span>";

					if($row['company_logo'] == "none"){
						$company_logo = "none.jpg";
					}else{
						$company_logo = $row['company_logo'];
					}

					//Get primary phone
					$query_phone = $db->prepare("
										SELECT comi_data
										FROM idk_companies_info
										WHERE comi_group = :comi_group AND comi_primary = :comi_primary AND comi_companyid = :comi_companyid");

					$query_phone->execute(array(
						':comi_group' => 1,
						':comi_primary' => 1,
						':comi_companyid' => $company_id));

					$row_phone = $query_phone->fetch();

					$comapny_phone = $row_phone['comi_data'];

					//Get primary email
					$query_email = $db->prepare("
										SELECT comi_data
										FROM idk_companies_info
										WHERE comi_group = :comi_group AND comi_primary = :comi_primary AND comi_companyid = :comi_companyid");

					$query_email->execute(array(
						':comi_group' => 2,
						':comi_primary' => 1,
						':comi_companyid' => $company_id));

					$row_email = $query_email->fetch();

					$company_email = $row_email['comi_data'];	
			
			?>
			<tr>
				<td style="width:30px;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $count++; ?></td>
				<td style="width:400px;min-height:25px;text-align:left;padding:15px 15px;"><?php echo $company_name; ?></td>
				<td style="width:250px;min-height:25px;text-align:left;padding:15px 15px;font-weight:700;" class="text-success"><?php echo $company_idnum; ?></td>
				<td style="width:200px;min-height:25px;text-align:left;padding:15px 15px;" class="text-success"><?php echo $company_address; ?></td>
				<td style="width:200px;min-height:25px;text-align:left;padding:15px 15px;" class="text-success"><?php echo $company_city; ?></td>
				<td style="width:200px;min-height:25px;text-align:left;padding:15px 15px;" class="text-success"><?php echo $company_country; ?></td>
				<td style="width:200px;min-height:25px;text-align:left;padding:15px 15px;" class="text-success"><?php echo $comapny_phone; ?></td>
				<td style="width:200px;min-height:25px;text-align:left;padding:15px 15px;" class="text-success"><?php echo $company_email; ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<script>
	$(document).ready(function(){
		$("#CASTABLEmmds").table2excel({
			exclude: ".noExl",
			name: "Kompanije(<?php echo date("Y-m-d");?>)",
			filename: "Kompanije(<?php echo date("Y-m-d");?>)",
			fileext: ".xls"
		}); 	  
	})
	</script>	
</div>

<?php
	
break;	

case "export_naloga_excel":

?>
<div class="" style="height:0px;overflow:hidden;">
	<table class="table" id="CASTABLEmmds" style="100%;font-weight:bold!important;" class="display">
		<thead>
			<tr>
				<th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
				<th style="width:100px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Broj naloga</th>
				<th style="width:400px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Naziv naloga</th>
				<th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Kompanija</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Projekt menadžer</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Status</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Vrsta</th>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Kreirano</th>th>
			</tr>
		</thead>
		<tbody>
			<?php
				$query = $db->prepare("
								SELECT nalog_id, nalog_broj, nalog_naziv, company_name, e.employee_firstname, e.employee_lastname, nalog_status, nalog_ugovor, nalog_kreirano
								FROM idk_nalozi 
								JOIN idk_companies ON kompanija_id = company_id
								JOIN idk_employees e ON e.employee_id = idk_nalozi.employee_id 
								WHERE nalog_status != 8
				");

				$query->execute();
				
				$count = 1;
				while($row = $query->fetch()){

					$nalog_id = $row['nalog_id'];
					$nalog_broj = $row['nalog_broj'];
					$nalog_naziv = $row['nalog_naziv'];
					$company_name = $row['company_name'];
					$menadzer = $row['employee_firstname']." ".$row['employee_lastname'];
					$nalog_status = $row['nalog_status'];
					$nalog_ugovor = $row['nalog_ugovor'];
					$nalog_kreirano = date('d-m-Y', strtotime($row['nalog_kreirano']));
					
					if($nalog_status == 1){
						$nalog_status_txt = 'Potpis';
					}elseif($nalog_status == 2){
						$nalog_status_txt = 'Čeka se uplata';
					}elseif($nalog_status == 3){
						$nalog_status_txt = 'Marketing';
					}elseif($nalog_status == 4){
						$nalog_status_txt = 'Prijave u toku';
					}elseif($nalog_status == 5){
						$nalog_status_txt = 'Obrada prijava';
					}elseif($nalog_status == 6){
						$nalog_status_txt = 'Nalog kod poslodavca';
					}elseif($nalog_status == 7){
						$nalog_status_txt = 'Casting';
					}elseif($nalog_status == 8){
						$nalog_status_txt = 'Završeno';
					}elseif($nalog_status == 9){
						$nalog_status_txt = 'Na čekanju';
					}elseif($nalog_status == 10){
						$nalog_status_txt = 'Kandidati u odlasku';
					}elseif($nalog_status == 11){
						$nalog_status_txt = 'Završeno (nenaplaćeno)';
					}
			
			?>
			<tr>
				<td style="width:30px;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $count++; ?></td>
				<td style="width:100px;min-height:25px;text-align:left;padding:15px 15px;"><?php echo $nalog_broj; ?></td>
				<td style="width:400px;min-height:25px;text-align:left;padding:15px 15px;"><?php echo $nalog_naziv; ?></td>
				<td style="width:250px;min-height:25px;text-align:left;padding:15px 15px;" class="text-success"><?php echo $company_name; ?></td>
				<td style="width:200px;min-height:25px;text-align:left;padding:15px 15px;" class="text-success"><?php echo $menadzer; ?></td>
				<td style="width:200px;min-height:25px;text-align:left;padding:15px 15px;" class="text-success"><?php echo $nalog_status_txt; ?></td>
				<td style="width:200px;min-height:25px;text-align:left;padding:15px 15px;" class="text-success"><?php echo $nalog_ugovor; ?></td>
				<td style="width:200px;min-height:25px;text-align:left;padding:15px 15px;" class="text-success"><?php echo $nalog_kreirano; ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<script>
	$(document).ready(function(){
		$("#CASTABLEmmds").table2excel({
			exclude: ".noExl",
			name: "Nalozi(<?php echo date("Y-m-d");?>)",
			filename: "Nalozi(<?php echo date("Y-m-d");?>)",
			fileext: ".xls"
		}); 	  
	})
	</script>	
</div>

<?php
	
break;

case "export_financije_excel":

	$f_from_f = $_GET['datumod'];
	$f_to_f = $_GET['datumdo'];
	$nalog_ids = $_GET['nalogids'];

?>
<div class="" style="height:0px;overflow:hidden;">
	<table class="table" id="CASTABLEmmds" style="100%;font-weight:bold!important;" class="display">
		<thead>
			<tr>
				<th>Nalog</th>
				<th>Projekt menadžer</th>
				<th>Datum kreiranja</th>
				
				<?php
				$start = $month = strtotime($f_from_f);
				$end = strtotime($f_to_f);
				$br_mjeseci=0;
				$ukupna_cijena_po_nalozima=0;
				$mjesec_suma = array();
				for($i=1; $i<100;$i++){
					$mjesec_suma[$i] = 0;
				}
				while($month < $end){
					$br_mjeseci++;
				?>
				<th colspan="4" style="height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid black;"><?php echo date('F/Y', $month), PHP_EOL; ?></th>
				<?php $month = strtotime("+1 month", $month); } ?>
				<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(216,216,216);border-right:1px solid black;">UKUPNO</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$query = $db->prepare("
                               SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, nalog_opis, nalog_kreirano, nalog_status, comp.company_name, nalog_provizija, nalog_potrebno_kandidata, nalog_provizija, nalog_placena_prva_rata, employee_id
                               FROM idk_nalozi
                               INNER JOIN idk_companies comp ON kompanija_id = comp.company_id
                               WHERE (nalog_status != :nalog_status) AND nalog_id IN ($nalog_ids)
							   ORDER BY nalog_id DESC
							   ");

				$query->execute(array(':nalog_status' => 8));

				
				while($row = $query->fetch()){

                    $nalog_id = $row['nalog_id'];
					$nalog_broj = $row['nalog_broj'];
					$kompanija_id = $row['kompanija_id'];
					$nalog_naziv = $row['nalog_naziv'];
                    $kompanija = $row['company_name'];
                    $nalog_status = $row['nalog_status'];
                    $provizija = $row['nalog_provizija'];
                    $nalog_placena_prva_rata = $row['nalog_placena_prva_rata'];
                    $nalog_kreirano = date('d.m.Y.', strtotime($row['nalog_kreirano']));
					
                    $nalog_potrebno_kandidata = $row['nalog_potrebno_kandidata'];
                    $nalog_provizija = $row['nalog_provizija'];
                    $employee_id = $row['employee_id'];
					
					$query_name = $db->prepare("
									SELECT employee_firstname, employee_lastname
									FROM idk_employees
									WHERE employee_id = '$employee_id'");
					
					$query_name->execute();
					$query_name_row = $query_name->fetch();
					$projekt_manager = $query_name_row['employee_firstname']." ".$query_name_row['employee_lastname'];
					

					// PROCENAT ZA ODMAH UGOVOR
					$query_nalog_ugovor = $db->prepare("
									SELECT nr_procenat, nr_datum
									FROM idk_nalozi_rate
									WHERE nr_nalog = :nr_nalog AND nr_vrijeme_placanja = :nr_vrijeme_placanja
									");
	
					$query_nalog_ugovor->execute(array(
						'nr_nalog' => $nalog_id,
						'nr_vrijeme_placanja' => "odmah"
						));
					
					$query_ugovor = $query_nalog_ugovor->fetch();
						$odmah_procenat = $query_ugovor['nr_procenat'];					
						$ugovor_nr_datum = $query_ugovor['nr_datum'];
						$ugovor_nr_datum_f = date("mY", strtotime( $query_ugovor['nr_datum']));

											
					
					// PROCENAT ZA POTPIS UGOVORA
					$query_nalog_rate = $db->prepare("
									SELECT nr_procenat
									FROM idk_nalozi_rate
									WHERE nr_nalog = :nr_nalog AND nr_vrijeme_placanja = :nr_vrijeme_placanja
									");
	
					$query_nalog_rate->execute(array(
						'nr_nalog' => $nalog_id,
						'nr_vrijeme_placanja' => "ugovor"
						));
					
					$ugovor_p = $query_nalog_rate->fetch();
						$ugovor_procenat = $ugovor_p['nr_procenat'];
						
					// PROCENAT ZA POCETAK RADA
					$query_nalog_pocetakrada = $db->prepare("
									SELECT nr_procenat
									FROM idk_nalozi_rate
									WHERE nr_nalog = :nr_nalog AND nr_vrijeme_placanja = :nr_vrijeme_placanja
									");
	
					$query_nalog_pocetakrada->execute(array(
						'nr_nalog' => $nalog_id,
						'nr_vrijeme_placanja' => "pocetak rada"
						));
					
					$pocetakrada_p = $query_nalog_pocetakrada->fetch();
						$pocetakrada_procenat = $pocetakrada_p['nr_procenat'];			

					// PROCENAT NAKON POCETKA RADA
					$query_nalog_nakonrada = $db->prepare("
									SELECT nr_procenat, nr_mjeseci_nakon
									FROM idk_nalozi_rate
									WHERE nr_nalog = :nr_nalog AND nr_vrijeme_placanja = :nr_vrijeme_placanja
									");
	
					$query_nalog_nakonrada->execute(array(
						'nr_nalog' => $nalog_id,
						'nr_vrijeme_placanja' => "mjeseci nakon"
						));
					
					$nakonrada = $query_nalog_nakonrada->fetch();
						$nakonrada_procenat = $nakonrada['nr_procenat'];							
						$nr_mjeseci_nakon = $nakonrada['nr_mjeseci_nakon'];							
					
					if($nr_mjeseci_nakon == 1)
						$mjesec_sufix = "mjesec";
					else if($nr_mjeseci_nakon == 2 or $nr_mjeseci_nakon == 3 or $nr_mjeseci_nakon == 4)
						$mjesec_sufix = "mjeseca";
					else
						$mjesec_sufix = "mjeseci";
					
					// BROJ RATA NAKON POCETKA RADA
					$query_nalog_broj_rata_npr = $db->prepare("
									SELECT COUNT(nr_id) as broj_rata_npr
									FROM idk_nalozi_rate
									WHERE nr_nalog = :nr_nalog AND nr_vrijeme_placanja = :nr_vrijeme_placanja
									");
					$query_nalog_broj_rata_npr->execute(array(
						'nr_nalog' => $nalog_id,
						'nr_vrijeme_placanja' => "mjeseci nakon"
						));
					$row_brnpr = $query_nalog_broj_rata_npr->fetch();
					$broj_rata_npr = $row_brnpr['broj_rata_npr'];
					
					if($broj_rata_npr == 2){
						$query_nalog_mjeseci_nakon2 = $db->prepare("
										SELECT nr_procenat, nr_mjeseci_nakon
										FROM idk_nalozi_rate
										WHERE nr_nalog = :nr_nalog AND nr_vrijeme_placanja = :nr_vrijeme_placanja
										ORDER BY nr_mjeseci_nakon DESC
										");
		
						$query_nalog_mjeseci_nakon2->execute(array(
							'nr_nalog' => $nalog_id,
							'nr_vrijeme_placanja' => "mjeseci nakon"
							));
						
						$row_mjeseci_nakon2 = $query_nalog_mjeseci_nakon2->fetch();
							$nakonrada_procenat2 = $row_mjeseci_nakon2['nr_procenat'];		
							$nr_mjeseci_nakon2 = $row_mjeseci_nakon2['nr_mjeseci_nakon'];
							
						if($row_mjeseci_nakon2 == 1)
							$mjesec_sufix = "mjesec";
						else if($row_mjeseci_nakon2 == 2 or $row_mjeseci_nakon2 == 3 or $row_mjeseci_nakon2 == 4)
							$mjesec_sufix2 = "mjeseca";
						else
							$mjesec_sufix2 = "mjeseci";
					}

			?>
			<tr>
				<!-- NAZIV NALOGA -->
				<td style="height:30px;text-align:left;padding:15px 15px; border-bottom:1px solid black; vertical-align: middle;"><?php echo $nalog_naziv." (".$nalog_broj.")"; ?></td>
				<td style="height:30px;text-align:left;padding:15px 15px; border-bottom:1px solid black; vertical-align: middle;"><?php echo $projekt_manager; ?></td>
				<td style="height:30px;text-align:left;padding:15px 15px; border-bottom:1px solid black; vertical-align: middle;"><?php echo $nalog_kreirano; ?></td>
				<?php
					$start_t = $month_t = strtotime($f_from_f);
					$end_t = strtotime($f_to_f);
					
					
				$cijena_ukupno_svi_mjeseci = 0;
				$cijena_ukupno_mjesec = 0;
				$trenutni_mjesec=1;
				$suma_cijena_ugovor_p = 0;
				$suma_cijena_ugovor_n = 0;
				$suma_cijena_pr_p = 0;
				$suma_cijena_pr_n = 0;
				$suma_cijena_nr_p = 0;
				$suma_cijena_nr_n = 0;
				$suma_cijena_nr2_p = 0;
				$suma_cijena_nr2_n = 0;
				$suma_cijena_odmah_p = 0;
				$suma_cijena_odmah_n = 0;
					while($month_t < $end_t){
						
						 $godina =  date('Y', $month_t);
						 $mjesec =  date('m', $month_t);
						 $mjesecgodina = "".$mjesec."". $godina."";
						 
						 if($mjesecgodina == $ugovor_nr_datum_f){
							  $odmah_cijena = ($nalog_provizija / 100 * $odmah_procenat) * $nalog_potrebno_kandidata;		
						 }else{
							  $odmah_cijena = 0;		
						 }
						if($nalog_placena_prva_rata == 1)
							$suma_cijena_odmah_p += $odmah_cijena;
						else
							$suma_cijena_odmah_n += $odmah_cijena;
								
				?>
				
				<td style="height:30px;text-align:left;padding:15px 15px; width:75%; border-bottom:1px solid black;">
				<?php
					if($_GET['exportokvirni'] == 0){
							
					}else{
						echo "</br>Rata odmah: </br>";
						echo "Potpis ugovora: </br>";
						echo "Pocetak rada: </br>";	
						echo "Nakon rada(1.rata): </br>";
						echo "Nakon rada(2.rata): </br>";
					}
				?>
				</td>
				<!-- PROJEKTI -->
				<td style="height:30px;text-align:left;padding:15px 15px; mso-number-format:\#\,\#\#0\.00; text-align: right; width:25%; border-bottom:1px solid black;">
				<?php
				
					/* $query_projects = $db->prepare("
									SELECT project_id, project_name, project_datetime, project_status, project_nalogid, project_order, project_datumtermina, project_datumterminado
									FROM idk_projects
									WHERE project_nalogid = :project_nalogid
									ORDER BY project_order DESC
									");
	
					$query_projects->execute(array(
						'project_nalogid' => $nalog_id));
						
						
					
					$suma_cijena = 0;
					while($rowp = $query_projects->fetch()){
	
						$project_id = $rowp['project_id'];
						$project_order = $rowp['project_order'];
						$project_name = $rowp['project_name'];
						$project_datetime = '<span class="label label-success">'.date('d.m.Y. - H:i', strtotime($rowp['project_datetime'])).'</span>';
						$project_status = $rowp['project_status'];
						$project_datumtermina = $rowp['project_datumtermina'];
						$project_datumterminado = $rowp['project_datumterminado'];

						$brojKandidataUProjektu = getNumberOfCandidatesProject($project_id); */
	
				?>
				
				<?php
				/* 
					// KANDIDATI POTPISAN UGOVOR
					$query_kandidati = $db->prepare("
									SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, cv_ba, cv_de
									FROM idk_kandidati
									INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
									WHERE (pk_projectid = :pk_projectid AND kandidat_status !=3) AND (MONTH(kandidat_datum_ugovora_mjesec) = :ugovormonth AND YEAR(kandidat_datum_ugovora_mjesec) = :ugovorgodina)");
				
					$query_kandidati->execute(array(
					':pk_projectid' => $project_id,
					':ugovormonth' => $mjesec,
					':ugovorgodina' => $godina
					));
					*/
					$query_kandidati_ugovor = $db->prepare("
									SELECT kf_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_type, kf_placeno, kf_status
									FROM idk_kandidat_financije
									WHERE (nalog_id = :nalog_id) AND (kf_type = 1 AND (kf_status = 1 OR kf_status = 3)) AND (MONTH(kf_datum) = :ugovormonth AND YEAR(kf_datum) = :ugovorgodina)");
					
					$query_kandidati_ugovor->execute(array(
									':nalog_id' => $nalog_id,
									':ugovormonth' => $mjesec,
									':ugovorgodina' => $godina
									));
					
					$cont = $query_kandidati_ugovor->rowCount();
					
					$kandidat = $query_kandidati_ugovor->fetch();
					$placeno_ugovor = $kandidat['kf_placeno'];
						if($cont > 0){
							$cijena = ($provizija / 100 * $ugovor_procenat) * $cont;
						}else{
							$cijena = 0;
						}
						if($placeno_ugovor == 1)
							$suma_cijena_ugovor_p += $cijena;
						else
							$suma_cijena_ugovor_n += $cijena;
					
					/* 
					// KANDIDATI POCETAK RADA
					$query_kandidati_pr = $db->prepare("
									SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, cv_ba, cv_de
									FROM idk_kandidati
									INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
									WHERE (pk_projectid = :pk_projectid AND kandidat_status !=3) AND (MONTH(kandidat_datum_pocetakrada_mjesec) = :ugovormonth AND YEAR(kandidat_datum_pocetakrada_mjesec) = :ugovorgodina)");
				
					$query_kandidati_pr->execute(array(
					':pk_projectid' => $project_id,
					':ugovormonth' => $mjesec,
					':ugovorgodina' => $godina
					));
					*/
					
					$query_kandidati_pocetak_rada = $db->prepare("
									SELECT kf_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_type, kf_placeno, kf_status
									FROM idk_kandidat_financije
									WHERE (nalog_id = :nalog_id) AND (kf_type = 2 AND (kf_status = 1 OR kf_status = 3)) AND (MONTH(kf_datum) = :ugovormonth AND YEAR(kf_datum) = :ugovorgodina)");
					
					$query_kandidati_pocetak_rada->execute(array(
									':nalog_id' => $nalog_id,
									':ugovormonth' => $mjesec,
									':ugovorgodina' => $godina
									));
					
					$cont_pr = $query_kandidati_pocetak_rada->rowCount();
					
					$kandidat_pr = $query_kandidati_pocetak_rada->fetch();
					$placeno_pr = $kandidat_pr['kf_placeno'];
						if($cont_pr > 0){
							$cijena_pr = ($provizija / 100 * $pocetakrada_procenat) * $cont_pr;
						}else{
							$cijena_pr = 0;
						}
						
						if($placeno_pr == 1)
							$suma_cijena_pr_p += $cijena_pr;
						else
							$suma_cijena_pr_n += $cijena_pr;
						
					/*
					// KANDIDATI POCETAK RADA
					$query_kandidati_nakonrada = $db->prepare("
									SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, cv_ba, cv_de
									FROM idk_kandidati
									INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
									WHERE (pk_projectid = :pk_projectid AND kandidat_status !=3) AND (MONTH(kandidat_datum_pocetakrada_mjesec + INTERVAL $nr_mjeseci_nakon MONTH) = :ugovormonth AND YEAR(kandidat_datum_pocetakrada_mjesec) = :ugovorgodina)");
				
					$query_kandidati_nakonrada->execute(array(
					':pk_projectid' => $project_id,
					':ugovormonth' => $mjesec,
					':ugovorgodina' => $godina
					));
					*/
					$query_kandidati_mjeseci_nakon = $db->prepare("
									SELECT kf_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_type, kf_placeno, kf_status
									FROM idk_kandidat_financije
									WHERE (nalog_id = :nalog_id) AND (kf_type = 3 AND (kf_status = 1 OR kf_status = 3)) AND (MONTH(kf_datum) = :ugovormonth AND YEAR(kf_datum) = :ugovorgodina)");
					
					$query_kandidati_mjeseci_nakon->execute(array(
									':nalog_id' => $nalog_id,
									':ugovormonth' => $mjesec,
									':ugovorgodina' => $godina
									));
									
					$cont_nr = $query_kandidati_mjeseci_nakon->rowCount();
					
					$kandidat_nr = $query_kandidati_mjeseci_nakon->fetch();
					$placeno_nr = $kandidat_nr['kf_placeno'];
						if($cont_nr > 0){
							$cijena_nr = ($provizija / 100 * $nakonrada_procenat) * $cont_nr;
						}else{
							$cijena_nr = 0;
						}
						if($placeno_nr == 1)
							$suma_cijena_nr_p += $cijena_nr;
						else
							$suma_cijena_nr_n += $cijena_nr;
						
					// NAKON POCETKA RADA - DRUGA RATA
					
					$query_kandidati_mjeseci_nakon2 = $db->prepare("
									SELECT kf_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_type, kf_placeno, kf_status
									FROM idk_kandidat_financije
									WHERE (nalog_id = :nalog_id) AND (kf_type = 4 AND (kf_status = 1 OR kf_status = 3)) AND (MONTH(kf_datum) = :ugovormonth AND YEAR(kf_datum) = :ugovorgodina)");
					
					$query_kandidati_mjeseci_nakon2->execute(array(
									':nalog_id' => $nalog_id,
									':ugovormonth' => $mjesec,
									':ugovorgodina' => $godina
									));
									
					$cont_nr2 = $query_kandidati_mjeseci_nakon2->rowCount();
					
					$kandidat_nr2 = $query_kandidati_mjeseci_nakon2->fetch();
					$placeno_nr2 = $kandidat_nr2['kf_placeno'];
						if($cont_nr2 > 0){
							$cijena_nr2 = ($provizija / 100 * $nakonrada_procenat2) * $cont_nr2;
						}else{
							$cijena_nr2 = 0;
						}
						$suma_cijena_nr2 += $cijena_nr2;		
						if($placeno_nr2 == 1)
							$suma_cijena_nr2_p += $cijena_nr2;
						else
							$suma_cijena_nr2_n += $cijena_nr2;
						
				?>
				
				<?php //}
				
					$ukupna_suma_zamjesec = $suma_cijena_odmah_p + $suma_cijena_odmah_n + $suma_cijena_ugovor_p + $suma_cijena_ugovor_n + $suma_cijena_pr_p + $suma_cijena_pr_n + $suma_cijena_nr_p + $suma_cijena_nr_n + $suma_cijena_nr2_p + $suma_cijena_nr2_n;
					$ukupna_suma = $suma_cijena_odmah_p + $suma_cijena_odmah_n + $suma_cijena_ugovor_p + $suma_cijena_ugovor_n + $suma_cijena_pr_p + $suma_cijena_pr_n + $suma_cijena_nr_p + $suma_cijena_nr_n + $suma_cijena_nr2_p + $suma_cijena_nr2_n;
					$ukupna_suma_p = $suma_cijena_odmah_p + $suma_cijena_ugovor_p + $suma_cijena_pr_p + $suma_cijena_nr_p + $suma_cijena_nr2_p;
					$ukupna_suma_n = $suma_cijena_odmah_n + $suma_cijena_ugovor_n + $suma_cijena_pr_n + $suma_cijena_nr_n + $suma_cijena_nr2_n;
					
					$suma_cijena_odmah = $suma_cijena_odmah_p + $suma_cijena_odmah_n;
					$suma_cijena_ugovor = $suma_cijena_ugovor_p + $suma_cijena_ugovor_n;
					$suma_cijena_pr = $suma_cijena_pr_p + $suma_cijena_pr_n;
					$suma_cijena_nr = $suma_cijena_nr_p + $suma_cijena_nr_n;
					$suma_cijena_nr2 = $suma_cijena_nr2_p + $suma_cijena_nr2_n;

					if($_GET['exportokvirni'] == 0){
						echo $ukupna_suma_p;	
					}else{
						echo "Plaćeno<br/>".$suma_cijena_odmah_p."</br>";
						echo $suma_cijena_ugovor_p."</br>";
						echo $suma_cijena_pr_p."</br>";	
						echo $suma_cijena_nr_p."</br>";	
						echo $suma_cijena_nr2_p."</br>";	
						echo $ukupna_suma_p;
						//echo '</br><p style="mso-number-format:\#\,\#\#0\.00">'.$ukupna_suma.'</p>';
						//echo '</br><div style="display: inline-block;">aaa</div><div style="display: inline-block;">dds</div>';
					}
				//echo "<br/>";
				//echo $odmah_naplata_suma;				
				$cijena_ukupno_svi_mjeseci += $ukupna_suma;
				
				?>
				</td>
				<td style="height:30px;text-align:left;padding:15px 15px; mso-number-format:\#\,\#\#0\.00; text-align: right; width:25%; border-bottom:1px solid black;">
					<?php if($_GET['exportokvirni'] == 0){
						echo $ukupna_suma_n;	
					}else{
						echo "Neplaćeno<br/>".$suma_cijena_odmah_n."</br>";
						echo $suma_cijena_ugovor_n."</br>";
						echo $suma_cijena_pr_n."</br>";	
						echo $suma_cijena_nr_n."</br>";	
						echo $suma_cijena_nr2_n."</br>";	
						echo $ukupna_suma_n;
						//echo '</br><p style="mso-number-format:\#\,\#\#0\.00">'.$ukupna_suma.'</p>';
						//echo '</br><div style="display: inline-block;">aaa</div><div style="display: inline-block;">dds</div>';
					}?>
				</td>
				<td style="height:30px;text-align:left;padding:15px 15px; mso-number-format:\#\,\#\#0\.00; text-align: right; width:25%; border-right:1px solid black; border-bottom:1px solid black;">
					<?php if($_GET['exportokvirni'] == 0){
						echo $ukupna_suma;	
					}else{
						echo "Ukupno<br/>".$suma_cijena_odmah."</br>";
						echo $suma_cijena_ugovor."</br>";
						echo $suma_cijena_pr."</br>";	
						echo $suma_cijena_nr."</br>";	
						echo $suma_cijena_nr2."</br>";	
						echo '<a style="mso-number-format:\#\,\#\#0\.00" href="'.getSiteUrlR().'redirekcijazaexcellink.php?nalogid='.$nalog_id.'&mjesec='.$mjesec.'&godina='.$godina.'" target=""_BLANK style="mso-number-format:\#\,\#\#0\.00">'.$ukupna_suma.'</a>';
						//echo '</br><p style="mso-number-format:\#\,\#\#0\.00">'.$ukupna_suma.'</p>';
						//echo '</br><div style="display: inline-block;">aaa</div><div style="display: inline-block;">dds</div>';
					}?>
				</td>
				<?php
				for($i=1; $i<=$br_mjeseci; $i++){
					if($i == $trenutni_mjesec){
						$mjesec_suma[$i] += $ukupna_suma;
						$mjesec_suma_p[$i] += $ukupna_suma_p;
						$mjesec_suma_n[$i] += $ukupna_suma_n;
					}
					
				}
				$month_t = strtotime("+1 month", $month_t); 
				$odmah_naplata_suma = 0;
				$suma_cijena_ugovor = 0;
				$suma_cijena_ugovor_p = 0;
				$suma_cijena_ugovor_n = 0;
				$suma_cijena_pr_p = 0;
				$suma_cijena_pr_n = 0;
				$suma_cijena_pr = 0;
				$suma_cijena_nr_p = 0;
				$suma_cijena_nr_n = 0;
				$suma_cijena_nr = 0;
				$suma_cijena_nr2_p = 0;
				$suma_cijena_nr2_n = 0;
				$suma_cijena_nr2 = 0;
				$suma_cijena_odmah_p = 0;
				$suma_cijena_odmah_n = 0;
				$suma_cijena_odmah = 0;
				$ukupna_suma = 0;
				
				$cijena_ukupno_mjesec += $ukupna_suma_zamjesec;
				$trenutni_mjesec++;
				}
				?>
				<td style="mso-number-format:\#\,\#\#0\.00">
				 <?php echo $cijena_ukupno_svi_mjeseci; 
						$ukupna_cijena_po_nalozima+=$cijena_ukupno_svi_mjeseci;
				 ?>
				</td>			
			</tr>
				
			<?php 
			
			} ?>

				
			

		</tbody>
		
		<thead>
			<tr>
				<!--<th>UKUPNO</th>-->
				
				<?php
				$start = $month_sum = strtotime($f_from_f);
				$end = strtotime($f_to_f);
				$suma_po_nalogu_mjesec = 0;
				?>
				<th style="height:30px;text-align:left;padding:15px 15px;">Suma za mjesec: </th>
				<?php
				$brojac_mjeseci=1;
				while($month_sum < $end){
					
				$suma_po_nalogu_mjesec += $ukupna_suma;
				 $godina_s =  date('Y', $month_sum);
				 $mjesec_s =  date('m', $month_sum);
				?>
				<th></th>
				<?php if($brojac_mjeseci == 1){ ?>
				<th></th><?php } ?>
				<th style="mso-number-format:\#\,\#\#0\.00; width:80px;text-align:right;padding:15px 15px;background:rgb(109,172,79);"><?php echo $mjesec_suma_p[$brojac_mjeseci]; ?></th>
				<th style="mso-number-format:\#\,\#\#0\.00; width:80px;text-align:right;padding:15px 15px;background:rgb(255, 51, 51);"><?php echo $mjesec_suma_n[$brojac_mjeseci]; ?></th>
				<th style="mso-number-format:\#\,\#\#0\.00; width:80px;text-align:right;padding:15px 15px;background:rgb(216,216,216); border-right:1px solid black;"><?php echo $mjesec_suma[$brojac_mjeseci]; ?></th>
				<?php $month_sum = strtotime("+1 month", $month_sum); $suma_po_nalogu_mjesec = 0; 
				$brojac_mjeseci++;
				} ?>
				<th style="mso-number-format:\#\,\#\#0\.00"><?php echo $ukupna_cijena_po_nalozima; ?></th>
			</tr>
		</thead>		
		
	</table>
	<script>
	$(document).ready(function(){
		$("#CASTABLEmmds").table2excel({
			exclude: ".noExl",
			name: "Financije(<?php echo date("Y-m-d");?>)",
			filename: "Financije(<?php echo date("Y-m-d");?>)",
			fileext: ".xls"
		}); 	  
	})
	</script>	
</div>

<?php
	
break;

case "export_mail_financije":


	$datum = $_GET['datum'];
	
	?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php include('includes/head.php'); ?>

</head>
<body>
<?php
	
	$query = $db->prepare("
                        SELECT *
                        FROM idk_kandidat_financije
                        WHERE kf_datum_stvarni = :kf_datum AND kf_status != 2");

    $query->execute(array(
					':kf_datum' => $datum
	));
	
		?>
		
		<table class="table" id="table_mail_export" style="100%;font-weight:bold!important;" class="display">
			<thead>
				<tr>
					<th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
					<th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Nalog</th>
					<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime</th>
					<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum</th>
					<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Tip rate</th>
					<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Iznos</th>
				</tr>
			</thead>
			<tbody>
		<?php
		$sumCount = 1;
		while($row = $query->fetch()){
			
			$kf_id = $row['kf_id'];
			$kf_datum = $row['kf_datum'];
			$nalog_id = $row['nalog_id'];
			$kandidat_id = $row['kandidat_id'];
			$kf_type = $row['kf_type'];
			
			$query_kandidat = $db->prepare("
								SELECT kandidat_ime, kandidat_prezime
								FROM idk_kandidati
								WHERE kandidat_id = :kandidat_id");

			$query_kandidat->execute(array(
							':kandidat_id' => $kandidat_id
			));
			
			$row_kandidat = $query_kandidat->fetch();
			$kandidat_ime = $row_kandidat['kandidat_ime'];
			$kandidat_prezime = $row_kandidat['kandidat_prezime'];
			
			$query_nalog = $db->prepare("
								SELECT nalog_naziv, nalog_provizija
								FROM idk_nalozi
								WHERE nalog_id = :nalog_id");

			$query_nalog->execute(array(
							':nalog_id' => $nalog_id
			));
			
			$row_nalog = $query_nalog->fetch();
			$nalog_naziv = $row_nalog['nalog_naziv'];
			$nalog_provizija = $row_nalog['nalog_provizija'];
			
			switch ($kf_type){
				case 1: 
					$tip_rate = "Potpis ugovora";
					$nr_vrijeme_placanja = "ugovor";
				break;
				case 2:
					$tip_rate = "Početak rada";
					$nr_vrijeme_placanja = "pocetak rada";
				break;
				case 3: 
					$tip_rate = "Nakon početka rada";
					$nr_vrijeme_placanja = "mjeseci nakon";
				break;
				case 4:
					$tip_rate = "Nakon početka rada";
					$nr_vrijeme_placanja = "mjeseci nakon";
				break;
			}
			
			$query_procenat = $db->prepare("
								SELECT nr_procenat
								FROM idk_nalozi_rate
								WHERE nr_nalog = :nalog_id AND nr_vrijeme_placanja = :nr_vrijeme_placanja ");

			$query_procenat->execute(array(
							':nalog_id' => $nalog_id,
							':nr_vrijeme_placanja' => $nr_vrijeme_placanja
			));
			
			$row_procenat = $query_procenat->fetch();
			$procenat = $row_procenat['nr_procenat'];
			$iznos = $nalog_provizija*($procenat/100);
			$iznos = number_format((float)$iznos, 2, '.', '');
			// var_dump($nalog_id);
			// exit();
			
			?>
				<tr>
					<td style="width:30px;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $sumCount++; ?></td>
					<td style="width:250;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $nalog_naziv; ?></td>
					<td style="width:200;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $kandidat_ime." ".$kandidat_prezime; ?></td>
					<td style="width:100;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $datum; ?></td>
					<td style="width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $tip_rate; ?></td>
					<td style="mso-number-format:\#\,\#\#0\.00; width:100;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $iznos; ?></td>
				</tr>
			
			<?php
		}
		?>
			</tbody>
		</table>
		<script>
		$(document).ready(function(){
			$("#table_mail_export").table2excel({
				exclude: ".noExl",
				name: "Financije(<?php echo $datum;?>)",
				filename: "financije(<?php echo $datum;?>)",
				fileext: ".xls"
			}); 	  
		})
		</script>
		</body>
		</html>
	<?php 
break;	
case "export_dak":

	$f_from = $_GET['datum_range'];
	$status_get = $_GET['type'];
	
	if($f_from != NULL){
		if (strpos($f_from, 'to') !== false) {
			$split = explode(" to ",$f_from);
			$datum_od = $split[0];
			$datum_do = $split[1];
		}else{
			$datum_od = $f_from;
			$datum_do = $f_from;
		}
	
		$f_from_f = date('Y-m-d', strtotime($datum_od));
		$f_to_f = date('Y-m-d', strtotime($datum_do));	
		$datum_range_query = "AND datumunosa_dak_kandidat between '$f_from_f' AND '$f_to_f'";
	}else{
		$datum_range_query = "";
	}
	
	?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php include('includes/head.php'); ?>

</head>
<body>
<?php
	
	$query = $db->prepare("
                        SELECT *
                        FROM idk_dak_kandidati
                        WHERE status_dak_kandidat = $status_get ".$datum_range_query." ORDER BY datumunosa_dak_kandidat");

    $query->execute();
	
		?>
		
		<table class="table" id="table_mail_export" style="100%;font-weight:bold!important;" class="display">
			<thead>
				<tr>
					<th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
					<th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime</th>
					<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum unosa</th>
					<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Porijeklo</th>
					<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Status</th>
					<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Rata 1</th>
					<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Rata 2</th>
				</tr>
			</thead>
			<tbody>
		<?php
		$sumCount = 1;
		while($row = $query->fetch()){
			
			$name_dak_kandidat = $row['name_dak_kandidat'];
			$lastname_dak_kandidat = $row['lastname_dak_kandidat'];
			$datumunosa_dak_kandidat = date('d.m.Y', strtotime($row['datumunosa_dak_kandidat']));
			$tipunosa_dak_kandidat = $row['tipunosa_dak_kandidat'];
			if($tipunosa_dak_kandidat == 0){
				$porijeklo = "Aplikacija";
			}else{
				$porijeklo = "Ručno";
			}
			$status_dak_kandidat = $row['status_dak_kandidat'];
			if($status_dak_kandidat == 0){
				$status = "Novi";
			}elseif($status_dak_kandidat == 1){
				$status = "U obradi";
			}elseif($status_dak_kandidat == 2){
				$status = "Poslan";
			}elseif($status_dak_kandidat == 3){
				$status = "Aktivan";
			}elseif($status_dak_kandidat == 4){
				$status = "Završen";
			}elseif($status_dak_kandidat == 5){
				$status = "Storniran poslije aktivacije";
			}else{
				$status = "Storniran";
			}
			
			if($row['rata1_dak_kandidat'] == 1){
				$rata1_dak_kandidat = "DA";
			}else{
				$rata1_dak_kandidat = "NE";
			}
			
			if($row['rata2_dak_kandidat'] == 1){
				$rata2_dak_kandidat = "DA";
			}else{
				$rata2_dak_kandidat = "NE";
			}
			
			?>
				<tr>
					<td style="width:30px;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $sumCount++; ?></td>
					<td style="width:250;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $name_dak_kandidat." ".$lastname_dak_kandidat; ?></td>
					<td style="width:200;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $datumunosa_dak_kandidat; ?></td>
					<td style="width:100;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $porijeklo; ?></td>
					<td style="width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $status; ?></td>
					<td style="width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $rata1_dak_kandidat; ?></td>
					<td style="width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $rata2_dak_kandidat; ?></td>
				</tr>
			
			<?php
		}
		?>
			</tbody>
		</table>
		<script>
		$(document).ready(function(){
			$("#table_mail_export").table2excel({
				exclude: ".noExl",
				name: "Dak(<?php echo $status;?>)",
				filename: "dak(<?php echo $f_from;?>)",
				fileext: ".xls"
			}); 	  
		})
		</script>
		</body>
		</html>
	<?php 
break;

case "export_predracuna":

	$f_from_flat = $_GET['datum_range_start'];
	$f_to_flat = $_GET['datum_range_end'];
	
	if($f_from_flat != NULL){
		$datum_od = date('Y-m-d 00:00:00', strtotime($f_from_flat));
	}else{
		$datum_od = date("Y-m-d H:i:s");
	}
	if($f_to_flat != NULL){
		$datum_do = date('Y-m-d 23:59:00', strtotime($f_to_flat));
	}else{
		$datum_do = date("Y-m-d H:i:s");
	}
	$f_from_f = date('Y-m-d H:i:s', strtotime($datum_od));
	$f_to_f = date('Y-m-d H:i:s', strtotime($datum_do));	 
	$datum_range_query = "pr_datum_kreiranja between '$f_from_f' AND '$f_to_f'";
	
	$f_from_flat_du = $_GET['du_start'];
	$f_to_flat_du = $_GET['du_end'];
	
	if($f_from_flat_du != NULL){
		$datum_od_du = date('Y-m-d 00:00:00', strtotime($f_from_flat_du));
		$f_from_f_du = date('Y-m-d H:i:s', strtotime($datum_od_du));
		if($f_to_flat_du != NULL){
			$datum_do_du = date('Y-m-d 23:59:00', strtotime($f_to_flat_du));
			$f_to_f_du = date('Y-m-d H:i:s', strtotime($datum_do_du));	
			$datum_range_query_du = "pr_datum_uplate between '$f_from_f_du' AND '$f_to_f_du' AND ";
		}else{
			$datum_range_query_du = "";
		}
	}else{
		$datum_range_query_du = "";
	}
	
	$pr_domaca_valuta = $_GET['domaca_valuta'];
	$tip_uplate = $_GET['tip_uplate'];
	$rate = $_GET['rate'];
	$status_query = $_GET['status_query'];
	
	if($team_id == 1){
		$query_team = "";
	}else{
		$query_team = "employee_team = ".$team_id." AND";
	}
	
	?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php include('includes/head.php'); ?>

</head>
<body>
<?php
	
	$query = $db->prepare("
                        SELECT pr_datum_uplate, pr_datum_kreiranja, pr_kandidat_id, pr_rata, pr_broj_predracuna, pr_domaca_valuta, pr_vrijednost_BAM, pr_vrijednost_RSD, idk_employees.employee_firstname, idk_employees.employee_lastname
                        FROM idk_predracuni
						INNER JOIN idk_employees ON idk_predracuni.pr_zaposlenik = idk_employees.employee_id  
                        WHERE $query_team pr_domaca_valuta = '$pr_domaca_valuta' AND ".$datum_range_query." AND ".$datum_range_query_du."pr_uplaceno IN ($tip_uplate) AND pr_rata IN ($rate) AND pr_status IN ($status_query) AND pr_naplata_preko IN(0,2)");

    $query->execute();
	
	print_r($query->errorInfo());
	
		?>
		
		<table class="table" id="table_predracuni_export" style="100%;font-weight:bold!important;" class="display">
			<thead>
				<tr>
					<th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
					<th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime kandidata</th>
					<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum kreiranja</th>
					<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum uplate</th>
					<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Agent</th>
					<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Broj</th>
					<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Rata</th>
					<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Vrijednost</th>
					<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Valuta</th>
				</tr>
			</thead>
			<tbody>
		<?php
		$sumCount = 1;
		while($row = $query->fetch()){
			
			$pr_datum_kreiranja = date('d.m.Y', strtotime($row['pr_datum_kreiranja']));
			if($row['pr_datum_uplate'] != null)
				$pr_datum_uplate = date('d.m.Y', strtotime($row['pr_datum_uplate']));
			else
				$pr_datum_uplate = "-";
			$kandidat_name = getImePrezimeNDKanR($row['pr_kandidat_id']);
			$pr_rata = $row['pr_rata'];
			$pr_broj_predracuna = $row['pr_broj_predracuna'];
			$pr_kandidat_id	 = $row['pr_kandidat_id	'];
			$pr_domaca_valuta	 = $row['pr_domaca_valuta'];
			$pr_vrijednost_BAM	 = $row['pr_vrijednost_BAM'];
			$pr_vrijednost_RSD	 = $row['pr_vrijednost_RSD'];
			$employee_firstname	 = $row['employee_firstname'];
			$employee_lastname	 = $row['employee_lastname'];
			if($pr_domaca_valuta == "BAM"){
				$pr_vrijednost = $pr_vrijednost_BAM;
			}else{
				$pr_vrijednost = $pr_vrijednost_RSD;
			}
			
			?>
				<tr>
					<td style="width:30px;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $sumCount++; ?></td>
					<td style="width:250;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $kandidat_name; ?></td>
					<td style="width:200;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $pr_datum_kreiranja; ?></td>
					<td style="width:200;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $pr_datum_uplate; ?></td>
					<td style="width:200;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $employee_firstname." ".$employee_lastname; ?></td>
					<td style="width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $pr_broj_predracuna; ?></td>
					<td style="width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $pr_rata; ?></td>
					<td style="mso-number-format:\#\,\#\#0\.00;width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $pr_vrijednost; ?></td>
					<td style="width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $pr_domaca_valuta; ?></td>
				</tr>
			
			<?php
		}
		?>
			</tbody>
		</table>
		<script>
		$(document).ready(function(){
			$("#table_predracuni_export").table2excel({
				exclude: ".noExl",
				name: "Predracuni",
				filename: "Predracuni",
				fileext: ".xls"
			}); 	  
		})
		</script>
		</body>
		</html>
	<?php 
break;

case "export_DIPL":
	$uex = intval($_POST["uex"]);
	$teamex = intval($_POST["teamex"]);
	$employess_id_team = implode(", ", getIdOfEmployeeTeam($teamex));
	if($teamex == 1){
		$uslov_zap_quer = " zaduzen_zaposlenik_nd_kandidata is not null";
	}else{
		$uslov_zap_quer = " zaduzen_zaposlenik_nd_kandidata IN (".$employess_id_team.")";
	}
	if($uex == 1){
		// $tr_vr_xx = date('Y-m-d H:i:s');
		// $vr_za_ispis_xx = date('Y-m-d H:i:s',strtotime('-1 month',strtotime($tr_vr_xx)));
		$puniquery = "SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, dodao_zaposlenik_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, povijest_vrsta_nd_kandidata, kampanja_id FROM idk_nd_kandidata WHERE vrijeme_kreiranja_nd_kandidata is not null AND ".$uslov_zap_quer." ";
	}else if($uex == 2){
		
		$ue1 =  explode( ',', $_POST["ue1"]);
		$ue2 =  explode( ',', $_POST["ue2"]);
		$ue9 =  explode( ',', $_POST["ue9"]);
		$ue3 = $_POST["ue3"];
		$ue4 = $_POST["ue4"];
		$ue11 =  explode( ',', $_POST["ue11"]);
		$ue15 = (($_REQUEST['ue15'] != "") ? explode( ',', $_REQUEST['ue15']) : null);
		$ue5 =  explode( ',', $_POST["ue5"]);
		$ue6 =  explode( ',', $_POST["ue6"]);
		$ue7 =  explode( ',', $_POST["ue7"]);
		$ue8 =  explode( ',', $_POST["ue8"]);
		$ue10 =  explode( ',', $_POST["ue10"]);
		$ue12 =  explode( ',', $_POST["ue12"]);
		$ue13 = explode( ',',$_POST["ue13"]);
		if($_POST["ue13"] != 0){
			$glavni_query = "SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, 
									dodao_zaposlenik_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, 
									povijest_vrsta_nd_kandidata, kampanja_id, skola_naziv, ss_naziv, naziv_struke, kandidat_status_prijave
									FROM idk_nd_kandidata 
									LEFT JOIN idk_skole on skola_nd_kandidata = idk_skole.skola_id
									LEFT JOIN idk_skole_smjerovi on skola_smjer_nd_kandidata = idk_skole_smjerovi.ss_id
									LEFT JOIN idk_struke on idk_skole_smjerovi.ss_struka_id = idk_struke.id_struke
									LEFT JOIN idk_kandidati ON id_broj_nd_kandidata = kandidat_dipl_id 
									WHERE id_broj_nd_kandidata > 0 AND ".$uslov_zap_quer." ";

		}
		else{
			$glavni_query = "SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, 
									dodao_zaposlenik_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, 
									povijest_vrsta_nd_kandidata, kampanja_id, skola_naziv, ss_naziv, naziv_struke
									FROM idk_nd_kandidata 
									LEFT JOIN idk_skole on skola_nd_kandidata = idk_skole.skola_id
									LEFT JOIN idk_skole_smjerovi on skola_smjer_nd_kandidata = idk_skole_smjerovi.ss_id
									LEFT JOIN idk_struke on idk_skole_smjerovi.ss_struka_id = idk_struke.id_struke
									WHERE id_broj_nd_kandidata > 0 AND ".$uslov_zap_quer." ";

 		}
		
		$subqueryUe15 = ""; 
		if ($ue15 != null) {
			$ue15_imp = implode(",", $ue15);
			$subqueryUe15 = "
				AND id_broj_nd_kandidata IN (
					SELECT 
						kb.id_kandidata_biljeska_nd
					FROM 
						idk_nd_kandidata_biljeske kb 
					JOIN 
						idk_nd_kandidata kan 
					ON 
						kb.id_kandidata_biljeska_nd = kan.id_broj_nd_kandidata
						AND 
						(
							(
								kan.status_nd_kandidata = 1
								AND 
								kan.pstatus_nd_kandidata = 4
							) 
							OR 
							(
								kan.status_nd_kandidata = 7
							)
						)
					JOIN 
						(
							SELECT 
								kb1.id_kandidata_biljeska_nd AS biljeskaKandidatId, 
								MAX(kb1.id_biljeska_nd) AS biljeskaId
							FROM 
								idk_nd_kandidata_biljeske kb1 
							WHERE  
								kb1.status_biljeska_nd = 2 
								AND 
								kb1.tip_biljeska_nd IN (3,15,13) 
							GROUP BY
								kb1.id_kandidata_biljeska_nd
						) AS kbmax
					ON 
						kb.id_biljeska_nd = kbmax.biljeskaId
					WHERE 
						kb.razlog_biljeska_nd IN (
							".$ue15_imp."
						)
				)
			";
		}

		$glavni_query = $glavni_query." ".$subqueryUe15;
		
		if($_POST["ue1"] != ""){
			$f_u1 = $ue1;
			$f_u1x = implode(',', $f_u1);
			if(in_array("11", $f_u1)){
				$ps11 = "1";
			}
			else{
				$ps11 = "0";
			}
			if(in_array("12", $f_u1)){
				$ps12 = "2";
			}
			else{
				$ps12 = "0";
			}
			if(in_array("13", $f_u1)){
				$ps13 = "3"; 
			}
			else{
				$ps13 = "0";
			}
			if(in_array("14", $f_u1)){
				$ps14 = "4"; 
			}
			else{
				$ps14 = "0";
			}
			if(in_array("15", $f_u1)){
				$ps15 = "5"; 
			}
			else{
				$ps15 = "0";
			}
			if(in_array("16", $f_u1)){
				$ps16 = "6"; 
			}
			else{
				$ps16 = "0";
			}
			//111Adis222 15 START
			if(in_array("17", $f_u1)){
				$ps17 = "7"; 
			}
			else{
				$ps17 = "0";
			}
			if(in_array("18", $f_u1)){
				$ps18 = "8"; 
			}
			else{
				$ps18 = "0";
			}
			if(in_array("19", $f_u1)){
				$ps19 = "9"; 
			}
			else{
				$ps19 = "0";
			}
			if(in_array("20", $f_u1)){
				$ps20 = "10"; 
			}
			else{
				$ps20 = "0";
			}
			if(in_array("21", $f_u1)){
				$ps21 = "11"; 
			}
			else{
				$ps21 = "0";
			}
			if(in_array("22", $f_u1)){
				$ps22 = "12"; 
			}
			else{
				$ps22 = "0";
			}
			//111Adis222 15 START
			$uslov_1 = "AND (status_nd_kandidata IN (".$f_u1x.") OR (status_nd_kandidata = 1 AND pstatus_nd_kandidata IN(".$ps11.",".$ps12.",".$ps13.",".$ps14.",".$ps15.",".$ps16.",".$ps17.",".$ps18.",".$ps19.",".$ps20.",".$ps21.",".$ps22.")))";
		}else{
			$uslov_1  = "";
		}
		if($_POST["ue2"] != ""){
			$f_u2 = $ue2;
			$f_u2x = implode(',', $f_u2);
			if(in_array("0", $f_u2)){
				$pu1 = "0";
				$puv1 = " is null";
			}
			else{
				$pu1 = "10";
				$puv1 = " = 10";
			}
			if(in_array("1", $f_u2)){
				$pu2 = "1";
				$puv2 = "1";
			}
			else{
				$pu2 = "10";
				$puv2 = "10";
			}
			if(in_array("2", $f_u2)){
				$pu3 = "1";
				$puv3 = "2";
			}
			else{
				$pu3 = "10";
				$puv3 = "10";
			}
			if(in_array("3", $f_u2)){
				$pu4 = "1";
				$puv4 = "3";
			}
			else{
				$pu4 = "10";
				$puv4 = "10";
			}
			if(in_array("4", $f_u2)){
				$pu5 = "1";
				$puv5 = "4";
			}
			else{
				$pu5 = "10";
				$puv5 = "10";
			}
			if(in_array("5", $f_u2)){
				$pu6 = "2";
				$puv6 = "1";
			}
			else{
				$pu6 = "10";
				$puv6 = "10";
			}
			if(in_array("6", $f_u2)){
				$pu7 = "2";
				$puv7 = "2";
			}
			else{
				$pu7 = "10";
				$puv7 = "10";
			}
			if(in_array("7", $f_u2)){
				$pu8 = "3";
				$puv8 = "1";
			}
			else{
				$pu8 = "10";
				$puv8 = "10";
			}
			if(in_array("8", $f_u2)){
				$pu9 = "4";
				$puv9 = "1";
			}
			else{
				$pu9 = "10";
				$puv9 = "10";
			}
			if(in_array("9", $f_u2)){
				$pu10 = "1";
				$puv10 = "5";
			}
			else{
				$pu10 = "10";
				$puv10 = "10";
			}
			if(in_array("10", $f_u2)){
				$pu11 = "5";
				$puv11 = "1";
				if($_POST["ue9"] != ""){
					$f_u8 = $ue9;
					$f_u8x = implode(',', $f_u8);
					$uslov_8 = "AND kampanja_id IN (".$f_u8x.") ";
				}
				else{
					$uslov_8 = "";
				}
			}
			else{
				$pu11 = "10";
				$puv11 = "10";
				$uslov_8 = "";
				//OR (povijest_nd_kandidata = ".$pu11." AND povijest_vrsta_nd_kandidata = ".$puv11.") 
			}
			$uslov_2 = "AND (
								(povijest_nd_kandidata = ".$pu1." AND povijest_vrsta_nd_kandidata".$puv1.") OR
								(povijest_nd_kandidata = ".$pu2." AND povijest_vrsta_nd_kandidata = ".$puv2.") OR
								(povijest_nd_kandidata = ".$pu3." AND povijest_vrsta_nd_kandidata = ".$puv3.") OR
								(povijest_nd_kandidata = ".$pu4." AND povijest_vrsta_nd_kandidata = ".$puv4.") OR
								(povijest_nd_kandidata = ".$pu5." AND povijest_vrsta_nd_kandidata = ".$puv5.") OR
								(povijest_nd_kandidata = ".$pu6." AND povijest_vrsta_nd_kandidata = ".$puv6.") OR
								(povijest_nd_kandidata = ".$pu7." AND povijest_vrsta_nd_kandidata = ".$puv7.") OR
								(povijest_nd_kandidata = ".$pu8." AND povijest_vrsta_nd_kandidata = ".$puv8.") OR
								(povijest_nd_kandidata = ".$pu9." AND povijest_vrsta_nd_kandidata = ".$puv9.") OR
								(povijest_nd_kandidata = ".$pu10." AND povijest_vrsta_nd_kandidata = ".$puv10.") OR 
								(povijest_nd_kandidata = ".$pu11." AND povijest_vrsta_nd_kandidata = ".$puv11." ".$uslov_8.")
							)";
		}else{
			$uslov_2  = "";
		}
		if(($_POST["ue3"] != "") AND ($_POST["ue4"] != "")){
			$datum_ulaskaod_filx = $ue3." 00:00:00";
			$datum_ulaskado_filx = $ue4." 23:59:59";
			$datum_ulaska_od_filx = date("Y-m-d H:i:s", strtotime($datum_ulaskaod_filx));
			$datum_ulaska_do_filx = date("Y-m-d H:i:s", strtotime($datum_ulaskado_filx));
			$uslov_3 = "AND vrijeme_kreiranja_nd_kandidata BETWEEN '".$datum_ulaska_od_filx."' AND '".$datum_ulaska_do_filx."'  ";
		}else{
			$uslov_3 = "";
		}
		if($_POST["ue11"] != ""){
			$f_struke = implode(',', $ue11);
			$uslov_struke = "AND skola_smjer_nd_kandidata IN (SELECT ss_id FROM idk_skole_smjerovi WHERE ss_struka_id IN (".$f_struke.")) ";
		}
		else{
			$uslov_struke = "";
		}
		if($_POST["ue5"] != ""){
			$f_u4 = $ue5;
			$f_u4x = implode(',', $f_u4);
			$uslov_4 = "AND skola_nd_kandidata IN (".$f_u4x.") ";
		}
		else{
			$uslov_4 = "";
		}
		if($_POST["ue6"] != ""){
			$f_u5 = $ue6;
			$f_u5x = implode(',', $f_u5);
			$uslov_5 = "AND skola_smjer_nd_kandidata IN (".$f_u5x.") ";
		}
		else{
			$uslov_5 = "";
		}
		if($_POST["ue7"] != ""){
			$f_u6 = $ue7;
			$f_u6x = implode(',', $f_u6);
			$uslov_6 = "AND zaduzen_zaposlenik_nd_kandidata IN (".$f_u6x.") ";
		}
		else{
			$uslov_6 = "";
		}
		if($_POST["ue8"] != ""){
			$f_u7 = $ue8;
			$f_u7x = implode(',', $f_u7);
			$uslov_7 = "AND vrsta_ugovora_nd_kandidata IN (".$f_u7x.") ";
		}
		else{
			$uslov_7 = "";
		}
		if($_POST['ue10'] != ""){
			$f_u8 = $ue10;
			$f_u8x = implode(',', $f_u8);
			
			$pu_bih = "";
			$pu_srb = "";
			$pu_de = "";
			$pu_ost = "";
			
			if(in_array("387", $f_u8)){
				$pu_bih = "(mobilni_nd_kandidata LIKE '+387%')";
			}else{
				$pu_bih = "(mobilni_nd_kandidata = '0000')";
			} 
			if(in_array("381", $f_u8)){
				$pu_srb = "(mobilni_nd_kandidata LIKE '+381%')";
			}else{
				$pu_srb = "(mobilni_nd_kandidata = '0000')";
			} 
			if(in_array("49", $f_u8)){
				$pu_de = "(mobilni_nd_kandidata LIKE '+49%')";
			}else{
				$pu_de = "(mobilni_nd_kandidata = '0000')";
			} 
			if(in_array("ostalo", $f_u8)){
				$pu_ost = "((mobilni_nd_kandidata NOT LIKE '+387%' AND mobilni_nd_kandidata NOT LIKE '+381%' AND mobilni_nd_kandidata NOT LIKE '+49%') OR mobilni_nd_kandidata IS NULL )";
			}else{
				$pu_ost = "mobilni_nd_kandidata = '0000'";
			}
			$uslov_8 = "AND (
				".$pu_bih." OR 
				".$pu_srb." OR 
				".$pu_de." OR 
				".$pu_ost."
			)";
		}
		else{
			$uslov_8 = "";
		}
		if($_REQUEST['ue12'] != ""){
			$f_u9 = $ue12;
			$f_u9x = implode(',', $f_u9);
			$uslov_9 = "AND nivo_poznavanja_jezika IN (".$f_u9x.") ";
		}
		else{
			$uslov_9 = "";
		}
		if($_REQUEST['ue13'] != ""){
			$f_u10 = $ue13;
			$f_u10x = implode(',', $f_u10);
			$uslov_10 = "AND kandidat_status_prijave IN (".$f_u10x.") ";
		}
		else{
			$uslov_10 = "";
		}
		$puniquery = $glavni_query." ".$uslov_1." ".$uslov_2." ".$uslov_3." ".$uslov_struke." ".$uslov_4." ".$uslov_5." ".$uslov_6." ".$uslov_7." ".$uslov_8." ".$uslov_9." ".$uslov_10;
	}
?>
	<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php //include('includes/head.php'); ?>

</head>
<body>
	<?php 
		if($puniquery != ""){
			$file_name_ex = date('dmYHis');
			$query_export = $db->prepare("".$puniquery."");
			$query_export->execute();
	?>
		<table class="table" id="table_export_dipl" style="100%;font-weight:bold!important;" class="display">
			<thead>
				<tr>
					<th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
					<th style="width:200px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime</th>
					<th style="width:200px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Telefon</th>
					<th style="width:200px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Email</th>
					<th style="width:200px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Status</th>
					<th style="width:100px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Kreiran</th>
					<th style="width:100px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Kreirao</th>
					<th style="width:100px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Kampanja</th>
					<th style="width:100px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Menadžer</th>
					<th style="width:100px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Škola</th>
					<th style="width:100px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Smjer</th>
					<th style="width:100px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Struka</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$brojac = 0;
				while($query_export_row = $query_export->fetch()){
					$brojac++;
					$id_nd_kandidata_ispis = $query_export_row['id_broj_nd_kandidata'];
					$ime_nd_kandidata_ispis = $query_export_row['ime_nd_kandidata'];
					$prezime_nd_kandidata_ispis = $query_export_row['prezime_nd_kandidata'];
					$mobilni_nd_kandidata_ispis = $query_export_row['mobilni_nd_kandidata'];
					$email_nd_kandidata_ispis = $query_export_row['email_nd_kandidata'];
					$vrijeme_kreiranja_nd_kandidata_ispis = date('d.m.Y H:i', strtotime($query_export_row['vrijeme_kreiranja_nd_kandidata']));
					$dodao_zaposlenik_nd_kandidata_ispis = getZaposlenikimeR($query_export_row['dodao_zaposlenik_nd_kandidata']);
					$zaduzen_zaposlenik_nd_kandidata_ispis = getZaposlenikimeR($query_export_row['zaduzen_zaposlenik_nd_kandidata']);
					$status_nd_kandidata_ispis = $query_export_row['status_nd_kandidata'];
					$pstatus_nd_kandidata_ispis = $query_export_row['pstatus_nd_kandidata'];
					$povijest_nd_kandidata_ispis = $query_export_row['povijest_nd_kandidata'];
					$povijest_vrsta_nd_kandidata_ispis = $query_export_row['povijest_vrsta_nd_kandidata'];
					$skola_naziv_ispis = $query_export_row['skola_naziv'];
					$ss_naziv_ispis = $query_export_row['ss_naziv'];
					$naziv_struke_ispis = $query_export_row['naziv_struke'];
					$kampanja = "NEMA";
					
					if($povijest_nd_kandidata_ispis == 0){
						$kreirao_nd_kandidata = $dodao_zaposlenik_nd_kandidata_ispis;
					}
					else if($povijest_nd_kandidata_ispis == 1){
						if($povijest_vrsta_nd_kandidata_ispis == 1){
							$povijest_vrsta_ispisx = 'SMS';
						}
						else if($povijest_vrsta_nd_kandidata_ispis == 2){
							$povijest_vrsta_ispisx = 'JobStep Messenger';
						}
						else if($povijest_vrsta_nd_kandidata_ispis == 3){
							$povijest_vrsta_ispisx = 'CRM';
						}
						else if($povijest_vrsta_nd_kandidata_ispis == 4){
							$povijest_vrsta_ispisx = 'Viber';
						}
						else if($povijest_vrsta_nd_kandidata_ispis == 4){
							$povijest_vrsta_ispisx = 'Viber - Stornirani';
						}
						$kreirao_nd_kandidata = "Kandidati -".$povijest_vrsta_ispisx;
					}else if($povijest_nd_kandidata_ispis == 2){
						if($povijest_vrsta_nd_kandidata_ispis == 1){
							$povijest_vrsta_ispisx = 'APP';
						}
						else if($povijest_vrsta_nd_kandidata_ispis == 2){
							$povijest_vrsta_ispisx = 'WEB';
						}
						$kreirao_nd_kandidata = "Sve za vize ".$povijest_vrsta_ispisx;
					}
					else if($povijest_nd_kandidata_ispis == 3){
						$kreirao_nd_kandidata = "JobStep Partner APP";
					}
					else if($povijest_nd_kandidata_ispis == 4){
						$kreirao_nd_kandidata = "JobStep Web";
					}
					else if($povijest_nd_kandidata_ispis == 5){
						$kreirao_nd_kandidata = "Kampanje";
						$kampanja = getKampanjeSkrNazivDIPLR($query_export_row['kampanja_id']);
					}
					
					if($status_nd_kandidata_ispis == 1){
						if($pstatus_nd_kandidata_ispis == 1){
							$pstatus_nd_kandidata_ispis1 = 'Lead';
						}
						else if($pstatus_nd_kandidata_ispis == 2){
							$pstatus_nd_kandidata_ispis1 = 'Neuspješan Kontakt 3';
						}
						else if($pstatus_nd_kandidata_ispis == 3){
							$pstatus_nd_kandidata_ispis1 = 'Zainteresiran Lead';
						}
						else if($pstatus_nd_kandidata_ispis == 4){
							$pstatus_nd_kandidata_ispis1 = 'Nezainteresiran Lead';
						}
						else if($pstatus_nd_kandidata_ispis == 5){
							$pstatus_nd_kandidata_ispis1 = 'U obradi Lead';
						}
						else if($pstatus_nd_kandidata_ispis == 6){
							$pstatus_nd_kandidata_ispis1 = 'Neuspješan Kontakt 1';
						}
						else if($pstatus_nd_kandidata_ispis == 7){
							$pstatus_nd_kandidata_ispis1 = 'Neuspješan Lead 1';
						}
						else if($pstatus_nd_kandidata_ispis == 8){
							$pstatus_nd_kandidata_ispis1 = 'Neuspješan Lead 2';
						}
						else if($pstatus_nd_kandidata_ispis == 9){
							$pstatus_nd_kandidata_ispis1 = 'Termin zainteresiran';
						}
						else if($pstatus_nd_kandidata_ispis == 10){
							$pstatus_nd_kandidata_ispis1 = 'Termin ostali';
						}
						else if($pstatus_nd_kandidata_ispis == 11){
							$pstatus_nd_kandidata_ispis1 = 'Lead NL';
						}
						else if($pstatus_nd_kandidata_ispis == 12){
							$pstatus_nd_kandidata_ispis1 = 'Lead NZ';
						}
						$status_nd_kandidata_ispis1 = $pstatus_nd_kandidata_ispis1;
					}
					else if($status_nd_kandidata_ispis == 2){
						$status_nd_kandidata_ispis1 = "Prikupljanje dokumentacije";
					}
					else if($status_nd_kandidata_ispis == 3){ 
						$status_nd_kandidata_ispis1 = "Poslana pošta";
					}
					else if($status_nd_kandidata_ispis == 4){
						$status_nd_kandidata_ispis1 = "U obradi";
					}
					else if($status_nd_kandidata_ispis == 5){
						$status_nd_kandidata_ispis1 = "Dopuna dokumentacije";
					}
					else if($status_nd_kandidata_ispis == 6){
						$status_nd_kandidata_ispis1 = "Završen";
					}
					else if($status_nd_kandidata_ispis == 7){
						$status_nd_kandidata_ispis1 = "Arhiviran";
					}
			?>
				<tr>
					<td style="width:30px;min-height:25px;text-align:center;padding:15px 15px; border-bottom: 1px solid #111;"><?php echo $brojac; ?></td>
					<td style="width:200px;min-height:25px;text-align:center;padding:15px 15px; border-bottom: 1px solid #111;"><?php echo $ime_nd_kandidata_ispis." ".$prezime_nd_kandidata_ispis; ?></td>
					<td style="width:200px;min-height:25px;text-align:center;padding:15px 15px; border-bottom: 1px solid #111;"><?php echo $mobilni_nd_kandidata_ispis; ?></td>
					<td style="width:200px;min-height:25px;text-align:center;padding:15px 15px; border-bottom: 1px solid #111;"><?php echo $email_nd_kandidata_ispis; ?></td>
					<td style="width:200px;min-height:25px;text-align:center;padding:15px 15px; border-bottom: 1px solid #111;"><?php echo $status_nd_kandidata_ispis1; ?></td>
					<td style="width:100px;min-height:25px;text-align:center;padding:15px 15px; border-bottom: 1px solid #111;"><?php echo $vrijeme_kreiranja_nd_kandidata_ispis; ?></td>
					<td style="width:100px;min-height:25px;text-align:center;padding:15px 15px; border-bottom: 1px solid #111;"><?php echo $kreirao_nd_kandidata; ?></td>
					<td style="width:100px;min-height:25px;text-align:center;padding:15px 15px; border-bottom: 1px solid #111;"><?php echo $kampanja; ?></td>
					<td style="width:100px;min-height:25px;text-align:center;padding:15px 15px; border-bottom: 1px solid #111;"><?php echo $zaduzen_zaposlenik_nd_kandidata_ispis; ?></td>
					<td style="width:100px;min-height:25px;text-align:center;padding:15px 15px; border-bottom: 1px solid #111;"><?php echo $skola_naziv_ispis; ?></td>
					<td style="width:100px;min-height:25px;text-align:center;padding:15px 15px; border-bottom: 1px solid #111;"><?php echo $ss_naziv_ispis; ?></td>
					<td style="width:100px;min-height:25px;text-align:center;padding:15px 15px; border-bottom: 1px solid #111;"><?php echo $naziv_struke_ispis; ?></td>
				</tr>
			<?php 
				}
			?>
			</tbody>
		</table>
		<script>
			$(document).ready(function(){
				$("#table_export_dipl").table2excel({
					exclude: ".noExl",
					name: "DIPL(<?php echo $file_name_ex ;?>)",
					filename: "DIPL_exp(<?php echo $file_name_ex ;?>)",
					fileext: ".xls"
				}); 	  
			})
		</script>
	<?php
		}
		else{
	?>
		<div class = "row">
			<div class = "col-xs-12">
				<div class="alert material-alert material-alert_danger">Postoji problem sa exportom! Obratite se administratoru sistema!</div>
			</div>
		</div>
	<?php	
		}
	?>
</body>
</html>
<?php	
	break;
	
	case "export_mail_mikrofin":


		$datum_za_slati = $_GET['datum'];
		
		$novi_datum = date('Y-m-d', $datum_za_slati);
		
		$juce_poct = date('Y-m-d 00:00:00', strtotime($novi_datum." - 1 days"));
		$juce_kraj = date('Y-m-d 23:59:59', strtotime($novi_datum." - 1 days"));
		// $juce_poct = date('2020-12-14 00:00:00');
		// $juce_kraj = date('2020-12-23 00:00:00');
		$datum = date('d.m.Y', $datum_za_slati);
		//$datum = date('16.12.2020');
		
		?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php getTitle(); ?></title>

		<?php include('includes/head.php'); ?>

	</head>
	<body>
	<?php
		
		$query = $db->prepare("
							SELECT pr_broj_predracuna, pr_file, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.grad_nd_kandidata, kan.ulica_nd_kandidata, kan.postanski_broj_nd_kandidata, kan.jmbg_nd_kandidata, kan.mobilni_nd_kandidata
							FROM idk_predracuni pre
							JOIN idk_nd_kandidata kan
							ON pre.pr_kandidat_id = kan.id_broj_nd_kandidata
							WHERE (pr_datum_kreiranja BETWEEN :juce_poct AND :juce_kraj)
							AND pr_domaca_valuta = 'BAM'
							AND (kan.vrsta_ugovora_nd_kandidata = 11 OR kan.vrsta_ugovora_nd_kandidata = 12)");

		$query->execute(array(
						':juce_poct' => $juce_poct,
						':juce_kraj' => $juce_kraj
		));
		
			?>
				
			<table class="" id="table_mf_export" style="50%;font-weight:bold!important;" class="display">
				<thead>
					<tr>
						<th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
						<th style="width:100;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime</th>
						<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Prezime</th>
						<th style="width:300px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Adresa</th>
						<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Broj telefona</th>
						<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">JMBG</th>
						<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Predračun</th>
					</tr>
				</thead>
				<tbody>
			<?php
			$ct = 1;
			while($row_number = $query->fetch()){
				$ime = $row_number["ime_nd_kandidata"];
				$prezime = $row_number["prezime_nd_kandidata"];
				$ulica = $row_number["ulica_nd_kandidata"];
				$pbroj = $row_number["postanski_broj_nd_kandidata"];
				$grad = $row_number["grad_nd_kandidata"];
				$adresa = $ulica.", ".$pbroj." ".$grad;
				$jmbg = $row_number["jmbg_nd_kandidata"];
				$pr_file = $row_number["pr_file"];
				$broj_predracuna = $row_number["pr_broj_predracuna"];
				$mobilni_nd_kandidata = $row_number["mobilni_nd_kandidata"];
				
				?>
					<tr>
						<td style="width:30px;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $ct++; ?></td>
						<td style="width:100;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $ime; ?></td>
						<td style="width:100;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $prezime; ?></td>
						<td style="width:300;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $adresa; ?></td>
						<td style="mso-number-format:'\@'; width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $mobilni_nd_kandidata; ?></td>
						<td style="mso-number-format:'\@'; width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $jmbg; ?></td>
						<td style="width:150;min-height:25px;text-align:center;padding:15px 15px;"><a href="<?php getSiteUrl(); ?>files/predracuni_dipl/<?php echo $pr_file; ?>" target="_BLANK" ><?php echo $broj_predracuna; ?></td>
					
					</tr>
			
			<?php
			}
			?>
				</tbody>
			</table>
			<script>
			$(document).ready(function(){
				var tabelaaa = $("#table_mf_export").table2excel({
					exclude: ".noExl",
					name: "Predracuni(<?php echo $datum;?>)",
					filename: "predracuni(<?php echo $datum;?>)",
					fileext: ".xls"
				});
				/*console.log(tabelaaa);
				$.ajax({
					url: 'https://jobstep-app.com/cron_mail_mikrofin.php',
					type: 'POST',
					data: {"tabelaaa": tabelaaa},
					dataType: 'json',
					success: function(data) {
						
					}
				});*/
			})
			</script>
			</body>
			</html>
		<?php 
    break;
    case "export_lista_provzija":

        $selectedis = $_GET['selectedis'];
        $idArray = explode(",",$selectedis);
        
        $f_from = $_GET['f_from'];
        
        if (strpos($f_from, 'to') !== false) {
            $split = explode("to",$f_from);
            $datum_od = $split[0];
            $datum_do = $split[1];
        }else{
            $datum_od = "2021-01-01 00:00:00";
            $datum_do = date("Y-m-d H:i:s");
        }
        
        $f_from_f = date('Y-m-d 00:00:00', strtotime($datum_od));
        $f_to_f = date('Y-m-d 23:59:59', strtotime($datum_do));	
        // var_dump($datum_od);
        // var_dump($datum_do);
        // exit();
    ?>
    <div class="" style="height:0px;overflow:hidden;">
        <table class="table" id="lista_provizija" style="100%;" class="display">
            <thead>
                <tr>
                    <th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
                    <th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime</th>
                    <th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Odjel</th>
                    <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Moguća uplata</th>
                    <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Konto</th>
                    <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Isplaćeno</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sumCount = 1;
                foreach($idArray as $employee_id){
                    //employee_firstname, employee_lastname, employee_odjel
                    $get_provizije = $db->prepare("
                                    SELECT emp.employee_firstname, emp.employee_lastname, emp.employee_odjel,
                                    SUM(CASE WHEN status_predracuna = 0 AND status_obracuna = 0 AND vrijeme_kreiranja BETWEEN '$f_from_f' AND '$f_to_f' THEN iznos_obracuna_bam ELSE NULL END) as suma_bam_moguca,
                                    SUM(CASE WHEN status_predracuna = 1 AND status_obracuna = 0 AND vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f' THEN iznos_obracuna_bam ELSE NULL END) as suma_bam_konto,
                                    SUM(CASE WHEN status_predracuna = 1 AND status_obracuna = 1 AND vrijeme_isplate BETWEEN '$f_from_f' AND '$f_to_f' THEN iznos_obracuna_bam ELSE NULL END) as suma_bam_isplaceno
                                    FROM idk_obracuni
                                    JOIN idk_employees emp ON emp.employee_id = idk_obracuni.employee_id
                                    WHERE idk_obracuni.employee_id = :employee_id
                    ");
                    $get_provizije->execute(array(
                                    'employee_id' => $employee_id
                    ));
    
                    $row_prov = $get_provizije->fetch();
                    $employee_firstname = $row_prov['employee_firstname'];
                    $employee_lastname = $row_prov['employee_lastname'];
                    $suma_bam_moguca = $row_prov['suma_bam_moguca'];
                    $suma_bam_konto = $row_prov['suma_bam_konto'];
					$suma_bam_isplaceno = $row_prov['suma_bam_isplaceno'];
					if ($suma_bam_isplaceno == null)
						$suma_bam_isplaceno = 0;

                    if($row_prov['employee_odjel'] == 1){
                        $employee_odjel = "Uprava";
                    }elseif($row_prov['employee_odjel'] == 2){
                        $employee_odjel = "Financije";
                    }elseif($row_prov['employee_odjel'] == 3){
                        $employee_odjel = "Prodaja";
                    }elseif($row_prov['employee_odjel'] == 4){
                        $employee_odjel = "Obrada";
                    }elseif($row_prov['employee_odjel'] == 5){
                        $employee_odjel = "Sve za vizu";
                    }elseif($row_prov['employee_odjel'] == 6){
                        $employee_odjel = "Marketing";
                    }elseif($row_prov['employee_odjel'] == 7){
                        $employee_odjel = "Tehnika";
                    }elseif($row_prov['employee_odjel'] == 8){
                        $employee_odjel = "Development";
                    }elseif($row_prov['employee_odjel'] == 9){
                        $employee_odjel = "Ostalo";
                    }else{
                        $employee_odjel = "-";
                    }
                
                ?>
                <tr>
                    <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $sumCount++; ?></td>
                    <td style="width:250px; text-align:left;padding:15px 15px;" class="text-success"><?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></td>
                    <td style="width:100px; text-align:left;padding:15px 15px;"><?php echo $employee_odjel; ?></td>
					<td style="mso-number-format:\#\,\#\#0\.00;width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $suma_bam_moguca; ?></td>
					<td style="mso-number-format:\#\,\#\#0\.00;width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $suma_bam_konto; ?></td>
					<td style="mso-number-format:\#\,\#\#0\.00;width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $suma_bam_isplaceno; ?></td>

                </tr>
                <?php } ?>
            </tbody>
        </table>
        <script>
        $(document).ready(function(){
            $("#lista_provizija").table2excel({
                exclude: ".noExl",
                name: "listaProvizija(<?php echo date("Y-m-d");?>)",
                filename: "listaProvizija(<?php echo date("Y-m-d");?>)",
                fileext: ".xls"
            }); 	  
        })
        </script>	
    </div>
    
    <?php
        
    break;
	
	case "export_isplata_provizija":

		$danas =  date("Y-m-d H:i:s");
		$selectedis = $_GET['selectedis'];
		$idArray = explode(",",$selectedis);
        
        $f_from = $_GET['f_from'];
        
        if (strpos($f_from, 'to') !== false) {
            $split = explode("to",$f_from);
            $datum_od = $split[0];
            $datum_do = $split[1];
        }else{
            $datum_od = "2021-01-01 00:00:00";
            $datum_do = date("Y-m-d H:i:s");
        }
		$dan_p = date('d.m', strtotime($datum_od));
		$dan_k = date('d.m', strtotime($datum_do));
		$mjesec_p = date('m', strtotime($datum_od));
		$mjesec_k = date('m', strtotime($datum_do));
		if($mjesec_p == $mjesec_k){
			switch($mjesec_p){
				case "01": $mjesec = "Januar"; break;
				case "02": $mjesec = "Februar"; break;
				case "03": $mjesec = "Mart"; break;
				case "04": $mjesec = "April"; break;
				case "05": $mjesec = "Maj"; break;
				case "06": $mjesec = "Juni"; break;
				case "07": $mjesec = "Juli"; break;
				case "08": $mjesec = "August"; break;
				case "09": $mjesec = "Septembar"; break;
				case "10": $mjesec = "Oktobar"; break;
				case "11": $mjesec = "Novembar"; break;
				case "12": $mjesec = "Decembar"; break;
			}
		}else
			$mjesec = "";
        
        $f_from_f = date('Y-m-d 00:00:00', strtotime($datum_od));
        $f_to_f = date('Y-m-d 23:59:59', strtotime($datum_do));	
		
		//var_dump($update_obracuni->errorInfo());
        // var_dump($mjesec_p);
        // var_dump($mjesec);
        //exit();
    ?>
    <div class="" style="height:0px;overflow:hidden;">
        <table class="table" id="isplata_provizija" style="100%;" class="display">
            <thead>
                <tr>
                    <th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
                    <th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime</th>
                    <th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Odjel</th>
                    <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Isplaćeno</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sumCount = 1;
                foreach($idArray as $employee_id){
                    
					//GET RESTO
					$get_resto = $db->prepare("
                                    SELECT oo_iznos
                                    FROM idk_ostatci_obracuna
                                    WHERE oo_employee_id = :employee_id AND oo_status = 0
                    ");
                    $get_resto->execute(array(
                                    'employee_id' => $employee_id
                    ));
					$row_rest = $get_resto->fetch();
					$oo_iznos = $row_rest['oo_iznos'];
					if(is_null($oo_iznos))
						$oo_iznos = 0;
					
					
					//employee_firstname, employee_lastname, employee_odjel
                    $get_provizije = $db->prepare("
                                    SELECT emp.employee_firstname, emp.employee_lastname, emp.employee_odjel,
                                    SUM(CASE WHEN status_predracuna = 1 AND status_obracuna = 0 AND vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f' AND employee_poslovnica NOT IN (5,10) THEN iznos_obracuna_bam ELSE NULL END) as suma_bam_za_isplatiti
                                    FROM idk_obracuni
                                    JOIN idk_employees emp ON emp.employee_id = idk_obracuni.employee_id
                                    WHERE idk_obracuni.employee_id = :employee_id AND employee_poslovnica NOT IN (5,10)
                    ");
                    $get_provizije->execute(array(
                                    'employee_id' => $employee_id
                    ));
					
                    $row_prov = $get_provizije->fetch();
                    $employee_firstname = $row_prov['employee_firstname'];
                    $employee_lastname = $row_prov['employee_lastname'];
					$suma_bam_za_isplatiti = $row_prov['suma_bam_za_isplatiti'];
					if ($suma_bam_za_isplatiti == null)
						$suma_bam_za_isplatiti = 0;
					
					$ukupno = $suma_bam_za_isplatiti + $oo_iznos;
					$za_isplatiti = floor($ukupno);
					$resto = $ukupno - $za_isplatiti;
					$resto = number_format((float)$resto, 4, '.', '');

                    if($row_prov['employee_odjel'] == 1){
                        $employee_odjel = "Uprava";
                    }elseif($row_prov['employee_odjel'] == 2){
                        $employee_odjel = "Financije";
                    }elseif($row_prov['employee_odjel'] == 3){
                        $employee_odjel = "Prodaja";
                    }elseif($row_prov['employee_odjel'] == 4){
                        $employee_odjel = "Obrada";
                    }elseif($row_prov['employee_odjel'] == 5){
                        $employee_odjel = "Sve za vizu";
                    }elseif($row_prov['employee_odjel'] == 6){
                        $employee_odjel = "Marketing";
                    }elseif($row_prov['employee_odjel'] == 7){
                        $employee_odjel = "Tehnika";
                    }elseif($row_prov['employee_odjel'] == 8){
                        $employee_odjel = "Development";
                    }elseif($row_prov['employee_odjel'] == 9){
                        $employee_odjel = "Ostalo";
                    }else{
                        $employee_odjel = "-";
                    }
					if($employee_firstname != ""){
                ?>
                <tr>
                    <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $sumCount++; ?></td>
                    <td style="width:250px; text-align:left;padding:15px 15px;" class="text-success"><?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></td>
                    <td style="width:100px; text-align:left;padding:15px 15px;"><?php echo $employee_odjel; ?></td>
					<td style="mso-number-format:\#\,\#\#0\.00;width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $za_isplatiti; ?></td>

                </tr>
                <?php 
						
						// var_dump($ukupno);
						// var_dump($suma_bam_za_isplatiti);
						// var_dump($oo_iznos);
						// var_dump($za_isplatiti);
						// var_dump($resto);
						//exit();
						
						//UPDATE OBRACUNA DA SU ISPLACENI
						
						$update_obracuni = $db->prepare("
										UPDATE idk_obracuni
										SET status_obracuna = 1, vrijeme_isplate = '$danas'
										WHERE status_predracuna = 1 AND status_obracuna = 0 AND vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f' AND employee_id = $employee_id
						");
						$update_obracuni->execute();
						
						//UPDATE TABELE OSTATAKA
						$update_ost = $db->prepare("
										UPDATE idk_ostatci_obracuna
										SET oo_status = 1
										WHERE oo_status = 0 AND oo_employee_id = $employee_id
						");
						$update_ost->execute();
						
						//INSERT U TABELU OSTATAKA
						$insert_ost = $db->prepare("
								INSERT INTO idk_ostatci_obracuna
									(oo_employee_id, oo_valuta, oo_iznos, oo_vrijeme_kreiranja, oo_period_od, oo_period_do, oo_status)
								VALUES
									(:oo_employee_id, :oo_valuta, :oo_iznos, :oo_vrijeme_kreiranja, :oo_period_od, :oo_period_do, :oo_status)
						");
						
						$insert_ost->execute(array(
								':oo_employee_id' => $employee_id,
								':oo_valuta' => "BAM",
								':oo_iznos' => $resto,
								':oo_vrijeme_kreiranja' => $danas,
								':oo_period_od' => $f_from_f,
								':oo_period_do' => $f_to_f,
								':oo_status' => 0
						));
					}
				}
				
				?>
            </tbody>
        </table>
		<table class="table" id="isplata_provizija_rs" style="100%;" class="display">
            <thead>
                <tr>
                    <th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
                    <th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime</th>
                    <th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Odjel</th>
                    <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Isplaćeno</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sumCount2 = 1;
				
				foreach($idArray as $employee_id){
                    
					//GET RESTO
					$get_resto = $db->prepare("
                                    SELECT oo_iznos
                                    FROM idk_ostatci_obracuna
                                    WHERE oo_employee_id = :employee_id AND oo_status = 0
                    ");
                    $get_resto->execute(array(
                                    'employee_id' => $employee_id
                    ));
					$row_rest = $get_resto->fetch();
					$oo_iznos = $row_rest['oo_iznos'];
					if(is_null($oo_iznos))
						$oo_iznos = 0;
					
					
					//employee_firstname, employee_lastname, employee_odjel
                    $get_provizije = $db->prepare("
                                    SELECT emp.employee_firstname, emp.employee_lastname, emp.employee_odjel,
                                    SUM(CASE WHEN status_predracuna = 1 AND status_obracuna = 0 AND vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f' AND employee_poslovnica IN (5,10) THEN iznos_obracuna_rsd ELSE NULL END) as suma_rsd_za_isplatiti
                                    FROM idk_obracuni
                                    JOIN idk_employees emp ON emp.employee_id = idk_obracuni.employee_id
                                    WHERE idk_obracuni.employee_id = :employee_id AND employee_poslovnica IN (5,10)
                    ");
                    $get_provizije->execute(array(
                                    'employee_id' => $employee_id
                    ));
    
                    $row_prov = $get_provizije->fetch();
                    $employee_firstname = $row_prov['employee_firstname'];
                    $employee_lastname = $row_prov['employee_lastname'];
					$suma_rsd_za_isplatiti = $row_prov['suma_rsd_za_isplatiti'];
					if ($suma_rsd_za_isplatiti == null)
						$suma_rsd_za_isplatiti = 0;

					$ukupno = $suma_rsd_za_isplatiti + $oo_iznos;
					$za_isplatiti = floor($ukupno);
					$resto = $ukupno - $za_isplatiti;
					$resto = number_format((float)$resto, 4, '.', '');
					
                    if($row_prov['employee_odjel'] == 1){
                        $employee_odjel = "Uprava";
                    }elseif($row_prov['employee_odjel'] == 2){
                        $employee_odjel = "Financije";
                    }elseif($row_prov['employee_odjel'] == 3){
                        $employee_odjel = "Prodaja";
                    }elseif($row_prov['employee_odjel'] == 4){
                        $employee_odjel = "Obrada";
                    }elseif($row_prov['employee_odjel'] == 5){
                        $employee_odjel = "Sve za vizu";
                    }elseif($row_prov['employee_odjel'] == 6){
                        $employee_odjel = "Marketing";
                    }elseif($row_prov['employee_odjel'] == 7){
                        $employee_odjel = "Tehnika";
                    }elseif($row_prov['employee_odjel'] == 8){
                        $employee_odjel = "Development";
                    }elseif($row_prov['employee_odjel'] == 9){
                        $employee_odjel = "Ostalo";
                    }else{
                        $employee_odjel = "-";
                    }
					if($employee_firstname != ""){
                ?>
                <tr>
                    <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $sumCount2++; ?></td>
                    <td style="width:250px; text-align:left;padding:15px 15px;" class="text-success"><?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></td>
                    <td style="width:100px; text-align:left;padding:15px 15px;"><?php echo $employee_odjel; ?></td>
					<td style="mso-number-format:\#\,\#\#0\.00;width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $za_isplatiti; ?></td>

                </tr>
                <?php 
						var_dump($suma_bam_za_isplatiti);
						var_dump($oo_iznos);
						var_dump($za_isplatiti);
						var_dump($resto);
						//exit();
						
						//UPDATE OBRACUNA DA SU ISPLACENI
						
						$update_obracuni = $db->prepare("
										UPDATE idk_obracuni
										SET status_obracuna = 1, vrijeme_isplate = '$danas'
										WHERE status_predracuna = 1 AND status_obracuna = 0 AND vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f' AND employee_id = $employee_id
						");
						$update_obracuni->execute();
						
						//UPDATE TABELE OSTATAKA
						$update_ost = $db->prepare("
										UPDATE idk_ostatci_obracuna
										SET oo_status = 1
										WHERE oo_status = 0 AND oo_employee_id = $employee_id
						");
						$update_ost->execute();
						
						//INSERT U TABELU OSTATAKA
						$insert_ost = $db->prepare("
								INSERT INTO idk_ostatci_obracuna
									(oo_employee_id, oo_valuta, oo_iznos, oo_vrijeme_kreiranja, oo_period_od, oo_period_do, oo_status)
								VALUES
									(:oo_employee_id, :oo_valuta, :oo_iznos, :oo_vrijeme_kreiranja, :oo_period_od, :oo_period_do, :oo_status)
						");
						
						$insert_ost->execute(array(
								':oo_employee_id' => $employee_id,
								':oo_valuta' => "RSD",
								':oo_iznos' => $resto,
								':oo_vrijeme_kreiranja' => $danas,
								':oo_period_od' => $f_from_f,
								':oo_period_do' => $f_to_f,
								':oo_status' => 0
						));
					}
				}
				?>
            </tbody>
        </table>
        <script>
        $(document).ready(function(){
			$("#isplata_provizija").table2excel({
                exclude: ".noExl",
                name: "Provizije_BiH <?php echo $mjesec.' ('.$dan_p.'-'.$dan_k.')';?>",
                filename: "Provizije_BiH <?php echo $mjesec.' ('.$dan_p.'-'.$dan_k.')';?>",
                fileext: ".xls"
            }); 	 
            $("#isplata_provizija_rs").table2excel({
                exclude: ".noExl",
                name: "Provizije_Srbija <?php echo $mjesec.' ('.$dan_p.'-'.$dan_k.')';?>",
                filename: "Provizije_Srbija <?php echo $mjesec.' ('.$dan_p.'-'.$dan_k.')';?>",
                fileext: ".xls"
            }); 	  
        })
        </script>	
    </div>
    
    <?php
        
    break;

	case "export_predracuni_perioda":

		$id_men = $_GET['id_men'];
		$uslov = $_GET['q1'];
		$uslov_men = $_GET['q2'];

		?>
		<div class="" style="height:0px;overflow:hidden;">
			<table class="table" id="export_pred" class="display" style="100%;">
					<tr>
						<td style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime kandidata</td>
						
						<?php
						if ($id_men<0){
							?>
							<td style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime agenta</td>
						<?php
						}
						?>
						
						<td style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Naziv Predracuna</td>
						<td style="width:170px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum kreiranja ugovora</td>
						<td style="width:175px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Status</td>
						<td style="width:225px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Vrsta ugovora</td>

					</tr>
					<?php
					$get_uplaceni = $db->prepare("
						
						SELECT pred.pr_zaposlenik, kan.vrsta_ugovora_nd_kandidata, pred.pr_datum_kreiranja, 
							pred.pr_broj_predracuna, pred.pr_file, pred.pr_status, 
							kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime,
							pred.pr_datum_uplate

						FROM idk_predracuni pred
						JOIN idk_nd_kandidata kan 
						ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
						WHERE kan.vrijeme_kreiranja_nd_kandidata >= '2020-12-04 00:00:00' 
						AND pred.pr_status != 0
						AND pred.pr_rata = 1
						".$uslov_men."
						AND pr_datum_kreiranja ".$uslov
						
					
					);

					$get_uplaceni->execute();

					$cnt=0;
					while ($row_file = $get_uplaceni->fetch()){
						$cnt++;
						$id_zaposlenik=$row_file['pr_zaposlenik'];
						$status_predr = $row_file['pr_status'];
						$pr_file = $row_file['pr_file'];
						$ime = $row_file['ime'];
						$prezime = $row_file['prezime'];
						$br_predracuna = $row_file['pr_broj_predracuna'];
						$datum_kreiranja = substr($row_file['pr_datum_kreiranja'],0,10);
						$vrsta_ugovora_kandidata_ispis_otvoren = $row_file['vrsta_ugovora_nd_kandidata'];
						$datum_uplate = $row_file['pr_datum_uplate'];
						$datum_kreiranja = date("d.m.Y",strtotime($datum_kreiranja));
						$datum_uplate = date("d.m.Y",strtotime($datum_uplate));
					

						?>
							
						<tr>
							<td style="width:250px"><?php echo $ime." ".$prezime;?></td>
							<?php
								if($id_men<0){
									$get_menadzer = $db->prepare('
										SELECT employee_firstname, employee_lastname FROM idk_employees WHERE employee_id=:id_zaposlenik
									');

									$get_menadzer->execute(array(
										
										':id_zaposlenik'=>$id_zaposlenik

									));
					
									$tmp = $get_menadzer->fetch();
									$ime_men = $tmp['employee_firstname'];
									$prezime_men = $tmp['employee_lastname'];
									echo '<td style="width:250px;height:30px;text-align:left;padding:15px 15px;">'.$ime_men.' '.$prezime_men.'</td>';			
								}
							?>
							<td style="width:150px;height:30px;text-align:left;padding:15px 15px;"><?php echo $br_predracuna;?></td>
							<td style="width:175px;height:30px;text-align:left;padding:15px 15px;"><?php echo $datum_kreiranja;?></td>
							
						<?php
						
							if($status_predr == 0){
								$status_show = '<td style="width:175px;height:30px;text-align:left;padding:15px 15px">Arhiviran</td>';
							}else if($status_predr == 1){
								$status_show = '<td style="width:175px;height:30px;text-align:left;padding:15px 15px;">Poslan</td>';
							}else if($status_predr == 2){
								$status_show = '<td style="width:175px;height:30px;text-align:left;padding:15px 15px;">Uplaćen na: '.date("d.m.Y", strtotime($datum_uplate)).'</td>';
							}else if($status_predr == 3){
								$status_show = '<td style="width:175px;height:30px;text-align:left;padding:15px 15px;">In Caso 1</td>';
							}else if($status_predr == 4){
								$status_show = '<td style="width:175px;height:30px;text-align:left;padding:15px 15px;">In Caso 2</td>';
							}else if($status_predr == 5){
								$status_show = '<td style="width:175px;height:30px;text-align:left;padding:15px 15px;">In Caso 3</td>';
							}

							echo $status_show;
							
						
							if($vrsta_ugovora_kandidata_ispis_otvoren == 1){
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor bez popusta na 2 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 2){
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa popustom na 2 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 3){
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor bez popusta na 5 rata!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 4){
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa popustom na 5 rata!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 5){
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor bez popusta na 3 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 6){
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa popustom na 3 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 7){
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor bez popusta na 4 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 8){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa popustom na 4 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 9){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor bez popusta na 1 ratu!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 10){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa popustom na 1 ratu!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 11){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Mikrofin ugovor bez popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 12){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Mikrofin ugovor sa popustom!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 21){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa 20% popusta na 1 ratu!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 22){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa 20% popusta na 2 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 23){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa 20% popusta na 3 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 24){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa 20% popusta na 4 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 25){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa 20% popusta na 5 rata!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 71){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 1 ratu sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 72){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 2 rate sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 73){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 3 rate sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 74){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 4 rate sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 75){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 5 rata sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 51){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 1 ratu sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 52){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 2 rate sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 53){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 3 rate sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 54){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 4 rate sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 55){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 5 rata sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 99){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 1 ratu sa 100% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 41){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 1 ratu sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 42){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 2 rate sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 43){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 3 rate sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 44){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 4 rate sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 45){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 5 rata sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 61){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 1 ratu sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 62){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 2 rate sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 63){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 3 rate sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 64){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 4 rate sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 65){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor za struke na 5 rata sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 82){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa popustom 10% na 2 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 83){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa popustom 10% na 3 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 84){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa popustom 10% na 4 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 85){ 
								echo '<td style="width:225px;height:30px;text-align:left;padding:15px 15px;">Ugovor sa popustom 10% na 5 rata!</td>';
							}
		?>
					</tr>
					<?php
				}
				?>
			</table>
			<script>
				$(document).ready(function(){
					$("#export_pred").table2excel({
						exclude: ".noExl",
						name: "Predracuni",
						filename: "Statistika_predracuni(<?php echo date("Y-m-d H:i");?>)",
						fileext: ".xls"
					}); 	  
				});
			</script>
		</div>

		<?php
		break;
		
		case "export_list_inkaso_kandidati":
		
		$uslov = $_GET['uslov'];
		?>
		<div class="" style="height:0px;overflow:hidden;">
			<table class="table" id="export_pred" class="display" style="100%;">
				<tr>
					<td style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime kandidata</td>
					<td style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime agenta</td>
					<td style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Naziv Predracuna</td>
					<td style="width:170px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum kreiranja ugovora</td>
					<td style="width:175px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Status</td>
					<td style="width:225px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Vrsta ugovora</td>

				</tr>
			<?php
				$query_get_kandidate = $db->prepare("
					
					SELECT emp.employee_id, emp.employee_firstname, emp.employee_lastname,
					kan.id_broj_nd_kandidata, kan.ime_nd_kandidata, kan.prezime_nd_kandidata,
					pred.pr_status, date(pred.pr_datum_kreiranja) as pr_datum_kreiranja, date(pred.pr_datum_uplate) as pr_datum_uplate, kan.vrsta_ugovora_nd_kandidata,
					pred.pr_broj_predracuna
					
					FROM  idk_predracuni pred 
					JOIN idk_nd_kandidata_biljeske bilj 
					ON pred.pr_id = bilj.predracun_id
					JOIN idk_employees emp
					ON bilj.dodao_zaposlenik_biljeska_nd = emp.employee_id
					JOIN idk_nd_kandidata kan
					ON kan.id_broj_nd_kandidata = pred.pr_kandidat_id

					WHERE ".$uslov
					
					
				);
				$query_get_kandidate->execute();

				while ($row_get_kandidate = $query_get_kandidate->fetch()){
					$emp_id = $row_get_kandidate['employee_id'];
					$employee_firstname = $row_get_kandidate['employee_firstname'];
					$employee_lastname = $row_get_kandidate['employee_lastname'];
					$nd_kandidat_id = $row_get_kandidate['id_broj_nd_kandidata'];
					$ime_nd_kandidata = $row_get_kandidate['ime_nd_kandidata'];
					$prezime_nd_kandidata = $row_get_kandidate['prezime_nd_kandidata'];
					$pr_status = $row_get_kandidate['pr_status'];
					$pr_datum_kreiranja = date("d.m.Y",strtotime($row_get_kandidate['pr_datum_kreiranja']));
					$pr_datum_uplate = date("d.m.Y",strtotime($row_get_kandidate['pr_datum_uplate']));
					$kan_vrsta_ug = $row_get_kandidate['vrsta_ugovora_nd_kandidata'];
					$pr_broj_predracuna = $row_get_kandidate['pr_broj_predracuna'];
					
					?>
				
					<tr>
						<td><?php echo $ime_nd_kandidata." ".$prezime_nd_kandidata;?></td>
						<?php
						if($employee_id == 0){
						echo '<td>'.$employee_firstname.' '.$employee_lastname.'</td>';
						}
						?>
								
						<td><?php echo $pr_broj_predracuna;?></td>
						<td><?php echo $pr_datum_kreiranja;?></td>
						
					<?php
					
						if($pr_status == 0){
							$status_show = '<td><span>Arhiviran</span></td>';
						}else if($pr_status == 1){
							$status_show = '<td><span>Poslan</span></td>';
						}else if($pr_status == 2){
							$status_show = '<td><span>Uplaćen na: '.$pr_datum_uplate.'</span></td>';
						}else if($pr_status == 3){
							$status_show = '<td><span>In Caso 1</span></td>';
						}else if($pr_status == 4){
							$status_show = '<td><span>In Caso 2</span></td>';
						}else if($pr_status == 5){
							$status_show = '<td><span>In Caso 3</span></td>';
						}

						echo $status_show;
						
					
						if($kan_vrsta_ug == 1){
							echo '<td>Ugovor bez popusta na 2 rate!</td>';
						}
						else if($kan_vrsta_ug == 2){
							echo '<td>Ugovor sa popustom na 2 rate!</td>';
						}
						else if($kan_vrsta_ug == 3){
							echo '<td>Ugovor bez popusta na 5 rata!</td>';
						}
						else if($kan_vrsta_ug == 4){
							echo '<td>Ugovor sa popustom na 5 rata!</td>';
						}
						else if($kan_vrsta_ug == 5){
							echo '<td>Ugovor bez popusta na 3 rate!</td>';
						}
						else if($kan_vrsta_ug == 6){
							echo '<td>Ugovor sa popustom na 3 rate!</td>';
						}
						else if($kan_vrsta_ug == 7){
							echo '<td>Ugovor bez popusta na 4 rate!</td>';
						}
						else if($kan_vrsta_ug == 8){ 
							echo '<td>Ugovor sa popustom na 4 rate!</td>';
						}
						else if($kan_vrsta_ug == 9){ 
							echo '<td>Ugovor bez popusta na 1 ratu!</td>';
						}
						else if($kan_vrsta_ug == 10){ 
							echo '<td>Ugovor sa popustom na 1 ratu!</td>';
						}
						else if($kan_vrsta_ug == 11){ 
							echo '<td>Mikrofin ugovor bez popusta!</td>';
						}
						else if($kan_vrsta_ug == 12){ 
							echo '<td>Mikrofin ugovor sa popustom!</td>';
						}
						else if($kan_vrsta_ug == 21){ 
							echo '<td>Ugovor sa 20% popusta na 1 ratu!</td>';
						}
						else if($kan_vrsta_ug == 22){ 
							echo '<td>Ugovor sa 20% popusta na 2 rate!</td>';
						}
						else if($kan_vrsta_ug == 23){ 
							echo '<td>Ugovor sa 20% popusta na 3 rate!</td>';
						}
						else if($kan_vrsta_ug == 24){ 
							echo '<td>Ugovor sa 20% popusta na 4 rate!</td>';
						}
						else if($kan_vrsta_ug == 25){ 
							echo '<td>Ugovor sa 20% popusta na 5 rata!</td>';
						}
						else{
							echo '<td></td>';
						}

						?>
						</tr>
						<?php
					}
					?>		
				</tbody>
			</table>
			<script>
				$(document).ready(function(){
					$("#export_pred").table2excel({
						exclude: ".noExl",
						name: "Predracuni",
						filename: "List_inkaso_kandidati(<?php echo date("Y-m-d H:i");?>)",
						fileext: ".xls"
					}); 	  
				});
			</script>
		</div>
			<?php
		break;
		
		case "export_lista_predracuna":
		
			$tip_statistike = $_REQUEST['tip_stat'];
			$ids = $_REQUEST['selected'];
			?>
			
			<div class="" style="height:0px;overflow:hidden;">
				<table class="table" id="export_pred" class="display" style="100%;">
					<tr>
						<td style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime kandidata</td>
						<td style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime agenta</td>
						<td style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Naziv Predracuna</td>
						<td style="width:170px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum kreiranja ugovora</td>
						<td style="width:175px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Status</td>
						<td style="width:225px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Vrsta ugovora</td>

					</tr>
					<tbody>
					<?php
					$query_lista_predracuna = $db -> prepare('
						SELECT pred.pr_zaposlenik, pred.pr_kandidat_id, kan.vrsta_ugovora_nd_kandidata, pred.pr_datum_kreiranja, 
						pred.pr_broj_predracuna, pred.pr_file, pred.pr_status, kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime, 
						pred.pr_datum_uplate, emp.employee_firstname, emp.employee_lastname 
						FROM idk_predracuni pred 
						JOIN idk_nd_kandidata kan 
						ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
						JOIN idk_employees emp 
						ON emp.employee_id = pred.pr_zaposlenik 
						WHERE date(pr_datum_kreiranja) BETWEEN date(curdate() - interval '.$tip_statistike.' day) and date(curdate() - interval 1 day) 
						AND pr_rata = 1 
						AND pr_status != 0 
						AND pr_vrsta_predracuna = 1
						AND emp.employee_id IN ('.$ids.')
					');

					$query_lista_predracuna -> execute();
					while($row_lista_predracuna = $query_lista_predracuna->fetch()){
									
						$id_zaposlenik=$row_lista_predracuna['pr_zaposlenik'];
						$id_kan=$row_lista_predracuna['pr_kandidat_id'];
						$status_predr = $row_lista_predracuna['pr_status'];
						$pr_file = $row_lista_predracuna['pr_file'];
						$ime_kandidata = $row_lista_predracuna['ime'];
						$prezime_kandidata = $row_lista_predracuna['prezime'];
						$ime_zaposlenika = $row_lista_predracuna['employee_firstname'];
						$prezime_zaposlenika = $row_lista_predracuna['employee_lastname'];
						$br_predracuna = $row_lista_predracuna['pr_broj_predracuna'];
						$datum_kreiranja = substr($row_lista_predracuna['pr_datum_kreiranja'],0,10);
						$vrsta_ugovora_kandidata_ispis_otvoren = $row_lista_predracuna['vrsta_ugovora_nd_kandidata'];
						$datum_uplate = $row_lista_predracuna['pr_datum_uplate'];
						$datum_kreiranja = date("d.m.Y",strtotime($datum_kreiranja));
						$datum_uplate = date("d.m.Y",strtotime($datum_uplate));
					
					?>
					
						<tr>
							<td><?php echo $ime_kandidata." ".$prezime_kandidata; ?></td>
							<td><?php echo $ime_zaposlenika.' '.$prezime_zaposlenika; ?></td>	
							<td><?php echo $br_predracuna; ?></td>
							<td><?php echo $datum_kreiranja; ?></td>

							
						<?php
						
							if($status_predr == 0){
								$status_show = '<td>Arhiviran</td>';
							}
							else if($status_predr == 1){
								$status_show = '<td>Poslan</td>';
							}
							else if($status_predr == 2){
								$status_show = '<td>Uplaćen na: '.date("d.m.Y", strtotime($datum_uplate)).'</span></td>';
							}
							else if($status_predr == 3){
								$status_show = '<td>In Caso 1</td>';
							}
							else if($status_predr == 4){
								$status_show = '<td>In Caso 2</td>';
							}
							else if($status_predr == 5){
								$status_show = '<td>In Caso 3></td>';
							}

							echo $status_show;
						
							if($vrsta_ugovora_kandidata_ispis_otvoren == 1){
								echo '<td>Ugovor bez popusta na 2 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 2){
								echo '<td>Ugovor sa popustom na 2 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 3){
								echo '<td>Ugovor bez popusta na 5 rata!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 4){
								echo '<td>Ugovor sa popustom na 5 rata!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 5){
								echo '<td>Ugovor bez popusta na 3 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 6){
								echo '<td>Ugovor sa popustom na 3 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 7){
								echo '<td>Ugovor bez popusta na 4 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 8){ 
								echo '<td>Ugovor sa popustom na 4 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 9){ 
								echo '<td>Ugovor bez popusta na 1 ratu!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 10){ 
								echo '<td>Ugovor sa popustom na 1 ratu!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 11){ 
								echo '<td>Mikrofin ugovor bez popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 12){ 
								echo '<td>Mikrofin ugovor sa popustom!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 21){ 
								echo '<td>Ugovor sa 20% popusta na 1 ratu!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 22){ 
								echo '<td>Ugovor sa 20% popusta na 2 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 23){ 
								echo '<td>Ugovor sa 20% popusta na 3 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 24){ 
								echo '<td>Ugovor sa 20% popusta na 4 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 25){ 
								echo '<td>Ugovor sa 20% popusta na 5 rata!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 71){ 
								echo '<td>Ugovor za struke na 1 ratu sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 72){ 
								echo '<td>Ugovor za struke na 2 rate sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 73){ 
								echo '<td>Ugovor za struke na 3 rate sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 74){ 
								echo '<td>Ugovor za struke na 4 rate sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 75){ 
								echo '<td>Ugovor za struke na 5 rata sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 51){ 
								echo '<td>Ugovor za struke na 1 ratu sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 52){ 
								echo '<td>Ugovor za struke na 2 rate sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 53){ 
								echo '<td>Ugovor za struke na 3 rate sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 54){ 
								echo '<td>Ugovor za struke na 4 rate sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 55){ 
								echo '<td>Ugovor za struke na 5 rata sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 99){ 
								echo '<td>Ugovor za struke na 1 ratu sa 100% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 41){ 
								echo '<td>Ugovor za struke na 1 ratu sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 42){ 
								echo '<td>Ugovor za struke na 2 rate sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 43){ 
								echo '<td>Ugovor za struke na 3 rate sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 44){ 
								echo '<td>Ugovor za struke na 4 rate sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 45){ 
								echo '<td>Ugovor za struke na 5 rata sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 61){ 
								echo '<td>Ugovor za struke na 1 ratu sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 62){ 
								echo '<td>Ugovor za struke na 2 rate sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 63){ 
								echo '<td>Ugovor za struke na 3 rate sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 64){ 
								echo '<td>Ugovor za struke na 4 rate sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 65){ 
								echo '<td>Ugovor za struke na 5 rata sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 82){ 
								echo '<td>Ugovor sa popustom 10% na 2 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 83){ 
								echo '<td>Ugovor sa popustom 10% na 3 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 84){ 
								echo '<td>Ugovor sa popustom 10% na 4 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 85){ 
								echo '<td>Ugovor sa popustom 10% na 5 rata!</td>';
							}
							

						?>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
				<script>
					$(document).ready(function(){
						$("#export_pred").table2excel({
							exclude: ".noExl",
							name: "Predracuni",
							filename: "Lista_predracuna(<?php echo date("Y-m-d H:i");?>)",
							fileext: ".xls"
						}); 	  
					});
				</script>
			</div>
			<?php
		break;
		
		
		case "export_lista_uplata":
		
			$tip_statistike = $_REQUEST['tip_stat'];
			$ids = $_REQUEST['selected'];
			?>
			
			<div class="" style="height:0px;overflow:hidden;">
				<table class="table" id="export_pred" class="display" style="100%;">
					<tr>
						<td style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime kandidata</td>
						<td style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime agenta</td>
						<td style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Naziv Predracuna</td>
						<td style="width:170px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum kreiranja ugovora</td>
						<td style="width:175px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Status</td>
						<td style="width:225px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Vrsta ugovora</td>

					</tr>
					<tbody>
					<?php
					$query_lista_predracuna = $db -> prepare('
						SELECT pred.pr_zaposlenik, pred.pr_kandidat_id, kan.vrsta_ugovora_nd_kandidata, pred.pr_datum_kreiranja, 
						pred.pr_broj_predracuna, pred.pr_file, pred.pr_status, kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime, 
						pred.pr_datum_uplate, emp.employee_firstname, emp.employee_lastname 
						FROM idk_predracuni pred 
						JOIN idk_nd_kandidata kan 
						ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
						JOIN idk_employees emp 
						ON emp.employee_id = pred.pr_zaposlenik 
						WHERE date(pr_datum_uplate) BETWEEN date(curdate() - interval '.$tip_statistike.' day) and date(curdate() - interval 1 day) 
						AND pr_rata = 1 
						AND pr_status != 0 
						AND pr_vrsta_predracuna = 1
						AND pr_uplaceno = 1
						AND emp.employee_id IN ('.$ids.')
					');

					$query_lista_predracuna -> execute();
					while($row_lista_predracuna = $query_lista_predracuna->fetch()){
									
						$id_zaposlenik=$row_lista_predracuna['pr_zaposlenik'];
						$id_kan=$row_lista_predracuna['pr_kandidat_id'];
						$status_predr = $row_lista_predracuna['pr_status'];
						$pr_file = $row_lista_predracuna['pr_file'];
						$ime_kandidata = $row_lista_predracuna['ime'];
						$prezime_kandidata = $row_lista_predracuna['prezime'];
						$ime_zaposlenika = $row_lista_predracuna['employee_firstname'];
						$prezime_zaposlenika = $row_lista_predracuna['employee_lastname'];
						$br_predracuna = $row_lista_predracuna['pr_broj_predracuna'];
						$datum_kreiranja = substr($row_lista_predracuna['pr_datum_kreiranja'],0,10);
						$vrsta_ugovora_kandidata_ispis_otvoren = $row_lista_predracuna['vrsta_ugovora_nd_kandidata'];
						$datum_uplate = $row_lista_predracuna['pr_datum_uplate'];
						$datum_kreiranja = date("d.m.Y",strtotime($datum_kreiranja));
						$datum_uplate = date("d.m.Y",strtotime($datum_uplate));
					
					?>
					
						<tr>
							<td><?php echo $ime_kandidata." ".$prezime_kandidata; ?></td>
							<td><?php echo $ime_zaposlenika.' '.$prezime_zaposlenika; ?></td>	
							<td><?php echo $br_predracuna; ?></td>
							<td><?php echo $datum_kreiranja; ?></td>

							
						<?php
						
							if($status_predr == 0){
								$status_show = '<td>Arhiviran</td>';
							}
							else if($status_predr == 1){
								$status_show = '<td>Poslan</td>';
							}
							else if($status_predr == 2){
								$status_show = '<td>Uplaćen na: '.date("d.m.Y", strtotime($datum_uplate)).'</span></td>';
							}
							else if($status_predr == 3){
								$status_show = '<td>In Caso 1</td>';
							}
							else if($status_predr == 4){
								$status_show = '<td>In Caso 2</td>';
							}
							else if($status_predr == 5){
								$status_show = '<td>In Caso 3></td>';
							}

							echo $status_show;
						
							if($vrsta_ugovora_kandidata_ispis_otvoren == 1){
								echo '<td>Ugovor bez popusta na 2 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 2){
								echo '<td>Ugovor sa popustom na 2 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 3){
								echo '<td>Ugovor bez popusta na 5 rata!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 4){
								echo '<td>Ugovor sa popustom na 5 rata!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 5){
								echo '<td>Ugovor bez popusta na 3 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 6){
								echo '<td>Ugovor sa popustom na 3 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 7){
								echo '<td>Ugovor bez popusta na 4 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 8){ 
								echo '<td>Ugovor sa popustom na 4 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 9){ 
								echo '<td>Ugovor bez popusta na 1 ratu!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 10){ 
								echo '<td>Ugovor sa popustom na 1 ratu!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 11){ 
								echo '<td>Mikrofin ugovor bez popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 12){ 
								echo '<td>Mikrofin ugovor sa popustom!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 21){ 
								echo '<td>Ugovor sa 20% popusta na 1 ratu!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 22){ 
								echo '<td>Ugovor sa 20% popusta na 2 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 23){ 
								echo '<td>Ugovor sa 20% popusta na 3 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 24){ 
								echo '<td>Ugovor sa 20% popusta na 4 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 25){ 
								echo '<td>Ugovor sa 20% popusta na 5 rata!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 71){ 
								echo '<td>Ugovor za struke na 1 ratu sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 72){ 
								echo '<td>Ugovor za struke na 2 rate sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 73){ 
								echo '<td>Ugovor za struke na 3 rate sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 74){ 
								echo '<td>Ugovor za struke na 4 rate sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 75){ 
								echo '<td>Ugovor za struke na 5 rata sa 30% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 51){ 
								echo '<td>Ugovor za struke na 1 ratu sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 52){ 
								echo '<td>Ugovor za struke na 2 rate sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 53){ 
								echo '<td>Ugovor za struke na 3 rate sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 54){ 
								echo '<td>Ugovor za struke na 4 rate sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 55){ 
								echo '<td>Ugovor za struke na 5 rata sa 50% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 99){ 
								echo '<td>Ugovor za struke na 1 ratu sa 100% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 41){ 
								echo '<td>Ugovor za struke na 1 ratu sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 42){ 
								echo '<td>Ugovor za struke na 2 rate sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 43){ 
								echo '<td>Ugovor za struke na 3 rate sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 44){ 
								echo '<td>Ugovor za struke na 4 rate sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 45){ 
								echo '<td>Ugovor za struke na 5 rata sa 70% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 61){ 
								echo '<td>Ugovor za struke na 1 ratu sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 62){ 
								echo '<td>Ugovor za struke na 2 rate sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 63){ 
								echo '<td>Ugovor za struke na 3 rate sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 64){ 
								echo '<td>Ugovor za struke na 4 rate sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 65){ 
								echo '<td>Ugovor za struke na 5 rata sa 20% popusta!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 82){ 
								echo '<td>Ugovor sa popustom 10% na 2 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 83){ 
								echo '<td>Ugovor sa popustom 10% na 3 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 84){ 
								echo '<td>Ugovor sa popustom 10% na 4 rate!</td>';
							}else if($vrsta_ugovora_kandidata_ispis_otvoren == 85){ 
								echo '<td>Ugovor sa popustom 10% na 5 rata!</td>';
							}
							

						?>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
				<script>
					$(document).ready(function(){
						$("#export_pred").table2excel({
							exclude: ".noExl",
							name: "Predracuni",
							filename: "Lista_predracuna(<?php echo date("Y-m-d H:i");?>)",
							fileext: ".xls"
						}); 	  
					});
				</script>
			</div>
			<?php
		break;
		case "export_predracuni_ch":
			$filter_kreiran_predracun_period 	= $_POST['filter_kreiran_predracun_period'];
			$filter_uplacen_predracun_period 	= $_POST['filter_uplacen_predracun_period'];
			$filter_select_is_uplaceno 			= $_POST['filter_select_is_uplaceno'];
			$filter_select_rate 				= $_POST['filter_select_rate'];
			$filter_select_status_predracuna	= $_POST['filter_select_status_predracuna'];
			$filter_select_drzavu 				= $_POST['filter_select_drzavu'];
			
			$flag_vise_drzava 					= false;
			$uslov_vrijeme_kreiranja_predracuna = "";
			$uslov_vrijeme_uplate_predracuna 	= "";
			$uslov_is_uplacen 					= "";
			$uslov_rata 						= "";
			$uslov_status 						= "";
			$uslov_drzava						= "";

			if(count($filter_select_drzavu) > 1){
				$flag_vise_drzava = true;
			}else{
				$flag_vise_drzava = false;
			}

			if($filter_select_drzavu != null){
				$uslov_drzava = "AND pr.pr_domaca_valuta IN (".implode(',',$filter_select_drzavu).")"; 
			}else{
				$uslov_drzava = "";
			}
			
			if(strpos($filter_kreiran_predracun_period, "to")){
				$tmp_period = array();
				$tmp_period = explode('to', $filter_kreiran_predracun_period);
				$filter_kreiran_predracun_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
				$filter_kreiran_predracun_datum_do = date("Y-m-d", strtotime($tmp_period[1]));
				$uslov_vrijeme_kreiranja_predracuna = " AND pr.pr_datum_kreiranja BETWEEN '".$filter_kreiran_predracun_datum_od."' AND '".$filter_kreiran_predracun_datum_do."'";
			}
			else if($filter_kreiran_predracun_period != ""){
				$uslov_vrijeme_kreiranja_predracuna = " AND pr.pr_datum_kreiranja = ".date("d.m.Y",strtotime($filter_kreiran_predracun_period));
			}
			

			if(strpos($filter_uplacen_predracun_period, "to")){
				$tmp_period = array();
				$tmp_period = explode('to', $filter_uplacen_predracun_period);
				$filter_uplacen_predracun_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
				$filter_uplacen_predracun_datum_do = date("Y-m-d", strtotime($tmp_period[1]));
				$uslov_vrijeme_uplate_predracuna = " AND pr.pr_datum_uplate BETWEEN '".$filter_uplacen_predracun_datum_od."' AND '".$filter_uplacen_predracun_datum_do."'";
			}
			else if($filter_uplacen_predracun_period != ""){
				$uslov_vrijeme_uplate_predracuna = " AND pr.pr_datum_uplate = ".date("d.m.Y",strtotime($filter_uplacen_predracun_period));
			}

			if(in_array('0',$filter_select_is_uplaceno) && !in_array('1',$filter_select_is_uplaceno)){
				$uslov_is_uplacen = "AND pr.pr_uplaceno = 0";
			}
			else if(!in_array('0',$filter_select_is_uplaceno) && in_array('1',$filter_select_is_uplaceno)){
				$uslov_is_uplacen = "AND pr.pr_uplaceno = 1";
			}
			
			$uslov_rata = "AND pr.pr_rata IN (".implode(',',$filter_select_rate).")";

			$uslov_status = "AND pr.pr_status IN (".implode(',',$filter_select_status_predracuna).")";
								
			$query_get_predracune = $db->prepare('
				SELECT pr.*, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.id_broj_nd_kandidata, emp.employee_firstname, emp.employee_lastname
				FROM idk_predracuni pr
				JOIN idk_nd_kandidata kan
				ON kan.id_broj_nd_kandidata = pr.pr_kandidat_id
				JOIN idk_employees emp
				ON emp.employee_id = kan.zaduzen_zaposlenik_nd_kandidata
				WHERE pr.pr_naplata_preko = 1
				AND pr.pr_file IS NOT NULL
				'.$uslov_vrijeme_kreiranja_predracuna.'
				'.$uslov_vrijeme_uplate_predracuna.'
				'.$uslov_is_uplacen.'
				'.$uslov_rata.'
				'.$uslov_status.'
				'.$uslov_drzava.'
			');
			// var_dump($query_get_predracune);
			// exit();
			$query_get_predracune -> execute();
			$cnt = 0;
			?>
			<table id = "table_export_predracuni_ch">
				<thead>
					<th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
					<th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ime i prezime kandidata</th>
					<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum kreiranja</th>
					<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Datum uplate</th>
					<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Status predračuna</th>
					<th style="width:200px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Agent</th>
					<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Broj</th>
					<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Rata</th>
					<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Vrijednost</th>
					<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Valuta</th>
				</thead>
				<tbody>
				
			<?php
			while($row_get_predracune = $query_get_predracune->fetch()){
				$cnt++;
				$kandidat_fullname 	= $row_get_predracune['ime_nd_kandidata'].' '.$row_get_predracune['prezime_nd_kandidata'];
				$agent_fullname 	= $row_get_predracune['employee_firstname'].' '.$row_get_predracune['employee_lastname'];
				$pr_datum_kreiranja = date("d.m.Y", strtotime($row_get_predracune['pr_datum_kreiranja']));
				$pr_datum_uplate 	= $row_get_predracune['pr_datum_uplate'];
				$pr_broj 			= $row_get_predracune['pr_broj_predracuna'];
				$pr_rata 			= $row_get_predracune['pr_rata'];
				$pr_vrijednost_EUR 	= $row_get_predracune['pr_vrijednost_EUR'];
				$pr_status 	= $row_get_predracune['pr_status'];
				if($pr_status == 0){
					$status_predracuna = 'Arhiviran';
				}else if($pr_status == 1){
					$status_predracuna = 'Poslan';
				}else if($pr_status == 2){
					$status_predracuna = 'Uplaćen';
				}else if($pr_status == 3){
					$status_predracuna = 'In Caso 1';
				}else if($pr_status == 4){
					$status_predracuna = 'In Caso 2';
				}else if($pr_status == 5){
					$status_predracuna = 'In Caso 3';
				}
				
				
				if(is_null($pr_datum_uplate)){
					$pr_datum_uplate = "-";
				}
				else{
					$pr_datum_uplate = date("d.m.Y", strtotime($pr_datum_kreiranja));
				}
					?>
					<tr>
						<td style="width:30px;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $cnt;?></td>
						<td style="width:250;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $kandidat_fullname;?></td>
						<td style="width:200;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $pr_datum_kreiranja;?></td>
						<td style="width:200;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $pr_datum_uplate;?></td>
						<td style="width:200;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $status_predracuna;?></td>
						<td style="width:200;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $agent_fullname;?></td>
						<td style="width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $pr_broj;?></td>
						<td style="width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $pr_rata;?></td>
						<td style="mso-number-format:\#\,\#\#0\.00;width:150;min-height:25px;text-align:center;padding:15px 15px;"><?php echo $pr_vrijednost_EUR;?></td>
						<td style="width:150;min-height:25px;text-align:center;padding:15px 15px;">EUR</td>
					</tr>
					<?php
			}
			?>
				</tbody>
			</table>

			<?php
		break;
		case "export_racuni_ch":
			$filter_period 			= $_POST['filter_period'];
			$filter_status 			= $_POST['filter_status'];
			$filter_select_drzavu 	= $_POST['filter_select_drzavu'];
			
			$uslov_datum_kreiranja 	= "";
			$uslov_status 			= "";
			$uslov_drzava 			= "";
			$flag_vise_drzava 		= false;
			
			if(strpos($filter_period, "to")){
				$tmp_period = array();
				$tmp_period = explode('to', $filter_period);
				$filter_kreiran_racun_datum_od = date("Y-m-d 00:00:00", strtotime($tmp_period[0]));
				$filter_kreiran_racun_datum_do = date("Y-m-d 23:59:59", strtotime($tmp_period[1]));
				$uslov_datum_kreiranja = " AND rc.racun_datum_kreiranja BETWEEN '".$filter_kreiran_racun_datum_od."' AND '".$filter_kreiran_racun_datum_do."'";
			}
			else if($filter_period != ""){
				$uslov_datum_kreiranja = " AND rc.racun_datum_kreiranja = ".date("d.m.Y",strtotime($filter_period));
			}
			if($filter_status != ""){
				$uslov_status = "AND rc.racun_status IN (".implode(',', $filter_status).")";
			}
			
			if(count($filter_select_drzavu) > 1){
				$flag_vise_drzava = true;
			}
			
			if($filter_select_drzavu != ""){
				$uslov_drzava = " AND rc.racun_domaca_valuta IN (".implode(',', $filter_select_drzavu).")";
			}
			
			$query_get_racune = $db -> prepare("
				SELECT kan.ime_nd_kandidata, kan.prezime_nd_kandidata, rc.racun_datum_kreiranja, pr.pr_datum_uplate, rc.racun_status,
				rc.racun_broj, pr.pr_rata, kan.vrsta_ugovora_nd_kandidata, pr.pr_vrijednost_EUR, pr.pr_domaca_valuta, pr.pr_vrijednost_BAM, pr.pr_vrijednost_RSD
				FROM idk_racuni rc
				JOIN idk_nd_kandidata kan
				ON kan.id_broj_nd_kandidata = rc.racun_kandidat_id
				JOIN idk_predracuni pr
				ON pr.pr_id = rc.predracun_id
				WHERE rc.racun_broj LIKE ('%CH%')
				".$uslov_datum_kreiranja."
				".$uslov_status."
				".$uslov_drzava."
			");
			
			$query_get_racune -> execute();
			?>
			<table id="table_export_racuni_ch">
				<thead>
					<tr>
						<th style = "background:rgb(146,208,80);text-align:center;">#</th>
						<th style = "background:rgb(146,208,80);text-align:center;">Ime i prezime kandidata</th>
						<th style = "background:rgb(146,208,80);text-align:center;">Datum kreiranja</th>
						<th style = "background:rgb(146,208,80);text-align:center;">Datum uplate</th>
						<th style = "background:rgb(146,208,80);text-align:center;">Broj</th>
						<th style = "background:rgb(146,208,80);text-align:center;">Rata</th>
						<th style = "background:rgb(146,208,80);text-align:center;">Cijena EUR</th>
						<th style = "background:rgb(146,208,80);text-align:center;">Popust</th>
						<th style = "background:rgb(146,208,80);text-align:center;">Ukupno</th>
						<th style = "background:rgb(146,208,80);text-align:center;">UST</th>
						<th style = "background:rgb(146,208,80);text-align:center;">Vrijednost za plaćanje</th>
						<th style = "background:rgb(146,208,80);text-align:center;">Valuta</th>
						<th style = "background:rgb(146,208,80);text-align:center;">Valuta uplate</th>
						<th style = "background:rgb(146,208,80);text-align:center;">Iznos u valuti</th>
					</tr>
			<?php
			$cnt = 0;
			while($row_get_racune = $query_get_racune -> fetch()){
				$cnt++;
				$kandidat_fullname 	= $row_get_racune['ime_nd_kandidata'].' '.$row_get_racune['prezime_nd_kandidata'];
				$datum_kreiranja	= $row_get_racune['racun_datum_kreiranja'];
				$pr_datum_uplate	= $row_get_racune['pr_datum_uplate'];
				$racun_broj			= $row_get_racune['racun_broj'];
				$pr_rata			= $row_get_racune['pr_rata'];
				$vrsta_ugovora		= $row_get_racune['vrsta_ugovora_nd_kandidata'];
				$pr_vrijednost_EUR	= number_format($row_get_racune['pr_vrijednost_EUR'], 2, '.', '');
				$pr_domaca_valuta	= $row_get_racune['pr_domaca_valuta'];
				$racun_status 		= $row_get_racune['racun_status'];
				
				$is_storno_operator 	= "";
				$is_storno_background  	= "";
				if($racun_status == 2){
					$is_storno_operator 	= '-';
					$is_storno_background 	= 'style = "background:rgb(255,141,141)"';
				}
				
				$cijena_eur			= number_format(getIznosRate($vrsta_ugovora, "ch", "rata".$pr_rata), 2, '.', '');
				$ust 				= 0;
				$valuta				= "EUR";
				
				$iznos_u_valuti		= "";
				$popust_procent		= 0;
				if($pr_domaca_valuta == "BAM"){
					$iznos_u_valuti = number_format($row_get_racune['pr_vrijednost_BAM'], 2, '.', '');
				}
				else if($pr_domaca_valuta == "RSD"){
					$iznos_u_valuti = number_format(number_format($row_get_racune['pr_vrijednost_RSD'], 0, '.', '').'.00', 2, '.', '');
				}
				else{
					$iznos_u_valuti = $pr_vrijednost_EUR;
				}
				if(in_array(($vrsta_ugovora), array(21,22,23,24,25,61,62,63,64,65))){
					$popust_procent = 20;
				}elseif(in_array(($vrsta_ugovora), array(2,4,6,8,10,12,71,72,73,74,75))){
					$popust_procent = 30;
				}elseif(in_array(($vrsta_ugovora), array(9,82,83,84,85))){
					$popust_procent = 10;
				}elseif(in_array(($vrsta_ugovora), array(51,52,53,54,55))){
					$popust_procent = 50;
				}elseif(in_array(($vrsta_ugovora), array(41,42,43,44,45))){
					$popust_procent = 70;
				}elseif($vrsta_ugovora == 99){ 
					$popust_procent = 100;
				}else{
					$popust_procent = 0;
				}
				
				echo '
					<tr>
						<td '.$is_storno_background.'>'.$cnt.'</td>
						<td '.$is_storno_background.'>'.$kandidat_fullname.'</td>
						<td '.$is_storno_background.'>'.date('d.m.Y',strtotime($datum_kreiranja)).'</td>
						<td '.$is_storno_background.'>'.date('d.m.Y',strtotime($pr_datum_uplate)).'</td>
						<td '.$is_storno_background.'>'.$racun_broj.'</td>
						<td '.$is_storno_background.'>'.$pr_rata.'</td>
						<td '.$is_storno_background.'>'.$is_storno_operator.number_format(($pr_vrijednost_EUR/(1-($popust_procent/100))), 2, ',', '').'</td>
						<td '.$is_storno_background.'>-'.$popust_procent.'%</td>
						<td '.$is_storno_background.'>'.$is_storno_operator.$pr_vrijednost_EUR.'</td>
						<td '.$is_storno_background.'>0%</td>
						<td '.$is_storno_background.'>'.$is_storno_operator.$pr_vrijednost_EUR.'</td>
						<td '.$is_storno_background.'>EUR</td>
						<td '.$is_storno_background.'>'.$pr_domaca_valuta.'</td>
						<td '.$is_storno_background.'>'.$is_storno_operator.$iznos_u_valuti.'</td>
					</tr>
				';
			}
			?>
			</table>
			<?php
		break;
		
		case "export_ch_muster_predracuni":
			$filter_period 			= $_POST['filter_period'];
			$filter_select_drzavu 	= $_POST['filter_select_drzavu'];

			$uslov_datum_kreiranja 	= "";
			$uslov_drzava 			= "";

			if(strpos($filter_period, "to")){
				$tmp_period = array();
				$tmp_period = explode('to', $filter_period);
				$filter_kreiran_predracun_datum_od = date("Y-m-d 00:00:00", strtotime($tmp_period[0]));
				$filter_kreiran_predracun_datum_do = date("Y-m-d 23:59:59", strtotime($tmp_period[1]));
				$uslov_datum_kreiranja = " AND pr.pr_datum_kreiranja BETWEEN '".$filter_kreiran_predracun_datum_od."' AND '".$filter_kreiran_predracun_datum_do."'";
			}
			else if($filter_period != ""){
				$uslov_datum_kreiranja = " AND pr.pr_datum_kreiranja = ".date("d.m.Y",strtotime($filter_period));
			}

			if($filter_select_drzavu != ""){
				$uslov_drzava = " AND pr.pr_domaca_valuta IN (".implode(',', $filter_select_drzavu).")";
			}

			$query_get_predracune = $db -> prepare("
				SELECT kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.id_broj_nd_kandidata, pr.pr_datum_kreiranja,
				pr.pr_broj_predracuna, pr.pr_rata, kan.vrsta_ugovora_nd_kandidata, pr.pr_vrijednost_EUR, pr.pr_domaca_valuta, pr.pr_vrijednost_BAM, pr.pr_vrijednost_RSD
				FROM idk_predracuni pr
				JOIN idk_nd_kandidata kan
				ON kan.id_broj_nd_kandidata = pr.pr_kandidat_id
				WHERE pr.pr_broj_predracuna LIKE ('%CH%')
				AND pr.pr_file IS NOT NULL
				AND pr.pr_uplaceno = 0
				AND pr.pr_status IN (1,3,4,5)
				".$uslov_datum_kreiranja."
				".$uslov_drzava."
			");
			
			$query_get_predracune -> execute();
			?>
			<table id="table_export_muster_ch_predracuni">
				<thead>
					<tr>
						<th>Record Nr</th>
						<th>Version</th>
						<th>Datum</th>
						<th>Konto</th>
						<th>Gegenkonto</th>
						<th>Buchungstext</th>
						<th>Betrag</th>
						<th>Text2</th>
						<th>SH</th>
						<th>(KST SOLL) Buchungsebene 1</th>
						<th>KST Haben Gegenseite Buchungsebene 1</th>
						<th>Belegnummer</th>
						<th>Kurs</th>
						<th>Kursart</th>
						<th>FWBetrag</th>
						<th>Sammelbuchungs Identifier</th>
						<th>Spec1</th>
						<th>Spec2</th>
						<th>Reserve</th>
						<th>Valuta Datum</th>
						<th>Sam Position</th>
						<th>Akonto</th>
						<th>Mandant Nr</th>
						<th>ISO</th>
						<th>ISO2</th>
						<th>Menge</th>
						<th>Ansatz</th>
						<th>Buchungsebene 2</th>
						<th>Gegenseite Buchungsebene 2</th>
						<th>Fond1</th>
						<th>Fond2</th>
						<th>Reserve3</th>
						<th>Reserve4</th>
						<th>Reserve5</th>
						<th>Codefeld</th>
						<th>Mwst Code</th>
						<th>Mwst Satz</th>
						<th>Mwst Incl</th>
						<th>Mwst Methode</th>
						<th>Mwst Land</th>
						<th>Mwst Koeff</th>
						<th>Mwst Konto</th>
						<th>Mwst Gegenkonto</th>
						<th>Mwst SH</th>
						<th>Mwst Betrag</th>
						<th>Mwst FW Betrag</th>
						<th>Mwst Betrag Rest</th>
						<th>Mwst FW Betrag Rest</th>
						<th>Reserve6</th>
						<th>Mwst Typ</th>
						<th>Reserve7</th>
						<th>Reserve8</th>
						<th>Reserve9</th>
						<th>Geschäftsbereich</th>
						<th>Soll Ist</th>
						<th>HabenVerdSamBetrag</th>
						<th>HabenVerdSamBetragFW</th>
						<th>Euro Koeff1</th>
						<th>Euro Koeff2</th>
						<th>Intercompany</th>
						<th>Kurs2</th>
						<th>Konsolidierungscode</th>
						<th>Buchungsebene 3</th>
						<th>Gegenseite Buchungsebene 3</th>
						<th>Ende Flag</th>
					</tr>
			<?php
			$cnt = 1;
			while($row_get_predracune = $query_get_predracune -> fetch()){
				$id_broj_nd_kandidata 	= $row_get_predracune['id_broj_nd_kandidata'];
				$kandidat_fullname 	= $row_get_predracune['ime_nd_kandidata'].' '.$row_get_predracune['prezime_nd_kandidata'];
				$datum_kreiranja	= $row_get_predracune['pr_datum_kreiranja'];
				$predracun_broj		= $row_get_predracune['pr_broj_predracuna'];
				$pr_rata			= $row_get_predracune['pr_rata'];
				$vrsta_ugovora		= $row_get_predracune['vrsta_ugovora_nd_kandidata'];
				$pr_vrijednost_EUR	= number_format($row_get_predracune['pr_vrijednost_EUR'], 2, '.', '');
				$pr_domaca_valuta	= $row_get_predracune['pr_domaca_valuta'];
				
				$broj_rata 			= getBrojRataNDR($id_broj_nd_kandidata);
				$cijena_eur			= number_format(getIznosRate($vrsta_ugovora, "ch", "rata".$pr_rata), 2, '.', '');
				$ust 				= 0;
				$valuta				= "EUR";
				
				$iznos_u_valuti		= "";
				$popust_procent		= 0;
				
				
				$gegenkonto = 300500;
				if($pr_domaca_valuta == "BAM"){
					$iznos_u_valuti = number_format($row_get_predracune['pr_vrijednost_BAM'], 2, '.', '');
					$konto = 114000;
				}
				else if($pr_domaca_valuta == "RSD"){
					$iznos_u_valuti = number_format(number_format($row_get_predracune['pr_vrijednost_RSD'], 0, '.', '').'.00', 2, '.', '');
					$konto = 114100;
				}
				else{
					$iznos_u_valuti = $pr_vrijednost_EUR;
					$konto = 114200;
				}
				if(in_array(($vrsta_ugovora), array(21,22,23,24,25,61,62,63,64,65))){
					$popust_procent = 20;
					$popust = true;
				}elseif(in_array(($vrsta_ugovora), array(2,4,6,8,10,12,71,72,73,74,75))){
					$popust_procent = 30;
					$popust = true;
				}elseif(in_array(($vrsta_ugovora), array(9,82,83,84,85))){
					$popust_procent = 10;
					$popust = true;
				}elseif(in_array(($vrsta_ugovora), array(51,52,53,54,55))){
					$popust_procent = 50;
					$popust = true;
				}elseif(in_array(($vrsta_ugovora), array(41,42,43,44,45))){
					$popust_procent = 70;
					$popust = true;
				}elseif($vrsta_ugovora == 99){ 
					$popust_procent = 100;
					$popust = true;
				}else{
					$popust_procent = 0;
					$popust = false;
				}
				
				$ukupno_eur = number_format(($pr_vrijednost_EUR/(1-($popust_procent/100))), 2, '.', '');
				$popust_eur = $ukupno_eur - $pr_vrijednost_EUR;
				
				
				
					echo '
						<tr>
							<td>'.$cnt++.'</td>
							<td>J</td>
							<td>'.date('d.m.Y',strtotime($datum_kreiranja)).'</td>
							<td>'.$konto.'</td>
							<td>'.$gegenkonto.'</td>
							<td>'.$kandidat_fullname.'</td>
							<td></td>
							<td></td>
							<td>S</td>
							<td></td>
							<td></td>
							<td>'.$predracun_broj.'</td>
							<td>22</td>
							<td>M</td>
							<td>'.$ukupno_eur.'</td>
							<td></td>
							<td></td>
							<td>F</td>
							<td></td>
							<td></td>
							<td></td>
							<td style="mso-number-format:\@;">'.$pr_rata.'/'.$broj_rata.'</td>
							<td></td>
							<td>EUR</td>
							<td>EUR</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>UEA</td>
							<td>0</td>
							<td>I</td>
							<td>0</td>
							<td>CH</td>
							<td>0</td>
							<td>300500</td>
							<td>220000</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td></td>
							<td>1</td>
							<td></td>
							<td></td>
							<td></td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>1</td>
							<td>1</td>
							<td>0</td>
							<td>0</td>
							<td></td>
							<td>0</td>
							<td>0</td>
							<td>E</td>
						</tr>
					';
					if($popust){
						echo '
							<tr>
							<td>'.$cnt++.'</td>
							<td>J</td>
							<td>'.date('d.m.Y',strtotime($datum_kreiranja)).'</td>
							<td>'.$konto.'</td>
							<td>380000</td>
							<td>'.$kandidat_fullname.'</td>
							<td></td>
							<td></td>
							<td>S</td>
							<td></td>
							<td></td>
							<td>'.$predracun_broj.'</td>
							<td>22</td>
							<td>M</td>
							<td>-'.$popust_eur.'</td>
							<td></td>
							<td></td>
							<td>F</td>
							<td></td>
							<td></td>
							<td></td>
							<td style="mso-number-format:\@;">'.$pr_rata.'/'.$broj_rata.'</td>
							<td></td>
							<td>EUR</td>
							<td>EUR</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>UEA</td>
							<td>0</td>
							<td>I</td>
							<td>0</td>
							<td>CH</td>
							<td>0</td>
							<td>300500</td>
							<td>220000</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td></td>
							<td>1</td>
							<td></td>
							<td></td>
							<td></td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>1</td>
							<td>1</td>
							<td>0</td>
							<td>0</td>
							<td></td>
							<td>0</td>
							<td>0</td>
							<td>E</td>
							</tr>
						';
					}
				
				
			}
			?>
			</table>
			<?php

		break;

		case "export_ch_muster":
			$filter_period 			= $_POST['filter_period'];
			$filter_status 			= $_POST['filter_status'];
			$filter_select_drzavu 	= $_POST['filter_select_drzavu'];
			
			$uslov_datum_kreiranja 	= "";
			$uslov_status 			= "";
			$uslov_drzava 			= "";
			$flag_vise_drzava 		= false;
			
			if(strpos($filter_period, "to")){
				$tmp_period = array();
				$tmp_period = explode('to', $filter_period);
				$filter_kreiran_racun_datum_od = date("Y-m-d 00:00:00", strtotime($tmp_period[0]));
				$filter_kreiran_racun_datum_do = date("Y-m-d 23:59:59", strtotime($tmp_period[1]));
				$uslov_datum_kreiranja = " AND rc.racun_datum_kreiranja BETWEEN '".$filter_kreiran_racun_datum_od."' AND '".$filter_kreiran_racun_datum_do."'";
			}
			else if($filter_period != ""){
				$uslov_datum_kreiranja = " AND rc.racun_datum_kreiranja = ".date("d.m.Y",strtotime($filter_period));
			}
			if($filter_status != ""){
				$uslov_status = "AND rc.racun_status IN (".implode(',', $filter_status).")";
			}
			
			if(count($filter_select_drzavu) > 1){
				$flag_vise_drzava = true;
			}
			
			if($filter_select_drzavu != ""){
				$uslov_drzava = " AND rc.racun_domaca_valuta IN (".implode(',', $filter_select_drzavu).")";
			}
			
			$query_get_racune = $db -> prepare("
				SELECT kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.id_broj_nd_kandidata, rc.racun_datum_kreiranja, pr.pr_datum_uplate,
				rc.racun_broj, pr.pr_rata, kan.vrsta_ugovora_nd_kandidata, pr.pr_vrijednost_EUR, pr.pr_domaca_valuta, pr.pr_vrijednost_BAM, pr.pr_vrijednost_RSD, rc.racun_stornirano
				FROM idk_racuni rc
				JOIN idk_nd_kandidata kan
				ON kan.id_broj_nd_kandidata = rc.racun_kandidat_id
				JOIN idk_predracuni pr
				ON pr.pr_id = rc.predracun_id
				WHERE rc.racun_broj LIKE ('%CH%')
				".$uslov_datum_kreiranja."
				".$uslov_status."
				".$uslov_drzava."
			");
			
			$query_get_racune -> execute();
			?>
			<table id="table_export_muster_ch">
				<thead>
					<tr>
						<th>Record Nr</th>
						<th>Version</th>
						<th>Datum</th>
						<th>Konto</th>
						<th>Gegenkonto</th>
						<th>Buchungstext</th>
						<th>Betrag</th>
						<th>Text2</th>
						<th>SH</th>
						<th>(KST SOLL) Buchungsebene 1</th>
						<th>KST Haben Gegenseite Buchungsebene 1</th>
						<th>Belegnummer</th>
						<th>Kurs</th>
						<th>Kursart</th>
						<th>FWBetrag</th>
						<th>Sammelbuchungs Identifier</th>
						<th>Spec1</th>
						<th>Spec2</th>
						<th>Reserve</th>
						<th>Valuta Datum</th>
						<th>Sam Position</th>
						<th>Akonto</th>
						<th>Mandant Nr</th>
						<th>ISO</th>
						<th>ISO2</th>
						<th>Menge</th>
						<th>Ansatz</th>
						<th>Buchungsebene 2</th>
						<th>Gegenseite Buchungsebene 2</th>
						<th>Fond1</th>
						<th>Fond2</th>
						<th>Reserve3</th>
						<th>Reserve4</th>
						<th>Reserve5</th>
						<th>Codefeld</th>
						<th>Mwst Code</th>
						<th>Mwst Satz</th>
						<th>Mwst Incl</th>
						<th>Mwst Methode</th>
						<th>Mwst Land</th>
						<th>Mwst Koeff</th>
						<th>Mwst Konto</th>
						<th>Mwst Gegenkonto</th>
						<th>Mwst SH</th>
						<th>Mwst Betrag</th>
						<th>Mwst FW Betrag</th>
						<th>Mwst Betrag Rest</th>
						<th>Mwst FW Betrag Rest</th>
						<th>Reserve6</th>
						<th>Mwst Typ</th>
						<th>Reserve7</th>
						<th>Reserve8</th>
						<th>Reserve9</th>
						<th>Geschäftsbereich</th>
						<th>Soll Ist</th>
						<th>HabenVerdSamBetrag</th>
						<th>HabenVerdSamBetragFW</th>
						<th>Euro Koeff1</th>
						<th>Euro Koeff2</th>
						<th>Intercompany</th>
						<th>Kurs2</th>
						<th>Konsolidierungscode</th>
						<th>Buchungsebene 3</th>
						<th>Gegenseite Buchungsebene 3</th>
						<th>Ende Flag</th>
					</tr>
			<?php
			$cnt = 1;
			while($row_get_racune = $query_get_racune -> fetch()){
				$id_broj_nd_kandidata 	= $row_get_racune['id_broj_nd_kandidata'];
				$kandidat_fullname 	= $row_get_racune['ime_nd_kandidata'].' '.$row_get_racune['prezime_nd_kandidata'];
				$datum_kreiranja	= $row_get_racune['racun_datum_kreiranja'];
				$pr_datum_uplate	= $row_get_racune['pr_datum_uplate'];
				$racun_broj			= $row_get_racune['racun_broj'];
				$pr_rata			= $row_get_racune['pr_rata'];
				$vrsta_ugovora		= $row_get_racune['vrsta_ugovora_nd_kandidata'];
				$pr_vrijednost_EUR	= number_format($row_get_racune['pr_vrijednost_EUR'], 2, '.', '');
				$pr_domaca_valuta	= $row_get_racune['pr_domaca_valuta'];
				$racun_stornirano	= $row_get_racune['racun_stornirano'];
				
				$broj_rata 			= getBrojRataNDR($id_broj_nd_kandidata);
				$cijena_eur			= number_format(getIznosRate($vrsta_ugovora, "ch", "rata".$pr_rata), 2, '.', '');
				$ust 				= 0;
				$valuta				= "EUR";
				
				$iznos_u_valuti		= "";
				$popust_procent		= 0;
				
				
				$gegenkonto = 300500;
				if($pr_domaca_valuta == "BAM"){
					$iznos_u_valuti = number_format($row_get_racune['pr_vrijednost_BAM'], 2, '.', '');
					$konto = 114000;
				}
				else if($pr_domaca_valuta == "RSD"){
					$iznos_u_valuti = number_format(number_format($row_get_racune['pr_vrijednost_RSD'], 0, '.', '').'.00', 2, '.', '');
					$konto = 114100;
				}
				else{
					$iznos_u_valuti = $pr_vrijednost_EUR;
					$konto = 114200;
				}
				if(in_array(($vrsta_ugovora), array(21,22,23,24,25,61,62,63,64,65))){
					$popust_procent = 20;
					$popust = true;
				}elseif(in_array(($vrsta_ugovora), array(2,4,6,8,10,12,71,72,73,74,75))){
					$popust_procent = 30;
					$popust = true;
				}elseif(in_array(($vrsta_ugovora), array(9,82,83,84,85))){
					$popust_procent = 10;
					$popust = true;
				}elseif(in_array(($vrsta_ugovora), array(51,52,53,54,55))){
					$popust_procent = 50;
					$popust = true;
				}elseif(in_array(($vrsta_ugovora), array(41,42,43,44,45))){
					$popust_procent = 70;
					$popust = true;
				}elseif($vrsta_ugovora == 99){ 
					$popust_procent = 100;
					$popust = true;
				}else{
					$popust_procent = 0;
					$popust = false;
				}
				
				$ukupno_eur = number_format(($pr_vrijednost_EUR/(1-($popust_procent/100))), 2, '.', '');
				$popust_eur = $ukupno_eur - $pr_vrijednost_EUR;
				
				if($racun_stornirano == 1){
					echo '
						<tr>
							<td>'.$cnt++.'</td>
							<td>J</td>
							<td>'.date('d.m.Y',strtotime($datum_kreiranja)).'</td>
							<td>'.$konto.'</td>
							<td>'.$gegenkonto.'</td>
							<td>'.$kandidat_fullname.'</td>
							<td></td>
							<td></td>
							<td>S</td>
							<td></td>
							<td></td>
							<td>'.$racun_broj.'</td>
							<td>22</td>
							<td>M</td>
							<td>-'.$ukupno_eur.'</td>
							<td></td>
							<td></td>
							<td>F</td>
							<td></td>
							<td></td>
							<td></td>
							<td style="mso-number-format:\@;">'.$pr_rata.'/'.$broj_rata.'</td>
							<td></td>
							<td>EUR</td>
							<td>EUR</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>UEA</td>
							<td>0</td>
							<td>I</td>
							<td>0</td>
							<td>CH</td>
							<td>0</td>
							<td>300500</td>
							<td>220000</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td></td>
							<td>1</td>
							<td></td>
							<td></td>
							<td></td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>1</td>
							<td>1</td>
							<td>0</td>
							<td>0</td>
							<td></td>
							<td>0</td>
							<td>0</td>
							<td>E</td>
						</tr>
					';
				}else{
					echo '
						<tr>
							<td>'.$cnt++.'</td>
							<td>J</td>
							<td>'.date('d.m.Y',strtotime($datum_kreiranja)).'</td>
							<td>'.$konto.'</td>
							<td>'.$gegenkonto.'</td>
							<td>'.$kandidat_fullname.'</td>
							<td></td>
							<td></td>
							<td>S</td>
							<td></td>
							<td></td>
							<td>'.$racun_broj.'</td>
							<td>22</td>
							<td>M</td>
							<td>'.$ukupno_eur.'</td>
							<td></td>
							<td></td>
							<td>F</td>
							<td></td>
							<td></td>
							<td></td>
							<td style="mso-number-format:\@;">'.$pr_rata.'/'.$broj_rata.'</td>
							<td></td>
							<td>EUR</td>
							<td>EUR</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>UEA</td>
							<td>0</td>
							<td>I</td>
							<td>0</td>
							<td>CH</td>
							<td>0</td>
							<td>300500</td>
							<td>220000</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td></td>
							<td>1</td>
							<td></td>
							<td></td>
							<td></td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>1</td>
							<td>1</td>
							<td>0</td>
							<td>0</td>
							<td></td>
							<td>0</td>
							<td>0</td>
							<td>E</td>
						</tr>
					';
					if($popust){
						echo '
							<tr>
							<td>'.$cnt++.'</td>
							<td>J</td>
							<td>'.date('d.m.Y',strtotime($datum_kreiranja)).'</td>
							<td>'.$konto.'</td>
							<td>380000</td>
							<td>'.$kandidat_fullname.'</td>
							<td></td>
							<td></td>
							<td>S</td>
							<td></td>
							<td></td>
							<td>'.$racun_broj.'</td>
							<td>22</td>
							<td>M</td>
							<td>-'.$popust_eur.'</td>
							<td></td>
							<td></td>
							<td>F</td>
							<td></td>
							<td></td>
							<td></td>
							<td style="mso-number-format:\@;">'.$pr_rata.'/'.$broj_rata.'</td>
							<td></td>
							<td>EUR</td>
							<td>EUR</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>UEA</td>
							<td>0</td>
							<td>I</td>
							<td>0</td>
							<td>CH</td>
							<td>0</td>
							<td>300500</td>
							<td>220000</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td></td>
							<td>1</td>
							<td></td>
							<td></td>
							<td></td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>0</td>
							<td>1</td>
							<td>1</td>
							<td>0</td>
							<td>0</td>
							<td></td>
							<td>0</td>
							<td>0</td>
							<td>E</td>
							</tr>
						';
					}
				}
				/*
				if($popust){
					echo '
					<tr>
						<td>'.$cnt.'</td>
						<td>J</td>
						<td>'.date('d.m.Y',strtotime($datum_kreiranja)).'</td>
						<td>'.$konto.'</td>
						<td>380000</td>
						<td>'.$kandidat_fullname.'</td>
						<td>'.date('d.m.Y',strtotime($pr_datum_uplate)).'</td>
						<td>'.$racun_broj.'</td>
						<td>'.$pr_rata.'</td>
						<td>'.number_format(($pr_vrijednost_EUR/(1-($popust_procent/100))), 2, ',', '').'</td>
						<td>-'.$popust_procent.'%</td>
						<td>'.$pr_vrijednost_EUR.'</td>
						<td>0%</td>
						<td>'.$pr_vrijednost_EUR.'</td>
						<td>EUR</td>
						<td>'.$pr_domaca_valuta.'</td>
						<td>'.$iznos_u_valuti.'</td>
					</tr>
				';
				}*/
			}
			?>
			</table>
			<?php
		break;
		
 }
?>