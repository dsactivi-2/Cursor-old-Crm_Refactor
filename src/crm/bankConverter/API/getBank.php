<?php
include_once 'functions.php';
include_once 'vendor/autoload.php';
include_once 'ProcreditSRB/ProcreditSRB.php';
include_once 'ProcreditDE/ProcreditDE.php';
include_once 'Postfinance/Postfinance.php';
include_once 'BBI/BBI.php';

header("Access-Control-Allow-Origin: *");

foreach ($_FILES as $file) {
    $filename = $file["name"];
    $inputFileName = $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API/uploadedFiles/$filename";
    move_uploaded_file($file["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . '/bankConverter/API/uploadedFiles/' . $filename);


    $bank = detectBankFromFile($inputFileName, 1, 1, 1);

    if ($bank) {
        echo get_class($bank);
    } else {
        http_response_code(400);
        die("File $filename nije validan!\n");
    }

}
