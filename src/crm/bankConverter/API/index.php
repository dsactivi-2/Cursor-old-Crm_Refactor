<?php
include_once 'functions.php';
include_once 'vendor/autoload.php';
include_once 'ProcreditSRB/ProcreditSRB.php';
include_once 'ProcreditDE/ProcreditDE.php';
include_once 'Postfinance/Postfinance.php';
include_once 'BBI/BBI.php';

header("Access-Control-Allow-Origin: *");

if (!isset($_POST["chfToEur"]) || !isset($_POST["bamToEur"])) {
    http_response_code(500);
    die("Missing parameters!\n");
}
$chfToEur = floatval($_POST["chfToEur"]);
$bamToEur = floatval($_POST["bamToEur"]);
$rsdToEur = floatval($_POST["rsdToEur"]);
$data = [["Title", "Amount", "Payment Date", "Bank Account", "Category"]];

foreach ($_FILES as $file) {
    $filename = $file["name"];
    $inputFileName = $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API/uploadedFiles/$filename";
    move_uploaded_file($file["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . '/bankConverter/API/uploadedFiles/' . $filename);


    $bank = detectBankFromFile($inputFileName, $chfToEur, $bamToEur, $rsdToEur);

    if ($bank) {
        $data = array_merge($data, $bank->parse($inputFileName));
    } else {
        http_response_code(400);
        die("File $filename nije validan!\n");
    }

}

// round to 2 decimal places
for ($i = 1; $i < count($data); $i++) {
    $data[$i][1] = round($data[$i][1], 2);
}

echo json_encode($data);
