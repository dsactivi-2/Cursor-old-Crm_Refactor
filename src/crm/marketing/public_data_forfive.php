<?php 


/****************************************
	OMOGUĆEN PRISTUP FOR FIVE TIMU -
	
	SADRŽAJ:
			***** LINKOVI GENERISANI ZA NJIHOVE MARKETING KAMPANJE
			***** BROJ ULAZA U SISTEM, PUTEM NJIHOVIH LINKOVA
	
	LINKOVI SE ZASAD RUČNO DODAVAJU
			
	
	OBIČNA TABELA
****************************************/




header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");


/* KAMPANJA_ID U SLJEDEĆI ARRAY RADI IZVLAČENJA IZ BAZE */

$kampanje = array(
'70','68', '67', '66', '65', '64', '62', '60', '59', '58', '57', '56', '55', '54', '53', '52', '51','50','49');

?>
<!DOCTYPE html>
<html>
<head>

<div class="container-fluid">
	<div class="row">
		<div class="col-xs-8">
			<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> STATISTIKA ULAZA</h1>
		</div>
		<div class="col-xs-12">
			<hr />
			<table id = "main-table" width="50%" class = "table" border="1">
				<thead>
					<th>GENERISANI LINK</th>
					<th>BROJ PRIJAVA</th>
				</thead>
				<tbody>
				<?php 
					
					//open connection za CURL
						$ch = curl_init();
						$url = "https://crm.job-step.com/public_kandidati.php?page=getEntriesPerLink&";
						
						$data = http_build_query($kampanje);
						$url = $url.$data;
						// var_dump($url);
						// $data = file_get_contents($url);
						// var_dump($data);
						// curl_setopt($ch,CURLOPT_URL, $url); 
						// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						// $result = curl_exec($ch);
						// echo curl_error($ch);
						// curl_close();
						// echo ($result);
						// var_dump ($result);
						curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);    
						$result = curl_exec($ch);
						echo curl_error($ch);
						curl_close();
						echo ($result);
						var_dump ($result);
				?>
						
				</tbody>
			</table>
		</div>
	</div>
</div>

</body>
</html>