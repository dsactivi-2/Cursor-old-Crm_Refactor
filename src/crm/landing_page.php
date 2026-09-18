<?php
include("includes/functions.php");
if(isset($_REQUEST['page'])){
	$page = $_REQUEST['page'];
}
?>
<!DOCTYPE html>
<html>
	<head>
	<script
	  src="https://code.jquery.com/jquery-3.6.0.js"
	  integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
	  crossorigin="anonymous">
	 </script>
		<title>Jobstep</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@800;900&display=swap" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />

		<style>
			body {
				background: #F7F9FF; 
				overflow-x: hidden;
			}
			.container {
				background: #F7F9FF;
				margin-top: 50px;
			}
			.naslov {
				font-family: 'Inter', sans-serif;
				color: #18191F;
				font-weight: 900;
				font-size: 60px;
			}
			.slika {
				padding: 0;
				margin-top: 45px;
			}
			.text-box {
				padding: 0px;
				position: static;
				width: 459px;
				height: 272px;
				left: 0px;
				top: 0px;
				margin-top: 100px;
			}
			.text {
				font-family: 'Inter', sans-serif;
				color: #8A8A8A;
			}
			.form-select {
				height: 60px;
				background: #FFFFFF;
				box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
				border-color: #FFFFFF;
				border-radius: 0;
			}
			.posalji {
				width:100%;
				height: 60px;
				background: #3A4053;
				box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
				font-family: Inter;
				font-style: normal;
				font-weight: bold;
				font-size: 18px;
				line-height: 20px;
				text-align: center;
				font-feature-settings: 'salt' on, 'liga' off;
				color: #FFFFFF;
				border-color: #3A4053;
			}
			
			.krug{
				visibility: hidden;
				width: 170px;
				height: 170px;
				background: #6097A0;
				border-radius: 50%;
				position: absolute;
				top: -75px;
				right: -75px;
			}
			.forma{
				display: flex;
				justify-content: center;
				align-items:center;
				
			}
			.forma-child{
				flex:1;
				margin-left: 5px;
				margin-right: 5px;
			}
			.zvjezdica{
				color:#6097A0;
			}
		@media (max-width: 991px) {
			.forma {
				flex-direction: column;
			}
			.forma-child{
				width: 85vw;
				margin-bottom: 10px; 
			}
			.slika{
				position: absolute;
			}
			.img{
				width: 110vw;
				margin-top: 30px;
				margin-bottom: 30px;
			}
			.naslov {
				font-size: 30px;
			}
			.container {
				background: #F7F9FF;
				margin-top: 0;
			}
			.text-box {
				margin-top: 70px;
				padding: 7vw;
			}
			.krug {
				visibility: visible;
			}
		}

		</style>
	</head>
	<body>
		<?php switch($page){
			case "input_forma":
			
			if(isset($_GET['id'])){
				$hash_id = $_GET['id'];
			} else {
				$hash_id = null;
			}


			$stmt_update_status_pracenja = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET status_pracenja = 2 WHERE hash_id_dipl = :hash_id");
			$stmt_update_status_pracenja->execute(array(
				":hash_id" => $hash_id
			));
						
		?>
			<div class="container">
				<div class="row">
					<div class="col text-box">
					  <h2 class="naslov">Želiš li raditi u struci u Njemačkoj?</h2>
					  <p class="text">Navedi tačne podatke o svom obrazovanju, jer na taj način osiguravaš da prvo tebe pozovemo kada se otvori konkurs koji odgovara tvojoj struci.</p>
					</div>
					<div class="col slika" id="slika">
					<img src="/images/Group-elements.png" class="img" id="img"></img>
					</div>
				</div>
				
					<form action="<?php getSiteURL(); ?>landing_page?page=add_input" method="POST" class="forma">
						<input type="hidden" value="<?php echo $hash_id; ?>" name="hash_id">
						<div class="forma-child">
							<label for="select">Završeno obrazovanje<span class="zvjezdica">*</span>:</label> <br>
								<select class="form-select" id="select" name="skola_id" required>
									<option id="option" disabled selected>Odaberi...</option>
								<?php
									$stmt_skola = $db->prepare("SELECT 
																	skola_naziv,
																	skola_id
																FROM
																	idk_skole
															");
									$stmt_skola->execute();
									while($result_skola = $stmt_skola->fetch()){
										$naziv_skole = $result_skola['skola_naziv'];
										$id_skole = $result_skola['skola_id'];
								?>
										<option value="<?php echo $id_skole; ?>"><?php echo $naziv_skole; ?></option>
								<?php
									}
								?>
								</select>
						</div>
						<script>
							$('#select').on('change', function() {
								var skola_id = $(this).val();
								$.ajax({
									url: '/landing_page_ajax.php',
									type: 'POST',
									data: {'skola_id':skola_id},
									dataType: 'html',
									success: function(data) {
										$("#select2").empty().append(data);
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
							});
						</script>
						<div class="forma-child">
							<label for="select2">Zvanje/smjer<span class="zvjezdica">*</span>:</label> <br>
								<select class="form-select" id="select2" name="smjer_id" required>
									<option disabled selected>Odaberi...</option>
								</select>
						</div>
						<div class="forma-child">
							<label for="select3">Nivo poznavanja jezika<span class="zvjezdica">*</span>:</label> <br>
								<select class="form-select" id="select3" name="jezik" required>
									<option disabled selected>Odaberi...</option>
									<option value="1">Bez znanja</option>
									<option value="2">A1</option>
									<option value="3">A2</option>
									<option value="4">B1</option>
									<option value="5">B2</option>
									<option value="6">C1</option>
									<option value="7">C2</option>
								</select>
						</div>
						<div class="forma-child">
						<br>
							<button class="posalji" type="submit" id="submit">POŠALJI</button>
						</div>
					</form>
				
			</div>
			<div class="krug"></div>
			<div id="img-container"></div>
			<script>
				const imgContainer = document.getElementById("img-container");
				const img = document.getElementById("img");
				const slika = document.getElementById("slika");

				$( window ).resize(function() {
				  if($( window ).width() < 992){
					imgContainer.appendChild(img);
				  }
				  else{
					slika.appendChild(img);
				  }
				});
				
				if($( window ).width() < 992){
					imgContainer.appendChild(img);
				  }
				  
				$(document).ready(function(){
					 $('#submit').attr('disabled', 'disabled');

					function updateFormEnabled() {
						if (verifySettings()) {
							$('#submit').removeAttr('disabled');
						} else {
							$('#submit').attr('disabled', 'disabled');
						}
					}

					function verifySettings() {
						if ($('#select').val() != '' && $('#select2').val() != '' && $('#select3').val() != '') {
							return true;
						} else {
							return false
						}
					}

					$('#select').change(updateFormEnabled);

					$('#select2').change(updateFormEnabled);
					
					$('#select3').change(updateFormEnabled);
				});
				
			</script>
		<?php
			break;
			
			case "add_input":
				$hash_id = $_POST['hash_id'];
				$skola_id = $_POST['skola_id'];
				$smjer_id = $_POST['smjer_id'];
				$nivo_jezik = $_POST['jezik'];
				
				switch($nivo_jezik){
						case 1:
							$jezik = "Bez znanja";
						break;
						
						case 2:
							$jezik = "A1";
						break;
						
						case 3:
							$jezik = "A2";
						break;
						
						case 4:
							$jezik = "B1";
						break;
						
						case 5:
							$jezik = "B2";
						break;
						
						case 6:
							$jezik = "C1";
						break;
						
						case 7:
							$jezik = "C2";
						break;
				}
				
				$stmt_update_status_pracenja = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET status_pracenja = 3 WHERE hash_id_dipl = :hash_id");
				$stmt_update_status_pracenja->execute(array(
					":hash_id" => $hash_id
				));
				
				if($skola_id != null && $smjer_id != null && $nivo_jezik != null){
					
					$stmt_update_status_informacija = $db->prepare("
																	UPDATE 
																		idk_nd_kandidati_ciscenje 
																	SET 
																		status_informacija = 0, 
																		treba_prebaciti_jezik_u_dipl = 1, 
																		znanje_jezika_iz_idk_kan_jezici = :jezik,
																		treba_prebaciti_skolu_smjer_u_dipl = 1,
																		skola = :skola,
																		smjer = :smjer
																	WHERE 
																		hash_id_dipl = :hash_id
																");
					$stmt_update_status_informacija->execute(array(
						":hash_id" => $hash_id,
						":jezik" => $jezik,
						":skola" => $skola_id,
						":smjer" => $smjer_id
					));
					
					$stmt_id_kandidata = $db->prepare("
													SELECT
														id_kandidata_dipl,
														id_kandidata_posao
													FROM
														idk_nd_kandidati_ciscenje
													WHERE
														arhiva = 0 
													AND
														hash_id_dipl = :hash_id
												");
					$stmt_id_kandidata->execute(array(
						":hash_id" => $hash_id
					));
					$result_id_kandidata = $stmt_id_kandidata->fetch();
					$id_kandidata_dipl = $result_id_kandidata['id_kandidata_dipl'];
					$id_kandidata_posao = $result_id_kandidata['id_kandidata_posao'];
					
					$stmt_update_dipl = $db->prepare("UPDATE idk_nd_kandidata SET nivo_poznavanja_jezika = :nivo_jezika, skola_nd_kandidata = :skola_id, skola_smjer_nd_kandidata = :smjer_id 
														WHERE id_broj_nd_kandidata = :id_dipl");
					$stmt_update_dipl->execute(array(
						":nivo_jezika" => $nivo_jezik,
						":skola_id" => $skola_id,
						":smjer_id" => $smjer_id,
						":id_dipl" => $id_kandidata_dipl
					));
					
					$stmt_insert_jezik = $db->prepare("
														INSERT INTO 
															idk_kandidat_jezici 
															(
																kj_naziv,
																kj_slusanje, 
																kj_citanje, 
																kj_govorna_interakcija, 
																kj_govorna_produkcija,
																kj_pisanje,
																kj_kandidatid
															)
														VALUES
															(
																:kj_naziv,
																:kj_slusanje, 
																:kj_citanje, 
																:kj_govorna_interakcija, 
																:kj_govorna_produkcija,
																:kj_pisanje,
																:kj_kandidatid
															)
													");
					$stmt_insert_jezik->execute(array(
						":kj_naziv" => "Njemački",
						":kj_slusanje" => $jezik, 
						":kj_citanje" => $jezik, 
						":kj_govorna_interakcija" => $jezik, 
						":kj_govorna_produkcija" => $jezik,
						":kj_pisanje" => $jezik,
						":kj_kandidatid" => $id_kandidata_posao
					));
					$stmt_skola_smjer = $db->prepare("
													SELECT
														ss_naziv,
														skola_naziv,
														skola_tip_obrazovanja
													FROM
														idk_skole_smjerovi
													JOIN
														idk_skole
													ON
														idk_skole_smjerovi.ss_skola_id = idk_skole.skola_id
													WHERE
														skola_id = :skola_id
													AND
														ss_id = :smjer_id
												");
					$stmt_skola_smjer->execute(array(
						":skola_id" => $skola_id,
						":smjer_id" => $smjer_id
					));
					$result_skola_smjer = $stmt_skola_smjer->fetch();
					$naziv_skole = $result_skola_smjer['skola_naziv'];
					$naziv_smjera = $result_skola_smjer['ss_naziv'];
					$tip_obrazovanja = $result_skola_smjer['skola_tip_obrazovanja'];
					
					$stmt_insert_skola = $db->prepare("
														INSERT INTO
															idk_kandidat_edukacija
															(
																ke_smjer_id,
																ke_naziv_kvalifikacije,
																ke_skola_id,
																ke_naziv,
																ke_kandidat_id,
																ke_vrsta_obrazovanja
															)
														VALUES
															(
																:smjer_id,
																:naziv_smjera,
																:skola_id,
																:naziv_skole,
																:id_kandidata_posao,
																:tip_obrazovanja
															)
													");
					$stmt_insert_skola->execute(array(
						":smjer_id" => $smjer_id,
						":naziv_smjera" => $naziv_smjera,
						":skola_id" => $skola_id,
						":naziv_skole" => $naziv_skole,
						":id_kandidata_posao" => $id_kandidata_posao,
						":tip_obrazovanja" => $tip_obrazovanja
					));
					
					header("Location: https://crm.job-step.com/landing_page.php?page=success");
				}
			break;
			
			case "success":
			?>
				<div class="container">
					<div class="row">
						<div class="col text-box">
						  <h2 class="naslov">Hvala na izdvojenom vremenu!</h2>
						  <p class="text">Sada ste u potpunosti ispunili Vaše podatke u sistemu. Bit ćete kontaktirani ukoliko se otvori oglas za posao Vaše struke.</p>
						</div>
						<div class="col slika" id="slika">
						<img src="/images/Group-elements.png" class="img" id="img"></img>
						</div>
					</div>
				</div>
				<div class="krug"></div>
				<div id="img-container"></div>
				<script>
					const imgContainer = document.getElementById("img-container");
					const img = document.getElementById("img");
					const slika = document.getElementById("slika");

					$( window ).resize(function() {
					  if($( window ).width() < 992){
						imgContainer.appendChild(img);
					  }
					  else{
						slika.appendChild(img);
					  }
					});
					
					if($( window ).width() < 992){
						imgContainer.appendChild(img);
					  }
				</script>
			<?php	
			break;
		}
		?>
		
	</body>
</html>