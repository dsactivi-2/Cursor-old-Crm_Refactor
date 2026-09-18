<?php
include("includes/functions.php");
//include("includes/common.php");

$case="";

if(isset($_REQUEST["case"])) {
	$case = $_REQUEST["case"];

switch ($case)
{

case "getNumberOfNotifications":

	$query = $db->prepare("
					SELECT COUNT(notification_id) AS notification_total
					FROM idk_notifications
					WHERE notification_userid = :notification_userid AND notification_status = :notification_status AND notification_datetime <= NOW()");

	$query->execute(array(
			':notification_status' => 1,
			':notification_userid' => $logged_user_id));

	$row = $query->fetch();

	if($row['notification_total'] > 0){
		echo '<span>' . $row['notification_total'] . '</span>';
	}else{
		echo '';
	}

break;

case "getNotifications_20":

	$query = $db->prepare("
					SELECT notification_id, notification_title, notification_icon, notification_link, notification_status
					FROM idk_notifications
					WHERE notification_userid = :notification_userid AND notification_datetime <= NOW()
					ORDER BY notification_datetime DESC
					LIMIT 20");

	$query->execute(array(
			':notification_userid' => $logged_user_id));

	while($row = $query->fetch()){

		$notification_id = $row['notification_id'];
		$notification_title = $row['notification_title'];
		$notification_icon = $row['notification_icon'];
		$notification_link = $row['notification_link'];
		if($row['notification_status'] == 1){ $notification_status = 'class="idk_highlight_bg"'; }else{ $notification_status = ''; }

		echo '<li ' . $notification_status . '><a href="' . $notification_link . '&nid=' . $notification_id . '"><i class="fa fa-' . $notification_icon . '"></i></i> ' . $notification_title . '</a></li>';

	}

break;

case "getNumberOfMessages":

	$query = $db->prepare("
					SELECT COUNT(mu_id) AS messages_total
					FROM idk_messages_users
					WHERE mu_userid = :mu_userid AND mu_status = :mu_status");

	$query->execute(array(
			':mu_status' => 0,
			':mu_userid' => $logged_user_id));

	$row = $query->fetch();

	if($row['messages_total'] > 0){
		echo '<span>' . $row['messages_total'] . '</span>';
	}else{
		echo '';
	}

break;

case "getMessages_10":

	$inbox_query = $db->prepare("
							SELECT message_id, message_subject, message_datetime, user_fullname, user_image, mu_status
							FROM idk_messages
							INNER JOIN idk_messages_users ON idk_messages.message_id = idk_messages_users.mu_messageid
							INNER JOIN idk_users ON idk_messages.message_sentid = idk_users.user_id
							WHERE mu_userid = :mu_userid AND mu_status != :mu_status
							ORDER BY message_id DESC");

	$inbox_query->execute(array(
					':mu_userid' => $logged_user_id,
					':mu_status' => 2));

	while($inbox_row = $inbox_query->fetch()){

		$message_id = $inbox_row['message_id'];
		$employee_fullname = $inbox_row['user_fullname'];
		if($inbox_row['user_image'] == NULL){
			$user_image = "none.jpg";
		}else{
			$user_image = $inbox_row['user_image'];
		}
		$message_subject = $inbox_row['message_subject'];
		$message_datetime = $inbox_row['message_datetime'];
		$message_datetime_f = date('d.m.Y H:i', strtotime($inbox_row['message_datetime']));
		$mu_status = $inbox_row['mu_status'];

		if($mu_status == 0){
			$mess_status_style = 'class="idk_highlight_bg"';
		}else{
			$mess_status_style = '';
		}

		echo '<li ' . $mess_status_style . '>
			<a href="' . getSiteURLr() . 'messages?page=open&id=' . $message_id . '">
				<div class="pull-left"><img src="' . getSiteURLr() . 'files/users/' . $user_image . '" class="img-circle" alt="User Image"></div>
				<h4>' . $employee_fullname . '</h4>
				<p>' . $message_subject . '</p>
			</a>
		</li>';
	}

break;

case "getTaskInfo":

if (isset($_POST['id']) && !empty($_POST['id'])) {

	$pt_id = intval($_POST['id']);

	$query = $db->prepare("
						SELECT pt_name, pt_desc, pt_datetime, employee_firstname, employee_lastname, employee_id, pt_projectid
						FROM idk_projects_tasks
						LEFT JOIN idk_employees ON idk_projects_tasks.pt_employeeid = idk_employees.employee_id
						WHERE pt_id = :pt_id");

	$query->execute(array(
				':pt_id'=>$pt_id));

	$row = $query->fetch();

	$pt_name = $row['pt_name'];
	$pt_desc = $row['pt_desc'];
	$employee_id = $row['employee_id'];
	$employee_firstname = $row['employee_firstname'];
	$employee_lastname = $row['employee_lastname'];
	$pt_projectid = $row['pt_projectid'];
	$pt_datetime = date('d.m.Y H:i', strtotime($row['pt_datetime']));

	$array = array(
    	"pt_id" => $pt_id,
    	"pt_name" => $pt_name,
    	"pt_desc" => $pt_desc,
    	"employee_id" => $employee_id,
    	"employee_firstname" => $employee_firstname,
    	"employee_lastname" => $employee_lastname,
    	"pt_projectid" => $pt_projectid,
    	"pt_datetime" => $pt_datetime
  	);

	echo json_encode($array);
	exit;
}

break;


}
}

?>
