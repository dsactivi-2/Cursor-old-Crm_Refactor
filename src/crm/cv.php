<?php
	include("includes/functions.php");
	include("includes/common.php");
	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: kandidati?page=list");
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
		<div class="container-fluid idk_margin_top10">
			<div class="container">
				<div class="row">
					<div class="col-xs-3 text-right">
					<?php if($getEmployeeStatus == 1){ ?>
						<img src="<?php getSiteUrl(); ?>images/Jobstep-logo.png" width="200">
					<?php }else{ 
						$query_img = $db->prepare("
										SELECT employee_image
										FROM idk_employees
										WHERE employee_id = :employee_id");

						$query_img->execute(array(
									':employee_id' => $logged_employee_id));

						$img = $query_img->fetch();
							$employee_image = $img['employee_image'];

					?>
						<img src="<?php getSiteUrl(); ?>files/employees/<?php echo $employee_image; ?>" width="200">
					<?php } ?>
					</div>
					<div class="col-xs-9 text-right">
					</div>
				</div>
			</div>
		</div>
		<hr>
		<div class="container-fluid">
		<div class="container idk_cv_container" id="htmltopdff">
		<?php
			switch ($page){

				case "open":

					$kandidat_id = $_GET['id'];
					

					$query = $db->prepare("
									SELECT kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo, kandidat_bracnostanje, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_slika, kandidat_datetime,kandidat_check, kandidat_vozacka_dozvola, kandidat_prijava_na
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
						$kandidat_bracnostanje = $row['kandidat_bracnostanje'];
						$kandidat_adresa = $row['kandidat_adresa'];
						$kandidat_grad = $row['kandidat_grad'];
						$kandidat_pbroj = $row['kandidat_pbroj'];
						$kandidat_drzava = $row['kandidat_drzava'];
						$kandidat_email = $row['kandidat_email'];
						$kandidat_drzava = $row['kandidat_drzava'];
						$kandidat_datetime = $row['kandidat_datetime'];
						$kandidat_vozacka_dozvola = $row['kandidat_vozacka_dozvola'];
						$kandidat_prijava_na = $row['kandidat_prijava_na'];
						$kandidat_datumrodjenja = date('d.m.Y.', strtotime($row['kandidat_datumrodjenja']));


						if($row['kandidat_slika'] == "none"){
							$kandidat_slika = "none.jpg";
						}else{
							$kandidat_slika = $row['kandidat_slika'];
						}

		?>
			<div class="row">
				<div class="col-sm-12">
				<br/>
					<div class="row">
						<div class="col-xs-3 text-right">
							<b><p>OSOBNE INFORMACIJE</p></b>
						</div>
						<div class="col-xs-6 idk_list_style">
							<h2><?php echo $kandidat_ime; ?> <?php echo $kandidat_prezime; ?></h2>
							<ul class="fa-ul">
								<li><i class="fa-li fa fa-id-card" aria-hidden="true"></i><p>ID: <?php echo $kandidat_id; ?></p></li>
								<li><i class="fa-li fa fa-map-marker" aria-hidden="true"></i><p><?php echo $kandidat_adresa; ?>, <?php echo $kandidat_pbroj; ?>, <?php echo $kandidat_grad; ?></p></li>
								<?php
									$select_query = $db->prepare("
														SELECT kki_id, kki_grupa, kki_naziv, kki_podatak
														FROM idk_kandidat_kontakt_info
														WHERE kki_kandidat_id = :kki_kandidat_id");
	
									$select_query->execute(array(
													':kki_kandidat_id' => $kandidat_id));
	
									while($select_row = $select_query->fetch()) {
	
										$kki_id = $select_row['kki_id'];
										$kki_grupa = $select_row['kki_grupa'];
										if($kki_grupa == 1){
											$kki_grupa = "Telefon";
											$ikona = 'fa-mobile';
											$kki_podatak = $select_row['kki_podatak'];

											if (strpos($kki_podatak, '+') === 0) {
												$kki_podatak = $select_row['kki_podatak'];
											 }else{
												$kki_podatak = "+".$kki_podatak;
											 }
											 
										}elseif($kki_grupa == 2){
											$kki_grupa = "E-mail";
											$ikona = 'fa-envelope';
											$kki_podatak = $select_row['kki_podatak'];
										}elseif($kki_grupa == 3){
											$kki_grupa = "Web";
											$ikona = 'fa-globe';
											$kki_podatak = $select_row['kki_podatak'];
										}elseif($kki_grupa == 4){
											$kki_grupa = "Messangeri";
											$ikona = 'fa-skype';
											$kki_podatak = $select_row['kki_podatak'];
										}else{};
										$kki_naziv = $select_row['kki_naziv'];
										
										
										
								?>
								<li><i class="fa-li fa <?php echo $ikona; ?>" aria-hidden="true"></i><p><?php echo $kki_naziv; ?>: <?php echo $kki_podatak; ?></p></li>
								<?php } ?>
	
							</ul>
							<ul class="list-inline idk_list_inline">
								<li><span>Spol</span><p><?php echo $kandidat_spol; ?></p></li>
								<li>|</li>
								<li><span>Datum rođenja</span><p><?php echo $kandidat_datumrodjenja; ?></p></li>
								<li>|</li>
								<li><span>Državljanstvo</span><p><?php echo $kandidat_drzavljanstvo; ?></p></li>
							</ul>
						</div>
						<div class="col-xs-3">
							<div class="idk_cv_image">
								<img src="<?php getSiteUrl(); ?>files/kandidati/<?php echo $kandidat_slika; ?>">
							</div>
						</div>
					</div>
				</div>
				
				<div class="col-xs-12">
				<br/>
					<div class="row">
						<div class="col-xs-3 text-right">
							<b><p>RADNO MJESTO NA KOJE SE PRIJAVLJUJETE</p></b>
						</div>
						<div class="col-xs-9 idk_list_style">
							<h2><?php echo $kandidat_prijava_na; ?></h2>
						</div>
					</div>
					<br/>
				</div>
				

				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<b><p>RADNO ISKUSTVO</p></b>
					</div>
					<div class="col-xs-9 idk_span_underline">
						<span></span>
					</div>
				</div>
				
				<?php
					$select_query = $db->prepare("
										SELECT kri_id, kri_darum_od, kri_datum_do, kri_pozicija, kri_naziv, kri_grad, kri_opis, kri_aktuelno
										FROM idk_kandidat_radno_iskustvo
										WHERE kri_kandidat_id = :kri_kandidat_id");
		
					$select_query->execute(array(
									':kri_kandidat_id' => $kandidat_id));
		
					while($select_row = $select_query->fetch()) {
		
						$kri_id = $select_row['kri_id'];
						$kri_darum_od = date('m.Y', strtotime($select_row['kri_darum_od']));
						$kri_datum_do = $select_row['kri_datum_do'];
						$kri_pozicija = $select_row['kri_pozicija'];
						$kri_naziv = $select_row['kri_naziv'];
						$kri_grad = $select_row['kri_grad'];
						$kri_opis = $select_row['kri_opis'];
						$kri_aktuelno = $select_row['kri_aktuelno'];
						
						if($kri_aktuelno != 1){
							$kri_datum_do_f = date('m.Y', strtotime($kri_datum_do));
						}else{
							$kri_datum_do_f = "Aktuelno";
						}						
				?>
				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<p><?php echo $kri_darum_od; ?> - <?php echo $kri_datum_do_f; ?></p>
					</div>
					<div class="col-xs-9 idk_h3_style">
						<h3><?php echo $kri_pozicija; ?></h3>
						<p><?php echo $kri_naziv; ?></p>
						<p><?php echo $kri_grad; ?></p>
						<p><?php echo $kri_opis; ?></p>
						<br/>
					</div>
				</div>
				<?php } ?>

				
				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<b><p>OBRAZOVANJE I OSPOSOBLJAVANJE</p></b>
					</div>
					<div class="col-xs-9 idk_span_underline">
						<span></span>
					</div>
				</div>
				
				<?php
					$select_query = $db->prepare("
										SELECT ke_id, ke_datumod, ke_datumdo, ke_naziv_kvalifikacije, ke_naziv, ke_grad, ke_opis, ke_aktuelno
										FROM idk_kandidat_edukacija
										WHERE ke_kandidat_id = :ke_kandidat_id");

					$select_query->execute(array(
									':ke_kandidat_id' => $kandidat_id));

					while($select_row = $select_query->fetch()) {

						$ke_id = $select_row['ke_id'];
						$ke_datumod = date('m.Y', strtotime($select_row['ke_datumod']));
						$ke_datumdo = $select_row['ke_datumdo'];
						$ke_naziv_kvalifikacije = $select_row['ke_naziv_kvalifikacije'];
						$ke_naziv = $select_row['ke_naziv'];
						$ke_grad = $select_row['ke_grad'];
						$ke_opis = $select_row['ke_opis'];
						$ke_aktuelno = $select_row['ke_aktuelno'];
						
						if($ke_aktuelno != 1){
							$ke_datumdo_f = date('m.Y', strtotime($ke_datumdo));
						}else{
							$ke_datumdo_f = "Aktuelno";
						}	
				?>
				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<p><?php echo $ke_datumod; ?> - <?php echo $ke_datumdo_f; ?></p>
					</div>
					<div class="col-xs-9 idk_h3_style">
						<h3><?php echo $ke_naziv_kvalifikacije; ?></h3>
						<p><?php echo $ke_naziv; ?></p>
						<p><?php echo $ke_grad; ?></p>
						<p><?php echo $ke_opis; ?></p>
						<br/>
					</div>
				</div>
				<?php } ?>
				
				
				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<b><p>OSOBNE VJEŠTINE</p></b>
					</div>
					<div class="col-xs-9 idk_span_underline">
						<span></span>
					</div>
				</div>
				<!--
				<div class="col-sm-12">
					<div class="col-sm-3 idk_text_align">
						<p>Materinski jezik</p>
					</div>
					<div class="col-sm-9 idk_h4_style">
						<h4>Bosanski</h4>
					</div>
				</div>
				-->
				
				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<p>Jezici</p>
					</div>
					<div class="col-xs-9">
									<table class="table table-hover">
										<thead>
											<tr>

												<th class="text-center">Jezik</th>
												<th class="text-center">Slušanje</th>
												<th class="text-center">Čitanje</th>
												<th class="text-center">Govorna interakcija</th>
												<th class="text-center">Govorna produkcija</th>
												<th class="text-center">Pisanje</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$select_query = $db->prepare("
																	SELECT kj_id, kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje
																	FROM idk_kandidat_jezici
																	WHERE kj_kandidatid = :kj_kandidatid");

												$select_query->execute(array(
																':kj_kandidatid' => $kandidat_id));

												while($select_row = $select_query->fetch()) {

													$kj_id = $select_row['kj_id'];
													$kj_naziv = $select_row['kj_naziv'];
													$kj_slusanje = $select_row['kj_slusanje'];
													$kj_citanje = $select_row['kj_citanje'];
													$kj_govorna_interakcija = $select_row['kj_govorna_interakcija'];
													$kj_govorna_produkcija = $select_row['kj_govorna_produkcija'];
													$kj_pisanje = $select_row['kj_pisanje'];
											?>
											<tr>

												<td class="text-center"><?php echo $kj_naziv; ?></td>
												<td class="text-center"><?php echo $kj_slusanje; ?></td>
												<td class="text-center"><?php echo $kj_citanje; ?></td>
												<td class="text-center"><?php echo $kj_govorna_interakcija; ?></td>
												<td class="text-center"><?php echo $kj_govorna_produkcija; ?></td>
												<td class="text-center"><?php echo $kj_pisanje; ?></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
					</div>
				</div>
				
				<div class="col-xs-12 idk_margin_top30">
					<div class="row">
						<div class="col-xs-3 idk_language_align">
						</div>
						<div class="col-xs-9 idk_table_p">
							<p>Stupnjevi: A1/2: Početnik - B1/2: Samostalni korisnik - C1/2 Iskusni korisnik</p>
							<a href="http://europass.cedefop.europa.eu/hr/resources/european-language-levels-cefr"><p>Zajednički europski referentni okvir za jezike</p></a>
						</div>
					</div>
				</div>
				
				<?php
					$select_query = $db->prepare("
										SELECT kv_id, kv_naziv, kv_grupa, kv_opis
										FROM idk_kandidat_vjestine
										WHERE kv_kandidat_id = :kv_kandidat_id");

					$select_query->execute(array(
									':kv_kandidat_id' => $kandidat_id));

					while($select_row = $select_query->fetch()) {

						$kv_id = $select_row['kv_id'];
						$kv_naziv = $select_row['kv_naziv'];
						$kv_grupa = $select_row['kv_grupa'];
						if($kv_grupa == 1){
							$kv_grupa = "Osnovne vještine";
						}elseif($kv_grupa == 2){
							$kv_grupa = "Digitalne kompetencije";
						}elseif($kv_grupa == 3){
							$kv_grupa = "Dodatne informacije";
						}else{};
						$kv_opis = $select_row['kv_opis'];
				?>
				<div class="col-xs-12 idk_margin_top30">
					<div class="row">
						<br/>
						<br/>
						<div class="col-xs-3 text-right">
							<p><?php echo $kv_grupa; ?></p>
						</div>
						<div class="col-xs-9 idk_h3_style">
							<h3><?php echo $kv_naziv; ?></h3>
							<p><?php echo $kv_opis; ?></p><br>
						</div>
					</div>
				</div>
				<?php } ?>
				
				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<b><p>Vozačka dozvola B kategorija</p></b>
					</div>
					<div class="col-xs-9 idk_h3_style">
						<p><?php echo $kandidat_vozacka_dozvola; ?></p><br>
					</div>
				</div>				
				
				
				<!--
				<div class="col-sm-12">
					<div class="col-sm-3 idk_text_align">
						<p>DODATNE INFORMACIJE</p>
					</div>
					<div class="col-sm-9 idk_span_underline">
						<span></span>
					</div>
				</div>
				
				<div class="col-sm-12">
					<div class="col-sm-3 idk_text_align">
						<p>additional</p>
					</div>
					<div class="col-sm-9 idk_h3_style">
						<p>Za detalje i dodatne informacije vezane uz preporuke molim Vas kontaktirajte direktora Vrtnog centra d.o.o. iz Zagreba g. Hrvoja Jukića, +385 1 529 86 66</p><br>
					</div>
				</div>
				
				<div class="col-sm-12">
					<div class="col-sm-3 idk_text_align">
						<p>annexes</p>
					</div>
					<div class="col-sm-9 idk_h3_style">
						<p>Molba i preporuka prijašnjeg poslodavca</p><br>
					</div>
				</div>
				-->
				
			</div>
		<?php
				break;	
			}
		?>
			
		</div>
		</div>
		<br/>
		<br/>
		<br/>

</body>
</html>
