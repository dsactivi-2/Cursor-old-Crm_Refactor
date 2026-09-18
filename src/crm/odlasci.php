<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());
    
    if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
        $page = "main_table";
		header("Location: odlasci?page=main_table");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Nalozi - Odlasci | <?php getTitle(); ?></title>

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
            switch ($page){

                case "main_table":
                    if(isset($_POST["filter_select_kompanija"])){
                        $company_array = $_POST["filter_select_kompanija"];
                        $companies_list = implode(',', $company_array);
                        $query_search_order_select = "AND kompanija_id IN ($companies_list)";
                        if(isset($_POST["filter_select_nalog"])){
                            $order_array = $_POST["filter_select_nalog"];
					        $order_list = implode(',', $order_array);
                            $query_search = "AND nalog_id IN ($order_list)";
                        }else{
                            $order_array = [];
                            $order_list = "";
                            $query_search = ""; 
                        }
                    }else{
                        $company_array = [];
                        $order_array = [];
                        $companies_list = "";
                        $order_list = "";
                        $query_search = "";
                        $query_search_order_select = "";
                    }

                    ?>
                    <div class="row">
                        <div class="col-xs-4">
                            <h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Nalozi - odlasci</h1>
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
                                        <form method="POST" action="" id="form_search">
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-lg-4 col-md-4">
                                                    <label for="filter_select_kompanija">Kompanija:</label>
                                                    <select id="filter_select_kompanija" class="selectpicker" name="filter_select_kompanija[]" data-live-search="true"  data-actions-box="true" multiple>
                                                        <?php 
                                                            $select_query = $db->prepare("SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, comp.company_name
                                                                FROM idk_nalozi
                                                                INNER JOIN idk_companies comp ON kompanija_id = comp.company_id
                                                                JOIN idk_kandidati kan ON nalog_id = kan.kandidat_nalog_id
                                                                WHERE nalog_status NOT IN (8,12)
                                                                GROUP BY kompanija_id  
                                                                ORDER BY kompanija_id DESC"
                                                            );

                                                            $select_query->execute();

                                                            $companies = $select_query->fetchAll();
                                                            foreach($companies as $company){
                                                                $selected_company = in_array($company["kompanija_id"], $company_array) ? 'selected' : '';
                                                                echo '<option '.$selected_company.' value="'.$company["kompanija_id"].'">'.$company["company_name"].'</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                                <script>
                                                    $('#filter_select_kompanija').on('change',function(){
                                                        var selectedValues = $('#filter_select_kompanija').val();
                                                        $.ajax({
                                                            url: 'ajax_data.php?page=get_company_orders_odlasci',
                                                            type: 'POST',
                                                            dataType: 'html',
                                                            data: {
                                                                    'companies': selectedValues
                                                                },
                                                            success: function(data) {
                                                                $("#filter_select_nalog").html(data).selectpicker("refresh");  
                                                            },
                                                            error: function (xhr, ajaxOptions, thrownError) {
                                                                alert(xhr.status);
                                                                alert(thrownError);
                                                            }
                                                        });
                                                    });
                                                </script>
                                                <div class="col-lg-4 col-md-4">
                                                    <label for="filter_select_nalog">Nalog:</label>
                                                    <select id="filter_select_nalog" class="selectpicker" name="filter_select_nalog[]" data-live-search="true" data-actions-box="true"  multiple>
                                                        <?php 
                                                            $select_query = $db->prepare("SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, comp.company_name
                                                                FROM idk_nalozi
                                                                INNER JOIN idk_companies comp ON kompanija_id = comp.company_id
                                                                JOIN idk_kandidati kan ON nalog_id = kan.kandidat_nalog_id
                                                                WHERE nalog_status NOT IN (8,12)
                                                                $query_search_order_select  
                                                                GROUP BY nalog_id  
                                                                ORDER BY `idk_nalozi`.`nalog_id` DESC"
                                                            );

                                                            $select_query->execute();

                                                            $orders = $select_query->fetchAll();
                                                            foreach($orders as $order){
                                                                $selected_nalog = in_array($order["nalog_id"], $order_array) ? 'selected' : '';
                                                                echo '<option '.$selected_nalog.' value="'.$order["nalog_id"].'">'.$order["nalog_broj"]." - ".$order["nalog_naziv"].' ('.$order["company_name"].')</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="row idk_margin_top20" style="display:flex; align-items: flex-end;">
                                                    <div class="col-lg-4 col-md-3 d-flex align-items-center">
                                                        <button type="submit" id="filter_button_trazi" style="width:100%" class="btn btn-success">Traži</button>
                                                    </div>
                                                </div>
                                            </div>
                                            </form>
                                            <script type="text/javascript">
                                                $(document).ready(function() {
                                                    var table = $('#idk_table').DataTable({
                                                        responsive: true,
                                                        "order": [[ 0, "desc" ]],
                                                        "aoColumns": [
                                                                { "width": "10%" },
                                                                { "width": "20%" },
                                                                { "width": "20%" },
                                                                { "width": "5%" },

                                                                { "width": "5%" },
                                                                { "width": "5%" },
                                                                { "width": "5%" },
                                                                { "width": "5%" },
                                                                { "width": "5%" },
                                                                { "width": "5%" },
                                                                { "width": "5%" },
                                                                { "width": "5%" },
                                                            ]
                                                    });
                                                } );
                                            </script>
                                            <table id="idk_table" class="display" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" rowspan="2">Broj</th>
                                                        <th class="text-center" rowspan="2">Naziv</th>
                                                        <th class="text-center" rowspan="2">Kompanija</th>
                                                        <th class="text-center" rowspan="2">Traženo</th>
                                                        <th class="text-center" colspan="2">Ukupno Nađeno</th>
                                                        <th class="text-center" colspan="2">Završeno</th>
                                                        <th class="text-center" colspan="2">Početak rada</th>
                                                        <th class="text-center" colspan="2">U procesu</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">%</th>
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">%</th>
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">%</th>
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">%</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $query = $db->prepare("
                                                        SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, nalog_status, comp.company_name, nalog_potrebno_kandidata, 
                                                            SUM(CASE  
                                                                    WHEN kan.kandidat_status_prijave in (7,8,9,12,15,18,21,24,27) THEN 1 ELSE 0 END
                                                            ) as broj_u_procesu,
                                                            SUM(CASE  
                                                                    WHEN kan.kandidat_status_prijave = 10 THEN 1 ELSE 0 END
                                                            ) as broj_poc_rada,
                                                            SUM(CASE  
                                                                    WHEN kan.kandidat_status_prijave = 4 THEN 1 ELSE 0 END
                                                            ) as broj_zavrsen
                                                        FROM idk_nalozi
                                                        INNER JOIN idk_companies comp ON kompanija_id = comp.company_id
                                                        JOIN idk_kandidati kan ON nalog_id = kan.kandidat_nalog_id
                                                        WHERE nalog_status NOT IN (8,12)
                                                        $query_search 
                                                        GROUP BY nalog_id  
                                                        ORDER BY `idk_nalozi`.`nalog_id` DESC;");

                                                    $query->execute();

                                                    while($row = $query->fetch()){

                                                        $nalog_id = $row['nalog_id'];
                                                        $nalog_broj = $row['nalog_broj'];
                                                        $kompanija_id = $row['kompanija_id'];
                                                        $nalog_naziv = $row['nalog_naziv'];
                                                        $kompanija = $row['company_name'];
                                                        $nalog_status = $row['nalog_status'];
                                                        $nalog_potrebno_kandidata = $row['nalog_potrebno_kandidata'];
                                                        $broj_u_procesu = $row['broj_u_procesu'];
                                                        $broj_poc_rada = $row['broj_poc_rada'];
                                                        $broj_zavrsen = $row['broj_zavrsen'];
                                                        $ukupno_nadjeno = $broj_u_procesu + $broj_poc_rada + $broj_zavrsen;
                                                        
                                                        
                                                        ?>
                                                        <tr>
                                                            <td class="text-center"><?php echo $nalog_broj; ?></td>
                                                            <td class="text-center"><a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id; ?>" target="_BLANK"><?php echo $nalog_naziv; ?></td>
                                                            <td class="text-center"><?php echo $kompanija; ?></td>
                                                            <td class="text-center"><?php echo $nalog_potrebno_kandidata; ?></td>
                                                            <td class="text-center" style="border-left: 3px solid #000;"><a href="<?php getSiteURL(); ?>odlasci?page=candidates_list&nalog_id=<?php echo $nalog_id; ?>&type=0" target="_BLANK"><?php echo $ukupno_nadjeno; ?></a></td>
                                                            <td class="text-center" style="border-right: 3px solid #000;"><?php echo round((100 * $ukupno_nadjeno)/$nalog_potrebno_kandidata, 2) . " %"; ?></td>
                                                            <td class="text-center"><a href="<?php getSiteURL(); ?>odlasci?page=candidates_list&nalog_id=<?php echo $nalog_id; ?>&type=1" target="_BLANK"><?php echo $broj_zavrsen; ?></a></td>
                                                            <td class="text-center" style="border-right: 1px solid #000;"><?php echo round((100 * $broj_zavrsen)/$nalog_potrebno_kandidata, 2) . " %"; ?></td>
                                                            <td class="text-center"><a href="<?php getSiteURL(); ?>odlasci?page=candidates_list&nalog_id=<?php echo $nalog_id; ?>&type=2" target="_BLANK"><?php echo $broj_poc_rada; ?></a></td>
                                                            <td class="text-center" style="border-right: 1px solid #000;"><?php echo round((100 * $broj_poc_rada)/$nalog_potrebno_kandidata, 2) . " %"; ?></td>
                                                            <td class="text-center"><a href="<?php getSiteURL(); ?>odlasci?page=candidates_list&nalog_id=<?php echo $nalog_id; ?>&type=3" target="_BLANK"><?php echo $broj_u_procesu; ?></a></td>
                                                            <td class="text-center" style="border-right: 1px solid #000;"><?php echo round((100 * $broj_u_procesu)/$nalog_potrebno_kandidata, 2) . " %"; ?></td>
                                                        
                                                        </tr><?php 
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                break; 

                case "candidates_list":
                    $nalog_id = $_GET['nalog_id'];
                    $type = $_GET['type'];

                    $nalog_naziv = getNalogNameById($nalog_id);
                    switch($type){
                        case 0:
                            $query_status_part = " AND kandidat_status_prijave IN (4,10,7,8,9,12,15,18,21,24,27) ";
                            $status_liste = "Ukupno";
                        break;
                        case 1:
                            $query_status_part = " AND kandidat_status_prijave IN (4) ";
                            $status_liste = "Završeni";
                        break;
                        case 2:
                            $query_status_part = " AND kandidat_status_prijave IN (10) ";
                            $status_liste = "Početak rada";
                        break;
                        case 3:
                            $query_status_part = " AND kandidat_status_prijave IN (7,8,9,12,15,18,21,24,27) ";
                            $status_liste = "U procesu";
                        break;
                        default: 
                            $query_status_part = "";
                            $status_liste = "";
                    } ?>
                    <div class="row">
                        <div class="col-xs-4">
                            <h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Nalog <?php echo $nalog_naziv; ?> - <?php echo $status_liste; ?></h1>
                        </div>
                        <div class="col-xs-12">
                            <hr />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <div class="content_box">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                var table = $('#idk_table').DataTable({
                                                    responsive: true,
                                                   
                                                });
                                            } );
                                        </script>
                                        <table id="idk_table" class="display" cellspacing="0" width="100%">
                                            <thead>
                                               
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Ime</th>
                                                    <th>Prezime</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $query = $db->prepare("
                                                    SELECT kandidat_id, kandidat_ime, kandidat_prezime, ksp.status_naziv 
                                                    FROM idk_kandidati 
                                                    JOIN idk_kandidat_status_prijave ksp ON kandidat_status_prijave = ksp.status_id
                                                    WHERE kandidat_nalog_id = $nalog_id $query_status_part
                                                ");

                                                $query->execute();

                                                while($row = $query->fetch()){

                                                    $kandidat_id = $row['kandidat_id'];
                                                    $kandidat_ime = $row['kandidat_ime'];
                                                    $kandidat_prezime = $row['kandidat_prezime'];
                                                    $status_naziv = $row['status_naziv'];
                                                    ?>
                                                    <tr>
                                                        <td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" target="_BLANK"><?php echo $kandidat_id; ?></td>
                                                        <td><?php echo $kandidat_ime; ?></td>
                                                        <td><?php echo $kandidat_prezime; ?></td>
                                                        <td class="text-center"><?php echo $status_naziv; ?></td>

                                                    </tr><?php 
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                break;
            } ?>
            
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