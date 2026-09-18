<?php
include("includes/functions.php");
include("includes/common.php");

if(isset($_REQUEST['page'])){
	$page = $_REQUEST['page'];
}else{
	$page = "";
}
switch($page){
	
	case "provjera_uslova":
	
		$query_prijave = $db->prepare("SELECT pk_kandidatid, pk_id, pk_projectid FROM idk_project_kandidati WHERE pk_projectid IN (SELECT project_id FROM idk_projects WHERE project_nalogid = 220 AND project_name LIKE '%prijave%')");
		$query_prijave->execute();
		while($result_prijave = $query_prijave->fetch()){
			$kandidat_id_p = $result_prijave['pk_kandidatid'];
			checkCandidateInputs($kandidat_id);
		}
		
	
	break;
	case "projects":
		$query_ostali = $db->prepare("SELECT pk_kandidatid FROM idk_project_kandidati WHERE pk_projectid IN (SELECT project_id FROM idk_projects WHERE project_nalogid = 217 AND project_name NOT LIKE '%prijave%')");
		$query_ostali->execute();
		$ids_cand_ostali = array();
		while($result_ostali = $query_ostali->fetch()){
			$kandidat_id_o = $result_ostali['pk_kandidatid'];
			$ids_cand_ostali[] = $kandidat_id_o;
		}
		
		$query_prijave = $db->prepare("SELECT pk_kandidatid, pk_id, pk_projectid FROM idk_project_kandidati WHERE pk_projectid IN (SELECT project_id FROM idk_projects WHERE project_nalogid = 217 AND project_name LIKE '%prijave%')");
		$query_prijave->execute();
		
		$i = 1;
		?>
		<table>
			<th>#</th>
			<th>id ke</th>
			<th>skola</th>
			<th>skola</th>
		<?php
			
		while($result_prijave = $query_prijave->fetch()){
			
			$kandidat_id_p = $result_prijave['pk_kandidatid'];
			$pk_id = $result_prijave['pk_id'];
			$pk_projectid = $result_prijave['pk_projectid'];
			if(in_array($kandidat_id_p, $ids_cand_ostali)){
				$dupli = "jeste";
				// $query_delete = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_id = $pk_id");
				// $query_delete->execute();
			}else{ $dupli = "nije";}
			?>
			<tr>
				<td><?php echo $i++ ; ?></td>
				<td><?php echo $kandidat_id_p ; ?></td>
				<td><?php echo $dupli ; ?></td>
				<td><?php echo $pk_id ; ?></td>
			</tr>
			<?php
		}
		?>
		<table>
		<?php
	
	break;
	
	case "ke_edit":
		//prvi put radjeno do ID-a 46003
		//drugi put od 46004 i 48342
		$query_skole = $db->prepare("	
									SELECT ke_id, ke_naziv, ke_naziv_kvalifikacije 
									FROM idk_kandidat_edukacija
									WHERE ke_vrsta_obrazovanja IN ('srednje', 'visoko') AND ke_skola_id is null
									AND ke_id BETWEEN 47000 AND 48342
								");
		$query_skole->execute();
		$i = 1;
		?>
		<table>
			<th>#</th>
			<th>id ke</th>
			<th>skola</th>
			<th>moguce skole</th>
			<th>moguce skole2</th>
			<th>moguci smjerovi</th>
			<th>smjer</th>
		<?php
			
		while($row_skole = $query_skole->fetch()){
			
			$ke_id = $row_skole['ke_id'];
			$skola_naziv = $row_skole['ke_naziv'];
			$smjer_naziv = $row_skole['ke_naziv_kvalifikacije'];
			
			$query_moguce_skole = $db->prepare("SELECT skola_id FROM idk_skole WHERE skola_naziv = '$skola_naziv'");
			$query_moguce_skole->execute();
			$array_moguce_skole = array();
			while($row_moguce_skole = $query_moguce_skole->fetch()){
				array_push($array_moguce_skole, $row_moguce_skole['skola_id']);
			}
			//if(count($array_moguce_skole) > 0){
				$moguce_skole = implode(',', $array_moguce_skole);
				
				$query_moguci_smjerovi = $db->prepare("SELECT ss_id, ss_skola_id FROM idk_skole_smjerovi WHERE ss_naziv = '$smjer_naziv' AND ss_skola_id IN ($moguce_skole)");
				$query_moguci_smjerovi->execute();
				$array_moguci_smjerovi = array();
				$array_moguce_skole_smjera = array();
				while($row_moguci_smjerovi = $query_moguci_smjerovi->fetch()){
					array_push($array_moguci_smjerovi, $row_moguci_smjerovi['ss_id']);
					array_push($array_moguce_skole_smjera, $row_moguci_smjerovi['ss_skola_id']);
				}
				$moguci_smjerovi = implode('mogsm', $array_moguci_smjerovi);
				$moguce_skole_smjera = implode(',', $array_moguce_skole_smjera);
				if(count($array_moguce_skole_smjera) > 0 AND count($array_moguci_smjerovi) == 1){
					/*
					$update_s_s = $db->prepare("
							UPDATE idk_kandidat_edukacija
							SET ke_skola_id = :ke_skola_id, ke_smjer_id = :ke_smjer_id
							WHERE ke_id = :ke_id
					");
					$update_s_s->execute(array(
							':ke_skola_id' => $moguce_skole_smjera,
							':ke_smjer_id' => $moguci_smjerovi,
							':ke_id' => $ke_id
					));*/
					?>
					<tr>
						<td><?php echo $i++ ; ?></td>
						<td><?php echo $ke_id ; ?></td>
						<td><?php echo $skola_naziv ; ?></td>
						<td><?php echo $moguce_skole ; ?></td>
						<td><?php echo $moguce_skole_smjera ; ?></td>
						<td><?php echo $moguci_smjerovi ; ?></td>
						<td><?php echo $smjer_naziv ; ?></td>
					</tr>
					<?php
				}
			//}
		}
		?>
		<table>
		<?php
	break;
	case "dupli_smjerovi":
		
		$query_skole = $db->prepare("
						SELECT skola_id, skola_naziv FROM idk_skole
		");
		$query_skole->execute();
		echo "<table>";
		$i = 1;
		while($row_skole = $query_skole->fetch()){
			$skola_id = $row_skole['skola_id'];
			$skola_naziv = $row_skole['skola_naziv'];
			$query_smjer = $db->prepare("
						SELECT ss_id, ss_naziv, ss_naziv_de FROM idk_skole_smjerovi WHERE ss_skola_id = $skola_id order by ss_naziv
			");
			$query_smjer->execute();
			$prethodni_smjer_id = "";
			$prethodni_smjer_naziv = "";
			$prethodni_smjer_naziv_de = "";
			while($row_smjer = $query_smjer->fetch()){
				$tren_smjer_id = $row_smjer['ss_id'];
				$tren_smjer_naziv = $row_smjer['ss_naziv'];
				$tren_smjer_naziv_de = $row_smjer['ss_naziv_de'];
				if(trim($tren_smjer_naziv, " ") === trim($prethodni_smjer_naziv, " ")){
					?>
					<tr>
						<td><?php echo $i ; ?></td>
						<td><?php echo $skola_id ; ?></td>
						<td><?php echo $skola_naziv ; ?></td>
						<td><?php echo $prethodni_smjer_id ; ?></td>
						<td><?php echo $prethodni_smjer_naziv ; ?></td>
						<td><?php echo $prethodni_smjer_naziv_de ; ?></td>
					</tr>
					<tr>
						<td><?php echo $i++ ; ?></td>
						<td><?php echo $skola_id ; ?></td>
						<td><?php echo $skola_naziv ; ?></td>
						<td><?php echo $tren_smjer_id ; ?></td>
						<td><?php echo $tren_smjer_naziv ; ?></td>
						<td><?php echo $tren_smjer_naziv_de ; ?></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<?php
				}
				$prethodni_smjer_id = $tren_smjer_id;
				$prethodni_smjer_naziv = $tren_smjer_naziv;
				$prethodni_smjer_naziv_de = $tren_smjer_naziv_de;
			}
		}
		echo "</table>";
		
	break;

}
?>