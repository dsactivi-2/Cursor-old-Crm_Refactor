<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());
    //Check module status
    if($module_employees_reports == 0){
        header("Location: index");
    }

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: employees-reports?page=list");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Izvještaji | <?php getTitle(); ?></title>

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

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-line-chart idk_color_green" aria-hidden="true"></i> Moji izvještaji</h1>
				</div>
                <div class="col-xs-4 text-right idk_margin_top10">
					<a href="#" data-toggle="modal" data-target="#myCalendar" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-calendar" aria-hidden="true"></i> <span>Kreiraj izvještaj</span></a>
				</div>
				<div class="modal material-modal material-modal_primary fade" id="myCalendar">
				  <div class="modal-dialog ">
					  <div class="modal-content material-modal__content">
						  <div class="modal-header material-modal__header">
							  <button class="close material-modal__close" data-dismiss="modal">&times;</button>
							  <h4 class="modal-title material-modal__title">Izaberi datum</h4>
						  </div>
						  <div class="modal-body material-modal__body">
							  <div class="row">
								  <div class="col-sm-2"></div>
								  <div class="col-sm-8">
								  	<section class="main">
			  							<div class="custom-calendar-wrap idk_modal_calendar">
			  								<div id="custom-inner" class="custom-inner">
			  									<div class="custom-header clearfix">
			  										<nav>
			  											<span id="custom-prev" class="custom-prev"></span>
			  											<span id="custom-next" class="custom-next"></span>
			  										</nav>
			  										<h2 id="custom-month" class="custom-month"></h2>
			  										<h3 id="custom-year" class="custom-year"></h3>
			  									</div>
			  									<div id="calendar" class="fc-calendar-container"></div>
			  								</div>
			  							</div>
			  						</section>
			  						<script type="text/javascript">
			  							$(function() {

			  								var transEndEventNames = {
			  										'WebkitTransition' : 'webkitTransitionEnd',
			  										'MozTransition' : 'transitionend',
			  										'OTransition' : 'oTransitionEnd',
			  										'msTransition' : 'MSTransitionEnd',
			  										'transition' : 'transitionend'
			  									},
			  								transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
			  								$wrapper = $( '#custom-inner' ),
			  								$calendar = $( '#calendar' ),
			  								cal = $calendar.calendario( {
			  									onDayClick : function( $el, $contentEl, dateProperties ) {

			  										window.location.href = "employees-reports?page=add&date=" + dateProperties.day + "-" + dateProperties.month + "-" + dateProperties.year;

			  									},
			  									displayWeekAbbr : true
			  								} ),
			  								$month = $( '#custom-month' ).html( cal.getMonthName() ),
			  								$year = $( '#custom-year' ).html( cal.getYear() );

			  								$( '#custom-next' ).on( 'click', function() {
			  									cal.gotoNextMonth( updateMonthYear );
			  								} );
			  								$( '#custom-prev' ).on( 'click', function() {
			  									cal.gotoPreviousMonth( updateMonthYear );
			  								} );

			  								function updateMonthYear() {
			  									$month.html( cal.getMonthName() );
			  									$year.html( cal.getYear() );
			  								}
			  							});
			  						</script>
								  </div>
							  </div>
						  </div>
					  </div>
				  </div>
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
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novi projekat.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil projekta.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali projekat.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 0, "desc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "30%" },
													{ "width": "25%" },
													{ "width": "15%" },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Datum</th>
											<th class="text-center">Ukupno sati</th>
											<th class="text-center">Broj stavki</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT er_id, er_date, COUNT(er_id) AS er_id_total, sum(TIMESTAMPDIFF(MINUTE, er_timefrom, er_timeto)) AS 'er_time_total'
															FROM idk_employees_reports
															WHERE er_employeeid = :er_employeeid
															GROUP BY er_date");

											$query->execute(Array(
														':er_employeeid' => $logged_employee_id));

											while($row = $query->fetch()){

												$er_date = date('d.m.Y.', strtotime($row['er_date']));
												$er_date_link = date('d-m-Y', strtotime($row['er_date']));
												$er_date_link_sort = date('Ymd', strtotime($row['er_date']));
												$er_id = $row['er_id'];
												$er_id_total = $row['er_id_total'];
												//$er_time_total = $row['er_time_total'];
												$er_time_total_f = floor($row['er_time_total'] / 60).':'.($row['er_time_total'] -   floor($row['er_time_total'] / 60) * 60);

										?>
										<tr>
											<td><span class='hidden'><?php echo $er_date_link_sort; ?></span><a href="employees-reports?page=add&date=<?php echo $er_date_link; ?>"><?php echo $er_date; ?></a></td>
											<td class="text-center"><?php echo $er_time_total_f; ?></td>
											<td class="text-center"><?php echo $er_id_total; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>employees-reports?page=add&date=<?php echo $er_date_link; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>employees-reports?page=del&date=<?php echo $er_date_link; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
													</ul>
												</div>
											</td>
										</tr>
										<?php } ?>
										<script>
											$(".archive").click(function () {
												var addressValue = $(this).attr("data");
												document.getElementById("archive_link").href = addressValue;
											});
										</script>
										<!-- Modal -->
										<div class="modal material-modal material-modal_danger fade" id="archiveModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Arhiviranje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite arhivirati zaposlenika?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
													</div>
												</div>
											</div>
										</div>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

				case "add":
                        $er_date = $_GET['date'];
						$er_date_input = date('Y-m-d', strtotime($er_date));
						$er_date_f = date('d.m.Y.', strtotime($er_date));
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-stack-overflow idk_color_green" aria-hidden="true"></i> Ispuni izvještaj za dan <?php echo $er_date_f; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>employees-reports?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-12 idk_full_center">
								<form id="idk_form" class="form-horizontal" action="<?php getSiteURL(); ?>do_modules.php?form=add_employees_report" method="post" enctype="multipart/form-data" role="form">
									<input type="hidden" name="er_date" value="<?php echo $er_date_input; ?>">
									<div class="row">
										<div class="col-sm-2"></div>
										<div class="col-sm-2">
											<div class="form-group">
													<label class="col-sm-2 text-right" for="er_timefrom">OD:</label>
													<div class="col-sm-10">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="er_timefrom" id="er_timefrom">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												<script>
													$('#er_timefrom').timeDropper({ format:'H:mm', setCurrentTime:false, mousewheel:true });
												</script>
											</div>
										</div>
										<div class="col-sm-2">
											<div class="form-group">
													<label class="col-sm-2 text-right" for="er_timeto">DO:</label>
													<div class="materail-input-block materail-input-block_success">
														<div class="col-sm-10">
															<input class="form-control materail-input" type="text" name="er_timeto" id="er_timeto">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												<script>
													$('#er_timeto').timeDropper({ format:'H:mm', setCurrentTime:false });
												</script>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<select class="selectpicker" id="er_nalogid" name="er_nalogid" data-live-search="true" required>
													<option value="">Izaberi nalog ...</option>
													<option value="0">Bez naloga</option>
													<?php
														$select_query = $db->prepare("
																			SELECT nalog_id, nalog_naziv
																			FROM idk_nalozi
																			WHERE nalog_status != 8");

														$select_query->execute();

														while($select_row = $select_query->fetch()) {
															echo "<option value='" . $select_row['nalog_id'] . "'>" . $select_row['nalog_naziv'] . "</option>";
														}
													?>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-2"></div>
										<div class="col-sm-7">
											<div class="form-group">
												<label class="col-sm-1 text-right" for="er_title">Opis:</label>
												<div class="col-sm-11">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="er_title" id="er_title" placeholder="Opis ..." />
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-sm-2 text-left">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
										</div>
									</div>
								</form>
								<hr>
							</div>
							<div class="col-xs-12">
								<h4><i class="fa fa-stack-overflow idk_color_green" aria-hidden="true"></i> Izvještaj:</h4>
								<div class="table-responsive">
									<table class="table table-hover">
										<thead>
											<tr>
												<th class="text-center" width="7%">OD</th>
												<th class="text-center" width="7%">DO</th>
												<th width="46%">Opis</th>
												<th width="30%">Nalog</th>
												<th class="text-center" width="10%">Obriši</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$query = $db->prepare("
																SELECT er_id, er_title, er_timefrom, er_timeto, nalog_naziv, er_projectid, er_nalogid
																FROM idk_employees_reports
																LEFT JOIN idk_nalozi ON idk_employees_reports.er_nalogid = idk_nalozi.nalog_id
																WHERE DATE(er_date) = :er_date AND er_employeeid = :er_employeeid
																ORDER BY er_timefrom ASC");

											$query->execute(array(
															':er_date' => $er_date_input,
															':er_employeeid' => $logged_employee_id));

											while($row = $query->fetch()){

												$er_id = $row['er_id'];
												$er_timefrom = date("H:i", strtotime($row['er_timefrom']));
												$er_timeto = date("H:i", strtotime($row['er_timeto']));
												$er_title = $row['er_title'];
												$er_nalogid = $row['er_nalogid'];
												$er_projectid = $row['er_projectid'];
												if($er_nalogid == null){
													$query_nalog_name = $db->prepare("
															SELECT nalog_naziv
															FROM idk_nalozi 
															JOIN idk_projects ON idk_nalozi.nalog_id = idk_projects.project_nalogid
															WHERE idk_projects.project_id = $er_projectid");
													$query_nalog_name->execute();
													$row_nalog_name = $query_nalog_name->fetch();
													$nalog_naziv = $row_nalog_name['nalog_naziv'];
												}else
													$nalog_naziv = $row['nalog_naziv'];
											?>
											<tr>
												<td class="text-center er_editable er_editable-time" data-er-type="er_timefrom" data-er-id="<?php echo $er_id; ?>"><?php echo $er_timefrom; ?></td>
												<td class="text-center er_editable er_editable-time" data-er-type="er_timeto" data-er-id="<?php echo $er_id; ?>"><?php echo $er_timeto; ?></td>
												<td class="er_editable er_editable-text" data-er-type="er_title" data-er-id="<?php echo $er_id; ?>"><?php echo $er_title; ?></td>
												<td><?php echo $nalog_naziv; ?></td>
												<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>employees-reports?page=del&id=<?php echo $er_id; ?>" data-toggle="modal" data-target="#deleteModal" class="delete btn material-btn material-btn-icon-danger material-btn_danger main-container__column"><i class="fa fa-trash" aria-hidden="true"></i> <span></span></button></td>
											</tr>
											<?php } ?>
											<script>
												$(".delete").click(function () {
													var addressValue = $(this).attr("data");
													document.getElementById("delete").href = addressValue;
												});
											</script>
											<!-- Modal -->
											<div class="modal material-modal material-modal_danger fade text-left" id="deleteModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Brisanje</h4>
														</div>
														<div class="modal-body material-modal__body">
															<p>Jeste li sigurni da želite obrisati stavku izvještaja?</p>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
															<a id="delete" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
														</div>
													</div>
												</div>
											</div>
										</tbody>
									</table>

									<script>

										function saveEditableElement()
										{
											if($('#currentlyEditedElement')[0].checkValidity())
											{
												var erType = $('#currentlyEditedElement').data('er-type');
												var erID = $('#currentlyEditedElement').data('er-id');
												var erOldValue = $('#currentlyEditedElement').data('er-old-val');
												var erNewValue = $('#currentlyEditedElement').val();
												$('.er_editable[data-er-type="'+erType+'"][data-er-id="'+erID+'"]').text(erNewValue);

												if(erOldValue != erNewValue)
												{
													$.ajax({
												   		data: {"er_type": erType, "er_id": erID, "er_new_value": erNewValue},
												   		type: 'POST',
												   		url: 'do_modules.php?form=edit_employees_report_item'
													});
												}
											}
											else
											{
												$('#currentlyEditedElement').addClass('invalid-value');
											}
										}

										$('body').click(function(event){    
										    if(event.target.id != "currentlyEditedElement" && $('#currentlyEditedElement').length == 1)
											{
												saveEditableElement();
											}
											if($(event.target).hasClass('er_editable-time'))
											{
												var currentValue = $(event.target).text();
												var erID = $(event.target).data('er-id');
												var erType = $(event.target).data('er-type');
												var input_template = '<input data-er-type="' + erType + '" data-er-id="' + erID + '" data-er-old-val="' + currentValue + '" id="currentlyEditedElement" class="currentlyEditableTime" type="text" pattern="([0-1]{1}[0-9]{1}|20|21|22|23):[0-5]{1}[0-9]{1}"/>';
												$(event.target).html(input_template);
												$('#currentlyEditedElement').focus();
												$('#currentlyEditedElement').val(currentValue);

												$("#currentlyEditedElement").on('keyup', function (event) {
												    if (event.keyCode == 13) {	// On ENTER key press
														saveEditableElement();
												    }
												});
											}
											if($(event.target).hasClass('er_editable-text'))
											{
												var currentValue = $(event.target).text();
												var erID = $(event.target).data('er-id');
												var erType = $(event.target).data('er-type');
												var input_template = '<input data-er-type="' + erType + '" data-er-id="' + erID + '" data-er-old-val="' + currentValue + '" id="currentlyEditedElement" class="currentlyEditableText" type="text" />';
												$(event.target).html(input_template);
												$('#currentlyEditedElement').focus();
												$('#currentlyEditedElement').val(currentValue);

												$("#currentlyEditedElement").on('keyup', function (event) {
												    if (event.keyCode == 13) {	// On ENTER key press
														saveEditableElement();
												    }
												});
											}
										});
									</script>
									<style>
										.currentlyEditableTime
										{
											width: 65%;
    										text-align: center;
											margin-top: -5px;
										}
										.currentlyEditableText
										{
											width: 100%;
											margin-top: -5px;
										}
										.currentlyEditableTime.invalid-value,
										.currentlyEditableText.invalid-value
										{
											border: 1px solid red;
										}
									</style>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

				case "list_all":

				if(!empty($employee_supervizor[0]) OR (in_array( "1" , $employee_status))){ 

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-line-chart idk_color_green" aria-hidden="true"></i> Izvještaji zaposlenika</h1>
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
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 1, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%", "bSortable": false },
													{ "width": "20%" },
													{ "width": "20%" },
													{ "width": "30%" },
													{ "width": "15%" }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th></th>
											<th>Ime i prezime</th>
											<th>Pozicija</th>
											<th>E-mail</th>
											<th>Zadnji izvještaj</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT employee_id, employee_firstname, employee_lastname, employee_email, employee_status, employee_image, employee_position
															FROM idk_employees");

											$query->execute();

											while($row = $query->fetch()){

												$employee_id = $row['employee_id'];
												$employee_firstname = $row['employee_firstname'];
												$employee_lastname = $row['employee_lastname'];
												$employee_email = $row['employee_email'];
												$employee_position = $row['employee_position'];

												if($row['employee_image'] == "none"){
													$employee_image = "none.jpg";
												}else{
													$employee_image = $row['employee_image'];
												}

												//Get last report
												$query_report = $db->prepare("
																		SELECT er_id, er_date
																		FROM idk_employees_reports
																		WHERE er_employeeid = :er_employeeid
																		ORDER BY er_id DESC
																		LIMIT 1");

												$query_report->execute(array('er_employeeid' => $employee_id));

												$row_report = $query_report->fetch();

												if(isset($row_report['er_date'])){
													$er_date = '<a href="employees-reports?page=open_report&date=' . date('d-m-Y', strtotime($row_report['er_date'])) . '&id=' . $employee_id . '">' . date('d.m.Y.', strtotime($row_report['er_date'])) . '</a>';
												}else{
													$er_date = "Nema izvještaja";
												}

										?>
										<tr>
											<td class="text-center"><a href="<?php getSiteURL(); ?>employees-reports?page=open_reports&id=<?php echo $employee_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/<?php echo getSubdomainr(); ?>_files/employees/<?php echo $employee_image; ?>"></a></td>
											<td><a href="<?php getSiteURL(); ?>employees-reports?page=open_reports&id=<?php echo $employee_id; ?>"><?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></a></td>
											<td><?php echo $employee_position; ?></td>
											<td><a href="mailto:<?php echo $employee_email; ?>"><?php echo $employee_email; ?></a></td>
											<td><?php echo $er_date; ?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
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

				case "open_reports":

				if(!empty($employee_supervizor[0]) OR (in_array( "1" , $employee_status))){

					$er_employeeid = $_GET['id'];

					//Get employee name
					$query_employee = $db->prepare("
									SELECT employee_firstname, employee_lastname
									FROM idk_employees
									WHERE employee_id = :employee_id");

					$query_employee->execute(array(
						':employee_id' => $er_employeeid));

					$row_employee = $query_employee->fetch();

						$employee_firstname = $row_employee['employee_firstname'];
						$employee_lastname = $row_employee['employee_lastname'];

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-line-chart idk_color_green" aria-hidden="true"></i> Izvještaji: <?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></h1>
				</div>
                <div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>employees-reports?page=list_all" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 0, "desc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "30%" },
													{ "width": "25%" },
													{ "width": "15%" }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Datum</th>
											<th class="text-center">Ukupno sati</th>
											<th class="text-center">Broj stavki</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT er_id, er_date, COUNT(er_id) AS er_id_total, sum(TIMESTAMPDIFF(MINUTE, er_timefrom, er_timeto)) AS 'er_time_total'
															FROM idk_employees_reports
															WHERE er_employeeid = :er_employeeid
															GROUP BY er_date");

											$query->execute(Array(
														':er_employeeid' => $er_employeeid));

											while($row = $query->fetch()){

												$er_date = date('d.m.Y.', strtotime($row['er_date']));
												$er_date_link_sort = date('Ymd', strtotime($row['er_date']));
												$er_date_link = date('d-m-Y', strtotime($row['er_date']));
												$er_id_total = $row['er_id_total'];
												//$er_time_total = $row['er_time_total'];
												$er_time_total_f = floor($row['er_time_total'] / 60).':'.($row['er_time_total'] -   floor($row['er_time_total'] / 60) * 60);

										?>
										<tr>
											<td><span class='hidden'><?php echo $er_date_link_sort; ?></span><a href="employees-reports?page=open_report&date=<?php echo $er_date_link; ?>&id=<?php echo $er_employeeid; ?>"><?php echo $er_date; ?></a></td>
											<td class="text-center"><?php echo $er_time_total_f; ?></td>
											<td class="text-center"><?php echo $er_id_total; ?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
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

				case "open_report":
					if(!empty($employee_supervizor[0]) OR (in_array( "1" , $employee_status))){

                        $er_employeeid = $_GET['id'];

						//Get employee name
						$query_employee = $db->prepare("
										SELECT employee_firstname, employee_lastname
										FROM idk_employees
										WHERE employee_id = :employee_id");

						$query_employee->execute(array(
							':employee_id' => $er_employeeid));

						$row_employee = $query_employee->fetch();

							$employee_firstname = $row_employee['employee_firstname'];
							$employee_lastname = $row_employee['employee_lastname'];


                        $er_date = $_GET['date'];
						$er_date_input = date('Y-m-d', strtotime($er_date));
						$er_date_f = date('d.m.Y.', strtotime($er_date));


		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-stack-overflow idk_color_green" aria-hidden="true"></i> Izvještaj za dan <?php echo $er_date_f; ?> | Zaposlenik: <?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>employees-reports?page=open_reports&id=<?php echo $er_employeeid; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<h4><i class="fa fa-stack-overflow idk_color_green" aria-hidden="true"></i> Izvještaj:</h4>
								<div class="table-responsive">
									<table class="table table-hover">
										<thead>
											<tr>
												<th class="text-center" width="7%">OD</th>
												<th class="text-center" width="7%">DO</th>
												<th width="46%">Opis</th>
												<th width="30%">Nalog</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$query = $db->prepare("
																SELECT er_id, er_title, er_timefrom, er_timeto, nalog_naziv, er_projectid, er_nalogid
																FROM idk_employees_reports
																LEFT JOIN idk_nalozi ON idk_employees_reports.er_nalogid = idk_nalozi.nalog_id
																WHERE DATE(er_date) = :er_date AND er_employeeid = :er_employeeid
																ORDER BY er_timefrom ASC");

											$query->execute(array(
															':er_date' => $er_date_input,
															':er_employeeid' => $er_employeeid));

											while($row = $query->fetch()){

												$er_id = $row['er_id'];
												$er_timefrom = date("H:i", strtotime($row['er_timefrom']));
												$er_timeto = date("H:i", strtotime($row['er_timeto']));
												$er_title = $row['er_title'];
												$er_nalogid = $row['er_nalogid'];
												$er_projectid = $row['er_projectid'];
												if($er_nalogid == null){
													$query_nalog_name = $db->prepare("
															SELECT nalog_naziv
															FROM idk_nalozi 
															JOIN idk_projects ON idk_nalozi.nalog_id = idk_projects.project_nalogid
															WHERE idk_projects.project_id = $er_projectid");
													$query_nalog_name->execute();
													$row_nalog_name = $query_nalog_name->fetch();
													$nalog_naziv = $row_nalog_name['nalog_naziv'];
												}else
													$nalog_naziv = $row['nalog_naziv'];
											?>
											<tr>
												<td class="text-center"><?php echo $er_timefrom; ?></td>
												<td class="text-center"><?php echo $er_timeto; ?></td>
												<td><?php echo $er_title; ?></td>
												<td><?php echo $nalog_naziv; ?></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
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

				case "del":
					if(!empty($employee_supervizor[0]) OR (in_array( "1" , $employee_status))){

						$er_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT er_date
												FROM idk_employees_reports
												WHERE er_id = :er_id");

						$query_select->execute(array(
											':er_id' => $er_id));

						$row_select = $query_select->fetch();

						$er_date = date('d-m-Y', strtotime($row_select['er_date']));

						//Delete from db
						$phone_del_query = $db->prepare("
													DELETE FROM idk_employees_reports
													WHERE er_id = :er_id");

						$phone_del_query->execute(array(
											':er_id' => $er_id));

						header("Location: " . getSiteURLr() . "employees-reports?page=add&date=$er_date");

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