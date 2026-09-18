<?php

include("includes/functions.php");

$action="";

if(isset($_REQUEST["action"])) {
	$action = $_REQUEST["action"];

	switch ($action) {


		case "new_task":

			//Get Task Data
			$task_id = $_GET['id'];

			$query_tasks = $db->prepare("
									SELECT task_txt, task_assignedid, task_employeeid, assigned.employee_firstname AS assigned_firstname, assigned.employee_lastname AS assigned_lastname, employee_email
									FROM idk_tasks
									INNER JOIN idk_employees assigned ON idk_tasks.task_assignedid = assigned.employee_id
									WHERE task_id = :task_id");

			$query_tasks->execute(array(
							':task_id' => $task_id));

			$row_tasks = $query_tasks->fetch();

				$task_txt = $row_tasks['task_txt'];
				$assigned_firstname = $row_tasks['assigned_firstname'];
				$assigned_lastname = $row_tasks['assigned_lastname'];
				$mail_name = $assigned_firstname . ' ' . $assigned_lastname;
				$mail_email = $row_tasks['employee_email'];
				$task_url = "" . getSiteUrlr() . "tasks?page=open&id=" . $task_id . "";
				$mail_subject = 'Napomena: Imate novi zadatak';
				$mail_body = '
									<p>Zadatak: ' . $task_txt . '</p>
									<p>Detalji zadatka: ' . $task_url . '</p>
				';
				 $mail_altbody = 'Zadatak: ' . $task_txt . ' | Detalji zadatka: ' . $task_url . '';

				sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);

		break;


	}
}

?>
