<?php 
	include("includes/functions.php");
	$id_skladiste = 139;
	$nizKandidatiExplode = array();
	//Kod je napisan al nije testiran
	//U medjuvremenu posao
	
	$trenutnoVrijeme = date("Y-m-d H:i:s");
	$queryKandidati = $db->prepare("
		SELECT 
			kan.id_broj_nd_kandidata
		FROM 
			idk_nd_kandidata kan
		INNER JOIN 
			idk_nd_kandidata_status_log log
		ON 
			kan.id_broj_nd_kandidata = log.idd_broj_nd_kandidata AND log.id_log_status_nd_kandidata = (
				SELECT 
					MAX(l.id_log_status_nd_kandidata)
				FROM 
					idk_nd_kandidata_status_log l
				WHERE 
					l.idd_broj_nd_kandidata = kan.id_broj_nd_kandidata
					AND 
					l.status_nd_kandidata = 1
					AND 
					l.pstatus_nd_kandidata = 8
			)
		WHERE 
			kan.status_nd_kandidata = 1 
			AND 
			kan.pstatus_nd_kandidata = 8
			AND 
			DATEDIFF('".$trenutnoVrijeme."', log.vrijeme_promjene_statusa_nd_kandidata) >= 7
	");
	$queryKandidati->execute();
	$brojKandidata = $queryKandidati->rowCount();
	if($brojKandidata != 0){
		//echo '<br>Kandidati<br><br>';
		while($rowKandidati = $queryKandidati->fetch()){
			$kanID = $rowKandidati["id_broj_nd_kandidata"];
			array_push($nizKandidatiExplode, $kanID);
			//echo '<br>'.$kanID;
			promjenaStatusaDIPLKandidat($kanID, 1, 11, 0);
			promjenaAgentaProdajeDiplKandidata($kanID, $id_skladiste, 0);
		}
		$nizKandidatiImplode = implode(",",$nizKandidatiExplode);
		if(count($nizKandidatiExplode) > 0){
			$log_desc = "Kanidati: (".$nizKandidatiImplode.") prelaze sa statusa Neuspješan Lead 2 na Lead NL";
			$log_date = date('Y-m-d H:i:s');

			$log_query = $db->prepare("
				insert into idk_logs
					(log_employeeid, log_desc, log_date)
				values
					(".$id_skladiste.", '".$log_desc."', '".$log_date."')
			");
			
			$log_query->execute();
		}
		//echo '<br><br>Ukupno kandidata: '.$brojKandidata.'<br><br>';
	}else{
		//echo '<br>Nema kandidata<br><br>';
	}
?>