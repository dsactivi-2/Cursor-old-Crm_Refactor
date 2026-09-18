<?php
include_once 'functions.php';
include_once 'vendor/autoload.php';
include_once 'ProcreditSRB/ProcreditSRB.php';
include_once 'ProcreditDE/ProcreditDE.php';
include_once 'Postfinance/Postfinance.php';
include_once 'BBI/BBI.php';

header("Access-Control-Allow-Origin: *");


$data = [["Intitulé", "Montant", "Date de règlement", "Bank Account", "Catégorie", "Type d'opération"]];

//$data = [["Title", "Amount", "Payment Date", "Bank Account"]];

foreach ($_FILES as $file) {
    $filename = $file["name"];
    $inputFileName = $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API/uploadedFiles/$filename";
    move_uploaded_file($file["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . '/bankConverter/API/uploadedFiles/' . $filename);

    $exchangeRates = getExchangeRates();
    $chfToEur = $exchangeRates["chfToEur"];
    $bamToEur = $exchangeRates["bamToEur"];
    $rsdToEur = $exchangeRates["rsdToEur"];

    $bank = detectBankFromFile($inputFileName, $chfToEur, $bamToEur, $rsdToEur);

    if ($bank) {
        $parsed = $bank->parse($inputFileName);
        foreach ($parsed as &$row) {
            $row[] = intval($row[1]) >= 0 ? "Inflow" : "Outflow";
            $row[1] = abs($row[1]);
        }
        $data = array_merge($data, $parsed);
    } else {
        http_response_code(400);
        die("File $filename nije validan!\n");
    }
}



$ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
$sheet = $ss->getActiveSheet();
$sheet->fromArray($data, NULL, "A1");

header('Content-Disposition: attachment;filename="myfile.xlsx"');
header('Cache-Control: max-age=0');

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($ss);
$writer->save('php://output');
