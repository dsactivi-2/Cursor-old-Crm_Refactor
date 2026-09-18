<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API" . "/functions.php";

class BBI extends Bank
{
    function parse($inputFileName)
    {
        $file = null;
        $file = simplexml_load_file($inputFileName);

        if ($file == false) {
            http_response_code(400);
            die("Invalid XML file!");
        }

        $bbiRacun = "1414755320046893";

        $data = [];
        foreach ($file->TRANSAKCIJE->TRANSAKCIJA as $transakcija) {
            $iznos = floatval($transakcija->IZNOS);
            if ($transakcija->DIRECTION == "+") {
                array_push($data, [(string)$transakcija->NAPOMENA, $iznos * $this->currencyMultiplier, (string)$transakcija->DATUM_KNJIZENJA, $bbiRacun, "Uncategorized"]);
            } else if ($transakcija->DIRECTION == "-") {
                array_push($data, [(string)$transakcija->NAPOMENA, $iznos * (-1) * $this->currencyMultiplier, (string)$transakcija->DATUM_KNJIZENJA, $bbiRacun, "Uncategorized"]);
            }
        }

        return $data;
    }
    static function isFileValid($inputFileName)
    {
        $file = null;
        libxml_use_internal_errors(true);
        $file = simplexml_load_file($inputFileName);

        if ($file == false) {
            return false;
        }

        if (!isset($file->TRANSAKCIJE) || !isset($file->ZAGLAVLJE)) {
            return false;
        }

        foreach ($file->TRANSAKCIJE->TRANSAKCIJA as $transakcija) {
            if (!isset($transakcija->DIRECTION)) {
                return false;
            }
            if ($transakcija->DIRECTION != "+" && $transakcija->DIRECTION != "-") {
                return false;
            }
            if (
                !isset($transakcija->NAPOMENA) ||
                !isset($transakcija->IZNOS)    ||
                !isset($transakcija->DATUM_KNJIZENJA)
            ) {
                return false;
            }
        }

        return true;
    }
}
