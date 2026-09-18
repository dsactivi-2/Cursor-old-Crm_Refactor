<?php

include("includes/functions.php");
/*
SELECT employee_id, SUM(iznos_obracuna_bam) FROM `idk_obracuni` WHERE predracun_id IN (1224,1924,1765,1223,1985,1543,1826,1736,2126,1789) AND status_obracuna = 1 GROUP BY employee_id
*/
$brojac = 1;
$query1 = $db->prepare("
	SELECT pr_kandidat_id FROM idk_predracuni WHERE pr_datum_kreiranja BETWEEN '2021-01-01 00:00:00' AND '2021-02-06 00:00:00' AND pr_status = 0
");
$query1->execute(); 
?>
<table>
	<thead>
		<th>#</th>
		<th>ID pred</th>
		<th>ID kand</th>
		<th>datum kreir</th>
		<th>agent</th>
		<th>rata</th>
		<th>status</th>
	</thead>
<?php
	while($row1 = $query1->fetch()){
		$pr_kandidat_id = $row1["pr_kandidat_id"];
		$query2 = $db->prepare("SELECT pr_id, pr_datum_kreiranja, pr_zaposlenik, pr_rata, pr_status FROM idk_predracuni WHERE pr_kandidat_id = $pr_kandidat_id AND pr_status != 0");
		$query2->execute();
		while($row2 = $query2->fetch()){
			$pr_id = $row2['pr_id'];
			$pr_datum_kreiranja = $row2['pr_datum_kreiranja'];
			$pr_zaposlenik = $row2['pr_zaposlenik'];
			$pr_rata = $row2['pr_rata'];
			$pr_status = $row2['pr_status'];
			
			?>
				<tr>
					<td><?php echo $brojac++; ?></td>
					<td><?php echo $pr_id; ?></td>
					<td><?php echo $pr_kandidat_id; ?></td>
					<td><?php echo $pr_datum_kreiranja; ?></td>
					<td><?php echo $pr_zaposlenik; ?></td>
					<td><?php echo $pr_rata; ?></td>
					<td><?php echo $pr_status; ?></td>
				</tr>
			<?php
			
		}
	}
?>
</table>
<?php

/********** Trazenje kandidata koji su zadnju ratu uplatili prije 1.7.2021,  a poslije toga nisu uplatili nista  START *****************/
/*
$brojac = 1;
$query1 = $db->prepare("
	SELECT pr1.pr_id, pr1.pr_kandidat_id, pr1.pr_rata, pr1.pr_domaca_valuta, pr1.pr_vrijednost_BAM, pr1.pr_datum_uplate 
	FROM idk_predracuni pr1
	WHERE pr1.pr_datum_uplate < '2021-07-01' AND pr1.pr_uplaceno = 1 AND pr1.pr_status = 2 AND pr1.pr_id = (
					SELECT MAX(pr_id)
					FROM idk_predracuni pr2
					WHERE pr2.pr_datum_uplate < '2021-07-01' AND pr2.pr_vrsta_predracuna = 1 AND pr2.pr_uplaceno = 1 AND pr2.pr_status = 2  AND pr2.pr_kandidat_id = pr1.pr_kandidat_id
				)
	ORDER BY pr1.pr_datum_uplate ASC
");


$query1->execute(); 
?>
<table>
	<thead>
		<th>#</th>
		<th>ID pred</th>
		<th>ID kand</th>
		<th>Ime</th>
		<th>Prezime</th>
		<th>Broj rate</th>
		<th>Ukupno rata</th>
		<th>datum uplate</th>
	</thead>
<?php
	while($row1 = $query1->fetch()){
		
		$id = intval($row1["pr_id"]);
		$pr_rata = intval($row1["pr_rata"]);
		$pr_kandidat_id = $row1["pr_kandidat_id"];
		$pr_datum_uplate = $row1["pr_datum_uplate"];
		$query2 = $db->prepare("SELECT ime_nd_kandidata, prezime_nd_kandidata, vrsta_ugovora_nd_kandidata FROM idk_nd_kandidata WHERE id_broj_nd_kandidata = $pr_kandidat_id");
		$query2->execute();
		$row2 = $query2->fetch();
		$vrsta_ugovora = $row2['vrsta_ugovora_nd_kandidata'];
		$ime_nd_kandidata = $row2['ime_nd_kandidata'];
		$prezime_nd_kandidata = $row2['prezime_nd_kandidata'];
		switch($vrsta_ugovora){
			case 1: case 2: case 22:
				$broj_rata = 2;
			break;
			case 5: case 6: case 23:
				$broj_rata = 3;
			break;
			case 7: case 8: case 24:
				$broj_rata = 4;
			break;
			case 3: case 4: case 25:
				$broj_rata = 5;
			break;
			default:
			$broj_rata = 1;
		}
		
		if($broj_rata-$pr_rata >= 1){
			
			$query3 = $db->prepare("SELECT pr_id FROM idk_predracuni WHERE pr_kandidat_id = :pr_kandidat_id AND pr_rata > :pr_rata AND pr_status = 2 AND pr_uplaceno = 1");
			$query3->execute(array(
						'pr_kandidat_id' => $pr_kandidat_id,
						':pr_rata' => $pr_rata
			));
			$novi_predracuni = $query3->rowCount();
			if($novi_predracuni == 0){
				?>
				<tr>
					<td><?php echo $brojac++; ?></td>
					<td><?php echo $id; ?></td>
					<td><?php echo $pr_kandidat_id; ?></td>
					<td><?php echo $ime_nd_kandidata; ?></td>
					<td><?php echo $prezime_nd_kandidata; ?></td>
					<td><?php echo $pr_rata; ?></td>
					<td><?php echo $broj_rata; ?></td>
					<td><?php echo $pr_datum_uplate; ?></td>
				</tr>
				<?php
			}
		}
	}
?>
</table>
<?php
*/

/********** Trazenje kandidata koji su zadnju ratu uplatili prije 1.7.2021,  a poslije toga nisu uplatili nista  END *****************/
?>
