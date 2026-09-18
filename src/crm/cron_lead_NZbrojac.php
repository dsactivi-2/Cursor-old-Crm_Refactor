<?php 
	include("includes/functions.php");
	$time_start = microtime(true);
	$trenutnoVrijeme = date("Y-m-d H:i:s");
	// $trenutnoVrijeme = date("Y-m-d H:i:s", strtotime("+2 days"));
	// echo $trenutnoVrijeme;
	// exit();
	$biljeskaIdExplode = array();
	$biljeskaIdImplode = "";
	$nizKandidatiExplode = array();
	$nizKandidatiImplode = "";
	$brojBiljeski = 0;
	//ovdje uzimam sve maximalne ID-eve biljeski za kandidate koji su u nezainteresiranom leadu
	//svaka biljeska takvogo tipa unutar sebe sadrzi razlog po kojim je kandidat dosao u taj status
	//ovim skraćujem krug pretrage - zbog joina sa biljeskama
	$pomocniQuery = $db->prepare("
		SELECT 
			MAX(b.id_biljeska_nd) AS id_bilj
		FROM 
			idk_nd_kandidata_biljeske b 
		INNER JOIN 
			idk_nd_kandidata k 
		ON 
			b.id_kandidata_biljeska_nd = k.id_broj_nd_kandidata 
		WHERE 
			(k.status_nd_kandidata = 1 AND k.pstatus_nd_kandidata = 4) 
			AND 
			(b.tip_biljeska_nd = 3 OR b.tip_biljeska_nd = 13)
			AND 
			b.status_biljeska_nd = 2 
			AND 
			b.razlog_biljeska_nd is not null 
		GROUP BY b.id_kandidata_biljeska_nd
	");
	$pomocniQuery->execute();
	$brojBiljeski = $pomocniQuery->rowCount();
	if($brojBiljeski != 0){
		while($pomocniRow = $pomocniQuery->fetch()){
			$idBiljeske = $pomocniRow["id_bilj"];
			array_push($biljeskaIdExplode, $idBiljeske);
		}
		$biljeskaIdImplode = implode(',', $biljeskaIdExplode);
		echo "ID biljeski: ".$biljeskaIdImplode."<br>";
		echo "BROJ biljeski: ".$brojBiljeski."<br>";
		if(count($biljeskaIdExplode) != 0){
			//ovdje uzimam sve razloge kod kojih je ponovno zvanje postavljeno kao 1
			//tj. takvi razlozi vode u nezainteresiran lead
			//i da je status aktivan - ne gledam deaktivirane i odbijene
			$brojRazloga = 0;
			$razloziQuery = $db->prepare("
				SELECT
					id_ro,
					br_dana_ro
				FROM 
					idk_ro_usluge
				WHERE 
					tip_ro = 1 
					AND 
					status_ro = 1 OR status_ro = 2
					AND 
					ponovno_zvanje_ro = 1
					AND 
					br_dana_ro is not null
			");
			$razloziQuery->execute();
			$brojRazloga = $razloziQuery->rowCount();
			if($brojRazloga != 0){
				while($razloziRow = $razloziQuery->fetch()){
					$idRazlog = $razloziRow["id_ro"];
					$brojDanaRazlog = $razloziRow["br_dana_ro"];
					$brojKandidata = 0;
					
					//sada je potrebno naci sve Id-eve kandidata koji su trenutno na statusu nezainteresiran lead pod razlogom prema iteraciji petlje
					//i kojima je prošao broj dana definisan za ponovno zvanje kandidata
					
					$queryKandidati = $db->prepare("
						SELECT
							k.id_broj_nd_kandidata AS kanID
						FROM 
							idk_nd_kandidata k
						INNER JOIN
							idk_nd_kandidata_biljeske b 
						ON 
							k.id_broj_nd_kandidata = b.id_kandidata_biljeska_nd
						WHERE 
							b.id_biljeska_nd IN (".$biljeskaIdImplode.") 
							AND 
							(k.status_nd_kandidata = 1 AND k.pstatus_nd_kandidata = 4)
							AND 
							(b.tip_biljeska_nd = 3 OR b.tip_biljeska_nd = 13)
							AND 
							b.status_biljeska_nd = 2 
							AND 
							b.razlog_biljeska_nd = ".$idRazlog."
							AND 
							DATEDIFF('".$trenutnoVrijeme."', b.vrijeme_dodavanja_biljeska_nd) >= ".$brojDanaRazlog." limit 50
					");
					$queryKandidati->execute();
					//var_dump($queryKandidati);
					$brojKandidata = $queryKandidati->rowCount();
					echo "Razlog ID: ".$idRazlog." --- broj kandidata: ".$brojKandidata."<br>";
					if($brojKandidata != 0){
						while($rowKandidati = $queryKandidati->fetch()){
							$kanID = $rowKandidati["kanID"];
							array_push($nizKandidatiExplode, $kanID);
							
							// Vrsiti sve radnje koje treba - promjena statusa i tako dalje
							
						}
					}
					
				}
				$nizKandidatiImplode = implode(",",$nizKandidatiExplode);
				echo "Kandidati prebaceni: ".$nizKandidatiImplode;
				echo "<br>BROJ prebacenih: ".count($nizKandidatiExplode);
			}
		}
	}
	$time_end = microtime(true);
	$result = ($time_end - $time_start)/60;
	echo "<br>".$result;

?>