<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());
    
    if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: module_permissions?page=list_all");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Permisije za module</title>

	<?php include('includes/head.php'); 
	if (in_array($getUserIp, $getIpWhiteList)){
	?>
	<!-- CK Editor ---------------------------------------------------------------------------------------->
	<script src="<?php getSiteURL(); ?>ckeditor/ckeditor.js" async></script>

	<!-- HTML to MD -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.9.0/showdown.min.js"></script>
	<!-- MD to HTML -->
	<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

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
            if(getModulePermission(1)){
                switch ($page){

                    case "list_all":
                        
                        ?>
                        <div class="row">
                            <div class="col-xs-4">
                                <h1><i class="fa fa-wrench idk_color_green" aria-hidden="true"></i> Permisije za module</h1>
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
                                                    var table = $('#idk_table').DataTable({
                                                        responsive: true,
                                                        "order": [[ 0, "asc" ]],
                                                        "aoColumns": [
                                                                { "width": "5%" },
                                                                { "width": "35%" },
                                                                { "width": "50%" },
                                                                { "width": "10%" },
                                                            ]
                                                    });
                                                } );
                                            </script>
                                            <table id="idk_table" class="display" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">ID</th>
                                                        <th class="text-center">Naziv</th>
                                                        <th class="text-center">Zaposlenici</th>
                                                        <th class="text-center">Akcija</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $query = $db->prepare("SELECT mp_id, mp_name, mp_employees FROM idk_module_permissions");

                                                    $query->execute();

                                                    while($row = $query->fetch()){

                                                        $mp_id = $row['mp_id'];
                                                        $mp_name = $row['mp_name'];
                                                        $mp_employees = $row['mp_employees'];
                                                        $nalog_naziv = $row['nalog_naziv'];

                                                        $employees_array = explode( ',' , $mp_employees);
                                                        $get_employees = $db->prepare("
                                                                SELECT CONCAT(employee_firstname, ' ', employee_lastname) AS full_name
                                                                FROM idk_employees
                                                                WHERE employee_id IN ($mp_employees) "
                                                        );
                                                        $get_employees->execute();
                                                        // var_dump($get_employees->fetchAll());
                                                        $employee_names_array = $get_employees->fetchAll(PDO::FETCH_ASSOC);
                                                        $employee_names_array = array_map('reset', $employee_names_array);
                                                        $employee_names = implode(", ",$employee_names_array);
                                                        // var_dump($employee_names_array);
                                                        
                                                        
                                                        ?>
                                                        <tr>
                                                            <td class="text-center"><?php echo $mp_id; ?></td>
                                                            <td class="text-center"><?php echo $mp_name; ?></td>
                                                            <td class="text-center"><?php echo $employee_names; ?></td>
                                                            <td class="text-center">
                                                                <div class="btn-group material-btn-group">
                                                                    <button class="material-btn material-btn_primary">
                                                                    <a style="color:white;" onclick="changeEmployees(this)"
                                                                        data-mp_id ="<?php echo $mp_id; ?>"
                                                                        data-mp_employees ="<?php echo $mp_employees; ?>"
                                                                    >
                                                                        Uredi</a>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                            
                                                        </tr><?php 
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- MODAL ZA DODAVANJE ZAPOSLENIKA -->
                            <div class="modal material-modal material-modal_primary fade text-left" id="changeEmployees">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content material-modal__content">
                                        <div class="modal-header material-modal__header">
                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-pencil-square" aria-hidden="true"></i>Uredi permisije</h4>
                                        </div> 
                                        <div class="modal-body material-modal__body">
                                            <form action="<?php getSiteURL(); ?>do?form=chang_module_permission_users" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="changeEmployeesForm">
                                                <input type="hidden" id="module_permission_id" name="module_permission_id">
                                                <input type="hidden" id="employees_ids" name="employees_ids">
                                                <div class="form-group">
                                                    <div class="col-md-offset-2 col-sm-8">
                                                        <select class="selectpicker" id="select_new_employees" name="select_new_employees[]" data-live-search="true" title = "Odaberite korisnike" required multiple>
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer material-modal__footer">
                                                    <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                    <button type="submit" class="btn btn-primary material-btn material-btn_success" form="changeEmployeesForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- MODAL ZA DODAVANJE ZAPOSLENIKA -->
                        <script>
                            function resetFormData() {
                                $('#changeEmployeesForm :input:hidden').removeAttr('value');
                                $('#select_new_employees option').remove();
                                $('#select_new_employees').selectpicker('refresh');
                            };
                            function getEmployeesForModulePermission(form_data) {
                                $.ajax({
                                    url: 'do?form=get_employees_for_module_permission',
                                    type: 'POST',
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    data:form_data,
                                    success : function (result){
                                        if (result == '') {
                                            resetFormData();
                                            alert("Postoji problem sa otvaranjem modala za uređivanje permisija! Molimo, obratite se administratoru sistema!");
                                        } else if (result == 'No results found for query!') {
                                            resetFormData();
                                            alert("Ne postoji ni jedan user koji se može postaviti na ovu permisiju! Molimo, obratite se administratoru sistema!");
                                        } else {
                                            $("#select_new_employees").html(result).selectpicker('refresh');
                                            $('#changeEmployees').modal('show');
                                        }
                                    },
                                    error: function (xhr, ajaxOptions, thrownError) {
                                        alert(xhr.status);
                                        alert(thrownError);
                                    }
                                });
                            };
                            
                            function changeEmployees(thisRow) {
                                var mp_id = $(thisRow).data('mp_id');
                                var mp_employees = $(thisRow).data('mp_employees');

                                var form_data = new FormData();

                                form_data.append('module_permission_id', mp_id);
                                form_data.append('employees_ids', mp_employees);

                                getEmployeesForModulePermission(form_data);

                                form_data.forEach((value, key) => {
                                    $("#" + key).val(value);
                                });
                            }
                        </script>
                        <?php
                    break;

                    
                } 
            }else{
                echo '
				<br/>
				<div class="alert material-alert material-alert_danger">
					<h4>NEMATE PRIVILEGIJE!</h4>
					<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
					<br />
				</div>';
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