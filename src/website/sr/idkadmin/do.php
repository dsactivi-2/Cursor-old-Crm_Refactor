<?php
include("includes/functions.php");

$form="";

if(isset($_REQUEST["form"])) {
	$form = $_REQUEST["form"];

switch ($form)
{

case "login":
	$login_email = $_POST['login_email'];
	$login_password = $_POST['login_password'];

	if(isset($_POST['login_rm'])){
		$login_rm = $_POST['login_rm'];
	}else{
		$login_rm = "off";
	}

	$login_query = $db->prepare("
							SELECT user_id, user_email, user_pass, user_key
							FROM idk_users
							WHERE user_email = :user_email AND user_status != :user_status");

	$login_query->execute(array(
					':user_email' => $login_email,
					':user_status' => 0));

	$user = $login_query->fetch();

	if(md5($login_password) == $user['user_pass']){

		if($login_rm == "on") {
			$month = time() + 60 * 60 * 24 * 30;
			setcookie('idk_cms_session', $user['user_key'], $month);
		}else{
			$hour = time() + 60 * 60;
			setcookie('idk_cms_session', $user['user_key'], $hour);
		}

		//Add to LOGS
		$log_userid = $user['user_id'];
		$log_desc = "Korisnik se prijavio";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_userid, log_desc, log_date)
						VALUES
							(:log_userid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_userid' => $log_userid,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: " . getSiteURLr() . "");

	}else{

		header("Location: login/poruka/2");

	}

break;

case "logout":

	//Add to LOGS
	$log_desc = "Zaposlenik se odjavio";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	unset($_COOKIE['idk_cms_session']);
	setcookie('idk_cms_session', '', time() - 60 * 60 * 24 * 30);

	header("Location: login/poruka/1");

break;

case "add_employees":

	$user_email = $_POST['user_email'];

	//Check if user exist
	$check_query = $db->prepare("
							SELECT user_email
							FROM idk_users
							WHERE user_email = :user_email");

	$check_query->execute(array(
					':user_email' => $user_email));

	$number_of_rows = $check_query->rowCount();

	if($number_of_rows == 0){

		$user_fullname = $_POST['user_fullname'];
		$user_pass = MD5($_POST['user_pass']);
		$user_key = MD5(rand());
		$user_color = $_POST['user_color'];
		$user_status = 1;
		$user_dor = date('Y-m-d H:i:s');

		//Upload and save user_image
		if($_FILES['user_image']['size'] !== 0) {
			$user_image = $_FILES['user_image'];

			//File properties
			$file_name = $user_image['name'];
			$file_tmp = $user_image['tmp_name'];
			$file_size = $user_image['size'];
			$file_error = $user_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$employee_image_final = uniqid() . '.' . $file_ext;
				$file_destination = 'files/users/' . $employee_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/users/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $employee_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $employee_image_final);
					} else if (preg_match('/[.](png)$/', $employee_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $employee_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $employee_image_final);

				}
			}
		}else{
			$employee_image_final = NULL;
		}

		//Add user to db
		$query = $db->prepare("
						INSERT INTO idk_users
							(user_fullname, user_email, user_pass, user_key, user_color, user_image, user_status, user_dor)
						VALUES
							(:user_fullname, :user_email, :user_pass, :user_key, :user_color, :user_image, :user_status, :user_dor)");

		$query->execute(array(
					':user_fullname' => $user_fullname,
					':user_email' => $user_email,
					':user_pass' => $user_pass,
					':user_key' => $user_key,
					':user_color' => $user_color,
					':user_image' => $employee_image_final,
					':user_status' => $user_status,
					':user_dor' => $user_dor));

		//Add to LOGS
		$log_desc = "Dodao novog administratora: " . $user_fullname . " ";
		$log_date = date('Y-m-d H:i:s');

			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_userid, log_desc, log_date)
								VALUES
								(:log_userid, :log_desc, :log_date)");
			$log_query->execute(array(
							':log_userid' => $logged_user_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));

		header("Location: employees?page=list&mess=1");

	}else{
		header("Location: employees?page=list&mess=2");
	}

break;

case "edit_employees":

	if( !empty($_POST['user_pass']) ) {

		$user_id = $_POST['user_id'];
		$user_fullname = $_POST['user_fullname'];
		$user_email = $_POST['user_email'];
		$user_pass = MD5($_POST['user_pass']);
		$user_color = $_POST['user_color'];

		//Upload and save user_image
		if($_FILES['user_image']['size'] !== 0) {

			//Delete old image
			$del_employee_img_query = $db->prepare("
										SELECT user_image
										FROM idk_users
										WHERE user_id = :user_id");

			$del_employee_img_query->execute(array(
									':user_id' => $user_id));

			$del_employee_img = $del_employee_img_query->fetch();

				$user_image = $del_employee_img['user_image'];

			if($user_image == "" OR $user_image == NULL){}else{
				unlink("files/users/" . $user_image);
			}

			$user_image = $_FILES['user_image'];

			//File properties
			$file_name = $user_image['name'];
			$file_tmp = $user_image['tmp_name'];
			$file_size = $user_image['size'];
			$file_error = $user_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$employee_image_final = uniqid() . '.' . $file_ext;
				$file_destination = 'files/users/' . $employee_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/users/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $employee_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $employee_image_final);
					} else if (preg_match('/[.](png)$/', $employee_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $employee_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $employee_image_final);

				}
			}
		}else{
			$employee_image_final = $_POST['user_image_url'];
		}

			$query = $db->prepare("
							UPDATE idk_users
							SET	user_fullname = :user_fullname, user_email = :user_email, user_pass = :user_pass, user_color = :user_color, user_image = :user_image
							WHERE user_id = :user_id");

			$query->execute(array(
					':user_fullname' => $user_fullname,
					':user_email' => $user_email,
					':user_pass' => $user_pass,
					':user_color' => $user_color,
					':user_image' => $employee_image_final,
					':user_id' => $user_id));

		//Add to LOGS
		$log_desc = "Uredio profil administratora: " . $user_fullname . " ";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_user_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: employees?page=list&mess=3");

	}else{

		$user_id = $_POST['user_id'];
		$user_fullname = $_POST['user_fullname'];
		$user_email = $_POST['user_email'];
		$user_color = $_POST['user_color'];

		//Upload and save user_image
		if($_FILES['user_image']['size'] !== 0) {

			//Delete old image
			$del_employee_img_query = $db->prepare("
										SELECT user_image
										FROM idk_users
										WHERE user_id = :user_id");

			$del_employee_img_query->execute(array(
									':user_id' => $user_id));

			$del_employee_img = $del_employee_img_query->fetch();

				$user_image = $del_employee_img['user_image'];

			if($user_image == "" OR $user_image == NULL){}else{
				unlink("files/users/" . $user_image);
			}

			$user_image = $_FILES['user_image'];

			//File properties
			$file_name = $user_image['name'];
			$file_tmp = $user_image['tmp_name'];
			$file_size = $user_image['size'];
			$file_error = $user_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$employee_image_final = uniqid() . '.' . $file_ext;
				$file_destination = 'files/users/' . $employee_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/users/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $employee_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $employee_image_final);
					} else if (preg_match('/[.](png)$/', $employee_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $employee_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $employee_image_final);

				}
			}
		}else{
			$employee_image_final = $_POST['user_image_url'];
		}

			$query = $db->prepare("
							UPDATE idk_users
							SET	user_fullname = :user_fullname, user_email = :user_email, user_color = :user_color, user_image = :user_image
							WHERE user_id = :user_id");

			$query->execute(array(
					':user_fullname' => $user_fullname,
					':user_email' => $user_email,
					':user_color' => $user_color,
					':user_image' => $employee_image_final,
					':user_id' => $user_id));

		//Add to LOGS
		$log_desc = "Uredio profil administratora: " . $user_fullname . "";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_user_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: employees?page=list&mess=3");
	}

break;

case "edit_profile":

	if( !empty($_POST['user_pass']) ) {

		$user_id = $_POST['user_id'];
		$user_fullname = $_POST['user_fullname'];
		$user_email = $_POST['user_email'];
		$user_pass = $_POST['user_pass'];
		$user_color = $_POST['user_color'];

		//Upload and save user_image
		if($_FILES['user_image']['size'] !== 0) {

			//Delete old image
			$del_employee_img_query = $db->prepare("
										SELECT user_image
										FROM idk_users
										WHERE user_id = :user_id");

			$del_employee_img_query->execute(array(
									':user_id' => $user_id));

			$del_employee_img = $del_employee_img_query->fetch();

				$user_image = $del_employee_img['user_image'];

			if($user_image == "" OR $user_image == NULL){}else{
				unlink("files/users/" . $user_image);
			}

			$user_image = $_FILES['user_image'];

			//File properties
			$file_name = $user_image['name'];
			$file_tmp = $user_image['tmp_name'];
			$file_size = $user_image['size'];
			$file_error = $user_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$employee_image_final = uniqid() . '.' . $file_ext;
				$file_destination = "files/users/" . $employee_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/users/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $employee_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $employee_image_final);
					} else if (preg_match('/[.](png)$/', $employee_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $employee_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $employee_image_final);

				}
			}
		}else{
			$employee_image_final = $_POST['user_image_url'];
		}

			$query = $db->prepare("
							UPDATE idk_users
							SET	user_fullname = :user_fullname, user_email = :user_email, user_pass = :user_pass, user_color = :user_color, user_image = :user_image
							WHERE user_id = :user_id");

			$query->execute(array(
					':user_fullname' => $user_fullname,
					':user_email' => $user_email,
					':user_pass' => $user_pass,
					':user_color' => $user_color,
					':user_image' => $employee_image_final,
					':user_id' => $user_id));

		//Add to LOGS
		$log_desc = "Uredio osobni profil: " . $user_fullname . " ";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_user_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: employees?page=edit_profile&mess=1");

	}else{

		$user_id = $_POST['user_id'];
		$user_fullname = $_POST['user_fullname'];
		$user_email = $_POST['user_email'];
		$user_color = $_POST['user_color'];

		//Upload and save user_image
		if($_FILES['user_image']['size'] !== 0) {

			//Delete old image
			$del_employee_img_query = $db->prepare("
										SELECT user_image
										FROM idk_users
										WHERE user_id = :user_id");

			$del_employee_img_query->execute(array(
									':user_id' => $user_id));

			$del_employee_img = $del_employee_img_query->fetch();

				$user_image = $del_employee_img['user_image'];

			if($user_image == "" OR $user_image == NULL){}else{
				unlink("files/users/" . $user_image);
			}

			$user_image = $_FILES['user_image'];

			//File properties
			$file_name = $user_image['name'];
			$file_tmp = $user_image['tmp_name'];
			$file_size = $user_image['size'];
			$file_error = $user_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$employee_image_final = uniqid() . '.' . $file_ext;
				$file_destination = "files/users/" . $employee_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/users/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $employee_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $employee_image_final);
					} else if (preg_match('/[.](png)$/', $employee_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $employee_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $employee_image_final);

				}
			}
		}else{
			$employee_image_final = $_POST['user_image_url'];
		}

			$query = $db->prepare("
							UPDATE idk_users
							SET	user_fullname = :user_fullname, user_email = :user_email, user_color = :user_color, user_image = :user_image
							WHERE user_id = :user_id");

			$query->execute(array(
					':user_fullname' => $user_fullname,
					':user_email' => $user_email,
					':user_color' => $user_color,
					':user_image' => $employee_image_final,
					':user_id' => $user_id));

		//Add to LOGS
		$log_desc = "Uredio osobni profil: " . $user_fullname . "";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_user_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: employees?page=edit_profile&mess=1");
	}

break;

case "notifications_mark_read":

	$notification_userid = (int)$_POST['id'];

	$query = $db->prepare("
					UPDATE idk_notifications
					SET	notification_status = :notification_status
					WHERE notification_userid = :notification_userid AND notification_datetime <= NOW()");

	$query->execute(array(
				':notification_status' => 2,
				':notification_userid' => $notification_userid));

break;

case "send_message":

	$message_subject = $_POST['message_subject'];
	$message_text = $_POST['message_text'];
	$message_sentid = $_POST['message_sentid'];
	$message_datetime = date('Y-m-d H:i:s');

	$query_message = $db->prepare("
					INSERT INTO idk_messages
						(message_subject, message_text, message_sentid, message_status, message_datetime)
					VALUES
						(:message_subject, :message_text, :message_sentid, :message_status, :message_datetime)");

	$query_message->execute(array(
					':message_subject' => $message_subject,
					':message_text' => $message_text,
					':message_sentid' => $message_sentid,
					':message_status' => 1,
					':message_datetime' => $message_datetime));

	//Get last ID
	$mu_messageid = $db->lastInsertId();

	//Who receive a email
	foreach ($_POST['mu_userid'] as $mess_to_array){

		$query_send = $db->prepare("
						INSERT INTO idk_messages_users
							(mu_messageid, mu_userid, mu_status)
						VALUES
							(:mu_messageid, :mu_userid, :mu_status)");

		$query_send->execute(array(
						':mu_messageid' => $mu_messageid,
						':mu_userid' => $mess_to_array,
						':mu_status' => 0));

		//Get User Info
		$user_query = $db->prepare("
								SELECT user_fullname, user_email
								FROM idk_users
								WHERE user_id = :user_id");

		$user_query->execute(array(
						':user_id' => $mess_to_array));

		$user = $user_query->fetch();

		$user_fullname = $user['user_fullname'];
		$user_email = $user['user_email'];

		//Send email to user
		$mail_email = $user_email;
		$mail_name = $user_fullname;
		$mail_subject = "Imate novu poruku - IDK CRM";
		$mail_url = "" . getSiteUrlr() . "/messages?page=open&id=" . $mu_messageid . "";
		$mail_body = "
						<p>Imate novu poruku: " . $message_text . "</p>
						<p>Detalji poruke: " . $mail_url . "</p>
		";
		$mail_altbody = "
						<p>Imate novu poruku: " . $message_text . "</p>
						<p>Detalji poruke: " . $mail_url . "</p>
		";

		sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);

		//Add to LOGS
		$log_desc = "Poslao poruku korisniku: " . $user_fullname . "";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_user_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));
	}

	header("Location: messages?page=list&mess=1");

break;


case "add_support_ticket":

		$support_client = $_POST['support_client'];
		$support_email = $_POST['support_email'];
		$support_txt = $_POST['support_txt'];
		$mail_email = "projects@idkstudio.com";
		$mail_name = "Projects IDK Studio";
		$mail_subject = "IDK CRM Online podrška";

		$mail_body = "
						<p>Klijent: $support_client</p>
						<p>Email: $support_email</p>
						<p>Poruka: $support_txt</p>
		";

		$mail_altbody = "
						<p>Klijent: $support_client</p>
						<p>Email: $support_email</p>
						<p>Poruka: $support_txt</p>
		";


		sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);

		header("Location: index?mess=1");

break;

case "add_todo":
	$todo_title = $_POST['todo_title'];
	$todo_datetime = date('Y-m-d H:i:s');
	$todo_userid = $_POST['todo_userid'];
	$todo_status = 1;

	$query = $db->prepare("
					INSERT INTO idk_todo
						(todo_title, todo_datetime, todo_userid, todo_status)
					VALUES
						(:todo_title, :todo_datetime, :todo_userid, :todo_status)");

	$query->execute(array(
				':todo_title' => $todo_title,
				':todo_datetime' => $todo_datetime,
				':todo_userid' => $todo_userid,
				':todo_status' => $todo_status));

	header("Location: index");
break;

case "edit_todo":
	$todo_id = $_POST['todo_id'];
	if(isset($_POST['todo_status'])){ $todo_status = 2; }else{ $todo_status = 1; }

	$query = $db->prepare("
					UPDATE idk_todo
					SET	todo_status = :todo_status
					WHERE todo_id = :todo_id");

	$query->execute(array(
			':todo_status' => $todo_status,
			':todo_id' => $todo_id));

	header("Location: index");
break;

case "add_content":

	//Add content to db
	$content_sub = $_POST['content_sub'];
	$content_sort = $_POST['content_sort'];
	if(isset($_POST['content_comment'])){ $content_comment = $_POST['content_comment']; }else{ $content_comment = NULL; }
	if(isset($_POST['content_share'])){ $content_share = $_POST['content_share']; }else{ $content_share = NULL; }
	$content_status = $_POST['content_status'];
	$content_count = 0;
	$content_datetime = date('Y-m-d H:i:s');

	$query = $db->prepare("
					INSERT INTO idk_content
						(content_sub, content_sort, content_comment, content_share, content_status, content_count, content_datetime)
					VALUES
						(:content_sub, :content_sort, :content_comment, :content_share, :content_status, :content_count, :content_datetime)");

	$query->execute(array(
				':content_sub' => $content_sub,
				':content_sort' => $content_sort,
				':content_comment' => $content_comment,
				':content_share' => $content_share,
				':content_status' => $content_status,
				':content_count' => $content_count,
				':content_datetime' => $content_datetime));

	$update_id = $db->lastInsertId();

	$content_lang_name = $_POST['content_lang_name'];
	$content_lang_content = $_POST['content_lang_content'];

	//Upload and save content_lang_img
	if($_FILES['content_lang_img']['size'] !== 0) {
		$content_lang_img = $_FILES['content_lang_img'];

		//File properties
		$file_name = $content_lang_img['name'];
		$file_tmp = $content_lang_img['tmp_name'];
		$file_size = $content_lang_img['size'];
		$file_error = $content_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/content/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/content/";
					$path_to_thumbs_directory = "../files/content/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/content/";
					$path_to_thumbs_directory = "../files/content/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = NULL;
	}

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id, lang_content
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$content_lang_langid = $lang_row['lang_id'];
		$lang_content = $lang_row['lang_content'];

		//SEO
		if(isset($_POST['content_seo'])){

			$content_lang_seoname = $_POST['content_lang_seoname'];

			$slug_name = $_POST['content_lang_url'];
			$content_lang_url = "" . $lang_content . "/" . $slug_name . "/" . $update_id . "";

			$content_lang_seodesc = $_POST['content_lang_seodesc'];

			$content_lang_seokeywords = $_POST['content_lang_seokeywords'];

		}else{

			$content_lang_seoname = $content_lang_name;

			$slug_name = create_slug($content_lang_name);
			$content_lang_url = "" . $lang_content . "/" . $slug_name . "/" . $update_id . "";

			$content_lang_content_strip = strip_tags($content_lang_content);
			if(strlen($content_lang_content_strip) > 170){
				$pos = strpos($content_lang_content_strip, ' ', 160);
				$content_lang_seodesc = substr($content_lang_content_strip, 0, $pos) . " ...";
			}else{
				$content_lang_seodesc = $content_lang_content_strip;
			}

			$content_lang_seokeywords = create_keywords($content_lang_name);
		}

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_content_lang
							(content_lang_langid, content_lang_postid, content_lang_name, content_lang_url, content_lang_content, content_lang_img, content_lang_seoname, content_lang_seodesc, content_lang_seokeywords)
						VALUES
							(:content_lang_langid, :content_lang_postid, :content_lang_name, :content_lang_url, :content_lang_content, :content_lang_img, :content_lang_seoname, :content_lang_seodesc, :content_lang_seokeywords)");

		$query->execute(array(
					':content_lang_langid' => $content_lang_langid,
					':content_lang_postid' => $update_id,
					':content_lang_name' => $content_lang_name,
					':content_lang_url' => $content_lang_url,
					':content_lang_content' => $content_lang_content,
					':content_lang_img' => $file_name_new,
					':content_lang_seoname' => $content_lang_seoname,
					':content_lang_seodesc' => $content_lang_seodesc,
					':content_lang_seokeywords' => $content_lang_seokeywords));
	}

	//Add to navigation
	if(isset($_POST['content_nav'])){
		if(isset($_POST['nav_target'])){ $nav_target = $_POST['nav_target']; }else{ $nav_target = NULL; }
		$nav_sub = $_POST['nav_sub'];
		$nav_sort = $_POST['nav_sort'];

		$query = $db->prepare("
						INSERT INTO idk_navigation
							(nav_target, nav_contentid, nav_sub, nav_sort)
						VALUES
							(:nav_target, :nav_contentid, :nav_sub, :nav_sort)");

		$query->execute(array(
					':nav_target' => $nav_target,
					':nav_contentid' => $update_id,
					':nav_sub' => $nav_sub,
					':nav_sort' => $nav_sort));

		$update_id = $db->lastInsertId();

		$nav_lang_name = $_POST['nav_lang_name'];

		//add for all languages
		$lang_query = $db->prepare("
							SELECT lang_id
							FROM idk_langs");

		$lang_query->execute();

		while($lang_row = $lang_query->fetch()) {

			$nav_lang_langid = $lang_row['lang_id'];

			//Add to db
			$query = $db->prepare("
							INSERT INTO idk_navigation_lang
								(nav_lang_langid, nav_lang_navid, nav_lang_name)
							VALUES
								(:nav_lang_langid, :nav_lang_navid, :nav_lang_name)");

			$query->execute(array(
						':nav_lang_langid' => $nav_lang_langid,
						':nav_lang_navid' => $update_id,
						':nav_lang_name' => $nav_lang_name));
		}
	}

	//Upload photos to gallery
	for($i=0; $i<count($_FILES['cphotos_file']['name']); $i++) {

		$tmpFilePath = $_FILES['cphotos_file']['tmp_name'][$i];
		if ($tmpFilePath != ""){

			$filename_frompc = $_FILES['cphotos_file']['name'][$i];

			$file_ext = explode('.', $filename_frompc);
			$file_ext = strtolower(end($file_ext));

			$filename = uniqid() . "." . $file_ext;

			$newFilePath = "../files/content-photos/" . $filename;

			if(move_uploaded_file($tmpFilePath, $newFilePath)) {

						$query_photos = $db->prepare("
												INSERT INTO idk_content_photos
													(cphotos_contentid, cphotos_file, cphotos_sort)
												VALUES
													(:cphotos_contentid, :cphotos_file, :cphotos_sort)");

						$query_photos->execute(array(
										':cphotos_contentid' => $update_id,
										':cphotos_file' => $filename,
										':cphotos_sort' => 1));

						$path_to_image_directory = "../files/content-photos/";
						$path_to_thumbs_directory = "../files/content-photos/";
						$final_width_of_image = 1400;

						if(preg_match('/[.](jpg)$/', $filename)) {
							$im = imagecreatefromjpeg($path_to_image_directory . $filename);
						} else if (preg_match('/[.](gif)$/', $filename)) {
							$im = imagecreatefromgif($path_to_image_directory . $filename);
						} else if (preg_match('/[.](png)$/', $filename)) {
							$im = imagecreatefrompng($path_to_image_directory . $filename);
						}

						$ox = imagesx($im);
						$oy = imagesy($im);

						$nx = $final_width_of_image;
						$ny = floor($oy * ($final_width_of_image / $ox));

						$nm = imagecreatetruecolor($nx, $ny);

						imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

						if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
						}

						imagejpeg($nm, $path_to_thumbs_directory . $filename);


						//Thumbs
						$path_to_image_directory = "../files/content-photos/";
						$path_to_thumbs_directory = "../files/content-photos/thumbs/";
						$final_width_of_image = 400;

						if(preg_match('/[.](jpg)$/', $filename)) {
							$im = imagecreatefromjpeg($path_to_image_directory . $filename);
						} else if (preg_match('/[.](gif)$/', $filename)) {
							$im = imagecreatefromgif($path_to_image_directory . $filename);
						} else if (preg_match('/[.](png)$/', $filename)) {
							$im = imagecreatefrompng($path_to_image_directory . $filename);
						}

						$ox = imagesx($im);
						$oy = imagesy($im);

						$nx = $final_width_of_image;
						$ny = floor($oy * ($final_width_of_image / $ox));

						$nm = imagecreatetruecolor($nx, $ny);

						imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

						if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}
						imagejpeg($nm, $path_to_thumbs_directory . $filename);
			}
		}
	}

	//Add to LOGS
	$log_desc = "Dodao novi sadržaj: " . $content_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: content?page=list&mess=1");

break;

case "edit_content":

	//Add content to db
	$content_id = $_POST['content_id'];
	$content_sub = $_POST['content_sub'];
	$content_sort = $_POST['content_sort'];
	if(isset($_POST['content_comment'])){ $content_comment = $_POST['content_comment']; }else{ $content_comment = NULL; }
	if(isset($_POST['content_share'])){ $content_share = $_POST['content_share']; }else{ $content_share = NULL; }
	$content_status = $_POST['content_status'];

	$query = $db->prepare("
					UPDATE idk_content
					SET	content_sub = :content_sub, content_sort = :content_sort, content_comment = :content_comment, content_share = :content_share, content_status = :content_status
					WHERE content_id = :content_id");

	$query->execute(array(
				':content_sub' => $content_sub,
				':content_sort' => $content_sort,
				':content_comment' => $content_comment,
				':content_share' => $content_share,
				':content_status' => $content_status,
				':content_id' => $content_id));


	$content_lang_langid = $_POST['content_lang_langid'];
	$content_lang_name = $_POST['content_lang_name'];
	$content_lang_content = $_POST['content_lang_content'];

	//Upload and save content_lang_img
	if($_FILES['content_lang_img']['size'] !== 0) {

		$content_lang_img = $_FILES['content_lang_img'];

		//File properties
		$file_name = $content_lang_img['name'];
		$file_tmp = $content_lang_img['tmp_name'];
		$file_size = $content_lang_img['size'];
		$file_error = $content_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/content/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/content/";
					$path_to_thumbs_directory = "../files/content/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/content/";
					$path_to_thumbs_directory = "../files/content/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = $_POST['content_lang_img_input'];
	}

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_content
						FROM idk_langs
						WHERE lang_id = :lang_id");

	$lang_query->execute(array(
					':lang_id' => $content_lang_langid));

	$lang_row = $lang_query->fetch();

		$lang_content = $lang_row['lang_content'];

	//SEO
	if(isset($_POST['content_seo'])){

		$content_lang_seoname = $_POST['content_lang_seoname'];

		$slug_name = $_POST['content_lang_url'];
		$content_lang_url = "" . $lang_content . "/" . $slug_name . "/" . $content_id . "";

		$content_lang_seodesc = $_POST['content_lang_seodesc'];

		$content_lang_seokeywords = $_POST['content_lang_seokeywords'];

	}else{

		$content_lang_seoname = $content_lang_name;

		$slug_name = create_slug($content_lang_name);
		$content_lang_url = "" . $lang_content . "/" . $slug_name . "/" . $content_id . "";

		$content_lang_content_strip = strip_tags($content_lang_content);
		if(strlen($content_lang_content_strip) > 170){
			$pos = strpos($content_lang_content_strip, ' ', 160);
			$content_lang_seodesc = substr($content_lang_content_strip, 0, $pos) . " ...";
		}else{
			$content_lang_seodesc = $content_lang_content_strip;
		}

		$content_lang_seokeywords = create_keywords($content_lang_name);

	}

	//Update db
	$query = $db->prepare("
					UPDATE idk_content_lang
					SET	content_lang_name = :content_lang_name, content_lang_url = :content_lang_url, content_lang_content = :content_lang_content, content_lang_img = :content_lang_img, content_lang_seoname = :content_lang_seoname, content_lang_seodesc = :content_lang_seodesc, content_lang_seokeywords = :content_lang_seokeywords
					WHERE content_lang_langid = :content_lang_langid AND content_lang_postid = :content_lang_postid");

	$query->execute(array(
				':content_lang_name' => $content_lang_name,
				':content_lang_url' => $content_lang_url,
				':content_lang_content' => $content_lang_content,
				':content_lang_img' => $file_name_new,
				':content_lang_seoname' => $content_lang_seoname,
				':content_lang_seodesc' => $content_lang_seodesc,
				':content_lang_seokeywords' => $content_lang_seokeywords,
				':content_lang_langid' => $content_lang_langid,
				':content_lang_postid' => $content_id));

	//Upload photos to gallery
	for($i=0; $i<count($_FILES['cphotos_file']['name']); $i++) {

		$tmpFilePath = $_FILES['cphotos_file']['tmp_name'][$i];
		if ($tmpFilePath != ""){

			$filename_frompc = $_FILES['cphotos_file']['name'][$i];

			$file_ext = explode('.', $filename_frompc);
			$file_ext = strtolower(end($file_ext));

			$filename = uniqid() . "." . $file_ext;

			$newFilePath = "../files/content-photos/" . $filename;

			if(move_uploaded_file($tmpFilePath, $newFilePath)) {

						$query_photos = $db->prepare("
												INSERT INTO idk_content_photos
													(cphotos_contentid, cphotos_file, cphotos_sort)
												VALUES
													(:cphotos_contentid, :cphotos_file, :cphotos_sort)");

						$query_photos->execute(array(
										':cphotos_contentid' => $content_id,
										':cphotos_file' => $filename,
										':cphotos_sort' => 1));

						$path_to_image_directory = "../files/content-photos/";
						$path_to_thumbs_directory = "../files/content-photos/";
						$final_width_of_image = 1400;

						if(preg_match('/[.](jpg)$/', $filename)) {
							$im = imagecreatefromjpeg($path_to_image_directory . $filename);
						} else if (preg_match('/[.](gif)$/', $filename)) {
							$im = imagecreatefromgif($path_to_image_directory . $filename);
						} else if (preg_match('/[.](png)$/', $filename)) {
							$im = imagecreatefrompng($path_to_image_directory . $filename);
						}

						$ox = imagesx($im);
						$oy = imagesy($im);

						$nx = $final_width_of_image;
						$ny = floor($oy * ($final_width_of_image / $ox));

						$nm = imagecreatetruecolor($nx, $ny);

						imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

						if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
						}

						imagejpeg($nm, $path_to_thumbs_directory . $filename);


						//Thumbs
						$path_to_image_directory = "../files/content-photos/";
						$path_to_thumbs_directory = "../files/content-photos/thumbs/";
						$final_width_of_image = 400;

						if(preg_match('/[.](jpg)$/', $filename)) {
							$im = imagecreatefromjpeg($path_to_image_directory . $filename);
						} else if (preg_match('/[.](gif)$/', $filename)) {
							$im = imagecreatefromgif($path_to_image_directory . $filename);
						} else if (preg_match('/[.](png)$/', $filename)) {
							$im = imagecreatefrompng($path_to_image_directory . $filename);
						}

						$ox = imagesx($im);
						$oy = imagesy($im);

						$nx = $final_width_of_image;
						$ny = floor($oy * ($final_width_of_image / $ox));

						$nm = imagecreatetruecolor($nx, $ny);

						imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

						if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}
						imagejpeg($nm, $path_to_thumbs_directory . $filename);
			}
		}
	}

	//Add to LOGS
	$log_desc = "Uredio sadržaj: " . $content_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: content?page=list&mess=2");

break;

case "del_content_photo":

	$cphotos_id = $_GET['id'];

	//Delete image from folders
	$photo_open_query = $db->prepare("
								SELECT cphotos_file
								FROM idk_content_photos
								WHERE cphotos_id = :cphotos_id");

	$photo_open_query->execute(array(
							':cphotos_id' => $cphotos_id));

	$photo_open = $photo_open_query->fetch();

		$cphotos_file = $photo_open['cphotos_file'];

	if($cphotos_file == ""){}else{
		unlink("../files/content-photos/" . $cphotos_file);
		unlink("../files/content-photos/thumbs/" . $cphotos_file);
	}

	//Delete content
	$photo_del_query = $db->prepare("
								DELETE FROM idk_content_photos
								WHERE cphotos_id = :cphotos_id");

	$photo_del_query->execute(array(
						':cphotos_id' => $cphotos_id));

	header("Location: javascript: history.back()");

break;

case "add_partner":

	$partner_name = $_POST['partner_name'];

	if($_FILES['partner_logo']['size'] !== 0) {
		$partner_logo = $_FILES['partner_logo'];

		//File properties
		$file_name = $partner_logo['name'];
		$file_tmp = $partner_logo['tmp_name'];
		$file_size = $partner_logo['size'];
		$file_error = $partner_logo['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/partners/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {}
			}
		}
	}else{
		$file_name_new = NULL;
	}

	$partner_link = $_POST['partner_link'];
	$partner_sort = $_POST['partner_sort'];

	$query = $db->prepare("
					INSERT INTO idk_partners
						(partner_name, partner_logo, partner_link, partner_sort)
					VALUES
						(:partner_name, :partner_logo, :partner_link, :partner_sort)");

	$query->execute(array(
				':partner_name' => $partner_name,
				':partner_logo' => $file_name_new,
				':partner_link' => $partner_link,
				':partner_sort' => $partner_sort));

	//Add to LOGS
	$log_desc = "Dodao novog partnera: " . $partner_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: partners?page=list&mess=1");

break;

case "edit_partner":

	//Add content to db
	$partner_id = $_POST['partner_id'];
	$partner_name = $_POST['partner_name'];

	//Upload and save partner_logo
	if($_FILES['partner_logo']['size'] !== 0) {

		$partner_logo = $_FILES['partner_logo'];

		//File properties
		$file_name = $partner_logo['name'];
		$file_tmp = $partner_logo['tmp_name'];
		$file_size = $partner_logo['size'];
		$file_error = $partner_logo['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/partners/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {}
			}
		}
	}else{
		$file_name_new = $_POST['partner_logo_input'];
	}

	$partner_link = $_POST['partner_link'];
	$partner_sort = $_POST['partner_sort'];

	$query = $db->prepare("
					UPDATE idk_partners
					SET	partner_name = :partner_name, partner_logo = :partner_logo, partner_link = :partner_link, partner_sort = :partner_sort
					WHERE partner_id = :partner_id");

	$query->execute(array(
				':partner_name' => $partner_name,
				':partner_logo' => $file_name_new,
				':partner_link' => $partner_link,
				':partner_sort' => $partner_sort,
				':partner_id' => $partner_id));

	//Add to LOGS
	$log_desc = "Uredio profil partnera: " . $partner_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: partners?page=list&mess=2");

break;

case "add_postcat":

	$postcat_count = 0;
	$postcat_status = $_POST['postcat_status'];

	//Upload and save postcat_lang_img
	if($_FILES['postcat_lang_img']['size'] !== 0) {
		$postcat_lang_img = $_FILES['postcat_lang_img'];

		//File properties
		$file_name = $postcat_lang_img['name'];
		$file_tmp = $postcat_lang_img['tmp_name'];
		$file_size = $postcat_lang_img['size'];
		$file_error = $postcat_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/posts-cat/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/posts-cat/";
					$path_to_thumbs_directory = "../files/posts-cat/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/posts-cat/";
					$path_to_thumbs_directory = "../files/posts-cat/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = NULL;
	}

	$query = $db->prepare("
					INSERT INTO idk_postcat
						(postcat_count, postcat_status)
					VALUES
						(:postcat_count, :postcat_status)");

	$query->execute(array(
				':postcat_count' => $postcat_count,
				':postcat_status' => $postcat_status));

	$update_id = $db->lastInsertId();

	$postcat_lang_name = $_POST['postcat_lang_name'];

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id, lang_postcat
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$postcat_lang_langid = $lang_row['lang_id'];
		$lang_postcat = $lang_row['lang_postcat'];

		$postcat_url = create_slug($postcat_lang_name);
		$postcat_lang_url = "" . $lang_postcat . "/" . $postcat_url . "/" . $update_id . "";

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_postcat_lang
							(postcat_lang_langid, postcat_lang_postcatid, postcat_lang_name, postcat_lang_img, postcat_lang_url)
						VALUES
							(:postcat_lang_langid, :postcat_lang_postcatid, :postcat_lang_name, :postcat_lang_img, :postcat_lang_url)");

		$query->execute(array(
					':postcat_lang_langid' => $postcat_lang_langid,
					':postcat_lang_postcatid' => $update_id,
					':postcat_lang_name' => $postcat_lang_name,
					':postcat_lang_img' => $file_name_new,
					':postcat_lang_url' => $postcat_lang_url));
	}

	//Add to LOGS
	$log_desc = "Dodao novu kategoriju za novosti: " . $postcat_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: post-category?page=list&mess=1");

break;

case "edit_postcat":

	//Add content to db
	$postcat_id = $_POST['postcat_id'];
	$postcat_status = $_POST['postcat_status'];

	//Upload and save postcat_lang_img
	if($_FILES['postcat_lang_img']['size'] !== 0) {

		$postcat_lang_img = $_FILES['postcat_lang_img'];

		//File properties
		$file_name = $postcat_lang_img['name'];
		$file_tmp = $postcat_lang_img['tmp_name'];
		$file_size = $postcat_lang_img['size'];
		$file_error = $postcat_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/posts-cat/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/posts-cat/";
					$path_to_thumbs_directory = "../files/posts-cat/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/posts-cat/";
					$path_to_thumbs_directory = "../files/posts-cat/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = $_POST['postcat_lang_img_input'];
	}

	$query = $db->prepare("
					UPDATE idk_postcat
					SET	postcat_status = :postcat_status
					WHERE postcat_id = :postcat_id");

	$query->execute(array(
				':postcat_status' => $postcat_status,
				':postcat_id' => $postcat_id));


	$postcat_lang_langid = $_POST['postcat_lang_langid'];
	$postcat_lang_name = $_POST['postcat_lang_name'];

	$lang_query = $db->prepare("
						SELECT lang_postcat
						FROM idk_langs
						WHERE lang_id = :lang_id");

	$lang_query->execute(array(
					':lang_id' => $postcat_lang_langid));

	$lang_row = $lang_query->fetch();

		$lang_postcat = $lang_row['lang_postcat'];

		$postcat_url = create_slug($postcat_lang_name);
		$postcat_lang_url = "" . $lang_postcat . "/" . $postcat_url . "/" . $postcat_id . "";

	//Update db
	$query = $db->prepare("
					UPDATE idk_postcat_lang
					SET	postcat_lang_name = :postcat_lang_name, postcat_lang_img = :postcat_lang_img, postcat_lang_url = :postcat_lang_url
					WHERE postcat_lang_langid = :postcat_lang_langid AND postcat_lang_postcatid = :postcat_lang_postcatid");

	$query->execute(array(
				':postcat_lang_name' => $postcat_lang_name,
				':postcat_lang_img' => $file_name_new,
				':postcat_lang_url' => $postcat_lang_url,
				':postcat_lang_langid' => $postcat_lang_langid,
				':postcat_lang_postcatid' => $postcat_id));

	//Add to LOGS
	$log_desc = "Uredio kategoriju za novosti: " . $postcat_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: post-category?page=list&mess=2");

break;

case "add_post":

	//Add content to db
	$post_datetime = date('Y-m-d H:i:s');
	$post_catid = $_POST['post_catid'];
	$post_author = $_POST['post_author'];
	if(isset($_POST['post_featured'])){ $post_featured = $_POST['post_featured']; }else{ $post_featured = NULL; }
	if(isset($_POST['post_comment'])){ $post_comment = $_POST['post_comment']; }else{ $post_comment = NULL; }
	if(isset($_POST['post_share'])){ $post_share = $_POST['post_share']; }else{ $post_share = NULL; }
	$post_status = $_POST['post_status'];
	$post_count = 0;


	$query = $db->prepare("
					INSERT INTO idk_posts
						(post_datetime, post_catid, post_author, post_featured, post_comment, post_share, post_status, post_count)
					VALUES
						(:post_datetime, :post_catid, :post_author, :post_featured, :post_comment, :post_share, :post_status, :post_count)");

	$query->execute(array(
				':post_datetime' => $post_datetime,
				':post_catid' => $post_catid,
				':post_author' => $post_author,
				':post_featured' => $post_featured,
				':post_comment' => $post_comment,
				':post_share' => $post_share,
				':post_status' => $post_status,
				':post_count' => $post_count));

	$update_id = $db->lastInsertId();

	$post_lang_title = $_POST['post_lang_title'];
	$post_lang_content = $_POST['post_lang_content'];

	//Upload and save post_lang_img
	if($_FILES['post_lang_img']['size'] !== 0) {
		$post_lang_img = $_FILES['post_lang_img'];

		//File properties
		$file_name = $post_lang_img['name'];
		$file_tmp = $post_lang_img['tmp_name'];
		$file_size = $post_lang_img['size'];
		$file_error = $post_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/posts/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/posts/";
					$path_to_thumbs_directory = "../files/posts/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/posts/";
					$path_to_thumbs_directory = "../files/posts/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = NULL;
	}

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id, lang_post
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$post_lang_langid = $lang_row['lang_id'];
		$lang_post = $lang_row['lang_post'];

		//SEO
		if(isset($_POST['post_seo'])){

			$post_lang_seoname = $_POST['post_lang_seoname'];

			$slug_name = $_POST['post_lang_url'];
			$post_lang_url = "" . $lang_post . "/" . $slug_name . "/" . $update_id . "";

			$post_lang_seodesc = $_POST['post_lang_seodesc'];
			$post_lang_seokeywords = $_POST['post_lang_seokeywords'];

		}else{

			$post_lang_seoname = $post_lang_title;

			$slug_name = create_slug($post_lang_title);
			$post_lang_url = "" . $lang_post . "/" . $slug_name . "/" . $update_id . "";

			$post_lang_content_strip = strip_tags($post_lang_content);
			if(strlen($post_lang_content_strip) > 170){
				$pos = strpos($post_lang_content_strip, ' ', 160);
				$post_lang_seodesc = substr($post_lang_content_strip, 0, $pos) . " ...";
			}else{
				$post_lang_seodesc = $post_lang_content_strip;
			}

			$post_lang_seokeywords = create_keywords($post_lang_title);
		}

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_posts_lang
							(post_lang_langid, post_lang_postid, post_lang_title, post_lang_url, post_lang_content, post_lang_img, post_lang_seoname, post_lang_seodesc, post_lang_seokeywords)
						VALUES
							(:post_lang_langid, :post_lang_postid, :post_lang_title, :post_lang_url, :post_lang_content, :post_lang_img, :post_lang_seoname, :post_lang_seodesc, :post_lang_seokeywords)");

		$query->execute(array(
					':post_lang_langid' => $post_lang_langid,
					':post_lang_postid' => $update_id,
					':post_lang_title' => $post_lang_title,
					':post_lang_url' => $post_lang_url,
					':post_lang_content' => $post_lang_content,
					':post_lang_img' => $file_name_new,
					':post_lang_seoname' => $post_lang_seoname,
					':post_lang_seodesc' => $post_lang_seodesc,
					':post_lang_seokeywords' => $post_lang_seokeywords));
	}

	//Add to LOGS
	$log_desc = "Dodao novi članak: " . $post_lang_title . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: posts?page=list&mess=1");

break;

case "edit_post":

	//Add content to db
	$post_id = $_POST['post_id'];
	$post_catid = $_POST['post_catid'];
	$post_author = $_POST['post_author'];
	if(isset($_POST['post_featured'])){ $post_featured = $_POST['post_featured']; }else{ $post_featured = NULL; }
	if(isset($_POST['post_comment'])){ $post_comment = $_POST['post_comment']; }else{ $post_comment = NULL; }
	if(isset($_POST['post_share'])){ $post_share = $_POST['post_share']; }else{ $post_share = NULL; }
	$post_status = $_POST['post_status'];

	$query = $db->prepare("
					UPDATE idk_posts
					SET	post_catid = :post_catid, post_author = :post_author, post_featured = :post_featured, post_comment = :post_comment, post_share = :post_share, post_status = :post_status
					WHERE post_id = :post_id");

	$query->execute(array(
				':post_catid' => $post_catid,
				':post_author' => $post_author,
				':post_featured' => $post_featured,
				':post_comment' => $post_comment,
				':post_share' => $post_share,
				':post_status' => $post_status,
				':post_id' => $post_id));


	$post_lang_langid = $_POST['post_lang_langid'];
	$post_lang_title = $_POST['post_lang_title'];
	$post_lang_content = $_POST['post_lang_content'];

	//Upload and save post_lang_img
	if($_FILES['post_lang_img']['size'] !== 0) {

		$post_lang_img = $_FILES['post_lang_img'];

		//File properties
		$file_name = $post_lang_img['name'];
		$file_tmp = $post_lang_img['tmp_name'];
		$file_size = $post_lang_img['size'];
		$file_error = $post_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/posts/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/posts/";
					$path_to_thumbs_directory = "../files/posts/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/posts/";
					$path_to_thumbs_directory = "../files/posts/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = $_POST['post_lang_img_input'];
	}

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_post
						FROM idk_langs
						WHERE lang_id = :lang_id");

	$lang_query->execute(array(
					':lang_id' => $post_lang_langid));

	$lang_row = $lang_query->fetch();

		$lang_post = $lang_row['lang_post'];

	//SEO
	if(isset($_POST['post_seo'])){

		$post_lang_seoname = $_POST['post_lang_seoname'];

		$slug_name = $_POST['post_lang_url'];
		$post_lang_url = "" . $lang_post . "/" . $slug_name . "/" . $post_id . "";

		$post_lang_seodesc = $_POST['post_lang_seodesc'];

		$post_lang_seokeywords = $_POST['post_lang_seokeywords'];

	}else{

		$post_lang_seoname = $post_lang_title;

		$slug_name = create_slug($post_lang_title);
		$post_lang_url = "" . $lang_post . "/" . $slug_name . "/" . $post_id . "";

		$post_lang_content_strip = strip_tags($post_lang_content);
		if(strlen($post_lang_content_strip) > 170){
			$pos = strpos($post_lang_content_strip, ' ', 160);
			$post_lang_seodesc = substr($post_lang_content_strip, 0, $pos) . " ...";
		}else{
			$post_lang_seodesc = $post_lang_content_strip;
		}

		$post_lang_seokeywords = create_keywords($post_lang_title);

	}

	//Update db
	$query = $db->prepare("
					UPDATE idk_posts_lang
					SET	post_lang_title = :post_lang_title, post_lang_url = :post_lang_url, post_lang_content = :post_lang_content, post_lang_img = :post_lang_img, post_lang_seoname = :post_lang_seoname, post_lang_seodesc = :post_lang_seodesc, post_lang_seokeywords = :post_lang_seokeywords
					WHERE post_lang_langid = :post_lang_langid AND post_lang_postid = :post_lang_postid");

	$query->execute(array(
				':post_lang_title' => $post_lang_title,
				':post_lang_url' => $post_lang_url,
				':post_lang_content' => $post_lang_content,
				':post_lang_img' => $file_name_new,
				':post_lang_seoname' => $post_lang_seoname,
				':post_lang_seodesc' => $post_lang_seodesc,
				':post_lang_seokeywords' => $post_lang_seokeywords,
				':post_lang_langid' => $post_lang_langid,
				':post_lang_postid' => $post_id));

	//Add to LOGS
	$log_desc = "Uredio članak: " . $post_lang_title . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: posts?page=list&mess=2");

break;

case "add_faq":

	//Add to db
	$faq_date = date('Y-m-d H:i:s');
	$faq_status = 1;

	$query = $db->prepare("
					INSERT INTO idk_faq
						(faq_date, faq_status)
					VALUES
						(:faq_date, :faq_status)");

	$query->execute(array(
				':faq_date' => $faq_date,
				':faq_status' => $faq_status));

	$update_id = $db->lastInsertId();

	$faq_lang_question = $_POST['faq_lang_question'];
	$faq_lang_answer = $_POST['faq_lang_answer'];

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$faq_lang_langid = $lang_row['lang_id'];

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_faq_lang
							(faq_lang_langid, faq_lang_faqid, faq_lang_question, faq_lang_answer)
						VALUES
							(:faq_lang_langid, :faq_lang_faqid, :faq_lang_question, :faq_lang_answer)");

		$query->execute(array(
					':faq_lang_langid' => $faq_lang_langid,
					':faq_lang_faqid' => $update_id,
					':faq_lang_question' => $faq_lang_question,
					':faq_lang_answer' => $faq_lang_answer));
	}

	//Add to LOGS
	$log_desc = "Dodao novo najčešće pitanje: " . $faq_lang_question . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));


	header("Location: faq?page=list&mess=1");

break;

case "edit_faq":

	//Add content to db
	$faq_lang_faqid = $_POST['faq_id'];
	$faq_lang_langid = $_POST['faq_lang_langid'];
	$faq_lang_question = $_POST['faq_lang_question'];
	$faq_lang_answer = $_POST['faq_lang_answer'];

	//Update db
	$query = $db->prepare("
					UPDATE idk_faq_lang
					SET	faq_lang_question = :faq_lang_question, faq_lang_answer = :faq_lang_answer
					WHERE faq_lang_langid = :faq_lang_langid AND faq_lang_faqid = :faq_lang_faqid");

	$query->execute(array(
				':faq_lang_question' => $faq_lang_question,
				':faq_lang_answer' => $faq_lang_answer,
				':faq_lang_langid' => $faq_lang_langid,
				':faq_lang_faqid' => $faq_lang_faqid));

	//Add to LOGS
	$log_desc = "Uredio najčešće pitanje: " . $faq_lang_question . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: faq?page=list&mess=2");

break;

case "add_product":

	//Add content to db
	$product_number = $_POST['product_number'];
	if(isset($_POST['product_brand'])){ $product_brand = $_POST['product_brand']; }else{ $product_brand = NULL; }
	if(isset($_POST['product_featured'])){ $product_featured = $_POST['product_featured']; }else{ $product_featured = NULL; }
	if(isset($_POST['product_onsale'])){ $product_onsale = $_POST['product_onsale']; }else{ $product_onsale = NULL; }
	if(isset($_POST['product_comment'])){ $product_comment = $_POST['product_comment']; }else{ $product_comment = NULL; }
	if(isset($_POST['product_share'])){ $product_share = $_POST['product_share']; }else{ $product_share = NULL; }
	$product_count = 0;
	$product_status = $_POST['product_status'];

	$query = $db->prepare("
					INSERT INTO idk_products
						(product_number, product_brand, product_featured, product_onsale, product_comment, product_share, product_count, product_status)
					VALUES
						(:product_number, :product_brand, :product_featured, :product_onsale, :product_comment, :product_share, :product_count, :product_status)");

	$query->execute(array(
				':product_number' => $product_number,
				':product_brand' => $product_brand,
				':product_featured' => $product_featured,
				':product_onsale' => $product_onsale,
				':product_comment' => $product_comment,
				':product_share' => $product_share,
				':product_count' => $product_count,
				':product_status' => $product_status));

	$update_id = $db->lastInsertId();

	$product_lang_name = $_POST['product_lang_name'];
	$product_lang_desc = $_POST['product_lang_desc'];

	//Upload and save product_lang_img
	if($_FILES['product_lang_img']['size'] !== 0) {
		$product_lang_img = $_FILES['product_lang_img'];

		//File properties
		$file_name = $product_lang_img['name'];
		$file_tmp = $product_lang_img['tmp_name'];
		$file_size = $product_lang_img['size'];
		$file_error = $product_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/products/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/products/";
					$path_to_thumbs_directory = "../files/products/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/products/";
					$path_to_thumbs_directory = "../files/products/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = NULL;
	}

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id, lang_product
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$product_lang_langid = $lang_row['lang_id'];
		$lang_product = $lang_row['lang_product'];

		//SEO
		if(isset($_POST['product_seo'])){

			$product_lang_seoname = $_POST['product_lang_seoname'];

			$slug_name = $_POST['product_lang_url'];
			$product_lang_url = "" . $lang_product . "/" . $slug_name . "/" . $update_id . "";

			$product_lang_seodesc = $_POST['product_lang_seodesc'];

			$product_lang_seokeywords = $_POST['product_lang_seokeywords'];

		}else{

			$product_lang_seoname = $product_lang_name;

			$slug_name = create_slug($product_lang_name);
			$product_lang_url = "" . $lang_product . "/" . $slug_name . "/" . $update_id . "";

			$product_lang_desc_strip = strip_tags($product_lang_desc);
			if(strlen($product_lang_desc_strip) > 170){
				$pos = strpos($product_lang_desc_strip, ' ', 160);
				$product_lang_seodesc = substr($product_lang_desc_strip, 0, $pos) . " ...";
			}else{
				$product_lang_seodesc = $product_lang_desc_strip;
			}

			$product_lang_seokeywords = create_keywords($product_lang_name);
		}

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_products_lang
							(product_lang_langid, product_lang_productid, product_lang_name, product_lang_url, product_lang_desc, product_lang_img, product_lang_seoname, product_lang_seodesc, product_lang_seokeywords)
						VALUES
							(:product_lang_langid, :product_lang_productid, :product_lang_name, :product_lang_url, :product_lang_desc, :product_lang_img, :product_lang_seoname, :product_lang_seodesc, :product_lang_seokeywords)");

		$query->execute(array(
					':product_lang_langid' => $product_lang_langid,
					':product_lang_productid' => $update_id,
					':product_lang_name' => $product_lang_name,
					':product_lang_url' => $product_lang_url,
					':product_lang_desc' => $product_lang_desc,
					':product_lang_img' => $file_name_new,
					':product_lang_seoname' => $product_lang_seoname,
					':product_lang_seodesc' => $product_lang_seodesc,
					':product_lang_seokeywords' => $product_lang_seokeywords));
	}

	//Add category
	if(isset($_POST['productcat_id'])){
		$productcat_id = $_POST['productcat_id'];

		foreach($productcat_id as $productcat_relation_catid) {

			$query_cat = $db->prepare("
						INSERT INTO idk_productcat_relation
							(productcat_relation_productid, productcat_relation_catid)
						VALUES
							(:productcat_relation_productid, :productcat_relation_catid)");

			$query_cat->execute(array(
						':productcat_relation_productid' => $update_id,
						':productcat_relation_catid' => $productcat_relation_catid));
		}
	}

	//Add document
	if(isset($_POST['pdocs_lang_name']) AND $_FILES['pdocs_lang_file']['size'] !== 0){

		$pdocs_lang_name = $_POST['pdocs_lang_name'];

		$pdocs_lang_file = $_FILES['pdocs_lang_file'];

		//File properties
		$file_name = $pdocs_lang_file['name'];
		$file_tmp = $pdocs_lang_file['tmp_name'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

		if(in_array($file_ext, $allowed)) {

			$pdocs_lang_file_name_new = uniqid() . '.' . $file_ext;
			$file_destination = "../files/product-documents/" . $pdocs_lang_file_name_new;

			if(move_uploaded_file($file_tmp, $file_destination)){}
		}

		$query_docs = $db->prepare("
								INSERT INTO idk_product_docs
									(pdocs_productid, pdocs_status)
								VALUES
									(:pdocs_productid, :pdocs_status)");

		$query_docs->execute(array(
						':pdocs_productid' => $update_id,
						':pdocs_status' => 1));

		$pdocs_id = $db->lastInsertId();

		$docs_lang_query = $db->prepare("
							SELECT lang_id
							FROM idk_langs");

		$docs_lang_query->execute();

		while($docs_lang_row = $docs_lang_query->fetch()) {

			$pdocs_lang_langid = $docs_lang_row['lang_id'];

			$query_docs_lang = $db->prepare("
									INSERT INTO idk_product_docs_lang
										(pdocs_lang_langid, pdocs_lang_pdocsid, pdocs_lang_name, pdocs_lang_file)
									VALUES
										(:pdocs_lang_langid, :pdocs_lang_pdocsid, :pdocs_lang_name, :pdocs_lang_file)");

			$query_docs_lang->execute(array(
							':pdocs_lang_langid' => $pdocs_lang_langid,
							':pdocs_lang_pdocsid' => $pdocs_id,
							':pdocs_lang_name' => $pdocs_lang_name,
							':pdocs_lang_file' => $pdocs_lang_file_name_new));
		}
	}

	//Upload photos to gallery
	for($i=0; $i<count($_FILES['pphotos_file']['name']); $i++) {

		$tmpFilePath = $_FILES['pphotos_file']['tmp_name'][$i];
		if ($tmpFilePath != ""){

			$filename_frompc = $_FILES['pphotos_file']['name'][$i];

			$file_ext = explode('.', $filename_frompc);
			$file_ext = strtolower(end($file_ext));

			$filename = uniqid() . "." . $file_ext;

			$newFilePath = "../files/product-photos/" . $filename;

			if(move_uploaded_file($tmpFilePath, $newFilePath)) {

						$query_photos = $db->prepare("
												INSERT INTO idk_product_photos
													(pphotos_productid, pphotos_file, pphotos_sort)
												VALUES
													(:pphotos_productid, :pphotos_file, :pphotos_sort)");

						$query_photos->execute(array(
										':pphotos_productid' => $update_id,
										':pphotos_file' => $filename,
										':pphotos_sort' => 1));

						$path_to_image_directory = "../files/product-photos/";
						$path_to_thumbs_directory = "../files/product-photos/";
						$final_width_of_image = 1400;

						if(preg_match('/[.](jpg)$/', $filename)) {
							$im = imagecreatefromjpeg($path_to_image_directory . $filename);
						} else if (preg_match('/[.](gif)$/', $filename)) {
							$im = imagecreatefromgif($path_to_image_directory . $filename);
						} else if (preg_match('/[.](png)$/', $filename)) {
							$im = imagecreatefrompng($path_to_image_directory . $filename);
						}

						$ox = imagesx($im);
						$oy = imagesy($im);

						$nx = $final_width_of_image;
						$ny = floor($oy * ($final_width_of_image / $ox));

						$nm = imagecreatetruecolor($nx, $ny);

						imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

						if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
						}

						imagejpeg($nm, $path_to_thumbs_directory . $filename);


						//Thumbs
						$path_to_image_directory = "../files/product-photos/";
						$path_to_thumbs_directory = "../files/product-photos/thumbs/";
						$final_width_of_image = 400;

						if(preg_match('/[.](jpg)$/', $filename)) {
							$im = imagecreatefromjpeg($path_to_image_directory . $filename);
						} else if (preg_match('/[.](gif)$/', $filename)) {
							$im = imagecreatefromgif($path_to_image_directory . $filename);
						} else if (preg_match('/[.](png)$/', $filename)) {
							$im = imagecreatefrompng($path_to_image_directory . $filename);
						}

						$ox = imagesx($im);
						$oy = imagesy($im);

						$nx = $final_width_of_image;
						$ny = floor($oy * ($final_width_of_image / $ox));

						$nm = imagecreatetruecolor($nx, $ny);

						imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

						if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}
						imagejpeg($nm, $path_to_thumbs_directory . $filename);
			}
		}
	}

	//Add to LOGS
	$log_desc = "Dodao novi proizvod: " . $product_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: products?page=list&mess=1");

break;

case "edit_product":

	//Add content to db
	$product_id = $_POST['product_id'];
	$product_number = $_POST['product_number'];
	if(isset($_POST['product_brand'])){ $product_brand = $_POST['product_brand']; }else{ $product_brand = NULL; }
	if(isset($_POST['product_featured'])){ $product_featured = $_POST['product_featured']; }else{ $product_featured = NULL; }
	if(isset($_POST['product_onsale'])){ $product_onsale = $_POST['product_onsale']; }else{ $product_onsale = NULL; }
	if(isset($_POST['product_comment'])){ $product_comment = $_POST['product_comment']; }else{ $product_comment = NULL; }
	if(isset($_POST['product_share'])){ $product_share = $_POST['product_share']; }else{ $product_share = NULL; }
	$product_status = $_POST['product_status'];

	$query = $db->prepare("
					UPDATE idk_products
					SET	product_number = :product_number, product_brand = :product_brand, product_featured = :product_featured, product_onsale = :product_onsale, product_comment = :product_comment, product_share = :product_share, product_status = :product_status
					WHERE product_id = :product_id");

	$query->execute(array(
				':product_number' => $product_number,
				':product_brand' => $product_brand,
				':product_featured' => $product_featured,
				':product_onsale' => $product_onsale,
				':product_comment' => $product_comment,
				':product_share' => $product_share,
				':product_status' => $product_status,
				':product_id' => $product_id));


	$product_lang_langid = $_POST['product_lang_langid'];
	$product_lang_name = $_POST['product_lang_name'];
	$product_lang_desc = $_POST['product_lang_desc'];

	//Upload and save product_lang_img
	if($_FILES['product_lang_img']['size'] !== 0) {

		$product_lang_img = $_FILES['product_lang_img'];

		//File properties
		$file_name = $product_lang_img['name'];
		$file_tmp = $product_lang_img['tmp_name'];
		$file_size = $product_lang_img['size'];
		$file_error = $product_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/products/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/products/";
					$path_to_thumbs_directory = "../files/products/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/products/";
					$path_to_thumbs_directory = "../files/products/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = $_POST['product_lang_img_input'];
	}

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_product
						FROM idk_langs
						WHERE lang_id = :lang_id");

	$lang_query->execute(array(
					':lang_id' => $product_lang_langid));

	$lang_row = $lang_query->fetch();

		$lang_product = $lang_row['lang_product'];

	//SEO
	if(isset($_POST['product_seo'])){

		$product_lang_seoname = $_POST['product_lang_seoname'];

		$slug_name = $_POST['product_lang_url'];
		$product_lang_url = "" . $lang_product . "/" . $slug_name . "/" . $product_id . "";

		$product_lang_seodesc = $_POST['product_lang_seodesc'];

		$product_lang_seokeywords = $_POST['product_lang_seokeywords'];

	}else{

		$product_lang_seoname = $product_lang_name;

		$slug_name = create_slug($product_lang_name);
		$product_lang_url = "" . $lang_product . "/" . $slug_name . "/" . $product_id . "";

		$product_lang_desc_strip = strip_tags($product_lang_desc);
		if(strlen($product_lang_desc_strip) > 170){
			$pos = strpos($product_lang_desc_strip, ' ', 160);
			$product_lang_seodesc = substr($product_lang_desc_strip, 0, $pos) . " ...";
		}else{
			$product_lang_seodesc = $product_lang_desc_strip;
		}

		$product_lang_seokeywords = create_keywords($product_lang_name);

	}

	//Update db
	$query = $db->prepare("
					UPDATE idk_products_lang
					SET	product_lang_name = :product_lang_name, product_lang_url = :product_lang_url, product_lang_desc = :product_lang_desc, product_lang_img = :product_lang_img, product_lang_seoname = :product_lang_seoname, product_lang_seodesc = :product_lang_seodesc, product_lang_seokeywords = :product_lang_seokeywords
					WHERE product_lang_langid = :product_lang_langid AND product_lang_productid = :product_lang_productid");

	$query->execute(array(
				':product_lang_name' => $product_lang_name,
				':product_lang_url' => $product_lang_url,
				':product_lang_desc' => $product_lang_desc,
				':product_lang_img' => $file_name_new,
				':product_lang_seoname' => $product_lang_seoname,
				':product_lang_seodesc' => $product_lang_seodesc,
				':product_lang_seokeywords' => $product_lang_seokeywords,
				':product_lang_langid' => $product_lang_langid,
				':product_lang_productid' => $product_id));

	//Change Category
	$cat_tag_del_query = $db->prepare("
								DELETE FROM idk_productcat_relation
								WHERE productcat_relation_productid = :productcat_relation_productid");

	$cat_tag_del_query->execute(array(
						':productcat_relation_productid' => $product_id));

	//Add category
	if(isset($_POST['productcat_id'])){

		$productcat_id = $_POST['productcat_id'];

		foreach($productcat_id as $productcat_relation_catid) {

			$query_cat = $db->prepare("
						INSERT INTO idk_productcat_relation
							(productcat_relation_productid, productcat_relation_catid)
						VALUES
							(:productcat_relation_productid, :productcat_relation_catid)");

			$query_cat->execute(array(
						':productcat_relation_productid' => $product_id,
						':productcat_relation_catid' => $productcat_relation_catid));
		}
	}

	//Upload photos to gallery
	for($i=0; $i<count($_FILES['pphotos_file']['name']); $i++) {

		$tmpFilePath = $_FILES['pphotos_file']['tmp_name'][$i];
		if ($tmpFilePath != ""){

			$filename_frompc = $_FILES['pphotos_file']['name'][$i];

			$file_ext = explode('.', $filename_frompc);
			$file_ext = strtolower(end($file_ext));

			$filename = uniqid() . "." . $file_ext;

			$newFilePath = "../files/product-photos/" . $filename;

			if(move_uploaded_file($tmpFilePath, $newFilePath)) {

						$query_photos = $db->prepare("
												INSERT INTO idk_product_photos
													(pphotos_productid, pphotos_file, pphotos_sort)
												VALUES
													(:pphotos_productid, :pphotos_file, :pphotos_sort)");

						$query_photos->execute(array(
										':pphotos_productid' => $product_id,
										':pphotos_file' => $filename,
										':pphotos_sort' => 1));

						$path_to_image_directory = "../files/product-photos/";
						$path_to_thumbs_directory = "../files/product-photos/";
						$final_width_of_image = 1400;

						if(preg_match('/[.](jpg)$/', $filename)) {
							$im = imagecreatefromjpeg($path_to_image_directory . $filename);
						} else if (preg_match('/[.](gif)$/', $filename)) {
							$im = imagecreatefromgif($path_to_image_directory . $filename);
						} else if (preg_match('/[.](png)$/', $filename)) {
							$im = imagecreatefrompng($path_to_image_directory . $filename);
						}

						$ox = imagesx($im);
						$oy = imagesy($im);

						$nx = $final_width_of_image;
						$ny = floor($oy * ($final_width_of_image / $ox));

						$nm = imagecreatetruecolor($nx, $ny);

						imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

						if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
						}

						imagejpeg($nm, $path_to_thumbs_directory . $filename);


						//Thumbs
						$path_to_image_directory = "../files/product-photos/";
						$path_to_thumbs_directory = "../files/product-photos/thumbs/";
						$final_width_of_image = 400;

						if(preg_match('/[.](jpg)$/', $filename)) {
							$im = imagecreatefromjpeg($path_to_image_directory . $filename);
						} else if (preg_match('/[.](gif)$/', $filename)) {
							$im = imagecreatefromgif($path_to_image_directory . $filename);
						} else if (preg_match('/[.](png)$/', $filename)) {
							$im = imagecreatefrompng($path_to_image_directory . $filename);
						}

						$ox = imagesx($im);
						$oy = imagesy($im);

						$nx = $final_width_of_image;
						$ny = floor($oy * ($final_width_of_image / $ox));

						$nm = imagecreatetruecolor($nx, $ny);

						imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

						if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}
						imagejpeg($nm, $path_to_thumbs_directory . $filename);
			}
		}
	}

	//Add document
	if(isset($_POST['pdocs_lang_name']) AND $_FILES['pdocs_lang_file']['size'] !== 0){

		$pdocs_lang_name = $_POST['pdocs_lang_name'];

		$pdocs_lang_file = $_FILES['pdocs_lang_file'];

		//File properties
		$file_name = $pdocs_lang_file['name'];
		$file_tmp = $pdocs_lang_file['tmp_name'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

		if(in_array($file_ext, $allowed)) {

			$pdocs_lang_file_name_new = uniqid() . '.' . $file_ext;
			$file_destination = "../files/product-documents/" . $pdocs_lang_file_name_new;

			if(move_uploaded_file($file_tmp, $file_destination)){}
		}

		$query_docs = $db->prepare("
								INSERT INTO idk_product_docs
									(pdocs_productid, pdocs_status)
								VALUES
									(:pdocs_productid, :pdocs_status)");

		$query_docs->execute(array(
						':pdocs_productid' => $product_id,
						':pdocs_status' => 1));

		$pdocs_lang_pdocsid = $db->lastInsertId();


		$query_docs_lang = $db->prepare("
								INSERT INTO idk_product_docs_lang
									(pdocs_lang_langid, pdocs_lang_pdocsid, pdocs_lang_name, pdocs_lang_file)
								VALUES
									(:pdocs_lang_langid, :pdocs_lang_pdocsid, :pdocs_lang_name, :pdocs_lang_file)");

		$query_docs_lang->execute(array(
						':pdocs_lang_langid' => $product_lang_langid,
						':pdocs_lang_pdocsid' => $pdocs_lang_pdocsid,
						':pdocs_lang_name' => $pdocs_lang_name,
						':pdocs_lang_file' => $pdocs_lang_file_name_new));
	}

	//Add to LOGS
	$log_desc = "Uredio profil proizvoda: " . $product_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: products?page=list&mess=2");

break;

case "del_product_photo":

	$pphotos_id = $_GET['id'];

	//Delete image from folders
	$photo_open_query = $db->prepare("
								SELECT pphotos_file
								FROM idk_product_photos
								WHERE pphotos_id = :pphotos_id");

	$photo_open_query->execute(array(
							':pphotos_id' => $pphotos_id));

	$photo_open = $photo_open_query->fetch();

		$pphotos_file = $photo_open['pphotos_file'];

	if($pphotos_file == ""){}else{
		unlink("../files/product-photos/" . $pphotos_file);
		unlink("../files/product-photos/thumbs/" . $pphotos_file);
	}

	//Delete content
	$photo_del_query = $db->prepare("
								DELETE FROM idk_product_photos
								WHERE pphotos_id = :pphotos_id");

	$photo_del_query->execute(array(
						':pphotos_id' => $pphotos_id));

	header("Location: javascript: history.back()");

break;

case "add_productcat":

	$productcat_count = 0;
	$productcat_sub = $_POST['productcat_sub'];
	$productcat_status = $_POST['productcat_status'];

	//Upload and save productcat_lang_img
	if($_FILES['productcat_lang_img']['size'] !== 0) {
		$productcat_lang_img = $_FILES['productcat_lang_img'];

		//File properties
		$file_name = $productcat_lang_img['name'];
		$file_tmp = $productcat_lang_img['tmp_name'];
		$file_size = $productcat_lang_img['size'];
		$file_error = $productcat_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/product-cat/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/product-cat/";
					$path_to_thumbs_directory = "../files/product-cat/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/product-cat/";
					$path_to_thumbs_directory = "../files/product-cat/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = NULL;
	}

	$query = $db->prepare("
					INSERT INTO idk_productcat
						(productcat_sub, productcat_count, productcat_status)
					VALUES
						(:productcat_sub, :productcat_count, :productcat_status)");

	$query->execute(array(
				':productcat_sub' => $productcat_sub,
				':productcat_count' => $productcat_count,
				':productcat_status' => $productcat_status));

	$update_id = $db->lastInsertId();

	$productcat_lang_name = $_POST['productcat_lang_name'];

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id, lang_productcat
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$productcat_lang_langid = $lang_row['lang_id'];
		$lang_productcat = $lang_row['lang_productcat'];

		$productcat_url = create_slug($productcat_lang_name);
		$productcat_lang_url = "" . $lang_productcat . "/" . $productcat_url . "/" . $update_id . "";

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_productcat_lang
							(productcat_lang_langid, productcat_lang_productid, productcat_lang_name, productcat_lang_img, productcat_lang_url)
						VALUES
							(:productcat_lang_langid, :productcat_lang_productid, :productcat_lang_name, :productcat_lang_img, :productcat_lang_url)");

		$query->execute(array(
					':productcat_lang_langid' => $productcat_lang_langid,
					':productcat_lang_productid' => $update_id,
					':productcat_lang_name' => $productcat_lang_name,
					':productcat_lang_img' => $file_name_new,
					':productcat_lang_url' => $productcat_lang_url));
	}

	//Add to LOGS
	$log_desc = "Dodao novu kategoriju za proizvod: " . $productcat_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: product-category?page=list&mess=1");

break;

case "edit_productcat":

	//Add content to db
	$productcat_id = $_POST['productcat_id'];
	$productcat_sub = $_POST['productcat_sub'];
	$productcat_status = $_POST['productcat_status'];

	//Upload and save productcat_lang_img
	if($_FILES['productcat_lang_img']['size'] !== 0) {

		$productcat_lang_img = $_FILES['productcat_lang_img'];

		//File properties
		$file_name = $productcat_lang_img['name'];
		$file_tmp = $productcat_lang_img['tmp_name'];
		$file_size = $productcat_lang_img['size'];
		$file_error = $productcat_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/product-cat/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/product-cat/";
					$path_to_thumbs_directory = "../files/product-cat/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/product-cat/";
					$path_to_thumbs_directory = "../files/product-cat/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = $_POST['productcat_lang_img_input'];
	}

	$query = $db->prepare("
					UPDATE idk_productcat
					SET	productcat_sub = :productcat_sub, productcat_status = :productcat_status
					WHERE productcat_id = :productcat_id");

	$query->execute(array(
				':productcat_sub' => $productcat_sub,
				':productcat_status' => $productcat_status,
				':productcat_id' => $productcat_id));


	$productcat_lang_langid = $_POST['productcat_lang_langid'];
	$productcat_lang_name = $_POST['productcat_lang_name'];

	$lang_query = $db->prepare("
						SELECT lang_productcat
						FROM idk_langs
						WHERE lang_id = :lang_id");

	$lang_query->execute(array(
					':lang_id' => $productcat_lang_langid));

	$lang_row = $lang_query->fetch();

		$lang_productcat = $lang_row['lang_productcat'];

		$productcat_url = create_slug($productcat_lang_name);
		$productcat_lang_url = "" . $lang_productcat . "/" . $productcat_url . "/" . $productcat_id . "";

	//Update db
	$query = $db->prepare("
					UPDATE idk_productcat_lang
					SET	productcat_lang_name = :productcat_lang_name, productcat_lang_img = :productcat_lang_img, productcat_lang_url = :productcat_lang_url
					WHERE productcat_lang_langid = :productcat_lang_langid AND productcat_lang_productid = :productcat_lang_productid");

	$query->execute(array(
				':productcat_lang_name' => $productcat_lang_name,
				':productcat_lang_img' => $file_name_new,
				':productcat_lang_url' => $productcat_lang_url,
				':productcat_lang_langid' => $productcat_lang_langid,
				':productcat_lang_productid' => $productcat_id));

	//Add to LOGS
	$log_desc = "Uredio kategoriju za proizvode: " . $productcat_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: product-category?page=list&mess=2");

break;

case "add_nav_content":

	if(isset($_POST['nav_target'])){ $nav_target = $_POST['nav_target']; }else{ $nav_target = NULL; }
	$nav_contentid = $_POST['nav_contentid'];
	$nav_sub = $_POST['nav_sub'];
	$nav_sort = $_POST['nav_sort'];

	$query = $db->prepare("
					INSERT INTO idk_navigation
						(nav_target, nav_contentid, nav_sub, nav_sort)
					VALUES
						(:nav_target, :nav_contentid, :nav_sub, :nav_sort)");

	$query->execute(array(
				':nav_target' => $nav_target,
				':nav_contentid' => $nav_contentid,
				':nav_sub' => $nav_sub,
				':nav_sort' => $nav_sort));

	$update_id = $db->lastInsertId();

	$nav_lang_name = $_POST['nav_lang_name'];

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$nav_lang_langid = $lang_row['lang_id'];

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_navigation_lang
							(nav_lang_langid, nav_lang_navid, nav_lang_name)
						VALUES
							(:nav_lang_langid, :nav_lang_navid, :nav_lang_name)");

		$query->execute(array(
					':nav_lang_langid' => $nav_lang_langid,
					':nav_lang_navid' => $update_id,
					':nav_lang_name' => $nav_lang_name));
	}

	//Add to LOGS
	$log_desc = "Dodao novu navigaciju: " . $nav_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: navigation?page=list&mess=1");

break;

case "add_nav":

	if(isset($_POST['nav_target'])){ $nav_target = $_POST['nav_target']; }else{ $nav_target = NULL; }
	$nav_sub = $_POST['nav_sub'];
	$nav_sort = $_POST['nav_sort'];

	$query = $db->prepare("
					INSERT INTO idk_navigation
						(nav_target, nav_sub, nav_sort)
					VALUES
						(:nav_target, :nav_sub, :nav_sort)");

	$query->execute(array(
				':nav_target' => $nav_target,
				':nav_sub' => $nav_sub,
				':nav_sort' => $nav_sort));

	$update_id = $db->lastInsertId();

	$nav_lang_name = $_POST['nav_lang_name'];
	$nav_lang_link = $_POST['nav_lang_link'];

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$nav_lang_langid = $lang_row['lang_id'];

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_navigation_lang
							(nav_lang_langid, nav_lang_navid, nav_lang_name, nav_lang_link)
						VALUES
							(:nav_lang_langid, :nav_lang_navid, :nav_lang_name, :nav_lang_link)");

		$query->execute(array(
					':nav_lang_langid' => $nav_lang_langid,
					':nav_lang_navid' => $update_id,
					':nav_lang_name' => $nav_lang_name,
					':nav_lang_link' => $nav_lang_link));
	}

	//Add to LOGS
	$log_desc = "Dodao novu navigaciju: " . $nav_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: navigation?page=list&mess=1");

break;

case "edit_nav":

	//Add content to db
	$nav_id = $_POST['nav_id'];
	if(isset($_POST['nav_target'])){ $nav_target = $_POST['nav_target']; }else{ $nav_target = NULL; }
	if(isset($_POST['nav_contentid'])){ $nav_contentid = $_POST['nav_contentid']; }else{ $nav_contentid = NULL; }
	$nav_sub = $_POST['nav_sub'];
	$nav_sort = $_POST['nav_sort'];

	$query = $db->prepare("
					UPDATE idk_navigation
					SET	nav_target = :nav_target, nav_contentid = :nav_contentid, nav_sub = :nav_sub, nav_sort = :nav_sort
					WHERE nav_id = :nav_id");

	$query->execute(array(
				':nav_target' => $nav_target,
				':nav_contentid' => $nav_contentid,
				':nav_sub' => $nav_sub,
				':nav_sort' => $nav_sort,
				':nav_id' => $nav_id));

	$nav_lang_langid = $_POST['nav_lang_langid'];
	$nav_lang_name = $_POST['nav_lang_name'];
	$nav_lang_link = $_POST['nav_lang_link'];

	//Update db
	$query = $db->prepare("
					UPDATE idk_navigation_lang
					SET	nav_lang_name = :nav_lang_name, nav_lang_link = :nav_lang_link
					WHERE nav_lang_langid = :nav_lang_langid AND nav_lang_navid = :nav_lang_navid");

	$query->execute(array(
				':nav_lang_name' => $nav_lang_name,
				':nav_lang_link' => $nav_lang_link,
				':nav_lang_langid' => $nav_lang_langid,
				':nav_lang_navid' => $nav_id));

	//Add to LOGS
	$log_desc = "Uredio navigaciju: " . $nav_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: navigation?page=list&mess=2");

break;

case "add_slider":

	$slider_sort = $_POST['slider_sort'];
	$slider_status = $_POST['slider_status'];

	//Upload and save slider_lang_img
	if($_FILES['slider_lang_img']['size'] !== 0) {
		$slider_lang_img = $_FILES['slider_lang_img'];

		//File properties
		$file_name = $slider_lang_img['name'];
		$file_tmp = $slider_lang_img['tmp_name'];
		$file_size = $slider_lang_img['size'];
		$file_error = $slider_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/slider/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/slider/";
					$path_to_thumbs_directory = "../files/slider/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = NULL;
	}

	$query = $db->prepare("
					INSERT INTO idk_slider
						(slider_sort, slider_status)
					VALUES
						(:slider_sort, :slider_status)");

	$query->execute(array(
				':slider_sort' => $slider_sort,
				':slider_status' => $slider_status));

	$update_id = $db->lastInsertId();

	$slider_lang_title = $_POST['slider_lang_title'];
	$slider_lang_desc = $_POST['slider_lang_desc'];
	$slider_lang_link = $_POST['slider_lang_link'];

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$slider_lang_langid = $lang_row['lang_id'];

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_slider_lang
							(slider_lang_langid, slider_lang_sliderid, slider_lang_title, slider_lang_desc, slider_lang_img, slider_lang_link)
						VALUES
							(:slider_lang_langid, :slider_lang_sliderid, :slider_lang_title, :slider_lang_desc, :slider_lang_img, :slider_lang_link)");

		$query->execute(array(
					':slider_lang_langid' => $slider_lang_langid,
					':slider_lang_sliderid' => $update_id,
					':slider_lang_title' => $slider_lang_title,
					':slider_lang_desc' => $slider_lang_desc,
					':slider_lang_img' => $file_name_new,
					':slider_lang_link' => $slider_lang_link));
	}

	//Add to LOGS
	$log_desc = "Dodao novi slider: " . $slider_lang_title . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: slider?page=list&mess=1");

break;

case "edit_slider":

	//Add slider to db
	$slider_id = $_POST['slider_id'];
	$slider_sort = $_POST['slider_sort'];
	$slider_status = $_POST['slider_status'];

	//Upload and save slider_lang_img
	if($_FILES['slider_lang_img']['size'] !== 0) {

		$slider_lang_img = $_FILES['slider_lang_img'];

		//File properties
		$file_name = $slider_lang_img['name'];
		$file_tmp = $slider_lang_img['tmp_name'];
		$file_size = $slider_lang_img['size'];
		$file_error = $slider_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/slider/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/slider/";
					$path_to_thumbs_directory = "../files/slider/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = $_POST['slider_lang_img_input'];
	}

	$query = $db->prepare("
					UPDATE idk_slider
					SET	slider_sort = :slider_sort, slider_status = :slider_status
					WHERE slider_id = :slider_id");

	$query->execute(array(
				':slider_sort' => $slider_sort,
				':slider_status' => $slider_status,
				':slider_id' => $slider_id));


	$slider_lang_langid = $_POST['slider_lang_langid'];
	$slider_lang_title = $_POST['slider_lang_title'];
	$slider_lang_desc = $_POST['slider_lang_desc'];
	$slider_lang_link = $_POST['slider_lang_link'];

	//Update db
	$query = $db->prepare("
					UPDATE idk_slider_lang
					SET	slider_lang_title = :slider_lang_title, slider_lang_desc = :slider_lang_desc, slider_lang_img = :slider_lang_img, slider_lang_link = :slider_lang_link
					WHERE slider_lang_langid = :slider_lang_langid AND slider_lang_sliderid = :slider_lang_sliderid");

	$query->execute(array(
				':slider_lang_title' => $slider_lang_title,
				':slider_lang_desc' => $slider_lang_desc,
				':slider_lang_img' => $file_name_new,
				':slider_lang_link' => $slider_lang_link,
				':slider_lang_langid' => $slider_lang_langid,
				':slider_lang_sliderid' => $slider_id));

	//Add to LOGS
	$log_desc = "Uredio slider: " . $slider_lang_title . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: slider?page=list&mess=2");

break;

case "edit_lang_bs":

	$text_lang_bs = $_POST['text_lang_bs'];

	$file = "../langs/bs.php";
	$Saved_File = fopen($file, 'w+');
	fwrite($Saved_File, $text_lang_bs);
	fclose($Saved_File);

	header("Location: settings?page=language&mess=1");

break;

case "edit_lang_sr":

	$text_lang_sr = $_POST['text_lang_sr'];

	$file = "../langs/sr.php";
	$Saved_File = fopen($file, 'w+');
	fwrite($Saved_File, $text_lang_sr);
	fclose($Saved_File);

	header("Location: settings?page=language&mess=1");

break;

case "edit_lang_en":

	$text_lang_en = $_POST['text_lang_en'];

	$file = "../langs/en.php";
	$Saved_File = fopen($file, 'w+');
	fwrite($Saved_File, $text_lang_en);
	fclose($Saved_File);

	header("Location: settings?page=language&mess=2");

break;

case "edit_lang_de":

	$text_lang_de = $_POST['text_lang_de'];

	$file = "../langs/de.php";
	$Saved_File = fopen($file, 'w+');
	fwrite($Saved_File, $text_lang_de);
	fclose($Saved_File);

	header("Location: settings?page=language&mess=3");

break;

case "add_settings_contact":

	$settings_form_variable = $_POST['settings_form_variable'];

	$query = $db->prepare("
					INSERT INTO idk_settings_form
						(settings_form_variable, settings_form_type, settings_form_group)
					VALUES
						(:settings_form_variable, :settings_form_type, :settings_form_group)");

	$query->execute(array(
				':settings_form_variable' => $settings_form_variable,
				':settings_form_type' => 1,
				':settings_form_group' => 1));

	$update_id = $db->lastInsertId();

	$settings_form_lang_name = $_POST['settings_form_lang_name'];
	$settings_form_lang_value = $_POST['settings_form_lang_value'];

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$settings_form_lang_langid = $lang_row['lang_id'];

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_settings_form_lang
							(settings_form_lang_settingsformid, settings_form_lang_langid, settings_form_lang_name, settings_form_lang_value)
						VALUES
							(:settings_form_lang_settingsformid, :settings_form_lang_langid, :settings_form_lang_name, :settings_form_lang_value)");

		$query->execute(array(
					':settings_form_lang_settingsformid' => $update_id,
					':settings_form_lang_langid' => $settings_form_lang_langid,
					':settings_form_lang_name' => $settings_form_lang_name,
					':settings_form_lang_value' => $settings_form_lang_value));
	}

	//Add to LOGS
	$log_desc = "Dodao novu kontakt informaciju: " . $settings_form_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: settings?page=settings-contact&mess=1");

break;

case "edit_settings_contact":

	$settings_form_id = $_POST['settings_form_id'];
	$settings_form_lang_langid = $_POST['settings_form_lang_langid'];
	$settings_form_lang_name = $_POST['settings_form_lang_name'];
	$settings_form_lang_value = $_POST['settings_form_lang_value'];

	//Update db
	$query = $db->prepare("
					UPDATE idk_settings_form_lang
					SET	settings_form_lang_name = :settings_form_lang_name, settings_form_lang_value = :settings_form_lang_value
					WHERE settings_form_lang_langid = :settings_form_lang_langid AND settings_form_lang_settingsformid = :settings_form_lang_settingsformid");

	$query->execute(array(
				':settings_form_lang_name' => $settings_form_lang_name,
				':settings_form_lang_value' => $settings_form_lang_value,
				':settings_form_lang_langid' => $settings_form_lang_langid,
				':settings_form_lang_settingsformid' => $settings_form_id));

	//Add to LOGS
	$log_desc = "Uredio kontakt informaciju: " . $settings_form_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: settings?page=settings-contact&mess=2");

break;

case "add_testimonial":

	//Upload and save testimonial_img
	if($_FILES['testimonial_img']['size'] !== 0) {
		$testimonial_img = $_FILES['testimonial_img'];

		//File properties
		$file_name = $testimonial_img['name'];
		$file_tmp = $testimonial_img['tmp_name'];
		$file_size = $testimonial_img['size'];
		$file_error = $testimonial_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/testimonials/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/testimonials/";
					$path_to_thumbs_directory = "../files/testimonials/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/testimonials/";
					$path_to_thumbs_directory = "../files/testimonials/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = NULL;
	}

	$testimonial_date = date('Y-m-d');
	if(isset($_POST['testimonial_featured'])){ $testimonial_featured = $_POST['testimonial_featured']; }else{ $testimonial_featured = NULL; }
	$testimonial_status = $_POST['testimonial_status'];

	$query = $db->prepare("
					INSERT INTO idk_testimonials
						(testimonial_img, testimonial_date, testimonial_featured, testimonial_status)
					VALUES
						(:testimonial_img, :testimonial_date, :testimonial_featured, :testimonial_status)");

	$query->execute(array(
				':testimonial_img' => $file_name_new,
				':testimonial_date' => $testimonial_date,
				':testimonial_featured' => $testimonial_featured,
				':testimonial_status' => $testimonial_status));

	$update_id = $db->lastInsertId();

	$testimonial_lang_name = $_POST['testimonial_lang_name'];
	$testimonial_lang_txt = $_POST['testimonial_lang_txt'];
	$testimonial_lang_location = $_POST['testimonial_lang_location'];

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$testimonial_lang_langid = $lang_row['lang_id'];

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_testimonials_lang
							(testimonial_lang_testimonialid, testimonial_lang_langid, testimonial_lang_name, testimonial_lang_txt, testimonial_lang_location)
						VALUES
							(:testimonial_lang_testimonialid, :testimonial_lang_langid, :testimonial_lang_name, :testimonial_lang_txt, :testimonial_lang_location)");

		$query->execute(array(
					':testimonial_lang_testimonialid' => $update_id,
					':testimonial_lang_langid' => $testimonial_lang_langid,
					':testimonial_lang_name' => $testimonial_lang_name,
					':testimonial_lang_txt' => $testimonial_lang_txt,
					':testimonial_lang_location' => $testimonial_lang_location));
	}

	//Add to LOGS
	$log_desc = "Dodao novu izjavu korisnika: " . $testimonial_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: testimonials?page=list&mess=1");

break;

case "edit_testimonial":

	$testimonial_id = $_POST['testimonial_id'];

	//Upload and save testimonial_img
	if($_FILES['testimonial_img']['size'] !== 0) {

		$testimonial_img = $_FILES['testimonial_img'];

		//File properties
		$file_name = $testimonial_img['name'];
		$file_tmp = $testimonial_img['tmp_name'];
		$file_size = $testimonial_img['size'];
		$file_error = $testimonial_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/testimonials/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/testimonials/";
					$path_to_thumbs_directory = "../files/testimonials/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/testimonials/";
					$path_to_thumbs_directory = "../files/testimonials/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = $_POST['testimonial_img_input'];
	}

	$testimonial_featured = $_POST['testimonial_featured'];
	$testimonial_status = $_POST['testimonial_status'];

	$query = $db->prepare("
					UPDATE idk_testimonials
					SET	testimonial_img = :testimonial_img, testimonial_featured = :testimonial_featured, testimonial_status = :testimonial_status
					WHERE testimonial_id = :testimonial_id");

	$query->execute(array(
				':testimonial_img' => $file_name_new,
				':testimonial_featured' => $testimonial_featured,
				':testimonial_status' => $testimonial_status,
				':testimonial_id' => $testimonial_id));


	$testimonial_lang_langid = $_POST['testimonial_lang_langid'];
	$testimonial_lang_name = $_POST['testimonial_lang_name'];
	$testimonial_lang_txt = $_POST['testimonial_lang_txt'];
	$testimonial_lang_location = $_POST['testimonial_lang_location'];

	//Update db
	$query = $db->prepare("
					UPDATE idk_testimonials_lang
					SET	testimonial_lang_name = :testimonial_lang_name, testimonial_lang_txt = :testimonial_lang_txt, testimonial_lang_location = :testimonial_lang_location
					WHERE testimonial_lang_langid = :testimonial_lang_langid AND testimonial_lang_testimonialid = :testimonial_lang_testimonialid");

	$query->execute(array(
				':testimonial_lang_name' => $testimonial_lang_name,
				':testimonial_lang_txt' => $testimonial_lang_txt,
				':testimonial_lang_location' => $testimonial_lang_location,
				':testimonial_lang_langid' => $testimonial_lang_langid,
				':testimonial_lang_testimonialid' => $testimonial_id));

	//Add to LOGS
	$log_desc = "Uredio izjavu korisnika: " . $testimonial_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: testimonials?page=list&mess=2");

break;

case "add_team":

	//Upload and save team_img
	if($_FILES['team_img']['size'] !== 0) {
		$team_img = $_FILES['team_img'];

		//File properties
		$file_name = $team_img['name'];
		$file_tmp = $team_img['tmp_name'];
		$file_size = $team_img['size'];
		$file_error = $team_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/team/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/team/";
					$path_to_thumbs_directory = "../files/team/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/team/";
					$path_to_thumbs_directory = "../files/team/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = NULL;
	}

	$team_email = $_POST['team_email'];
	$team_phone = $_POST['team_phone'];
	$team_social = $_POST['team_social'];
	$team_sort = $_POST['team_sort'];
	$team_status = $_POST['team_status'];

	$query = $db->prepare("
					INSERT INTO idk_team
						(team_img, team_email, team_phone, team_social, team_sort, team_status)
					VALUES
						(:team_img, :team_email, :team_phone, :team_social, :team_sort, :team_status)");

	$query->execute(array(
				':team_img' => $file_name_new,
				':team_email' => $team_email,
				':team_phone' => $team_phone,
				':team_social' => $team_social,
				':team_sort' => $team_sort,
				':team_status' => $team_status));

	$update_id = $db->lastInsertId();

	$team_lang_fullname = $_POST['team_lang_fullname'];
	$team_lang_position = $_POST['team_lang_position'];

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$team_lang_langid = $lang_row['lang_id'];

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_team_lang
							(team_lang_teamid, team_lang_langid, team_lang_fullname, team_lang_position)
						VALUES
							(:team_lang_teamid, :team_lang_langid, :team_lang_fullname, :team_lang_position)");

		$query->execute(array(
					':team_lang_teamid' => $update_id,
					':team_lang_langid' => $team_lang_langid,
					':team_lang_fullname' => $team_lang_fullname,
					':team_lang_position' => $team_lang_position));
	}

	//Add to LOGS
	$log_desc = "Dodao novog člana tima: " . $team_lang_fullname . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: team?page=list&mess=1");

break;

case "edit_team":

	$team_id = $_POST['team_id'];

	//Upload and save team_img
	if($_FILES['team_img']['size'] !== 0) {

		$team_img = $_FILES['team_img'];

		//File properties
		$file_name = $team_img['name'];
		$file_tmp = $team_img['tmp_name'];
		$file_size = $team_img['size'];
		$file_error = $team_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/team/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/team/";
					$path_to_thumbs_directory = "../files/team/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/team/";
					$path_to_thumbs_directory = "../files/team/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = $_POST['team_img_input'];
	}

	$team_email = $_POST['team_email'];
	$team_phone = $_POST['team_phone'];
	$team_social = $_POST['team_social'];
	$team_sort = $_POST['team_sort'];
	$team_status = $_POST['team_status'];

	$query = $db->prepare("
					UPDATE idk_team
					SET	team_img = :team_img, team_email = :team_email, team_phone = :team_phone, team_social = :team_social, team_sort = :team_sort, team_status = :team_status
					WHERE team_id = :team_id");

	$query->execute(array(
				':team_img' => $file_name_new,
				':team_email' => $team_email,
				':team_phone' => $team_phone,
				':team_social' => $team_social,
				':team_sort' => $team_sort,
				':team_status' => $team_status,
				':team_id' => $team_id));


	$team_lang_langid = $_POST['team_lang_langid'];
	$team_lang_fullname = $_POST['team_lang_fullname'];
	$team_lang_position = $_POST['team_lang_position'];

	//Update db
	$query = $db->prepare("
					UPDATE idk_team_lang
					SET	team_lang_fullname = :team_lang_fullname, team_lang_position = :team_lang_position
					WHERE team_lang_langid = :team_lang_langid AND team_lang_teamid = :team_lang_teamid");

	$query->execute(array(
				':team_lang_fullname' => $team_lang_fullname,
				':team_lang_position' => $team_lang_position,
				':team_lang_langid' => $team_lang_langid,
				':team_lang_teamid' => $team_id));

	//Add to LOGS
	$log_desc = "Uredio člana tima: " . $team_lang_fullname . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: team?page=list&mess=2");

break;

case "add_catalog":

	$catalog_sort = $_POST['catalog_sort'];
	$catalog_date = date('Y-m-d');
	$catalog_status = $_POST['catalog_status'];

	$query = $db->prepare("
					INSERT INTO idk_catalogs
						(catalog_sort, catalog_date, catalog_status)
					VALUES
						(:catalog_sort, :catalog_date, :catalog_status)");

	$query->execute(array(
				':catalog_sort' => $catalog_sort,
				':catalog_date' => $catalog_date,
				':catalog_status' => $catalog_status));

	$update_id = $db->lastInsertId();

	//Upload and save catalog_lang_img
	if($_FILES['catalog_lang_img']['size'] !== 0) {
		$catalog_lang_img = $_FILES['catalog_lang_img'];

		//File properties
		$file_name = $catalog_lang_img['name'];
		$file_tmp = $catalog_lang_img['tmp_name'];
		$file_size = $catalog_lang_img['size'];
		$file_error = $catalog_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/catalogs/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/catalogs/";
					$path_to_thumbs_directory = "../files/catalogs/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

					//Thumbs
					$path_to_image_directory = "../files/catalogs/";
					$path_to_thumbs_directory = "../files/catalogs/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = NULL;
	}

	//add file
	$catalog_lang_file = $_FILES['catalog_lang_file'];

	//File properties
	$file_name = $catalog_lang_file['name'];
	$file_tmp = $catalog_lang_file['tmp_name'];

	//File extension
	$file_ext = explode('.', $file_name);
	$file_ext = strtolower(end($file_ext));

	$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

	if(in_array($file_ext, $allowed)) {

		$catalog_lang_file_name_new = uniqid() . '.' . $file_ext;
		$file_destination = "../files/catalogs-document/" . $catalog_lang_file_name_new;

		if(move_uploaded_file($file_tmp, $file_destination)){}
	}

	$catalog_lang_name = $_POST['catalog_lang_name'];

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$catalog_lang_langid = $lang_row['lang_id'];

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_catalogs_lang
							(catalog_lang_catalogid, catalog_lang_langid, catalog_lang_name, catalog_lang_file, catalog_lang_img)
						VALUES
							(:catalog_lang_catalogid, :catalog_lang_langid, :catalog_lang_name, :catalog_lang_file, :catalog_lang_img)");

		$query->execute(array(
					':catalog_lang_catalogid' => $update_id,
					':catalog_lang_langid' => $catalog_lang_langid,
					':catalog_lang_name' => $catalog_lang_name,
					':catalog_lang_file' => $catalog_lang_file_name_new,
					':catalog_lang_img' => $file_name_new));
	}

	//Add to LOGS
	$log_desc = "Dodao novi katalog: " . $catalog_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: catalogs?page=list&mess=1");

break;

case "edit_catalog":

	$catalog_id = $_POST['catalog_id'];
	$catalog_sort = $_POST['catalog_sort'];
	$catalog_date = date('Y-m-d');
	$catalog_status = $_POST['catalog_status'];

	$query = $db->prepare("
					UPDATE idk_catalogs
					SET	catalog_sort = :catalog_sort, catalog_date = :catalog_date, catalog_status = :catalog_status
					WHERE catalog_id = :catalog_id");

	$query->execute(array(
				':catalog_sort' => $catalog_sort,
				':catalog_date' => $catalog_date,
				':catalog_status' => $catalog_status,
				':catalog_id' => $catalog_id));

	//Upload and save catalog_lang_img
	if($_FILES['catalog_lang_img']['size'] !== 0) {

		$catalog_lang_img = $_FILES['catalog_lang_img'];

		//File properties
		$file_name = $catalog_lang_img['name'];
		$file_tmp = $catalog_lang_img['tmp_name'];
		$file_size = $catalog_lang_img['size'];
		$file_error = $catalog_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/catalogs/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/catalogs/";
					$path_to_thumbs_directory = "../files/catalogs/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

					//Thumbs
					$path_to_image_directory = "../files/catalogs/";
					$path_to_thumbs_directory = "../files/catalogs/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = $_POST['catalog_lang_img_input'];
	}

	//Add file
	if($_FILES['catalog_lang_file']['size'] !== 0) {
		$catalog_lang_file = $_FILES['catalog_lang_file'];

		//File properties
		$file_name = $catalog_lang_file['name'];
		$file_tmp = $catalog_lang_file['tmp_name'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

		if(in_array($file_ext, $allowed)) {

			$catalog_lang_file_name_new = uniqid() . '.' . $file_ext;
			$file_destination = "../files/catalogs-document/" . $catalog_lang_file_name_new;

			if(move_uploaded_file($file_tmp, $file_destination)){}
		}
	}else{
		$catalog_lang_file_name_new = $_POST['catalog_lang_file_input'];
	}

	$catalog_lang_langid = $_POST['catalog_lang_langid'];
	$catalog_lang_name = $_POST['catalog_lang_name'];

	//Update db
	$query = $db->prepare("
					UPDATE idk_catalogs_lang
					SET	catalog_lang_name = :catalog_lang_name, catalog_lang_file = :catalog_lang_file, catalog_lang_img = :catalog_lang_img
					WHERE catalog_lang_langid = :catalog_lang_langid AND catalog_lang_catalogid = :catalog_lang_catalogid");

	$query->execute(array(
				':catalog_lang_name' => $catalog_lang_name,
				':catalog_lang_file' => $catalog_lang_file_name_new,
				':catalog_lang_img' => $file_name_new,
				':catalog_lang_langid' => $catalog_lang_langid,
				':catalog_lang_catalogid' => $catalog_id));

	//Add to LOGS
	$log_desc = "Uredio katalog: " . $catalog_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: catalogs?page=list&mess=2");

break;

case "add_jobstype":

	$jtype_status = $_POST['jtype_status'];

	$query = $db->prepare("
					INSERT INTO idk_jobs_type
						(jtype_status)
					VALUES
						(:jtype_status)");

	$query->execute(array(
				':jtype_status' => $jtype_status));

	$update_id = $db->lastInsertId();

	$jtype_lang_name = $_POST['jtype_lang_name'];

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$jtype_lang_langid = $lang_row['lang_id'];

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_jobs_type_lang
							(jtype_lang_langid, jtype_lang_jtypeid, jtype_lang_name)
						VALUES
							(:jtype_lang_langid, :jtype_lang_jtypeid, :jtype_lang_name)");

		$query->execute(array(
					':jtype_lang_langid' => $jtype_lang_langid,
					':jtype_lang_jtypeid' => $update_id,
					':jtype_lang_name' => $jtype_lang_name));
	}

	//Add to LOGS
	$log_desc = "Dodao novo zanimanje: " . $jtype_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: jobs-type?page=list&mess=1");

break;

case "edit_jobstype":

	//Add content to db
	$jtype_id = $_POST['jtype_id'];
	$jtype_status = $_POST['jtype_status'];

	$query = $db->prepare("
					UPDATE idk_jobs_type
					SET	jtype_status = :jtype_status
					WHERE jtype_id = :jtype_id");

	$query->execute(array(
				':jtype_status' => $jtype_status,
				':jtype_id' => $jtype_id));


	$jtype_lang_langid = $_POST['jtype_lang_langid'];
	$jtype_lang_name = $_POST['jtype_lang_name'];

	//Update db
	$query = $db->prepare("
					UPDATE idk_jobs_type_lang
					SET	jtype_lang_name = :jtype_lang_name
					WHERE jtype_lang_langid = :jtype_lang_langid AND jtype_lang_jtypeid = :jtype_lang_jtypeid");

	$query->execute(array(
				':jtype_lang_name' => $jtype_lang_name,
				':jtype_lang_langid' => $jtype_lang_langid,
				':jtype_lang_jtypeid' => $jtype_id));

	//Add to LOGS
	$log_desc = "Uredio zanimanje: " . $jtype_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: jobs-type?page=list&mess=2");

break;

case "add_jobslocation":

	$jlocation_status = $_POST['jlocation_status'];

	$query = $db->prepare("
					INSERT INTO idk_jobs_locations
						(jlocation_status)
					VALUES
						(:jlocation_status)");

	$query->execute(array(
				':jlocation_status' => $jlocation_status));

	$update_id = $db->lastInsertId();

	$jlocation_lang_name = $_POST['jlocation_lang_name'];

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$jlocation_lang_langid = $lang_row['lang_id'];

		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_jobs_location_langs
							(jlocation_lang_langid, jlocation_lang_locationid, jlocation_lang_name)
						VALUES
							(:jlocation_lang_langid, :jlocation_lang_locationid, :jlocation_lang_name)");

		$query->execute(array(
					':jlocation_lang_langid' => $jlocation_lang_langid,
					':jlocation_lang_locationid' => $update_id,
					':jlocation_lang_name' => $jlocation_lang_name));
	}

	//Add to LOGS
	$log_desc = "Dodao novu lokaciju: " . $jlocation_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: jobs-locations?page=list&mess=1");

break;

case "edit_jobslocation":

	//Add content to db
	$jlocation_id = $_POST['jlocation_id'];
	$jlocation_status = $_POST['jlocation_status'];

	$query = $db->prepare("
					UPDATE idk_jobs_locations
					SET	jlocation_status = :jlocation_status
					WHERE jlocation_id = :jlocation_id");

	$query->execute(array(
				':jlocation_status' => $jlocation_status,
				':jlocation_id' => $jlocation_id));


	$jlocation_lang_langid = $_POST['jlocation_lang_langid'];
	$jlocation_lang_name = $_POST['jlocation_lang_name'];

	//Update db
	$query = $db->prepare("
					UPDATE idk_jobs_location_langs
					SET	jlocation_lang_name = :jlocation_lang_name
					WHERE jlocation_lang_langid = :jlocation_lang_langid AND jlocation_lang_locationid = :jlocation_lang_locationid");

	$query->execute(array(
				':jlocation_lang_name' => $jlocation_lang_name,
				':jlocation_lang_langid' => $jlocation_lang_langid,
				':jlocation_lang_locationid' => $jlocation_id));

	//Add to LOGS
	$log_desc = "Uredio lokaciju: " . $jlocation_lang_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: jobs-locations?page=list&mess=2");

break;

case "add_job":

	$job_typeid = $_POST['job_typeid'];
	$job_locationid = $_POST['job_locationid'];
	$job_date = date('Y-m-d');
	$job_status = $_POST['job_status'];
	$job_app_form = $_POST['job_app_form'];

	$query = $db->prepare("
					INSERT INTO idk_jobs
						(job_typeid, job_locationid, job_date, job_status, job_application_form_link)
					VALUES
						(:job_typeid, :job_locationid, :job_date, :job_status, :job_application_form_link)");

	$query->execute(array(
				':job_typeid' => $job_typeid,
				':job_locationid' => $job_locationid,
				':job_date' => $job_date,
				':job_status' => $job_status,
				':job_application_form_link' => $job_app_form));

	$update_id = $db->lastInsertId();

	$job_lang_title = $_POST['job_lang_title'];
	$job_lang_content = $_POST['job_lang_content'];
	$job_lang_salary = $_POST['job_lang_salary'];
	$job_lang_accommodation = $_POST['job_lang_accommodation'];

	//Upload and save job_lang_img
	if($_FILES['job_lang_img']['size'] !== 0) {
		$job_lang_img = $_FILES['job_lang_img'];

		//File properties
		$file_name = $job_lang_img['name'];
		$file_tmp = $job_lang_img['tmp_name'];
		$file_size = $job_lang_img['size'];
		$file_error = $job_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/jobs/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/jobs/";
					$path_to_thumbs_directory = "../files/jobs/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/jobs/";
					$path_to_thumbs_directory = "../files/jobs/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = NULL;
	}

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_id, lang_jobs
						FROM idk_langs");

	$lang_query->execute();

	while($lang_row = $lang_query->fetch()) {

		$job_lang_langid = $lang_row['lang_id'];
		$lang_jobs = $lang_row['lang_jobs'];

		$slug_name = create_slug($job_lang_title);
		$job_lang_url = "" . $lang_jobs . "/" . $slug_name . "/" . $update_id . "";


		//Add to db
		$query = $db->prepare("
						INSERT INTO idk_jobs_lang
							(job_lang_langid, job_lang_jobid, job_lang_url, job_lang_title, job_lang_img, job_lang_content, job_lang_salary, job_lang_accommodation)
						VALUES
							(:job_lang_langid, :job_lang_jobid, :job_lang_url, :job_lang_title, :job_lang_img, :job_lang_content, :job_lang_salary, :job_lang_accommodation)");

		$query->execute(array(
					':job_lang_langid' => $job_lang_langid,
					':job_lang_jobid' => $update_id,
					':job_lang_url' => $job_lang_url,
					':job_lang_title' => $job_lang_title,
					':job_lang_img' => $file_name_new,
					':job_lang_content' => $job_lang_content,
					':job_lang_salary' => $job_lang_salary,
					':job_lang_accommodation' => $job_lang_accommodation));
	}

	//Add to LOGS
	$log_desc = "Dodao novi posao: " . $job_lang_title . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: jobs?page=list&mess=1");

break;

case "edit_job":

	$job_id = $_POST['job_id'];
	$job_lang_langid = $_POST['job_lang_langid'];
	$job_typeid = $_POST['job_typeid'];
	$job_locationid = $_POST['job_locationid'];
	$job_status = $_POST['job_status'];
	$job_app_form = $_POST['job_app_form'];

	$query = $db->prepare("
					UPDATE idk_jobs
					SET	job_typeid = :job_typeid, job_locationid = :job_locationid, job_status = :job_status, job_application_form_link = :job_application_form_link
					WHERE job_id = :job_id");

	$query->execute(array(
				':job_typeid' => $job_typeid,
				':job_locationid' => $job_locationid,
				':job_status' => $job_status,
				':job_application_form_link' => $job_app_form,
				':job_id' => $job_id));


	$job_lang_langid = $_POST['job_lang_langid'];
	$job_lang_title = $_POST['job_lang_title'];
	$job_lang_content = $_POST['job_lang_content'];

	//Upload and save job_lang_img
	if($_FILES['job_lang_img']['size'] !== 0) {

		$job_lang_img = $_FILES['job_lang_img'];

		//File properties
		$file_name = $job_lang_img['name'];
		$file_tmp = $job_lang_img['tmp_name'];
		$file_size = $job_lang_img['size'];
		$file_error = $job_lang_img['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {
			if($file_error === 0) {

				$file_name_new = uniqid('', true) . '.' . $file_ext;
				$file_destination = '../files/jobs/' . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "../files/jobs/";
					$path_to_thumbs_directory = "../files/jobs/";
					$final_width_of_image = 1800;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);


					//Thumbs
					$path_to_image_directory = "../files/jobs/";
					$path_to_thumbs_directory = "../files/jobs/thumbs/";
					$final_width_of_image = 600;

					if(preg_match('/[.](jpg)$/', $file_name_new)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](gif)$/', $file_name_new)) {
						$im = imagecreatefromgif($path_to_image_directory . $file_name_new);
					} else if (preg_match('/[.](png)$/', $file_name_new)) {
						$im = imagecreatefrompng($path_to_image_directory . $file_name_new);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);

					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));

					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

					if(!file_exists($path_to_thumbs_directory)) {
						if(!mkdir($path_to_thumbs_directory)) {
							die("Greska! Pokusajte ponovo.");
						}
					}

					imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);

				}
			}
		}
	}else{
		$file_name_new = $_POST['job_lang_img_input'];
	}

	//add for all languages
	$lang_query = $db->prepare("
						SELECT lang_jobs
						FROM idk_langs
						WHERE lang_id = :lang_id");

	$lang_query->execute(array(
					':lang_id' => $job_lang_langid));

	$lang_row = $lang_query->fetch();

		$lang_jobs = $lang_row['lang_jobs'];

		$slug_name = create_slug($job_lang_title);
		$job_lang_url = "" . $lang_jobs . "/" . $slug_name . "/" . $job_id . "";

	//Update db
	$query = $db->prepare("
					UPDATE idk_jobs_lang
					SET	job_lang_url = :job_lang_url, job_lang_title = :job_lang_title, job_lang_img = :job_lang_img, job_lang_content = :job_lang_content
					WHERE job_lang_langid = :job_lang_langid AND job_lang_jobid = :job_lang_jobid");

	$query->execute(array(
				':job_lang_url' => $job_lang_url,
				':job_lang_title' => $job_lang_title,
				':job_lang_img' => $file_name_new,
				':job_lang_content' => $job_lang_content,
				':job_lang_langid' => $job_lang_langid,
				':job_lang_jobid' => $job_id));

	//Add to LOGS
	$log_desc = "Uredio posao: " . $job_lang_title . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_userid, log_desc, log_date)
					VALUES
						(:log_userid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_userid' => $logged_user_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: jobs?page=list&mess=2");

break;

}
}

?>
