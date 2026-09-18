<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());
    
    if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: message_providers?page=list_all");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Provideri za poruke</title>

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
            if(getModulePermission(6)){
                switch ($page){

                    case "list_all":
                        
                        ?>
                        <div class="row">
                            <div class="col-xs-8">
                                <h1><i class="fa fa-envelope idk_color_green" aria-hidden="true"></i> Provideri za poruke</h1>
                            </div>
                            <div class="col-xs-4 text-right idk_margin_top10">
                                <button onclick="changeProviderAll()" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-pencil" aria-hidden="true"></i> <span>Promjeni za sve grupe</span></button>
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
                                                        <th class="text-center">Grupa</th>
                                                        <th class="text-center">Provider</th>
                                                        <th class="text-center">Akcija</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $query = $db->prepare("SELECT * FROM idk_provider_for_sending_messages");

                                                    $query->execute();

                                                    while($row = $query->fetch()){

                                                        $psm_id = $row['psm_id'];
                                                        $psm_group_type	= $row['psm_group_type'];
                                                        $psm_group_name = $row['psm_group_name'];
                                                        $psm_active_provider = $row['psm_active_provider'];
                                                        if($psm_active_provider == 1){
                                                            $psm_active_provider_text = "Infobip";
                                                        }else if($psm_active_provider == 2){
                                                            $psm_active_provider_text = "NTH";
                                                        }else{
                                                            $psm_active_provider_text = "Greška";
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td class="text-center"><?php echo $psm_id; ?></td>
                                                            <td class="text-center"><?php echo $psm_group_name; ?></td>
                                                            <td class="text-center"><?php echo $psm_active_provider_text; ?></td>
                                                            <td class="text-center">
                                                                <div class="btn-group material-btn-group">
                                                                    <button class="material-btn material-btn_primary">
                                                                    <a style="color:white;" onclick="changeProvider(this)"
                                                                        data-psm_id ="<?php echo $psm_id; ?>"
                                                                        data-psm_group_type ="<?php echo $psm_group_type; ?>"
                                                                        data-psm_active_provider ="<?php echo $psm_active_provider; ?>"
                                                                        data-psm_group_name ="<?php echo $psm_group_name; ?>"
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
                        <!-- MODAL ZA PROMJENU PROVIDERA GRUPE -->
                            <div class="modal material-modal material-modal_primary fade text-left" id="changeProvider">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content material-modal__content">
                                        <div class="modal-header material-modal__header">
                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title material-modal__title" id="changeProviderTitle"><i style = "margin-right: 10px;" class="fa fa-pencil-square" aria-hidden="true"></i>Uredi providera</h4>
                                        </div> 
                                        <div class="modal-body material-modal__body">
                                            <form action="<?php getSiteURL(); ?>do?form=change_message_provider" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="changeProviderForm">
                                                <input type="hidden" id="psm_id" name="psm_id">
                                                <input type="hidden" id="psm_group_type" name="psm_group_type">
                                                <div class="form-group">
                                                    <div class="col-md-offset-2 col-sm-8">
                                                        <select class="selectpicker" id="psm_active_provider" name="psm_active_provider" title = "Odaberite providera" required>
                                                            <option value="1"> Infobip</option>
                                                            <option value="2"> NTH</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer material-modal__footer">
                                                    <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                    <button type="submit" class="btn btn-primary material-btn material-btn_success" form="changeProviderForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- MODAL ZA DODAVANJE ZAPOSLENIKA -->

                         <!-- MODAL ZA PROMJENU PROVIDERA SVIH GRUPA -->
                         <div class="modal material-modal material-modal_primary fade text-left" id="changeProviderAll">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content material-modal__content">
                                        <div class="modal-header material-modal__header">
                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-pencil-square" aria-hidden="true"></i>Promjeni providera za sve grupe poruka</h4>
                                        </div> 
                                        <div class="modal-body material-modal__body">
                                            <form action="<?php getSiteURL(); ?>do?form=change_message_provider_all" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="changeProviderFormAll">
                                                <div class="form-group">
                                                    <div class="col-md-offset-2 col-sm-8">
                                                        <select class="selectpicker" id="psm_active_provider_all" name="psm_active_provider_all" title = "Odaberite providera" required>
                                                            <option value="1"> Infobip</option>
                                                            <option value="2"> NTH</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer material-modal__footer">
                                                    <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                    <button type="submit" class="btn btn-primary material-btn material-btn_success" form="changeProviderFormAll"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                         <!-- MODAL ZA PROMJENU PROVIDERA SVIH GRUPA -->
                         <script>
                            
                            function changeProvider(thisRow) {
                                var psm_id = $(thisRow).data('psm_id');
                                var psm_group_type = $(thisRow).data('psm_group_type');
                                var psm_active_provider = $(thisRow).data('psm_active_provider');
                                var psm_group_name = $(thisRow).data('psm_group_name');
                                
                                $("#psm_id").val(psm_id);
                                $("#psm_group_type").val(psm_group_type);
                                $("#changeProviderTitle").html(psm_group_name);

                                $('#changeProviderForm')[0].reset();

                                if (psm_active_provider == '1') {
                                    $("#psm_active_provider").val('1'); 
                                } else if(psm_active_provider == '2'){
                                    $("#psm_active_provider").val('2'); 
                                } else{
                                    alert("Greška!");
                                }

                                $('#psm_active_provider').selectpicker('refresh');

                                $('#changeProvider').modal('show');
                            }
                            
                            function changeProviderAll() {

                                $('#changeProviderFormAll')[0].reset();
                                $('#psm_active_provider_all').selectpicker('refresh');

                                $('#changeProviderAll').modal('show');
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