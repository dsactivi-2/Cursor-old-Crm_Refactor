<?php
	include("includes/functions.php");
    include("includes/common.php");
    require_once 'gt/gtranslate.php';
	//$getEmployeeStatus = getEmployeeStatus();
	$employee_status = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: kandidati?page=list_ajax");
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
									SELECT kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo, kandidat_bracnostanje, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_slika, kandidat_datetime,kandidat_check, kandidat_vozacka_dozvola, kandidat_prijava_na, cv_de
									FROM idk_kandidati
									WHERE kandidat_id = :kandidat_id");

					$query->execute(array(
								':kandidat_id' => $kandidat_id));

					$row = $query->fetch();

					$cv_de = $row['cv_de'];
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

                    $gt = new gtranslate();

                    $kandidat_prijava_na_de = $gt->translate($kandidat_prijava_na, 'de','hr');
                    $kandidat_spol_de = $gt->translate($kandidat_spol, 'de','hr');
                    $kandidat_drzavljanstvo_de = $gt->translate($kandidat_drzavljanstvo, 'de','hr');
                    $kandidat_vozacka_dozvola_de = $gt->translate($kandidat_vozacka_dozvola, 'de','hr');

                    $check_trans_query = $db->prepare("
                                SELECT kde_id, kde_kandidat_id
                                FROM idk_kandidat_de
                                WHERE kde_kandidat_id = :kde_kandidat_id");

                    $check_trans_query->execute(array(
                                    ':kde_kandidat_id' => $kandidat_id));

                    $num = $check_trans_query->rowCount();

                    if($num > 0){

                        
                    }else{
                        $de_query = $db->prepare("
                            INSERT INTO idk_kandidat_de
                                (kde_kandidat_id, kde_prijava_na, kde_spol, kde_drzavljanstvo, kde_vozacka)
                                VALUES
                                (:kde_kandidat_id, :kde_prijava_na, :kde_spol, :kde_drzavljanstvo, :kde_vozacka)");
                        $de_query->execute(array(
                            ':kde_kandidat_id' => $kandidat_id,
                            ':kde_prijava_na' => $kandidat_prijava_na_de,
                            ':kde_spol' => $kandidat_spol_de,
                            ':kde_drzavljanstvo' => $kandidat_drzavljanstvo_de,
                            ':kde_vozacka' => $kandidat_vozacka_dozvola_de));
                    }

		?>
        <form action="<?php getSiteURL(); ?>do.php?form=profil_gen_de" name="frm-exp" id="frm-exp" method="POST">
            <input type="hidden" name="kand_id" value="<?php echo $kandidat_id; ?>">
			<div class="row">
				<div class="col-sm-12">
				<br/>
					<div class="row">
						<div class="col-xs-3 text-right">
							<b><p>PERSÖNLICHE INFORMATIONEN</p></b>
						</div>
						<div class="col-xs-6 idk_list_style">
							<h2><?php echo $kandidat_ime; ?></h2>
							<ul class="fa-ul">
								<li><i class="fa-li fa fa-id-card" aria-hidden="true"></i><p>ID: <?php echo $kandidat_id; ?></p></li>
								<li><i class="fa-li fa fa-map-marker" aria-hidden="true"></i><p><?php echo $kandidat_grad; ?></p></li>
							</ul>
							<ul class="list-inline idk_list_inline">
								<li><span>Geschlecht</span><p><?php echo $kandidat_spol_de; ?></p></li>
								<li>|</li>
								<li><span>Geburtsdatum</span><p><?php echo $kandidat_datumrodjenja; ?></p></li>
								<li>|</li>
								<li><span>Staatsangehörigkeit</span><p><?php echo $kandidat_drzavljanstvo_de; ?></p></li>
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
							<b><p>ANGESTREBTE STELLE</p></b>
						</div>
						<div class="col-xs-9 idk_list_style">
							<h2><?php echo $kandidat_prijava_na_de; ?></h2>
						</div>
					</div>
					<br/>
				</div>
				

				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<b><p>BERUFSERFAHRUNG</p></b>
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
                        $kri_pozicija_de = $gt->translate($kri_pozicija, 'de','hr');
						$kri_naziv = $select_row['kri_naziv'];
						$kri_grad = $select_row['kri_grad'];
                        $kri_opis = $select_row['kri_opis'];
                        $kri_opis_de = $gt->translate($kri_opis, 'de','hr');
						$kri_aktuelno = $select_row['kri_aktuelno'];
						
						if($kri_aktuelno != 1){
							$kri_datum_do_f = date('m.Y', strtotime($kri_datum_do));
						}else{
							$kri_datum_do_f = "Topisch";
                        }
                        
                        $query_kri = $db->prepare("
                        UPDATE idk_kandidat_radno_iskustvo
                                SET	kri_pozicija_de = :kri_pozicija_de, kri_opis_de = :kri_opis_de
                                WHERE kri_id = :kri_id");

                        $query_kri->execute(array(
                                ':kri_pozicija_de' => $kri_pozicija_de,
                                ':kri_opis_de' => $kri_opis_de,
                                ':kri_id' => $kri_id));
				?>
				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<p><?php echo $kri_darum_od; ?> - <?php echo $kri_datum_do_f; ?></p>
					</div>
					<div class="col-xs-9 idk_h3_style">
						<h3><?php echo $kri_pozicija_de; ?></h3>
						<p><?php echo $kri_naziv; ?></p>
						<p><?php echo $kri_grad; ?></p>
						<p><?php echo $kri_opis_de; ?></p>
						<br/>
					</div>
				</div>
				<?php } ?>

				
				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<b><p>SCHUL-UND BERUFSBILDUNG</p></b>
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
                        $ke_naziv_kvalifikacije_de = $gt->translate($ke_naziv_kvalifikacije, 'de','hr');
						$ke_naziv = $select_row['ke_naziv'];
						$ke_grad = $select_row['ke_grad'];
                        $ke_opis = $select_row['ke_opis'];
                        $ke_opis_de = $gt->translate($ke_opis, 'de','hr');
						$ke_aktuelno = $select_row['ke_aktuelno'];

                        $query_ke = $db->prepare("
                        UPDATE idk_kandidat_edukacija
                                SET	ke_naziv_kvalifikacije_de = :ke_naziv_kvalifikacije_de, ke_opis_de = :ke_opis_de
                                WHERE ke_id = :ke_id");

                        $query_ke->execute(array(
                                ':ke_naziv_kvalifikacije_de' => $ke_naziv_kvalifikacije_de,
                                ':ke_opis_de' => $ke_opis_de,
                                ':ke_id' => $ke_id));
						
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
						<h3><?php echo $ke_naziv_kvalifikacije_de; ?></h3>
						<p><?php echo $ke_naziv; ?></p>
						<p><?php echo $ke_grad; ?></p>
						<p><?php echo $ke_opis_de; ?></p>
						<br/>
					</div>
				</div>
				<?php } ?>
				
				
				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<b><p>PERSÖNLICHE FÄHIGKEITEN</p></b>
					</div>
					<div class="col-xs-9 idk_span_underline">
						<span></span>
					</div>
				</div>
				
				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<p>Sprachen</p>
					</div>
					<div class="col-xs-9">
                        <table class="table table-hover">
                            <thead>
                                <tr>

                                    <th class="text-center">Sprache</th>
                                    <th class="text-center">Hören</th>
                                    <th class="text-center">Lesen</th>
                                    <th class="text-center">An gesprächen teilnehmen</th>
                                    <th class="text-center">Zusammenhängendes sprechen</th>
                                    <th class="text-center">Schreiben</th>
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

                                        $kj_naziv_de = $gt->translate($kj_naziv, 'de','hr');
                                        $kj_slusanje_de = $gt->translate($kj_slusanje, 'de','hr');
                                        $kj_citanje_de = $gt->translate($kj_citanje, 'de','hr');
                                        $kj_govorna_interakcija_de = $gt->translate($kj_govorna_interakcija, 'de','hr');
                                        $kj_govorna_produkcija_de = $gt->translate($kj_govorna_produkcija, 'de','hr');
                                        $kj_pisanje_de = $gt->translate($kj_pisanje, 'de','hr');

                                        $query_ke = $db->prepare("
                                        UPDATE idk_kandidat_jezici
                                                SET	kj_naziv_de = :kj_naziv_de, kj_slusanje_de = :kj_slusanje_de, kj_citanje_de = :kj_citanje_de, kj_govorna_interakcija_de = :kj_govorna_interakcija_de, kj_govorna_produkcija_de = :kj_govorna_produkcija_de, kj_pisanje_de = :kj_pisanje_de
                                                WHERE kj_id = :kj_id");

                                        $query_ke->execute(array(
                                                ':kj_naziv_de' => $kj_naziv_de,
                                                ':kj_slusanje_de' => $kj_slusanje_de,
                                                ':kj_citanje_de' => $kj_citanje_de,
                                                ':kj_govorna_interakcija_de' => $kj_govorna_interakcija_de,
                                                ':kj_govorna_produkcija_de' => $kj_govorna_produkcija_de,
                                                ':kj_pisanje_de' => $kj_pisanje_de,
                                                ':kj_id' => $kj_id));
            
                                ?>
                                <tr>

                                    <td class="text-center"><?php echo $kj_naziv_de; ?></td>
                                    <td class="text-center"><?php echo $kj_slusanje_de; ?></td>
                                    <td class="text-center"><?php echo $kj_citanje_de; ?></td>
                                    <td class="text-center"><?php echo $kj_govorna_interakcija_de; ?></td>
                                    <td class="text-center"><?php echo $kj_govorna_produkcija_de; ?></td>
                                    <td class="text-center"><?php echo $kj_pisanje_de; ?></td>
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
							<p>Niveaus: A1/2: Anfänger - B1/2: Unabhängiger Benutzer - C1/2 Erfahrener Benutzer</p>
							<a href="http://europass.cedefop.europa.eu/de/resources/european-language-levels-cefr"><p>Gemeinsamer europäischer Referenzrahmen für Sprachen</p></a>
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
							$kv_grupa = "Grundlegende Fähigkeiten";
						}elseif($kv_grupa == 2){
							$kv_grupa = "Digitale Fähigkeiten";
						}elseif($kv_grupa == 3){
							$kv_grupa = "Zusätzliche informationen";
						}else{};
						$kv_opis = $select_row['kv_opis'];

						$kv_naziv_de = $gt->translate($kv_naziv, 'de','hr');
						$kv_opis_de = $gt->translate($kv_opis, 'de','hr');

						$query_ke = $db->prepare("
							UPDATE idk_kandidat_vjestine
									SET	kv_naziv_de = :kv_naziv_de, kv_opis_de = :kv_opis_de
									WHERE kv_id = :kv_id");

						$query_ke->execute(array(
								':kv_naziv_de' => $kv_naziv_de,
								':kv_opis_de' => $kv_opis_de,
								':kv_id' => $kv_id));
				?>
				<div class="col-xs-12 idk_margin_top30">
					<div class="row">
						<br/>
						<br/>
						<div class="col-xs-3 text-right">
							<p><?php echo $kv_grupa; ?></p>
						</div>
						<div class="col-xs-9 idk_h3_style">
							<h3><?php echo $kv_naziv_de; ?></h3>
							<p><?php echo $kv_opis_de; ?></p><br>
						</div>
					</div>
				</div>
				<?php } ?>
				
				<div class="col-xs-12">
					<div class="col-xs-3 text-right">
						<b><p>Führerschein B</p></b>
					</div>
					<div class="col-xs-9 idk_h3_style">
						<p><?php echo $kandidat_vozacka_dozvola_de; ?></p><br>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10 text-right">
					<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Spremi PDF</span></button>
				</div>
			</div>
		</form>
		<?php
		break;	

        case "edit":
		
           if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){

                $kandidat_id = $_GET['id'];
                
                $query = $db->prepare("
                SELECT kandidat_ime, kandidat_prezime, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_adresa, kandidat_grad, kandidat_drzava, kandidat_slika, cv_de, kde_kandidat_id, kde_prijava_na, kde_spol, kde_drzavljanstvo, kde_vozacka
                    FROM idk_kandidati
                    INNER JOIN idk_kandidat_de ON idk_kandidati.kandidat_id = idk_kandidat_de.kde_kandidat_id
                    WHERE kandidat_id = :kandidat_id");

                $query->execute(array(
                            ':kandidat_id' => $kandidat_id));

                $row = $query->fetch();

                $kandidat_ime = $row['kandidat_ime'];
                $kandidat_prezime = $row['kandidat_prezime'];
                $kandidat_spol = $row['kde_spol'];
                $kandidat_drzavljanstvo = $row['kde_drzavljanstvo'];
                $kandidat_prijava_na = $row['kde_prijava_na'];
                $kandidat_vozacka_dozvola = $row['kde_vozacka'];



		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Uredi prijevod za <?php echo $kandidat_ime; ?></h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=profil_de_edit" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>" />
                                    <div class="form-group">
										<label for="kde_spol" class="col-sm-3 control-label"><span class="text-danger">*</span> Spol:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kde_spol" id="kde_spol" value="<?php echo $kandidat_spol; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="kde_drzavljanstvo" class="col-sm-3 control-label">Državljanstvo:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kde_drzavljanstvo" id="kde_drzavljanstvo" value="<?php echo $kandidat_drzavljanstvo; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kde_prijava_na" class="col-sm-3 control-label">Prijava na:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kde_prijava_na" id="kde_prijava_na" value="<?php echo $kandidat_prijava_na; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kde_vozacka" class="col-sm-3 control-label">Vozačka dozvola:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kde_vozacka" id="kde_vozacka" value="<?php echo $kandidat_vozacka_dozvola; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<hr/>
									<?php
									$select_query_iskustvo = $db->prepare("
										SELECT kri_id, kri_pozicija_de, kri_opis_de
										FROM idk_kandidat_radno_iskustvo
										WHERE kri_kandidat_id = :kri_kandidat_id");
			
									$select_query_iskustvo->execute(array(
													':kri_kandidat_id' => $kandidat_id));
			
									while($select_row = $select_query_iskustvo->fetch()) {
								
										$kri_id = $select_row['kri_id'];
										$kri_pozicija = $select_row['kri_pozicija_de'];
										$kri_opis = $select_row['kri_opis_de'];

									?>
									<div class="form-group">
										<label for="kri_pozicija_opis" class="col-sm-3 control-label">Radno iskustvo:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_pozicija<?php echo $kri_id; ?>" id="kri_pozicija<?php echo $kri_id; ?>" value="<?php echo $kri_pozicija; ?>">
												<span class="materail-input-block__line"></span>
											</div>
											<div class="materail-input-block materail-input-block_success">
												<textarea class="form-control materail-input" type="text" name="kri_opis<?php echo $kri_id; ?>" id="kri_opis<?php echo $kri_id; ?>" value="<?php echo $kri_opis; ?>"><?php echo $kri_opis; ?></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<?php } ?>
									<hr/>
									<?php
									$select_query_obrazovanje = $db->prepare("
										SELECT ke_id, ke_naziv_kvalifikacije_de, ke_opis_de
										FROM idk_kandidat_edukacija
										WHERE ke_kandidat_id = :ke_kandidat_id");
				
									$select_query_obrazovanje->execute(array(
													':ke_kandidat_id' => $kandidat_id));
									
									while($select_row = $select_query_obrazovanje->fetch()) {
									
										$ke_id = $select_row['ke_id'];
										$ke_naziv_kvalifikacije = $select_row['ke_naziv_kvalifikacije_de'];
										$ke_opis = $select_row['ke_opis_de'];

									?>
									<div class="form-group">
										<label for="ke_edukacija" class="col-sm-3 control-label">Edukacija:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="ke_naziv_kvalifikacije<?php echo $ke_id; ?>" id="ke_naziv_kvalifikacije<?php echo $ke_id; ?>" value="<?php echo $ke_naziv_kvalifikacije; ?>">
												<span class="materail-input-block__line"></span>
											</div>
											<div class="materail-input-block materail-input-block_success">
												<textarea class="form-control materail-input" type="text" name="ke_opis<?php echo $ke_id; ?>" id="ke_opis<?php echo $ke_id; ?>" value="<?php echo $ke_opis; ?>"><?php echo $ke_opis; ?></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<?php } ?>
									<hr/>
									<?php
									$select_query_vjestine2 = $db->prepare("
										SELECT kv_id, kv_naziv_de, kv_opis_de
										FROM idk_kandidat_vjestine
										WHERE kv_kandidat_id = :kv_kandidat_id");
				
									$select_query_vjestine2->execute(array(
													':kv_kandidat_id' => $kandidat_id));
									
									while($select_row = $select_query_vjestine2->fetch()) {
									
										$kv_id = $select_row['kv_id'];
										$kv_naziv = $select_row['kv_naziv_de'];
										$kv_opis = $select_row['kv_opis_de'];
				

									?>
									<div class="form-group">
										<label for="kv_vjestine" class="col-sm-3 control-label">Vještine:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kv_naziv<?php echo $kv_id; ?>" id="kv_naziv<?php echo $kv_id; ?>" value="<?php echo $kv_naziv; ?>">
												<span class="materail-input-block__line"></span>
											</div>
											<div class="materail-input-block materail-input-block_success">
												<textarea class="form-control materail-input" type="text" name="kv_opis<?php echo $kv_id; ?>" id="kv_opis<?php echo $kv_id; ?>" value="<?php echo $kv_opis; ?>"><?php echo $kv_opis; ?></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<?php } ?>

									
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
											<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}

				break;

				case "edited":
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){

						$kandidat_id = $_GET['id'];
						
						$query = $db->prepare("
						SELECT kandidat_ime, kandidat_prezime, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_adresa, kandidat_grad, kandidat_drzava, kandidat_slika, cv_de, kde_kandidat_id, kde_prijava_na, kde_spol, kde_drzavljanstvo, kde_vozacka
							FROM idk_kandidati
							INNER JOIN idk_kandidat_de ON idk_kandidati.kandidat_id = idk_kandidat_de.kde_kandidat_id
							WHERE kandidat_id = :kandidat_id");

						$query->execute(array(
									':kandidat_id' => $kandidat_id));

						$row = $query->fetch();

						$kandidat_ime = $row['kandidat_ime'];
						$kandidat_prezime = $row['kandidat_prezime'];
						$kandidat_spol = $row['kde_spol'];
						$kandidat_drzavljanstvo = $row['kde_drzavljanstvo'];
						$kandidat_prijava_na = $row['kde_prijava_na'];
						$kandidat_vozacka_dozvola = $row['kde_vozacka'];



		?>
			<div class="row">
				<div class="col-xs-12">
					<div class="col-xs-8">
						<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Uredi prijevod za <?php echo $kandidat_ime; ?></h1>
					</div>
					<div class="col-xs-4 text_right">
						<form id="idk_formXX" action="<?php getSiteURL(); ?>do.php?form=profil_gen_de" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
							<input type="hidden" name="kand_id" value="<?php echo $kandidat_id; ?>" />
							<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Ažuriraj PDF</span></button>
						</form>
					</div>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=profil_de_edit" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>" />
                                    <div class="form-group">
										<label for="kde_spol" class="col-sm-3 control-label"><span class="text-danger">*</span> Spol:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kde_spol" id="kde_spol" value="<?php echo $kandidat_spol; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="kde_drzavljanstvo" class="col-sm-3 control-label">Državljanstvo:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kde_drzavljanstvo" id="kde_drzavljanstvo" value="<?php echo $kandidat_drzavljanstvo; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kde_prijava_na" class="col-sm-3 control-label">Prijava na:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kde_prijava_na" id="kde_prijava_na" value="<?php echo $kandidat_prijava_na; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kde_vozacka" class="col-sm-3 control-label">Vozačka dozvola:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kde_vozacka" id="kde_vozacka" value="<?php echo $kandidat_vozacka_dozvola; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<hr/>
									<?php
									$select_query_iskustvo = $db->prepare("
										SELECT kri_id, kri_pozicija_de, kri_opis_de
										FROM idk_kandidat_radno_iskustvo
										WHERE kri_kandidat_id = :kri_kandidat_id");
			
									$select_query_iskustvo->execute(array(
													':kri_kandidat_id' => $kandidat_id));
			
									while($select_row = $select_query_iskustvo->fetch()) {
								
										$kri_id = $select_row['kri_id'];
										$kri_pozicija = $select_row['kri_pozicija_de'];
										$kri_opis = $select_row['kri_opis_de'];

									?>
									<div class="form-group">
										<label for="kri_pozicija_opis" class="col-sm-3 control-label">Radno iskustvo:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_pozicija<?php echo $kri_id; ?>" id="kri_pozicija<?php echo $kri_id; ?>" value="<?php echo $kri_pozicija; ?>">
												<span class="materail-input-block__line"></span>
											</div>
											<div class="materail-input-block materail-input-block_success">
												<textarea class="form-control materail-input" type="text" name="kri_opis<?php echo $kri_id; ?>" id="kri_opis<?php echo $kri_id; ?>" value="<?php echo $kri_opis; ?>"><?php echo $kri_opis; ?></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<?php } ?>
									<hr/>
									<?php
									$select_query_obrazovanje = $db->prepare("
										SELECT ke_id, ke_naziv_kvalifikacije_de, ke_opis_de
										FROM idk_kandidat_edukacija
										WHERE ke_kandidat_id = :ke_kandidat_id");
				
									$select_query_obrazovanje->execute(array(
													':ke_kandidat_id' => $kandidat_id));
									
									while($select_row = $select_query_obrazovanje->fetch()) {
									
										$ke_id = $select_row['ke_id'];
										$ke_naziv_kvalifikacije = $select_row['ke_naziv_kvalifikacije_de'];
										$ke_opis = $select_row['ke_opis_de'];

									?>
									<div class="form-group">
										<label for="ke_edukacija" class="col-sm-3 control-label">Edukacija:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="ke_naziv_kvalifikacije<?php echo $ke_id; ?>" id="ke_naziv_kvalifikacije<?php echo $ke_id; ?>" value="<?php echo $ke_naziv_kvalifikacije; ?>">
												<span class="materail-input-block__line"></span>
											</div>
											<div class="materail-input-block materail-input-block_success">
												<textarea class="form-control materail-input" type="text" name="ke_opis<?php echo $ke_id; ?>" id="ke_opis<?php echo $ke_id; ?>" value="<?php echo $ke_opis; ?>"><?php echo $ke_opis; ?></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<?php } ?>
									<hr/>
									<?php
									$select_query_vjestine2 = $db->prepare("
										SELECT kv_id, kv_naziv_de, kv_opis_de
										FROM idk_kandidat_vjestine
										WHERE kv_kandidat_id = :kv_kandidat_id");
				
									$select_query_vjestine2->execute(array(
													':kv_kandidat_id' => $kandidat_id));
									
									while($select_row = $select_query_vjestine2->fetch()) {
									
										$kv_id = $select_row['kv_id'];
										$kv_naziv = $select_row['kv_naziv_de'];
										$kv_opis = $select_row['kv_opis_de'];
				

									?>
									<div class="form-group">
										<label for="kv_vjestine" class="col-sm-3 control-label">Vještine:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kv_naziv<?php echo $kv_id; ?>" id="kv_naziv<?php echo $kv_id; ?>" value="<?php echo $kv_naziv; ?>">
												<span class="materail-input-block__line"></span>
											</div>
											<div class="materail-input-block materail-input-block_success">
												<textarea class="form-control materail-input" type="text" name="kv_opis<?php echo $kv_id; ?>" id="kv_opis<?php echo $kv_id; ?>" value="<?php echo $kv_opis; ?>"><?php echo $kv_opis; ?></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<?php } ?>

									
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
											<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}

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
