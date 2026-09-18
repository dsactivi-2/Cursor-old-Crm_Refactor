<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: tasks?page=list&sort=new");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Zadaci | <?php getTitle(); ?></title>

	<?php include('includes/head.php'); 
	if (in_array($getUserIp, $getIpWhiteList)){
	?>

</head>
<body>
	<header>
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('menu.php'); ?>
	</div>
	<div id="content">
		<div class="container-fluid">
		<?php
			switch ($page){

				case "list":

					//Sort filter
					if(isset($_REQUEST["sort"])) {
						$sort = $_REQUEST["sort"];
					}else{
						$sort = "empty";
					}

					if($sort == "new"){
						$where = "WHERE task_status = '1' AND (task_assignedid = :task_assignedid OR task_employeeid = :task_employeeid)";
					}elseif($sort == "completed"){
						$where = "WHERE task_status = '2' AND (task_assignedid = :task_assignedid OR task_employeeid = :task_employeeid)";
					}elseif($sort == "notcompleted"){
						$where = "WHERE task_status = '3' AND (task_assignedid = :task_assignedid OR task_employeeid = :task_employeeid)";
					}else{
						$where = "WHERE task_assignedid = :task_assignedid OR task_employeeid = :task_employeeid";
					}
		?>
                    <div class="row">
                        <div class="col-xs-12">
                            <h1><i class="fa fa-tasks idk_color_green" aria-hidden="true"></i> Moji zadaci</h1>
                        </div>
                        <div class="col-xs-12">
                            <hr />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="content_box">
                                <div class="row">
                                    <div class="col-xs-12 text-right">
                                        <form>
                                            <ul class="list-inline">
                                                <li>Status:</li>
                                                <li>
                                                    <select id="dynamic_select" class="form-control">
                                                        <option value="tasks?page=list&sort=all" <?php if($sort == "all"){ echo 'selected'; }; ?>>Svi zadaci</option>
                                                        <option value="tasks?page=list&sort=new" <?php if($sort == "new"){ echo 'selected'; }; ?>>Novi zadaci</option>
                                                        <option value="tasks?page=list&sort=completed" <?php if($sort == "completed"){ echo 'selected'; }; ?>>Završeni zadaci</option>
                                                        <option value="tasks?page=list&sort=notcompleted" <?php if($sort == "notcompleted"){ echo 'selected'; }; ?>>Ne završeni zadaci</option>
                                                    </select>
                                                    <script>
                                                        $(function(){
                                                          // bind change event to select
                                                          $('#dynamic_select').on('change', function () {
                                                              var url = $(this).val(); // get selected value
                                                              if (url) { // require a URL
                                                                  window.location = url; // redirect
                                                              }
                                                              return false;
                                                          });
                                                        });
                                                    </script>
                                                </li>
                                            </ul>
                                        </form>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <ul class="cbp_tmtimeline">
                                            <?php

                                                $query_tasks = $db->prepare("
                                                                        SELECT task_id, task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_employeeid, task_datetime, task_status, task_group, task_dataid, assigned.employee_firstname AS assigned_firstname, assigned.employee_lastname AS assigned_lastname, employee.employee_firstname AS added_firstname, employee.employee_lastname AS added_lastname
                                                                        FROM idk_tasks
                                                                        INNER JOIN idk_employees assigned ON idk_tasks.task_assignedid = assigned.employee_id
                                                                        INNER JOIN idk_employees employee ON idk_tasks.task_employeeid = employee.employee_id
                                                                        $where
                                                                        ORDER BY task_status ASC, task_duedatetime DESC, task_id DESC");

                                                $query_tasks->execute(array(
													':task_assignedid' => $logged_employee_id,
													':task_employeeid' => $logged_employee_id));

                                                while($row_tasks = $query_tasks->fetch()){

                                                    $task_id = $row_tasks['task_id'];
                                                    $task_txt = $row_tasks['task_txt'];
                                                    $task_replytxt = $row_tasks['task_replytxt'];
                                                    $task_emailnotifi = $row_tasks['task_emailnotifi'];
                                                    $task_repeatnotifi = $row_tasks['task_repeatnotifi'];
                                                    $assigned_firstname = $row_tasks['assigned_firstname'];
                                                    $assigned_lastname = $row_tasks['assigned_lastname'];
                                                    $added_firstname = $row_tasks['added_firstname'];
                                                    $added_lastname = $row_tasks['added_lastname'];
                                                    $task_dataid = $row_tasks['task_dataid'];
                                                    $task_duedatetime_format = date('d.m.Y. - H:i', strtotime($row_tasks['task_duedatetime']));
                                                    $task_duedatetime = date('Y-m-d H:i:s', strtotime($row_tasks['task_duedatetime']));

                                                    if($row_tasks['task_status'] == 1 AND (new DateTime() >= new DateTime($task_duedatetime))){
                                                        $task_status = 'style="background-color: #f2a12e; color: #fff;"';
                                                        $task_icon = '<i class="fa fa-clock-o" aria-hidden="true"></i>';
                                                    }elseif($row_tasks['task_status'] == 1){
                                                        $task_status = 'style="background-color: #ddd; color: #fff;"';
                                                        $task_icon = '<i class="fa fa-check" aria-hidden="true"></i>';
                                                    }elseif($row_tasks['task_status'] == 2){
                                                        $task_status = 'style="color: #fff;"';
                                                        $task_icon = '<i class="fa fa-check" aria-hidden="true"></i>';
                                                    }elseif($row_tasks['task_status'] == 3){
                                                        $task_status = 'style="background-color: #f3413c; color: #fff;"';
                                                        $task_icon = '<i class="fa fa-times" aria-hidden="true"></i>';
                                                    }elseif($row_tasks['task_status'] == 4){
                                                        $task_status = '<i class="fa fa-sticky-note" aria-hidden="true"></i>';
                                                    }

													//task_dataid text
													if($row_tasks['task_group'] == '1'){
														//Contacts
														$query_group = $db->prepare("
		                                                                        SELECT contact_id, contact_firstname, contact_lastname
		                                                                        FROM idk_contacts
		                                                                        WHERE contact_id = :contact_id");

		                                                $query_group->execute(array(
																		':contact_id' => $task_dataid));

		                                                $row_group = $query_group->fetch();

															$task_dataid_txt = 'Kontakt: <a href="contacts?page=open&id=' . $row_group['contact_id'] . '">' . $row_group['contact_firstname'] . ' ' . $row_group['contact_lastname'] . '</a>';

													}elseif($row_tasks['task_group'] == '2'){
														//Employees
														$query_group = $db->prepare("
		                                                                        SELECT employee_id, employee_firstname, employee_lastname
		                                                                        FROM idk_employees
		                                                                        WHERE employee_id = :employee_id");

		                                                $query_group->execute(array(
																		':employee_id' => $task_dataid));

		                                                $row_group = $query_group->fetch();

															$task_dataid_txt = 'Zaposlenik: <a href="employees?page=open&id=' . $row_group['employee_id'] . '">' . $row_group['employee_firstname'] . ' ' . $row_group['employee_lastname'] . '</a>';
													}elseif($row_tasks['task_group'] == '3'){
														//Companies
														$query_group = $db->prepare("
		                                                                        SELECT company_id, company_name
		                                                                        FROM idk_companies
		                                                                        WHERE company_id = :company_id");

		                                                $query_group->execute(array(
																		':company_id' => $task_dataid));

		                                                $row_group = $query_group->fetch();

															$task_dataid_txt = 'Kompanija: <a href="companies?page=open&id=' . $row_group['company_id'] . '">' . $row_group['company_name'] . '</a>';
													}

                                            ?>
                                            <li>
                                                <time class="cbp_tmtime">
													<span data-toggle="tooltip" data-placement="top" title="Zadatak dodao: <?php echo $added_firstname; ?> <?php echo $added_lastname; ?>"><?php echo $assigned_firstname; ?> <?php echo $assigned_lastname; ?></span>
													<span class="idk_time_ago" datetime="<?php echo $task_duedatetime; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $task_duedatetime_format; ?>"></span>
													<br>
													<span><?php echo $task_dataid_txt; ?></span>
												</time>
                                                <div class="cbp_tmicon" <?php echo $task_status; ?> data-toggle="tooltip" data-placement="right" title="<?php echo $task_replytxt; ?>"><?php echo $task_icon; ?></div>
                                                <div class="cbp_tmlabel">
                                                    <div class="row">
                                                        <div class="col-lg-9 col-md-8 col-sm-7">
                                                            <?php echo $task_txt; ?>
                                                        </div>
                                                        <div class="col-lg-3 col-md-4 col-sm-5 text-right">
                                                            <ul class="list-inline">
                                                                <?php if($row_tasks['task_status'] == 1){ ?>
                                                                <li>
                                                                    <a href="#" data="<?php echo $task_id; ?>" data-toggle="modal" data-target="#taskDoneModal" class="task_done btn material-btn material-btn_success main-container__column"><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                    <script>
                                                                        $(".task_done").click(function () {
                                                                            var addressValue = $(this).attr("data");
                                                                            document.getElementById("task_id_done").value = addressValue;
                                                                        });
                                                                    </script>
                                                                    <div class="modal material-modal material-modal_primary fade text-left" id="taskDoneModal">
                                                                        <div class="modal-dialog ">
                                                                            <div class="modal-content material-modal__content">
                                                                                <div class="modal-header material-modal__header">
                                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                                    <h4 class="modal-title material-modal__title">Završi zadatak</h4>
                                                                                </div>
                                                                                <div class="modal-body material-modal__body">
                                                                                    <form action="<?php getSiteURL(); ?>do.php?form=add_task_done" method="post" role="form" class="form-horizontal">
                                                                                        <input type="hidden" name="task_id" id="task_id_done" value="" />
                                                                                        <div class="form-group">
                                                                                            <div class="col-md-offset-2 col-sm-8">
                                                                                                <div class="form-group materail-input-block materail-input-block_success">
                                                                                                    <textarea class="form-control materail-input material-textarea" name="task_replytxt" placeholder="Odgovor na zadatak" rows="6" required></textarea>
                                                                                                    <span class="materail-input-block__line"></span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer material-modal__footer">
                                                                                    <ul class="list-inline">
                                                                                        <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                                                        <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Završi</button></li>
                                                                                    </ul>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <a href="#" data="<?php echo $task_id; ?>" data-toggle="modal" data-target="#taskNotDoneModal" class="task_not_done btn material-btn material-btn_danger main-container__column"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                    <script>
                                                                        $(".task_not_done").click(function () {
                                                                            var addressValue = $(this).attr("data");
                                                                            document.getElementById("task_id_notdone").value = addressValue;
                                                                        });
                                                                    </script>
                                                                    <div class="modal material-modal material-modal_primary fade text-left" id="taskNotDoneModal">
                                                                        <div class="modal-dialog ">
                                                                            <div class="modal-content material-modal__content">
                                                                                <div class="modal-header material-modal__header">
                                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                                    <h4 class="modal-title material-modal__title">Poništi zadatak</h4>
                                                                                </div>
                                                                                <div class="modal-body material-modal__body">
                                                                                    <form action="<?php getSiteURL(); ?>do.php?form=add_task_notdone" method="post" role="form" class="form-horizontal">
                                                                                        <input type="hidden" name="task_id" id="task_id_notdone" value="" />
                                                                                        <div class="form-group">
                                                                                            <div class="col-md-offset-2 col-sm-8">
                                                                                                <div class="form-group materail-input-block materail-input-block_success">
                                                                                                    <textarea class="form-control materail-input material-textarea" name="task_replytxt" placeholder="Odgovor na zadatak" rows="6" required></textarea>
                                                                                                    <span class="materail-input-block__line"></span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer material-modal__footer">
                                                                                    <ul class="list-inline">
                                                                                        <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                                                        <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Poništi</button></li>
                                                                                    </ul>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <?php } ?>
                                                                <li>
                                                                    <a href="#" data="<?php getSiteURL(); ?>contacts?page=del_task&id=<?php echo $task_id; ?>" data-toggle="modal" data-target="#deleteTaskModal" class="delete_task btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                                    <script>
                                                                        $(".delete_task").click(function () {
                                                                            var addressValue = $(this).attr("data");
                                                                            document.getElementById("delete_task_link").href = addressValue;
                                                                        });
                                                                    </script>
                                                                    <!-- Modal -->
                                                                    <div class="modal material-modal material-modal_danger fade text-left" id="deleteTaskModal">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content material-modal__content">
                                                                                <div class="modal-header material-modal__header">
                                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                                    <h4 class="modal-title material-modal__title">Brisanje</h4>
                                                                                </div>
                                                                                <div class="modal-body material-modal__body">
                                                                                    <p>Jeste li sigurni da želite obrisati zadatak?</p>
                                                                                </div>
                                                                                <div class="modal-footer material-modal__footer">
                                                                                    <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                                                                    <a id="delete_task_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                            <hr>
                                                            <ul class="list-inline">
                                                                <?php if($row_tasks['task_status'] == 1){ ?>
                                                                <li>
                                                                    <?php
                                                                        if($task_emailnotifi == 0){
                                                                            echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Isključeno" class="task_emailnotifi1 btn material-btn material-btn_disabled main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_emailnotifi == 1){
                                                                            echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Na dan dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_emailnotifi == 2){
                                                                            echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Dan prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_emailnotifi == 3){
                                                                            echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Dva dana prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_emailnotifi == 4){
                                                                            echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Sedmicu dana prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_emailnotifi == 5){
                                                                            echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Mjesec dana prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                        }
                                                                    ?>
                                                                    <script>
                                                                        $(".task_emailnotifi1").click(function () {
                                                                            var addressValue = $(this).attr("data");
                                                                            document.getElementById("task_id_emailnotifi").value = addressValue;
                                                                        });
                                                                    </script>
                                                                    <div class="modal material-modal material-modal_primary fade text-left" id="taskEmailModal">
                                                                        <div class="modal-dialog ">
                                                                            <div class="modal-content material-modal__content">
                                                                                <div class="modal-header material-modal__header">
                                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                                    <h4 class="modal-title material-modal__title">E-mail napomena</h4>
                                                                                </div>
                                                                                <div class="modal-body material-modal__body">
                                                                                    <form action="<?php getSiteURL(); ?>do.php?form=add_task_emailnotifi" method="post" role="form" class="form-horizontal">
                                                                                        <input type="hidden" name="task_id" id="task_id_emailnotifi" value="" />
                                                                                        <div class="form-group">
                                                                                            <div class="col-md-offset-2 col-sm-8">
                                                                                                <select class="selectpicker" id="task_emailnotifi" name="task_emailnotifi" required>
                                                                                                    <option value="0">Isključeno</option>
                                                                                                    <option value="1">Na dan dospijeća</option>
                                                                                                    <option value="2">Dan prije dospijeća</option>
                                                                                                    <option value="3">Dva dana prije dospijeća</option>
                                                                                                    <option value="4">Sedmicu dana prije dospijeća</option>
                                                                                                    <option value="5">Mjesec dana prije dospijeća</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer material-modal__footer">
                                                                                    <ul class="list-inline">
                                                                                        <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                                                        <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Snimi</button></li>
                                                                                    </ul>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <?php
                                                                        if($task_repeatnotifi == 0){
                                                                            echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Isključeno" class="task_repeatnotifi1 btn material-btn material-btn_disabled main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_repeatnotifi == 1){
                                                                            echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Dnevno" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_repeatnotifi == 2){
                                                                            echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Sedmično" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_repeatnotifi == 3){
                                                                            echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Mjesečno" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_repeatnotifi == 4){
                                                                            echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Godišnje" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                        }
                                                                    ?>
                                                                    <script>
                                                                        $(".task_repeatnotifi1").click(function () {
                                                                            var addressValue = $(this).attr("data");
                                                                            document.getElementById("task_id_repeat").value = addressValue;
                                                                        });
                                                                    </script>
                                                                    <div class="modal material-modal material-modal_primary fade text-left" id="taskRepeatModal">
                                                                        <div class="modal-dialog ">
                                                                            <div class="modal-content material-modal__content">
                                                                                <div class="modal-header material-modal__header">
                                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                                    <h4 class="modal-title material-modal__title">Ponavljaj zadatak</h4>
                                                                                </div>
                                                                                <div class="modal-body material-modal__body">
                                                                                    <form action="<?php getSiteURL(); ?>do.php?form=add_task_repeat" method="post" role="form" class="form-horizontal">
                                                                                        <input type="hidden" name="task_id" id="task_id_repeat" value="" />
                                                                                        <div class="form-group">
                                                                                            <div class="col-md-offset-2 col-sm-8">
                                                                                                <select class="selectpicker" id="task_repeatnotifi" name="task_repeatnotifi" required>
                                                                                                    <option value="0">Isključeno</option>
                                                                                                    <option value="1">Dnevno</option>
                                                                                                    <option value="2">Sedmično</option>
                                                                                                    <option value="3">Mjesečno</option>
                                                                                                    <option value="4">Godišnje</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer material-modal__footer">
                                                                                    <ul class="list-inline">
                                                                                        <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                                                        <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Snimi</button></li>
                                                                                    </ul>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <?php } ?>
                                            <script>
                                                timeago().render($('.idk_time_ago'));
                                            </script>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

        <?php
                break;

				case "list_all":

				if(!empty($employee_supervizor[0]) OR (in_array( "1" , $employee_status))){

					//Sort filter
					if(isset($_REQUEST["sort"])) {
						$sort = $_REQUEST["sort"];
					}else{
						$sort = "empty";
					}

					if($sort == "new"){
						$where = "WHERE task_status = '1'";
					}elseif($sort == "completed"){
						$where = "WHERE task_status = '2'";
					}elseif($sort == "notcompleted"){
						$where = "WHERE task_status = '3'";
					}else{
						$where = "";
					}
			?>
                    <div class="row">
                        <div class="col-xs-12">
                            <h1><i class="fa fa-tasks idk_color_green" aria-hidden="true"></i> Svi zadaci</h1>
                        </div>
                        <div class="col-xs-12">
                            <hr />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="content_box">
                                <div class="row">
                                    <div class="col-xs-12 text-right">
                                        <form>
                                            <ul class="list-inline">
                                                <li>Status:</li>
                                                <li>
                                                    <select id="dynamic_select" class="form-control">
                                                        <option value="tasks?page=list_all&sort=all" <?php if($sort == "all"){ echo 'selected'; }; ?>>Svi zadaci</option>
                                                        <option value="tasks?page=list_all&sort=new" <?php if($sort == "new"){ echo 'selected'; }; ?>>Novi zadaci</option>
                                                        <option value="tasks?page=list_all&sort=completed" <?php if($sort == "completed"){ echo 'selected'; }; ?>>Završeni zadaci</option>
                                                        <option value="tasks?page=list_all&sort=notcompleted" <?php if($sort == "notcompleted"){ echo 'selected'; }; ?>>Ne završeni zadaci</option>
                                                    </select>
                                                    <script>
                                                        $(function(){
                                                          // bind change event to select
                                                          $('#dynamic_select').on('change', function () {
                                                              var url = $(this).val(); // get selected value
                                                              if (url) { // require a URL
                                                                  window.location = url; // redirect
                                                              }
                                                              return false;
                                                          });
                                                        });
                                                    </script>
                                                </li>
                                            </ul>
                                        </form>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <ul class="cbp_tmtimeline">
											<?php

                                                $query_tasks = $db->prepare("
                                                                        SELECT task_id, task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_employeeid, task_datetime, task_status, task_group, task_dataid, assigned.employee_firstname AS assigned_firstname, assigned.employee_lastname AS assigned_lastname, employee.employee_firstname AS added_firstname, employee.employee_lastname AS added_lastname
                                                                        FROM idk_tasks
                                                                        INNER JOIN idk_employees assigned ON idk_tasks.task_assignedid = assigned.employee_id
                                                                        INNER JOIN idk_employees employee ON idk_tasks.task_employeeid = employee.employee_id
                                                                        $where
                                                                        ORDER BY task_status ASC, task_duedatetime DESC, task_id DESC");

                                                $query_tasks->execute(array(
																':task_assignedid' => $logged_employee_id,
																':task_employeeid' => $logged_employee_id));

                                                while($row_tasks = $query_tasks->fetch()){

                                                    $task_id = $row_tasks['task_id'];
                                                    $task_txt = $row_tasks['task_txt'];
                                                    $task_replytxt = $row_tasks['task_replytxt'];
                                                    $task_emailnotifi = $row_tasks['task_emailnotifi'];
                                                    $task_repeatnotifi = $row_tasks['task_repeatnotifi'];
                                                    $assigned_firstname = $row_tasks['assigned_firstname'];
                                                    $assigned_lastname = $row_tasks['assigned_lastname'];
                                                    $added_firstname = $row_tasks['added_firstname'];
                                                    $added_lastname = $row_tasks['added_lastname'];
                                                    $task_dataid = $row_tasks['task_dataid'];
                                                    $task_duedatetime_format = date('d.m.Y. - H:i', strtotime($row_tasks['task_duedatetime']));
                                                    $task_duedatetime = date('Y-m-d H:i:s', strtotime($row_tasks['task_duedatetime']));

                                                    if($row_tasks['task_status'] == 1 AND (new DateTime() >= new DateTime($task_duedatetime))){
                                                        $task_status = 'style="background-color: #f2a12e; color: #fff;"';
                                                        $task_icon = '<i class="fa fa-clock-o" aria-hidden="true"></i>';
                                                    }elseif($row_tasks['task_status'] == 1){
                                                        $task_status = 'style="background-color: #ddd; color: #fff;"';
                                                        $task_icon = '<i class="fa fa-check" aria-hidden="true"></i>';
                                                    }elseif($row_tasks['task_status'] == 2){
                                                        $task_status = 'style="color: #fff;"';
                                                        $task_icon = '<i class="fa fa-check" aria-hidden="true"></i>';
                                                    }elseif($row_tasks['task_status'] == 3){
                                                        $task_status = 'style="background-color: #f3413c; color: #fff;"';
                                                        $task_icon = '<i class="fa fa-times" aria-hidden="true"></i>';
                                                    }elseif($row_tasks['task_status'] == 4){
                                                        $task_status = '<i class="fa fa-sticky-note" aria-hidden="true"></i>';
                                                    }

													//task_dataid text
													if($row_tasks['task_group'] == '1'){
														//Contacts
														$query_group = $db->prepare("
		                                                                        SELECT contact_id, contact_firstname, contact_lastname
		                                                                        FROM idk_contacts
		                                                                        WHERE contact_id = :contact_id");

		                                                $query_group->execute(array(
																		':contact_id' => $task_dataid));

		                                                $row_group = $query_group->fetch();

															$task_dataid_txt = 'Kontakt: <a href="contacts?page=open&id=' . $row_group['contact_id'] . '">' . $row_group['contact_firstname'] . ' ' . $row_group['contact_lastname'] . '</a>';

													}elseif($row_tasks['task_group'] == '2'){
														//Employees
														$query_group = $db->prepare("
		                                                                        SELECT employee_id, employee_firstname, employee_lastname
		                                                                        FROM idk_employees
		                                                                        WHERE employee_id = :employee_id");

		                                                $query_group->execute(array(
																		':employee_id' => $task_dataid));

		                                                $row_group = $query_group->fetch();

															$task_dataid_txt = 'Zaposlenik: <a href="employees?page=open&id=' . $row_group['employee_id'] . '">' . $row_group['employee_firstname'] . ' ' . $row_group['employee_lastname'] . '</a>';
													}elseif($row_tasks['task_group'] == '3'){
														//Companies
														$query_group = $db->prepare("
		                                                                        SELECT company_id, company_name
		                                                                        FROM idk_companies
		                                                                        WHERE company_id = :company_id");

		                                                $query_group->execute(array(
																		':company_id' => $task_dataid));

		                                                $row_group = $query_group->fetch();

															$task_dataid_txt = 'Kompanija: <a href="companies?page=open&id=' . $row_group['company_id'] . '">' . $row_group['company_name'] . '</a>';
													}

                                            ?>
                                            <li>
                                                <time class="cbp_tmtime">
													<span data-toggle="tooltip" data-placement="top" title="Zadatak dodao: <?php echo $added_firstname; ?> <?php echo $added_lastname; ?>"><?php echo $assigned_firstname; ?> <?php echo $assigned_lastname; ?></span>
													<span class="idk_time_ago" datetime="<?php echo $task_duedatetime; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $task_duedatetime_format; ?>"></span>
													<br>
													<span><?php echo $task_dataid_txt; ?></span>
												</time>
                                                <div class="cbp_tmicon" <?php echo $task_status; ?> data-toggle="tooltip" data-placement="right" title="<?php echo $task_replytxt; ?>"><?php echo $task_icon; ?></div>
                                                <div class="cbp_tmlabel">
                                                    <div class="row">
                                                        <div class="col-lg-9 col-md-8 col-sm-7">
                                                            <?php echo $task_txt; ?>
                                                        </div>
                                                        <div class="col-lg-3 col-md-4 col-sm-5 text-right">
                                                            <ul class="list-inline">
                                                                <?php if($row_tasks['task_status'] == 1){ ?>
                                                                <li>
                                                                    <a href="#" data="<?php echo $task_id; ?>" data-toggle="modal" data-target="#taskDoneModal" class="task_done btn material-btn material-btn_success main-container__column"><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                    <script>
                                                                        $(".task_done").click(function () {
                                                                            var addressValue = $(this).attr("data");
                                                                            document.getElementById("task_id_done").value = addressValue;
                                                                        });
                                                                    </script>
                                                                    <div class="modal material-modal material-modal_primary fade text-left" id="taskDoneModal">
                                                                        <div class="modal-dialog ">
                                                                            <div class="modal-content material-modal__content">
                                                                                <div class="modal-header material-modal__header">
                                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                                    <h4 class="modal-title material-modal__title">Završi zadatak</h4>
                                                                                </div>
                                                                                <div class="modal-body material-modal__body">
                                                                                    <form action="<?php getSiteURL(); ?>do.php?form=add_task_done" method="post" role="form" class="form-horizontal">
                                                                                        <input type="hidden" name="task_id" id="task_id_done" value="" />
                                                                                        <div class="form-group">
                                                                                            <div class="col-md-offset-2 col-sm-8">
                                                                                                <div class="form-group materail-input-block materail-input-block_success">
                                                                                                    <textarea class="form-control materail-input material-textarea" name="task_replytxt" placeholder="Odgovor na zadatak" rows="6" required></textarea>
                                                                                                    <span class="materail-input-block__line"></span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer material-modal__footer">
                                                                                    <ul class="list-inline">
                                                                                        <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                                                        <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Završi</button></li>
                                                                                    </ul>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <a href="#" data="<?php echo $task_id; ?>" data-toggle="modal" data-target="#taskNotDoneModal" class="task_not_done btn material-btn material-btn_danger main-container__column"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                                    <script>
                                                                        $(".task_not_done").click(function () {
                                                                            var addressValue = $(this).attr("data");
                                                                            document.getElementById("task_id_notdone").value = addressValue;
                                                                        });
                                                                    </script>
                                                                    <div class="modal material-modal material-modal_primary fade text-left" id="taskNotDoneModal">
                                                                        <div class="modal-dialog ">
                                                                            <div class="modal-content material-modal__content">
                                                                                <div class="modal-header material-modal__header">
                                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                                    <h4 class="modal-title material-modal__title">Poništi zadatak</h4>
                                                                                </div>
                                                                                <div class="modal-body material-modal__body">
                                                                                    <form action="<?php getSiteURL(); ?>do.php?form=add_task_notdone" method="post" role="form" class="form-horizontal">
                                                                                        <input type="hidden" name="task_id" id="task_id_notdone" value="" />
                                                                                        <div class="form-group">
                                                                                            <div class="col-md-offset-2 col-sm-8">
                                                                                                <div class="form-group materail-input-block materail-input-block_success">
                                                                                                    <textarea class="form-control materail-input material-textarea" name="task_replytxt" placeholder="Odgovor na zadatak" rows="6" required></textarea>
                                                                                                    <span class="materail-input-block__line"></span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer material-modal__footer">
                                                                                    <ul class="list-inline">
                                                                                        <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                                                        <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Poništi</button></li>
                                                                                    </ul>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <?php } ?>
                                                                <li>
                                                                    <a href="#" data="<?php getSiteURL(); ?>contacts?page=del_task&id=<?php echo $task_id; ?>" data-toggle="modal" data-target="#deleteTaskModal" class="delete_task btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                                    <script>
                                                                        $(".delete_task").click(function () {
                                                                            var addressValue = $(this).attr("data");
                                                                            document.getElementById("delete_task_link").href = addressValue;
                                                                        });
                                                                    </script>
                                                                    <!-- Modal -->
                                                                    <div class="modal material-modal material-modal_danger fade text-left" id="deleteTaskModal">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content material-modal__content">
                                                                                <div class="modal-header material-modal__header">
                                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                                    <h4 class="modal-title material-modal__title">Brisanje</h4>
                                                                                </div>
                                                                                <div class="modal-body material-modal__body">
                                                                                    <p>Jeste li sigurni da želite obrisati zadatak?</p>
                                                                                </div>
                                                                                <div class="modal-footer material-modal__footer">
                                                                                    <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                                                                    <a id="delete_task_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                            <hr>
                                                            <ul class="list-inline">
                                                                <?php if($row_tasks['task_status'] == 1){ ?>
                                                                <li>
                                                                    <?php
                                                                        if($task_emailnotifi == 0){
                                                                            echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Isključeno" class="task_emailnotifi1 btn material-btn material-btn_disabled main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_emailnotifi == 1){
                                                                            echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Na dan dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_emailnotifi == 2){
                                                                            echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Dan prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_emailnotifi == 3){
                                                                            echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Dva dana prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_emailnotifi == 4){
                                                                            echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Sedmicu dana prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_emailnotifi == 5){
                                                                            echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Mjesec dana prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                        }
                                                                    ?>
                                                                    <script>
                                                                        $(".task_emailnotifi1").click(function () {
                                                                            var addressValue = $(this).attr("data");
                                                                            document.getElementById("task_id_emailnotifi").value = addressValue;
                                                                        });
                                                                    </script>
                                                                    <div class="modal material-modal material-modal_primary fade text-left" id="taskEmailModal">
                                                                        <div class="modal-dialog ">
                                                                            <div class="modal-content material-modal__content">
                                                                                <div class="modal-header material-modal__header">
                                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                                    <h4 class="modal-title material-modal__title">E-mail napomena</h4>
                                                                                </div>
                                                                                <div class="modal-body material-modal__body">
                                                                                    <form action="<?php getSiteURL(); ?>do.php?form=add_task_emailnotifi" method="post" role="form" class="form-horizontal">
                                                                                        <input type="hidden" name="task_id" id="task_id_emailnotifi" value="" />
                                                                                        <div class="form-group">
                                                                                            <div class="col-md-offset-2 col-sm-8">
                                                                                                <select class="selectpicker" id="task_emailnotifi" name="task_emailnotifi" required>
                                                                                                    <option value="0">Isključeno</option>
                                                                                                    <option value="1">Na dan dospijeća</option>
                                                                                                    <option value="2">Dan prije dospijeća</option>
                                                                                                    <option value="3">Dva dana prije dospijeća</option>
                                                                                                    <option value="4">Sedmicu dana prije dospijeća</option>
                                                                                                    <option value="5">Mjesec dana prije dospijeća</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer material-modal__footer">
                                                                                    <ul class="list-inline">
                                                                                        <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                                                        <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Snimi</button></li>
                                                                                    </ul>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <li>
                                                                    <?php
                                                                        if($task_repeatnotifi == 0){
                                                                            echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Isključeno" class="task_repeatnotifi1 btn material-btn material-btn_disabled main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_repeatnotifi == 1){
                                                                            echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Dnevno" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_repeatnotifi == 2){
                                                                            echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Sedmično" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_repeatnotifi == 3){
                                                                            echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Mjesečno" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                        }elseif($task_repeatnotifi == 4){
                                                                            echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Godišnje" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                        }
                                                                    ?>
                                                                    <script>
                                                                        $(".task_repeatnotifi1").click(function () {
                                                                            var addressValue = $(this).attr("data");
                                                                            document.getElementById("task_id_repeat").value = addressValue;
                                                                        });
                                                                    </script>
                                                                    <div class="modal material-modal material-modal_primary fade text-left" id="taskRepeatModal">
                                                                        <div class="modal-dialog ">
                                                                            <div class="modal-content material-modal__content">
                                                                                <div class="modal-header material-modal__header">
                                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                                    <h4 class="modal-title material-modal__title">Ponavljaj zadatak</h4>
                                                                                </div>
                                                                                <div class="modal-body material-modal__body">
                                                                                    <form action="<?php getSiteURL(); ?>do.php?form=add_task_repeat" method="post" role="form" class="form-horizontal">
                                                                                        <input type="hidden" name="task_id" id="task_id_repeat" value="" />
                                                                                        <div class="form-group">
                                                                                            <div class="col-md-offset-2 col-sm-8">
                                                                                                <select class="selectpicker" id="task_repeatnotifi" name="task_repeatnotifi" required>
                                                                                                    <option value="0">Isključeno</option>
                                                                                                    <option value="1">Dnevno</option>
                                                                                                    <option value="2">Sedmično</option>
                                                                                                    <option value="3">Mjesečno</option>
                                                                                                    <option value="4">Godišnje</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer material-modal__footer">
                                                                                    <ul class="list-inline">
                                                                                        <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                                                        <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Snimi</button></li>
                                                                                    </ul>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <?php } ?>
                                            <script>
                                                timeago().render($('.idk_time_ago'));
                                            </script>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

        <?php
				}else{
					echo '
						<div class="alert material-alert material-alert_danger">
							<h4>NEMATE PRIVILEGIJE!</h4>
							<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
							<br />
							<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
						</div>
					';
				}

                break;

                case "open":

					$task_id = $_GET['id'];

					//Check permision and existing
					$query_task_check = $db->prepare("
											SELECT COUNT(task_id) AS task_id_total, task_assignedid, task_employeeid
											FROM idk_tasks
											WHERE task_id = :task_id AND (task_assignedid = :task_assignedid OR task_employeeid = :task_employeeid)");

					$query_task_check->execute(array(
									':task_id' => $task_id,
									':task_assignedid' => $logged_employee_id,
									':task_employeeid' => $logged_employee_id));

					$row_task_row = $query_task_check->fetch();

					if($row_task_row['task_id_total'] == 0){
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>GREŠKA!</h4>
								<p>Zadatak je obrisan ili nemate privilegije za uvid u ovaj zadatak. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}else{

        ?>
            <div class="row">
                <div class="col-xs-8">
                    <h1><i class="fa fa-tasks idk_color_green" aria-hidden="true"></i> Zadatak</h1>
                </div>
                <div class="col-xs-4 text-right idk_margin_top10">
                    <a href="<?php getSiteURL(); ?>tasks?page=list&sort=new" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
                </div>
                <div class="col-xs-12">
                    <hr />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="content_box">
                        <div class="row">
                            <div class="col-xs-12">
                                <ul class="cbp_tmtimeline">
                                    <?php

                                        //Mark as read
										if(isset($_GET['nid'])) {
											$notification_id = $_GET['nid'];

	                                        $query_update = $db->prepare("
	                                                            UPDATE idk_notifications
	                                                            SET	notification_status = :notification_status
	                                                            WHERE notification_id = :notification_id AND notification_datetime <= 'NOW()'");

	                                        $query_update->execute(array(
	                                                        ':notification_status' => 2,
	                                                        ':notification_id' => $notification_id));
										}

                                        //Get Task
                                        $query_tasks = $db->prepare("
                                                                SELECT task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_employeeid, task_datetime, task_group, task_dataid, task_status, assigned.employee_firstname AS assigned_firstname, assigned.employee_lastname AS assigned_lastname, employee.employee_firstname AS added_firstname, employee.employee_lastname AS added_lastname
                                                                FROM idk_tasks
                                                                INNER JOIN idk_employees assigned ON idk_tasks.task_assignedid = assigned.employee_id
                                                                INNER JOIN idk_employees employee ON idk_tasks.task_employeeid = employee.employee_id
                                                                WHERE task_id = :task_id");

                                        $query_tasks->execute(array(
                                            			':task_id' => $task_id));

                                        $row_tasks = $query_tasks->fetch();

                                            $task_txt = $row_tasks['task_txt'];
                                            $task_replytxt = $row_tasks['task_replytxt'];
                                            $task_emailnotifi = $row_tasks['task_emailnotifi'];
                                            $task_repeatnotifi = $row_tasks['task_repeatnotifi'];
                                            $task_dataid = $row_tasks['task_dataid'];
                                            $assigned_firstname = $row_tasks['assigned_firstname'];
                                            $assigned_lastname = $row_tasks['assigned_lastname'];
                                            $added_firstname = $row_tasks['added_firstname'];
                                            $added_lastname = $row_tasks['added_lastname'];
                                            $task_duedatetime_format = date('d.m.Y. - H:i', strtotime($row_tasks['task_duedatetime']));
                                            $task_duedatetime = date('Y-m-d H:i:s', strtotime($row_tasks['task_duedatetime']));

                                            if($row_tasks['task_status'] == 1 AND (new DateTime() >= new DateTime($task_duedatetime))){
                                                $task_status = 'style="background-color: #f2a12e; color: #fff;"';
                                                $task_icon = '<i class="fa fa-clock-o" aria-hidden="true"></i>';
                                            }elseif($row_tasks['task_status'] == 1){
                                                $task_status = 'style="background-color: #ddd; color: #fff;"';
                                                $task_icon = '<i class="fa fa-check" aria-hidden="true"></i>';
                                            }elseif($row_tasks['task_status'] == 2){
                                                $task_status = 'style="color: #fff;"';
                                                $task_icon = '<i class="fa fa-check" aria-hidden="true"></i>';
                                            }elseif($row_tasks['task_status'] == 3){
                                                $task_status = 'style="background-color: #f3413c; color: #fff;"';
                                                $task_icon = '<i class="fa fa-times" aria-hidden="true"></i>';
                                            }elseif($row_tasks['task_status'] == 4){
                                                $task_status = '<i class="fa fa-sticky-note" aria-hidden="true"></i>';
                                            }

											//task_dataid text
											if($row_tasks['task_group'] == '1'){
												//Contacts
												$query_group = $db->prepare("
																		SELECT contact_id, contact_firstname, contact_lastname
																		FROM idk_contacts
																		WHERE contact_id = :contact_id");

												$query_group->execute(array(
																':contact_id' => $task_dataid));

												$row_group = $query_group->fetch();

													$task_dataid_txt = 'Kontakt: <a href="contacts?page=open&id=' . $row_group['contact_id'] . '">' . $row_group['contact_firstname'] . ' ' . $row_group['contact_lastname'] . '</a>';

											}elseif($row_tasks['task_group'] == '2'){
												//Employees
												$query_group = $db->prepare("
																		SELECT employee_id, employee_firstname, employee_lastname
																		FROM idk_employees
																		WHERE employee_id = :employee_id");

												$query_group->execute(array(
																':employee_id' => $task_dataid));

												$row_group = $query_group->fetch();

													$task_dataid_txt = 'Zaposlenik: <a href="employees?page=open&id=' . $row_group['employee_id'] . '">' . $row_group['employee_firstname'] . ' ' . $row_group['employee_lastname'] . '</a>';
											}elseif($row_tasks['task_group'] == '3'){
												//Companies
												$query_group = $db->prepare("
																		SELECT company_id, company_name
																		FROM idk_companies
																		WHERE company_id = :company_id");

												$query_group->execute(array(
																':company_id' => $task_dataid));

												$row_group = $query_group->fetch();

													$task_dataid_txt = 'Kompanija: <a href="companies?page=open&id=' . $row_group['company_id'] . '">' . $row_group['company_name'] . '</a>';
											}
                                    ?>
                                    <li>
										<time class="cbp_tmtime">
											<span data-toggle="tooltip" data-placement="top" title="Zadatak dodao: <?php echo $added_firstname; ?> <?php echo $added_lastname; ?>"><?php echo $assigned_firstname; ?> <?php echo $assigned_lastname; ?></span>
											<span class="idk_time_ago" datetime="<?php echo $task_duedatetime; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $task_duedatetime_format; ?>"></span>
											<br>
											<span><?php echo $task_dataid_txt; ?></span>
										</time>
										<div class="cbp_tmicon" <?php echo $task_status; ?> data-toggle="tooltip" data-placement="right" title="<?php echo $task_replytxt; ?>"><?php echo $task_icon; ?></div>
                                        <div class="cbp_tmlabel">
                                            <div class="row">
                                                <div class="col-lg-9 col-md-8 col-sm-7">
                                                    <?php echo $task_txt; ?>
                                                </div>
                                                <div class="col-lg-3 col-md-4 col-sm-5 text-right">
                                                    <ul class="list-inline">
                                                        <?php if($row_tasks['task_status'] == 1){ ?>
                                                        <li>
                                                            <a href="#" data="<?php echo $task_id; ?>" data-toggle="modal" data-target="#taskDoneModal" class="task_done btn material-btn material-btn_success main-container__column"><i class="fa fa-check" aria-hidden="true"></i></a>
                                                            <script>
                                                                $(".task_done").click(function () {
                                                                    var addressValue = $(this).attr("data");
                                                                    document.getElementById("task_id_done").value = addressValue;
                                                                });
                                                            </script>
                                                            <div class="modal material-modal material-modal_primary fade text-left" id="taskDoneModal">
                                                                <div class="modal-dialog ">
                                                                    <div class="modal-content material-modal__content">
                                                                        <div class="modal-header material-modal__header">
                                                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                            <h4 class="modal-title material-modal__title">Završi zadatak</h4>
                                                                        </div>
                                                                        <div class="modal-body material-modal__body">
                                                                            <form action="<?php getSiteURL(); ?>do.php?form=add_task_done" method="post" role="form" class="form-horizontal">
                                                                                <input type="hidden" name="task_id" id="task_id_done" value="" />
                                                                                <div class="form-group">
                                                                                    <div class="col-md-offset-2 col-sm-8">
                                                                                        <div class="form-group materail-input-block materail-input-block_success">
                                                                                            <textarea class="form-control materail-input material-textarea" name="task_replytxt" placeholder="Odgovor na zadatak" rows="6" required></textarea>
                                                                                            <span class="materail-input-block__line"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                        </div>
                                                                        <div class="modal-footer material-modal__footer">
                                                                            <ul class="list-inline">
                                                                                <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                                                <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Završi</button></li>
                                                                            </ul>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <a href="#" data="<?php echo $task_id; ?>" data-toggle="modal" data-target="#taskNotDoneModal" class="task_not_done btn material-btn material-btn_danger main-container__column"><i class="fa fa-times" aria-hidden="true"></i></a>
                                                            <script>
                                                                $(".task_not_done").click(function () {
                                                                    var addressValue = $(this).attr("data");
                                                                    document.getElementById("task_id_notdone").value = addressValue;
                                                                });
                                                            </script>
                                                            <div class="modal material-modal material-modal_primary fade text-left" id="taskNotDoneModal">
                                                                <div class="modal-dialog ">
                                                                    <div class="modal-content material-modal__content">
                                                                        <div class="modal-header material-modal__header">
                                                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                            <h4 class="modal-title material-modal__title">Poništi zadatak</h4>
                                                                        </div>
                                                                        <div class="modal-body material-modal__body">
                                                                            <form action="<?php getSiteURL(); ?>do.php?form=add_task_notdone" method="post" role="form" class="form-horizontal">
                                                                                <input type="hidden" name="task_id" id="task_id_notdone" value="" />
                                                                                <div class="form-group">
                                                                                    <div class="col-md-offset-2 col-sm-8">
                                                                                        <div class="form-group materail-input-block materail-input-block_success">
                                                                                            <textarea class="form-control materail-input material-textarea" name="task_replytxt" placeholder="Odgovor na zadatak" rows="6" required></textarea>
                                                                                            <span class="materail-input-block__line"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                        </div>
                                                                        <div class="modal-footer material-modal__footer">
                                                                            <ul class="list-inline">
                                                                                <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                                                <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Poništi</button></li>
                                                                            </ul>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <?php } ?>
                                                        <li>
                                                            <a href="#" data="<?php getSiteURL(); ?>contacts?page=del_task&id=<?php echo $task_id; ?>" data-toggle="modal" data-target="#deleteTaskModal" class="delete_task btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                            <script>
                                                                $(".delete_task").click(function () {
                                                                    var addressValue = $(this).attr("data");
                                                                    document.getElementById("delete_task_link").href = addressValue;
                                                                });
                                                            </script>
                                                            <!-- Modal -->
                                                            <div class="modal material-modal material-modal_danger fade text-left" id="deleteTaskModal">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content material-modal__content">
                                                                        <div class="modal-header material-modal__header">
                                                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                            <h4 class="modal-title material-modal__title">Brisanje</h4>
                                                                        </div>
                                                                        <div class="modal-body material-modal__body">
                                                                            <p>Jeste li sigurni da želite obrisati zadatak?</p>
                                                                        </div>
                                                                        <div class="modal-footer material-modal__footer">
                                                                            <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                                                            <a id="delete_task_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                    <hr>
                                                    <ul class="list-inline">
                                                        <?php if($row_tasks['task_status'] == 1){ ?>
                                                        <li>
                                                            <?php
                                                                if($task_emailnotifi == 0){
                                                                    echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Isključeno" class="task_emailnotifi1 btn material-btn material-btn_disabled main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                }elseif($task_emailnotifi == 1){
                                                                    echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Na dan dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                }elseif($task_emailnotifi == 2){
                                                                    echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Dan prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                }elseif($task_emailnotifi == 3){
                                                                    echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Dva dana prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                }elseif($task_emailnotifi == 4){
                                                                    echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Sedmicu dana prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                }elseif($task_emailnotifi == 5){
                                                                    echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Mjesec dana prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
                                                                }
                                                            ?>
                                                            <script>
                                                                $(".task_emailnotifi1").click(function () {
                                                                    var addressValue = $(this).attr("data");
                                                                    document.getElementById("task_id_emailnotifi").value = addressValue;
                                                                });
                                                            </script>
                                                            <div class="modal material-modal material-modal_primary fade text-left" id="taskEmailModal">
                                                                <div class="modal-dialog ">
                                                                    <div class="modal-content material-modal__content">
                                                                        <div class="modal-header material-modal__header">
                                                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                            <h4 class="modal-title material-modal__title">E-mail napomena</h4>
                                                                        </div>
                                                                        <div class="modal-body material-modal__body">
                                                                            <form action="<?php getSiteURL(); ?>do.php?form=add_task_emailnotifi" method="post" role="form" class="form-horizontal">
                                                                                <input type="hidden" name="task_id" id="task_id_emailnotifi" value="" />
                                                                                <div class="form-group">
                                                                                    <div class="col-md-offset-2 col-sm-8">
                                                                                        <select class="selectpicker" id="task_emailnotifi" name="task_emailnotifi" required>
                                                                                            <option value="0">Isključeno</option>
                                                                                            <option value="1">Na dan dospijeća</option>
                                                                                            <option value="2">Dan prije dospijeća</option>
                                                                                            <option value="3">Dva dana prije dospijeća</option>
                                                                                            <option value="4">Sedmicu dana prije dospijeća</option>
                                                                                            <option value="5">Mjesec dana prije dospijeća</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                        </div>
                                                                        <div class="modal-footer material-modal__footer">
                                                                            <ul class="list-inline">
                                                                                <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                                                <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Snimi</button></li>
                                                                            </ul>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <?php
                                                                if($task_repeatnotifi == 0){
                                                                    echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Isključeno" class="task_repeatnotifi1 btn material-btn material-btn_disabled main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                }elseif($task_repeatnotifi == 1){
                                                                    echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Dnevno" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                }elseif($task_repeatnotifi == 2){
                                                                    echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Sedmično" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                }elseif($task_repeatnotifi == 3){
                                                                    echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Mjesečno" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                }elseif($task_repeatnotifi == 4){
                                                                    echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Godišnje" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
                                                                }
                                                            ?>
                                                            <script>
                                                                $(".task_repeatnotifi1").click(function () {
                                                                    var addressValue = $(this).attr("data");
                                                                    document.getElementById("task_id_repeat").value = addressValue;
                                                                });
                                                            </script>
                                                            <div class="modal material-modal material-modal_primary fade text-left" id="taskRepeatModal">
                                                                <div class="modal-dialog ">
                                                                    <div class="modal-content material-modal__content">
                                                                        <div class="modal-header material-modal__header">
                                                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                            <h4 class="modal-title material-modal__title">Ponavljaj zadatak</h4>
                                                                        </div>
                                                                        <div class="modal-body material-modal__body">
                                                                            <form action="<?php getSiteURL(); ?>do.php?form=add_task_repeat" method="post" role="form" class="form-horizontal">
                                                                                <input type="hidden" name="task_id" id="task_id_repeat" value="" />
                                                                                <div class="form-group">
                                                                                    <div class="col-md-offset-2 col-sm-8">
                                                                                        <select class="selectpicker" id="task_repeatnotifi" name="task_repeatnotifi" required>
                                                                                            <option value="0">Isključeno</option>
                                                                                            <option value="1">Dnevno</option>
                                                                                            <option value="2">Sedmično</option>
                                                                                            <option value="3">Mjesečno</option>
                                                                                            <option value="4">Godišnje</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                        </div>
                                                                        <div class="modal-footer material-modal__footer">
                                                                            <ul class="list-inline">
                                                                                <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                                                <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Snimi</button></li>
                                                                            </ul>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <script>
                                        timeago().render($('.idk_time_ago'));
                                    </script>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
				}
                break;

				case "del_task":
					if(!empty($employee_supervizor[0]) OR (in_array( "1" , $employee_status))){

						$task_id = $_GET['id'];

						//Get task_txt and task_dataid
						$task_open_query = $db->prepare("
													SELECT task_txt, task_dataid, task_emailnotifi, task_status
													FROM idk_tasks
													WHERE task_id = :task_id");

						$task_open_query->execute(array(
												':task_id' => $task_id));

						$task_open = $task_open_query->fetch();

							$task_txt = strip_tags($task_open['task_txt']);
							$task_dataid = $task_open['task_dataid'];
							$task_emailnotifi = $task_open['task_emailnotifi'];
							$task_status = $task_open['task_status'];

						//Add to LOGS
						$log_desc = "Obrisao zadatak: " . $task_txt . " ";
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

						//Delete task from db
						$task_del_query = $db->prepare("
													DELETE FROM idk_tasks
													WHERE task_id = :task_id");

						$task_del_query->execute(array(
											':task_id' => $task_id));

						//Remove Trigger
						if($task_emailnotifi != 0 && $task_status == 1){
							$url_tag = "email_task_" . $task_id . "";
							removeTrigger($url_tag);
						}

						header("Location: " . getSiteURLr() . "contacts?page=open&id=$task_dataid&mess=11");

					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;
			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
<?php }else{			
			echo '
				<br/>
				<div class="alert material-alert material-alert_danger">
					<h4>NEMATE PRIVILEGIJE!</h4>
					<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
					<br />
				</div>
';} ?>