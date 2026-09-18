<?php
include("includes/functions.php");

$form="";

if(isset($_REQUEST["form"])) {
	$form = $_REQUEST["form"];

switch ($form)
{

//Module projects start
case "add_project":

	$project_name = $_POST['project_name'];
	$project_plannedhours = $_POST['project_plannedhours'];
	$project_desc = $_POST['project_desc'];
	$project_companyid = $_POST['project_companyid'];
	$project_pmanagerid = $_POST['project_pmanagerid'];
	$project_datetime = date('Y-m-d H:i:s');
	$project_status = 1;
	$project_nalogid = $_POST['project_nalogid'];
	$project_datumtermina = $_POST['project_datumtermina'];
	$project_datumterminado = $_POST['project_datumterminado'];
	
	if($project_datumtermina != ""){
		$project_datumtermina_f = date("Y-m-d",strtotime("01.".$project_datumtermina));
	}else{
		$project_datumtermina_f = NULL;
	}

	if($project_datumterminado != ""){
		$project_datumterminado_f = date("Y-m-d",strtotime("01.".$project_datumterminado));
	}else{
		$project_datumterminado_f = NULL;
	}

	$query = $db->prepare("
					INSERT INTO idk_projects
						(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid, project_datumtermina, project_datumterminado)
					VALUES
						(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid, :project_datumtermina, :project_datumterminado)");

	$query->execute(array(
				':project_name' => $project_name,
				':project_plannedhours' => $project_plannedhours,
				':project_desc' => $project_desc,
				':project_companyid' => $project_companyid,
				':project_employeeid' => $logged_employee_id,
				':project_pmanagerid' => $project_pmanagerid,
				':project_datetime' => $project_datetime,
				':project_status' => $project_status,
				':project_nalogid' => $project_nalogid,
				':project_datumtermina' => $project_datumtermina_f,
				':project_datumterminado' => $project_datumterminado_f
				));

	//Add to LOGS
	$log_desc = "Dodao novi projekat: " . $project_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	if($project_nalogid != 0){
		header("Location: nalozi?page=open&id=$project_nalogid&mess=6");
	}else{
		header("Location: projects?page=list&mess=1");
	}


break;

case "cv_exp":

	$selectedrows = $_POST['selectedrows'];
	$lang = $_POST['lang'];
   
   	if($lang == 'ba'){
		$files = array();
		foreach($selectedrows as $kandidat_id){
			$query = $db->prepare("
				SELECT kandidat_ime, kandidat_prezime, cv_ba, cv_de
				FROM idk_kandidati
				WHERE kandidat_id = :kandidat_id");

			$query->execute(array(
			':kandidat_id' => $kandidat_id));

			$row = $query->fetch();
			
			$cv_ba = $row['cv_ba'];

				if($cv_ba == 1){
					$kandidat_ime = $row['kandidat_ime'];
					$kandidat_prezime = $row['kandidat_prezime'];
					$cv_filename = $kandidat_id."-".$kandidat_ime."_".$kandidat_prezime.".pdf";
					$file = "files/cv/ba/".$cv_filename; 
		
					$files[] = $file;
				}
		
		}
		$time = time();

		$zipname = "CV-Ba_".$time.".zip";
		$zip = new ZipArchive;
		$zip->open($zipname, ZipArchive::CREATE);
		foreach ($files as $file) {
			$zip->addFile($file);
		}
		$zip->close();

		///Then download the zipped file.
		header('Content-Type: application/zip');
		header('Content-disposition: attachment; filename='.$zipname);
		header('Content-Length: ' . filesize($zipname));
		header("Cache-control: private"); 
		readfile($zipname);

   	}elseif($lang == 'de'){
		$files = array();

		foreach($selectedrows as $kandidat_id){
			$query = $db->prepare("
				SELECT kandidat_ime, kandidat_prezime, cv_ba, cv_de
				FROM idk_kandidati
				WHERE kandidat_id = :kandidat_id");

			$query->execute(array(
			':kandidat_id' => $kandidat_id));

			$row = $query->fetch();
			
			$cv_de = $row['cv_de'];
			if($cv_de == 1){
				$kandidat_ime = $row['kandidat_ime'];
				$kandidat_prezime = $row['kandidat_prezime'];
				$cv_filename = $kandidat_id."-".$kandidat_ime."_".$kandidat_prezime.".pdf";
				$file = "files/cv/de/".$cv_filename; 
	
				$files[] = $file;
				}

			}

			$time = time();

			$zipname = "CV-De_".$time.".zip";
			$zip = new ZipArchive;
			$zip->open($zipname, ZipArchive::CREATE);
			foreach ($files as $file) {
				$zip->addFile($file);
			}
			$zip->close();
	
			///Then download the zipped file.
			header('Content-Type: application/zip');
			header('Content-disposition: attachment; filename='.$zipname);
			header('Content-Length: ' . filesize($zipname));
			header("Cache-control: private"); 
			readfile($zipname);
		
		}

		
   
   exit();

break;

case "cv_exp_de":

   $selectedrows = $_POST['selectedrows'];
   
   foreach($selectedrows as $row){
		
   }
   exit();

break;

case "edit_project":

	$project_id = $_POST['project_id'];
	$project_name = $_POST['project_name'];
	$project_desc = $_POST['project_desc'];
	$project_plannedhours = $_POST['project_plannedhours'];
	$project_companyid = $_POST['project_companyid'];
	$project_pmanagerid = $_POST['project_pmanagerid'];

		$query = $db->prepare("
						UPDATE idk_projects
						SET	project_name = :project_name, project_desc = :project_desc, project_plannedhours = :project_plannedhours, project_companyid = :project_companyid, project_pmanagerid = :project_pmanagerid
						WHERE project_id = :project_id");

		$query->execute(array(
				':project_name' => $project_name,
				':project_desc' => $project_desc,
				':project_plannedhours' => $project_plannedhours,
				':project_companyid' => $project_companyid,
				':project_pmanagerid' => $project_pmanagerid,
				':project_id' => $project_id));

	//Add to LOGS
	$log_desc = "Uredio profil projekta: " . $project_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: projects?page=list&mess=3");

break;

case "edit_project_settings":

	$ps_id_1 = $_POST['ps_id_1'];
	$ps_id_2 = $_POST['ps_id_2'];
	$ps_id_3 = $_POST['ps_id_3'];
	$ps_id_4 = $_POST['ps_id_4'];
	$ps_id_5 = $_POST['ps_id_5'];

		//Update1
		$query_1 = $db->prepare("
						UPDATE idk_projects_settings
						SET	ps_name = :ps_name
						WHERE ps_id = :ps_id");

		$query_1->execute(array(
				':ps_name' => $ps_id_1,
				':ps_id' => 1));

		//Update2
		$query_2 = $db->prepare("
						UPDATE idk_projects_settings
						SET	ps_name = :ps_name
						WHERE ps_id = :ps_id");

		$query_2->execute(array(
				':ps_name' => $ps_id_2,
				':ps_id' => 2));

		//Update3
		$query_3 = $db->prepare("
						UPDATE idk_projects_settings
						SET	ps_name = :ps_name
						WHERE ps_id = :ps_id");

		$query_3->execute(array(
				':ps_name' => $ps_id_3,
				':ps_id' => 3));

		//Update4
		$query_4 = $db->prepare("
						UPDATE idk_projects_settings
						SET	ps_name = :ps_name
						WHERE ps_id = :ps_id");

		$query_4->execute(array(
				':ps_name' => $ps_id_4,
				':ps_id' => 4));

		//Update5
		$query_5 = $db->prepare("
						UPDATE idk_projects_settings
						SET	ps_name = :ps_name
						WHERE ps_id = :ps_id");

		$query_5->execute(array(
				':ps_name' => $ps_id_5,
				':ps_id' => 5));


	//Add to LOGS
	$log_desc = "Snimio postavke faze projekta.";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: projects?page=settings&mess=1");

break;

case "edit_project_box_item":

	$pt_box = $_GET['pt_box'];
	$pt_id = $_GET['pt_id'];

	$query = $db->prepare("
					UPDATE idk_projects_tasks
					SET	pt_box = :pt_box
					WHERE pt_id = :pt_id");

	$query->execute(array(
					':pt_box' => $pt_box,
					':pt_id' => $pt_id));

break;

case "add_project_task":

	$pt_name = $_POST['pt_name'];
	$pt_desc = $_POST['pt_desc'];
	$pt_datetime = date('Y-m-d H:i:s');
	$pt_projectid = $_POST['pt_projectid'];
	$pt_employeeid = $_POST['pt_employeeid'];
	$pt_box = 1;
	$pt_sort = 99;

	$query = $db->prepare("
					INSERT INTO idk_projects_tasks
						(pt_name, pt_desc, pt_datetime, pt_projectid, pt_employeeid, pt_box, pt_sort)
					VALUES
						(:pt_name, :pt_desc, :pt_datetime, :pt_projectid, :pt_employeeid, :pt_box, :pt_sort)");

	$query->execute(array(
				':pt_name' => $pt_name,
				':pt_desc' => $pt_desc,
				':pt_datetime' => $pt_datetime,
				':pt_projectid' => $pt_projectid,
				':pt_employeeid' => $pt_employeeid,
				':pt_box' => $pt_box,
				':pt_sort' => $pt_sort));

	header("Location: projects?page=open&id=$pt_projectid");

break;

case "add_project_nalog":

	$pt_projectid = $_POST['pt_projectid'];
	$pt_nalogid = $_POST['nalog_id'];

	$query = $db->prepare("
							UPDATE idk_projects
							SET	project_nalogid = :project_nalogid
							WHERE project_id = :project_id");

			$query->execute(array(
							':project_nalogid' => $pt_nalogid,
							':project_id' => $pt_projectid));

	header("Location: projects?page=open&id=$pt_projectid&mess=10");

break;

case "sort_project_box_items":

	if(isset($_POST['order'])){

		$i = 1;

		foreach ($_POST['order'] as $value) {

			$query = $db->prepare("
							UPDATE idk_projects_tasks
							SET	pt_sort = :pt_sort
							WHERE pt_id = :pt_id");

			$query->execute(array(
							':pt_id' => $value,
							':pt_sort' => $i));
			$i++;
		}

	}

break;

case "get_progress":

	$project_id = $_GET['id'];

	//Total tasks
	$query_count = $db->prepare("
							SELECT COUNT(pt_id) AS total_tasks
							FROM idk_projects_tasks
							WHERE pt_projectid = :pt_projectid");

	$query_count->execute(Array(
				':pt_projectid' => $project_id));

	$row_count = $query_count->fetch();

	$total_tasks = $row_count['total_tasks'];

	//Total completed tasks
	$query_count_completed = $db->prepare("
									SELECT COUNT(pt_id) AS total_tasks_completed
									FROM idk_projects_tasks
									WHERE pt_projectid = :pt_projectid AND pt_box = :pt_box");

	$query_count_completed->execute(Array(
				':pt_projectid' => $project_id,
				':pt_box' => 5));

	$row_count_completed = $query_count_completed->fetch();

	$total_tasks_completed = $row_count_completed['total_tasks_completed'];

	if($total_tasks > 0){

		$percent = round(abs($total_tasks_completed / ($total_tasks / 100)));

	}else{
		$percent = 0;
	}

	echo $percent;


break;

case "edit_project_task_desc":

	$pt_id = $_POST['pt_id'];
	$pt_desc = $_POST['pt_desc'];

		$query = $db->prepare("
						UPDATE idk_projects_tasks
						SET	pt_desc = :pt_desc
						WHERE pt_id = :pt_id");

		$query->execute(array(
				':pt_desc' => $pt_desc,
				':pt_id' => $pt_id));

break;

case "get_task_desc":

	$pt_id = $_GET['id'];

	//Total tasks
	$query = $db->prepare("
						SELECT pt_desc
						FROM idk_projects_tasks
						WHERE pt_id = :pt_id");

	$query->execute(Array(
				':pt_id' => $pt_id));

	$row = $query->fetch();

	echo $pt_desc = $row['pt_desc'];

break;
//Module projects end

//Module employees report start
case "add_employees_report":

	$er_date = $_POST['er_date'];
	$er_date_link = date('d-m-Y', strtotime($er_date));
	$er_title = $_POST['er_title'];
	$er_timefrom = date("H:i:s", strtotime($_POST['er_timefrom']));
	$er_timeto = date("H:i:s", strtotime($_POST['er_timeto']));
	$er_nalogid = $_POST['er_nalogid'];
	$er_status = 1;

	$query = $db->prepare("
					INSERT INTO idk_employees_reports
						(er_date, er_title, er_timefrom, er_timeto, er_nalogid, er_employeeid, er_status)
					VALUES
						(:er_date, :er_title, :er_timefrom, :er_timeto, :er_nalogid, :er_employeeid, :er_status)");

	$query->execute(array(
				':er_date' => $er_date,
				':er_title' => $er_title,
				':er_timefrom' => $er_timefrom,
				':er_timeto' => $er_timeto,
				':er_nalogid' => $er_nalogid,
				':er_employeeid' => $logged_employee_id,
				':er_status' => $er_status));

	//Add to LOGS
	$log_desc = "Dodao novi izvještaj.";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: employees-reports?page=add&date=$er_date_link");

break;

case "edit_employees_report_item":

	$er_type = $_POST['er_type'];
	$er_id = $_POST['er_id'];
	$er_new_value = $_POST['er_new_value'];
	$er_date_link = date('d-m-Y', strtotime($er_date));
	switch($er_type)
	{
		case "er_timefrom":

			$query = $db->prepare("
					UPDATE idk_employees_reports
					SET er_timefrom = :er_timefrom
					WHERE er_id = :er_id");

			$query->execute(array(
				':er_id' => $er_id,
				':er_timefrom' => $er_new_value,
			));

		break;

		case "er_timeto":

			$query = $db->prepare("
					UPDATE idk_employees_reports
					SET er_timeto = :er_timeto
					WHERE er_id = :er_id");

			$query->execute(array(
				':er_id' => $er_id,
				':er_timeto' => $er_new_value,
			));

		break;

		case "er_title":

			$query = $db->prepare("
					UPDATE idk_employees_reports
					SET er_title = :er_title
					WHERE er_id = :er_id");

			$query->execute(array(
				':er_id' => $er_id,
				':er_title' => $er_new_value,
			));

		break;

		default:
			echo 'Invalid data!';
		break;
	}

	//Add to LOGS
	$log_desc = "Uredio stavku izvještaja sa ID-em: " + $er_id;
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));
break;
//Module employees report end


}
}

?>
