<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/env.php");
Global $envConfig;

  $mainUrl = $envConfig->WEBSITE_URL;
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
    // $ip = "nema";
}
// echo "1. ".$_SERVER['HTTP_CLIENT_IP'];
// echo "<br>2. ".$_SERVER['HTTP_CF_CONNECTING_IP'];
// echo "<br>3. ".$_SERVER['HTTP_X_FORWARDED_FOR'];
// echo "<br>4. ".$_SERVER['REMOTE_ADDR'];


$ipdat = @json_decode(file_get_contents( 
    "http://www.geoplugin.net/json.gp?ip=" . $ip)); 
   
$countryCode = strtolower($ipdat->geoplugin_countryCode);
if($countryCode == "de"){
    $countryUrl = $mainUrl . "de";
}elseif($countryCode == "rs"){
    $countryUrl = $mainUrl . "sr";
}elseif($countryCode == "en"){
    $countryUrl = $mainUrl . "en";
}else{
    $countryUrl = $mainUrl . "bs";
}

header("location: " . $countryUrl . "");

?>
