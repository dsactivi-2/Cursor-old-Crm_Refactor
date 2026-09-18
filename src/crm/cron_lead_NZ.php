<?php 
	include("includes/functions.php");
	$time_start = microtime(true);

	$idSkladiste = 139;
	$currentTimeStamp = date("Y-m-d H:i:s");
	$candidateExplode = array();
	$candidateImplode = "";
	
	$query = $db->prepare("
		SELECT 
			bilj.id_biljeska_nd AS biljeskaId,
			bilj.id_kandidata_biljeska_nd AS kandidatId,
			bilj.status_biljeska_nd AS statusBilj, 
			DATE(bilj.vrijeme_dodavanja_biljeska_nd) AS vrijemeBilj,
			bilj.razlog_biljeska_nd AS razlogBilj,
			rou.br_dana_ro AS brojDanaZvanje,
			DATEDIFF(CURRENT_TIMESTAMP, bilj.vrijeme_dodavanja_biljeska_nd) AS brojDanaStvarno
		FROM 
			idk_nd_kandidata_biljeske bilj 
		INNER JOIN 
			(
				SELECT 
					kan.id_broj_nd_kandidata AS kanId, 
					DATE(slog.vrijeme_promjene_statusa_nd_kandidata) AS vrijemeStatusa
				FROM 
					idk_nd_kandidata kan 
				INNER JOIN 
					idk_nd_kandidata_status_log slog 
				ON 
					kan.id_broj_nd_kandidata = slog.idd_broj_nd_kandidata
					AND 
					kan.status_nd_kandidata = 1 
					AND 
					kan.pstatus_nd_kandidata = 4 
					AND 
					slog.status_nd_kandidata = 1
					AND 
					slog.pstatus_nd_kandidata = 4
					AND 
					slog.broj_dana_statusa_nd_kandidata is null
			)
			AS 
			kanAndStatusLog
		ON 
			bilj.id_kandidata_biljeska_nd = kanAndStatusLog.kanId
			AND 
			DATE(bilj.vrijeme_dodavanja_biljeska_nd) = kanAndStatusLog.vrijemeStatusa
			AND 
			(
				(
					bilj.status_biljeska_nd = 3 
					AND 
					bilj.tip_biljeska_nd = 4
				)
				OR 
				(
					bilj.status_biljeska_nd = 2 
					AND 
					bilj.tip_biljeska_nd IN (3,13) 
				)
			)
			AND 
			bilj.razlog_biljeska_nd IS NOT NULL
		INNER JOIN
			idk_ro_usluge rou 
		ON 
			bilj.razlog_biljeska_nd = rou.id_ro  
			AND 
			rou.tip_ro IN (1,4)
			AND 
			rou.status_ro IN (1,2)
			AND 
			rou.ponovno_zvanje_ro = 1
			AND 
			rou.br_dana_ro IS NOT NULL
			AND 
			DATEDIFF(CURRENT_TIMESTAMP, bilj.vrijeme_dodavanja_biljeska_nd) >= rou.br_dana_ro
	");
	$query->execute();
	
	$rowCountQuery = $query->rowCount();
	if($rowCountQuery != 0){
		while($row = $query->fetch()){
			$candidateId = intval($row["kandidatId"]); 

			array_push($candidateExplode, $candidateId);
			promjenaStatusaDIPLKandidat($candidateId, 1, 12, 0);
			promjenaAgentaProdajeDiplKandidata($candidateId, $idSkladiste, 0);
		}

		if(count($candidateExplode) != 0){
			$candidateImplode = implode(",", $candidateExplode);
			
			$logDesc = "Kandidati: (".$candidateImplode.") prelaze sa statusa Nije Zainteresiran na Lead NZ.";
			$logDate = $currentTimeStamp;

			$logQuery = $db->prepare("
				insert into idk_logs
					(log_employeeid, log_desc, log_date)
				values
					(".$idSkladiste.", '".$logDesc."', '".$logDate."')
			");
			$logQuery->execute();
		}
	}
	/*
	echo "<br><br> Broj kandidati: ".count($candidateExplode)."<br><br>";
	echo "<br><br> Rezultat kandidati: ".$candidateImplode."<br><br>";
	*/

	unset($candidateExplode);
	
	$time_end = microtime(true);
	$result = ($time_end - $time_start);
	/*
	echo "<br><br><br> Vrijeme: ".$result;
	*/
?>