<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	} else {
		echo 0;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php getTitle(); ?></title>

		<?php include('includes/head.php'); 
		if (in_array($getUserIp, $getIpWhiteList)){
		?>
		<!-- CK Editor ---------------------------------------------------------------------------------------->
		<script src="<?php getSiteURL(); ?>ckeditor/ckeditor.js" async></script>
		<script src="pristup_poslodavcu_functions.js"></script>
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
                switch($page){

                    case "upload_leads":
                        if(isset($_GET['error'])){
                            $error = $_GET['error'];
                        } else {
                            $error = 0;
                        }
            ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="content_box">
                            <div class="row">
                                <div class = "col-lg-offset-2 col-lg-8">
                                    <div class="row">
                                        <?php if($error == 1){?>
                                            <div id="dokument_alert_len" class="row">
                                                <div class="col-md-offset-2 col-sm-8">
                                                    <div class="alert material-alert material-alert_danger">Greška: Dokument nije formatiran.</div>
                                                </div>
                                            </div>
                                        <?php }?>
                                        <form action="<?php getSiteURL(); ?>facebook_leads.php?page=insert_leads" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
                                        <div class="form-horizontal" id="form_kandidat_ugovor">
                                            <div class="form-group">
                                                <div class="col-md-offset-2 col-sm-8">
                                                    <div class="form-group materail-input-block materail-input-block_success">
                                                        <input type="text" class="form-control materail-input" name="id_kampanje" id="id_kampanje" placeholder="ID broj kampanje" required>
                                                        <span class="materail-input-block__line"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="dokument_select_alert_partner" class="row hidden" >
                                                <div class="col-md-offset-2 col-sm-8" style="margin-top: 20px !important;">
                                                    <div class="alert material-alert material-alert_danger">Greška: Nije moguće izvršiti upload ugovora jer kandidat nije dodijeljen partneru preko aplikacije za pristup poslodavcima.</div>
                                                </div>
                                            </div>
                                            <div id="dokument_alert_size" class="row hidden" >
                                                <div class="col-md-offset-2 col-sm-8" style="margin-top: 20px !important;">
                                                    <div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.</div>
                                                </div>
                                            </div>
                                            <div id="dokument_alert_ext" class="row hidden" >
                                                <div class="col-md-offset-2 col-sm-8" style="margin-top: 20px !important;">
                                                    <div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.</div>
                                                </div>
                                            </div>
                                            <div id="dokument_alert_len" class="row hidden">
                                                <div class="col-md-offset-2 col-sm-8" style="margin-top: 20px !important;">
                                                    <div class="alert material-alert material-alert_danger">Greška: Dozvoljeno je dodati maximalno 1 dokument.</div>
                                                </div>
                                            </div>
                                            <div class="form-group" id="dokumentDiv">
                                                <div class="col-md-offset-2 col-sm-8 text-center" style="margin-top: 20px;">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <span class="btn btn-default btn-file">
                                                            <span class="fileinput-new"> 
                                                                Izaberi dokument
                                                            </span>
                                                            <span class="fileinput-exists">
                                                                Promijeni
                                                            </span>
                                                            <input type="file" name="leads_export" id="leads_export">
                                                        </span>
                                                        <br>
                                                        <span style="padding-top: 10px; padding-left: 10px; padding-right: 10px; word-break: break-all;" id = "files-name">
                                                        </span>
                                                    <script>
                                                        $("#leads_export").change(function(){
                                                            $("#upload_btn").attr("disabled", false);
                                                            var file = $("#leads_export")[0].files[0].name;
                                                            $("#files-name").text(file);

                                                            if(this.files[0].size > 20388608){
                                                                $('#dokument_alert_size').removeClass('hidden');
                                                                setTimeout(function(){
                                                                    $('#dokument_alert_size').addClass('hidden');
                                                                }, 5000);
                                                                $("#leads_export").val(null);
                                                                $("#files-name").text("");
                                                                $("#upload_btn").attr("disabled", true);
                                                                return;
                                                            }
                                                            if(parseInt(this.files.lenght) > 1){
                                                                $('#dokument_alert_len').removeClass('hidden');
                                                                setTimeout(function(){
                                                                    $('#dokument_alert_len').addClass('hidden');
                                                                }, 5000);
                                                                $("#leads_export").val(null);
                                                                $("#files-name").text("");
                                                                $("#upload_btn").attr("disabled", true);
                                                                return;
                                                            }
                                                            var ext = $("#leads_export")[0].files[0].name.split('.').pop().toLowerCase();
                                                            if($.inArray(ext, ['csv', 'ods']) === -1){
                                                                $('#dokument_alert_ext').removeClass('hidden');
                                                                setTimeout(function(){
                                                                    $('#dokument_alert_ext').addClass('hidden');
                                                                }, 5000);
                                                                $("#leads_export").val(null);
                                                                $("#files-name").text("");
                                                                $("#upload_btn").attr("disabled", true);
                                                                return;
                                                            }
                                                        });
                                                    </script>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-5 col-sm-2">
                                                <button type="submit" id="upload_btn" class="btn btn-primary btn-block">Upload</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                    break;

                    case "insert_leads":
                        $tmpName = $_FILES['leads_export']['tmp_name'];
                        $kampanja_id = $_POST['id_kampanje'];
                        $findMe1 = "p";
                        $findMe2 = ":";

                        $row = 0;
                        if (($handle = fopen($tmpName, "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                if($row == 0){
                                    if($data[0] !== "Ime" && $data[1] !== "Prezime" && $data[2] !== "Telefon"){
                                        header("Location: facebook_leads.php?page=upload_leads&error=1");
                                        exit();
                                    }
                                    $row++;
                                    continue;
                                }
                                $params = [];
                                $ime = $data[0];
                                $prezime = $data[1];
                                $telefon = $data[2];
                                $pos1 = strpos($telefon, $findMe1);
                                $pos2 = strpos($telefon, $findMe2);
                                if($pos1 !== null && $pos2 !== null){
                                    $telefon = str_replace($findMe1, "", $telefon);
                                    $telefon = str_replace($findMe2, "", $telefon);
                                }

                                $params = [
                                    'kandidat_ime_dipl' => $ime,
                                    'kandidat_prezime_dipl' => $prezime,
                                    'kandidat_telefon_dipl' => $telefon,
                                    'kamp' => $kampanja_id
                                ];
                                $url = getSiteUrl()."public_kandidati.php?page=prijavaDIPLK";
                                $ch = curl_init();
                                $data = http_build_query($params);
                                
                                curl_setopt($ch,CURLOPT_URL, $url);
                                curl_setopt($ch,CURLOPT_POST, count($params));
                                curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                                $result = curl_exec($ch);
                                curl_close($ch);
                            }
                            fclose($handle);
                        }
                        header("Location: facebook_leads.php?page=upload_leads");
                        
                    break;
                }
            ?>
			</div>
		</div>
	</body>
</html>
<?php 
}else{			
		echo '
			<br/>
			<div class="alert material-alert material-alert_danger">
				<h4>NEMATE PRIVILEGIJE!</h4>
				<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
				<br />
			</div>
';} ?>