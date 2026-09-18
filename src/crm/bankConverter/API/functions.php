<?php
include_once "./ProcreditBiH/ProcreditBiH.php";
include_once "./ProcreditSRB/ProcreditSRB.php";
include_once "./ProcreditDE/ProcreditDE.php";
include_once "./Postfinance/Postfinance.php";
include_once "./CreditSuisse/CreditSuisse.php";
include_once "./BBI/BBI.php";
include_once "./Raiffeisen/Raiffeisen.php";
ini_set("display_errors", 0);
function saveCsv($data)
{
    $fp = fopen("out.csv", 'w');

    foreach ($data as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
}

function isValueInCell($spreadsheet, $row, $column, $expectedValue)
{
    if ($spreadsheet->getCell("{$column}{$row}")->getValue() != $expectedValue) {
        return false;
    }

    return true;
}

function assertCellValues($spreadsheet, $row, $column, $expectedValue)
{
    if (!isValueInCell($spreadsheet, $row, $column, $expectedValue)) {
        http_response_code(400);
        die("{$expectedValue} nije na ispravnom mjestu.");
    }
}
function detectBankFromFile($inputFileName, $chfToEur, $bamToEur, $rsdToEur)
{
    // Ako fajl odgovara za više banki, imamo problem!
    $numberOfValidBanks = 0;
    $ret = null;

    if (ProcreditBiH::isFileValid($inputFileName)) {
        $numberOfValidBanks++;
        $ret = new ProcreditBiH($bamToEur);
    }
    if (ProcreditSRB::isFileValid($inputFileName)) {
        $numberOfValidBanks++;
        $ret = new ProcreditSRB($rsdToEur);
    }
    if (ProcreditDE::isFileValid($inputFileName)) {
        $numberOfValidBanks++;
        $ret = new ProcreditDE(1);
    }
    if (BBI::isFileValid($inputFileName)) {
        $numberOfValidBanks++;
        $ret = new BBI($bamToEur);
    }
    if (Postfinance::isFileValid($inputFileName)) {
        $numberOfValidBanks++;
        $ret = new Postfinance(1);
    }
    if (CreditSuisse::isFileValid($inputFileName)) {
        $numberOfValidBanks++;
        $ret = new CreditSuisse($chfToEur);
    }
    if (Raiffeisen::isFileValid($inputFileName)) {
        $numberOfValidBanks++;
        $ret = new Raiffeisen($bamToEur);
    }

    if ($numberOfValidBanks > 1) {
        http_response_code(400);
        die("Fajl odgovara za više banki!");
    }
    return $ret;
}

function getExchangeRates()
{
    $chfToEur = null;
    $rsdToEur = null;
    $bamToEur = 0.5113;

    $today = new DateTime();
    $todayString = $today->format('Y-m-d');

    $url = "https://data-api.ecb.europa.eu/service/data/EXR/D.CHF.EUR.SP00.A?lastNObservations=1&endDate={$todayString}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/xml',
    ));
    $data = curl_exec($ch);
    curl_close($ch);


    try {
        $xml = simplexml_load_string($data);
        $v = $xml->xpath('//generic:ObsValue')[0];
        $f = floatval($v->attributes()[0]);
        if (is_nan($f) || $f === 0) {
            throw new Exception();
        }
        $chfToEur = 1 / $f;
    } catch (Exception $e) {
        http_response_code(500);
        exit;
    }

    $url = "https://kurs.resenje.org/api/v1/currencies/EUR/rates/today";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($data, true);
    $rsdToEur = 1 / floatval($json["exchange_middle"]);

    return [
        "chfToEur" => $chfToEur,
        "rsdToEur" => $rsdToEur,
        "bamToEur" => $bamToEur
    ];
}
