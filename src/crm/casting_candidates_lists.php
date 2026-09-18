<?php
include("includes/functions.php");
include("includes/common.php");
// Turn off all error reporting
error_reporting(0);
$getEmployeeStatus = getEmployeeStatus();

if(isset($_REQUEST["page"])) {
    $page = $_REQUEST["page"];
}else{}

switch ($page){

    case "agentFinished":
        $agent_id = $_REQUEST['id'];
        $status = $_REQUEST['status'];
        $casting_id = $_REQUEST["castingId"];
        if (isset($_REQUEST["castingId"])) {
            $casting_id = $_REQUEST["castingId"];
            $tsr_query = ' AND tsr.tsr_interview_id IN ('.$casting_id.')';
        }else{
            $tsr_query = "";
        }

        $ispis_name = getEmployeeFullnameById($agent_id);

        switch($status){
            case 3: $status_name = "Odustao";       $origin_status = 3; break;
            case 1: $status_name = "Došao";         $origin_status = 1; break;
            case 2: $status_name = "Nije došao";    $origin_status = 2; break;
            case 4: $status_name = "Ukupno";        $origin_status = 0; break;
        }
        if($origin_status == 0){
            $sql_status = ' AND tsr.tsr_status IN (1,2,3) ';
        }else{
            $sql_status = ' AND tsr.tsr_status = '.$origin_status.' ';
        }
        $sql = "
            SELECT 
                kan.kandidat_id, kan.kandidat_ime, kan.kandidat_prezime, tsr.tsr_status as ispis_status
            FROM idk_kandidati kan
            JOIN idk_tf_stats_reservations tsr
            ON kan.kandidat_id = tsr.tsr_candidate_id
            WHERE tsr.tsr_agent_id = $agent_id $sql_status $tsr_query
        ";

        $niz_kandidata = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);

    break;

    case "linkFinished":
        $link_id = $_REQUEST['id'];
        $status = $_REQUEST['status'];
        $casting_id = $_REQUEST["castingId"];
        if (isset($_REQUEST["castingId"])) {
            $casting_id = $_REQUEST["castingId"];
            $tsr_query = ' AND tsr.tsr_interview_id IN ('.$casting_id.')';
        }else{
            $tsr_query = "";
        }

        $ispis_name = getLinkNazivById($link_id);

        switch($status){
            case 3: $status_name = "Odustao";       $origin_status = 3; break;
            case 1: $status_name = "Došao";         $origin_status = 1; break;
            case 2: $status_name = "Nije došao";    $origin_status = 2; break;
            case 4: $status_name = "Ukupno";        $origin_status = 0; break;
        }
        if($origin_status == 0){
            $sql_status = ' AND tsr.tsr_status IN (1,2,3) ';
        }else{
            $sql_status = ' AND tsr.tsr_status = '.$origin_status.' ';
        }
        $sql = "
            SELECT 
                kan.kandidat_id, kan.kandidat_ime, kan.kandidat_prezime, tsr.tsr_status as ispis_status
            FROM idk_kandidati kan
            JOIN idk_tf_stats_reservations tsr
            ON kan.kandidat_id = tsr.tsr_candidate_id
            WHERE tsr.tsr_link_id = $link_id $sql_status $tsr_query
        ";

        $niz_kandidata = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
    break;
    
    case "agentCurrent":
        $agent_id = $_REQUEST['id'];
        $status = $_REQUEST['status'];
        $casting_id = $_REQUEST["castingId"];
        
        if (isset($_REQUEST["castingId"])) {
            $casting_id = $_REQUEST["castingId"];
            $pap_query = ' AND pap.pap_group_id IN ('.$casting_id.')';
            $tsr_query = ' AND tsr.tsr_interview_id IN ('.$casting_id.')';
        }else{
            $pap_query = "";
            $tsr_query = "";
        }
        
        $ispis_name = getEmployeeFullnameById($agent_id);

        switch($status){
            case 1: $status_name = "Pristao";       $origin_status = 4;    $case_sql = 2;  break;
            case 2: $status_name = "Dolazi";        $origin_status = 5;    $case_sql = 2;  break;
            case 3: $status_name = "Odustao";       $origin_status = 3;     $case_sql = 2;  break;
            case 4: $status_name = "Došao";         $origin_status = 1;     $case_sql = 2;  break;
            case 5: $status_name = "Nije došao";    $origin_status = 2;     $case_sql = 2;  break;
            case 6: $status_name = "Ukupno";        $origin_status = 0;     $case_sql = 2;  break;
        }
        if($origin_status == 0){
            // $sql_status1 = 'AND kan.kandidat_tf_status IN (16,18)';
            $sql_status1 = '';
            $sql_status2 = '';
        }else{
            // $sql_status1 = ' AND kan.kandidat_tf_status = '.$origin_status.' ' ;
            $sql_status1 = '' ;
            $sql_status2 = ' AND tsr.tsr_status = '.$origin_status.' ' ;
        }
        
        $sql_get_cand1 = "
            SELECT 
                kan.kandidat_id, kan.kandidat_ime, kan.kandidat_prezime, kan.kandidat_tf_status as ispis_status
            FROM idk_kandidati kan
            INNER JOIN idk_pp_cand_appts pca ON kan.kandidat_id = pca.pca_kandidat_id AND pca.pca_status = 1
            JOIN idk_pp_appointments pap on pap.pap_id = pca.pca_appointment_id 
            WHERE kan.tf_reserved_agent = $agent_id $sql_status1 $pap_query
            
        ";
        $sql_get_cand2 = "
            SELECT 
                kan.kandidat_id, kan.kandidat_ime, kan.kandidat_prezime, tsr.tsr_status as ispis_status
            FROM idk_tf_stats_reservations tsr
            JOIN idk_kandidati kan
            ON tsr.tsr_candidate_id = kan.kandidat_id
            WHERE tsr.tsr_agent_id = $agent_id $sql_status2 $tsr_query
        ";
        
        if($origin_status == 0){
            // var_dump($sql_get_cand1);exit();
            $res1 = $db->query($sql_get_cand1)->fetchAll(PDO::FETCH_CLASS);
            $res2 = $db->query($sql_get_cand2)->fetchAll(PDO::FETCH_CLASS);
            // $niz_kandidata = array_merge($res1, $res2);
            $niz_kandidata = $res2;
        }else{
            $niz_kandidata = $db->query(${'sql_get_cand'.$case_sql})->fetchAll(PDO::FETCH_CLASS);
        }
        // $niz_kandidata = array_values($results);
        // var_dump($niz_kandidata);
    break;

    case "linkCurrent":

        $link_id = $_REQUEST['id'];
        $status = $_REQUEST['status'];
        $casting_id = $_REQUEST["castingId"];
        
        if (isset($_REQUEST["castingId"])) {
            $casting_id = $_REQUEST["castingId"];
            $pap_query = ' AND pap.pap_group_id IN ('.$casting_id.')';
            $tsr_query = ' AND tsr.tsr_interview_id IN ('.$casting_id.')';
        }else{
            $pap_query = "";
            $tsr_query = "";
        }
        
        $ispis_name = getLinkNazivById($link_id);

        switch($status){
            case 1: $status_name = "Pristao";       $origin_status = 4;    $case_sql = 2;  break;
            case 2: $status_name = "Dolazi";        $origin_status = 5;    $case_sql = 2;  break;
            case 3: $status_name = "Odustao";       $origin_status = 3;     $case_sql = 2;  break;
            case 4: $status_name = "Došao";         $origin_status = 1;     $case_sql = 2;  break;
            case 5: $status_name = "Nije došao";    $origin_status = 2;     $case_sql = 2;  break;
            case 6: $status_name = "Ukupno";        $origin_status = 0;     $case_sql = 2;  break;
        }
        if($origin_status == 0){
            // $sql_status1 = 'AND kan.kandidat_tf_status IN (16,18)';
            $sql_status1 = '';
            $sql_status2 = '';
        }else{
            // $sql_status1 = ' AND kan.kandidat_tf_status = '.$origin_status.' ' ;
            $sql_status1 = '' ;
            $sql_status2 = ' AND tsr.tsr_status = '.$origin_status.' ' ;
        }
        
        $sql_get_cand1 = "
            SELECT 
                kan.kandidat_id, kan.kandidat_ime, kan.kandidat_prezime, kan.kandidat_tf_status as ispis_status
            FROM idk_kandidati kan
            INNER JOIN idk_pp_cand_appts pca ON kan.kandidat_id = pca.pca_kandidat_id
            JOIN idk_pp_appointments pap on pap.pap_id = pca.pca_appointment_id 
            WHERE kan.kandidat_visitedurl = $link_id $sql_status1 $pap_query
            
        ";
        $sql_get_cand2 = "
            SELECT 
                kan.kandidat_id, kan.kandidat_ime, kan.kandidat_prezime, tsr.tsr_status as ispis_status
            FROM idk_tf_stats_reservations tsr
            JOIN idk_kandidati kan
            ON tsr.tsr_candidate_id = kan.kandidat_id
            WHERE tsr.tsr_link_id = $link_id $sql_status2 $tsr_query
        ";
        
        if($origin_status == 0){
            // var_dump($sql_get_cand1);exit();
            $res1 = $db->query($sql_get_cand1)->fetchAll(PDO::FETCH_CLASS);
            $res2 = $db->query($sql_get_cand2)->fetchAll(PDO::FETCH_CLASS);
            $niz_kandidata = $res2;
        }else{
            $niz_kandidata = $db->query(${'sql_get_cand'.$case_sql})->fetchAll(PDO::FETCH_CLASS);
        }

    break;
}
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $ispis_name." - ".$status_name; ?></title>

    <?php include('includes/head.php'); 
if (in_array($getUserIp, $getIpWhiteList)){ ?>

    <script src="<?php getSiteURL(); ?>js/sortable.min.js"></script>

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
            <div class="row">
                <div class="col-xs-8">
                    <h1><i class="fa fa-tty idk_color_green" aria-hidden="true"></i> <b><?php echo $ispis_name; ?> </b> - Status: <?php echo $status_name;?> </h1>
                </div>
                <div class="col-xs-12">
                    <hr />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="content_box">
                        <script type="text/javascript">
                            $(document).ready(function() {
                                $('#idk_table').DataTable({

                                    responsive: true,

                                    "order": [[ 0, "asc" ]],

                                        "bAutoWidth": false,

                                    "aoColumns": [
                                            { "width": "10%" },
                                            { "width": "90%" },
                                            // { "width": "50%" },
                                        ]
                                });
                            } );
                        </script>
                        <table id="idk_table" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ime i prezime</th>
                                    <!-- <th>Status</th> -->
                                </tr>
                            </thead>    
                            <tbody>
                                <?php 
                                foreach($niz_kandidata as $kandidat){
                                    echo '
                                    <tr>
                                        <td><a href="kandidati?page=open&id='.$kandidat->kandidat_id.'" target="_BLANK">'.$kandidat->kandidat_id.'</a></td>
                                        <td><a href="kandidati?page=open&id='.$kandidat->kandidat_id.'" target="_BLANK">'.$kandidat->kandidat_ime.' '.$kandidat->kandidat_prezime.'</a></td>
                                        </tr>
                                        ';
                                    }
                                    // <td>'.$kandidat->ispis_status.'</td>
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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

