<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API" . "/functions.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API" . "/Bank/Bank.php";

class Raiffeisen extends Bank
{
    function parse($inputFileName)
    {
        $file = null;
        $file = simplexml_load_file($inputFileName);

        if ($file == false) {
            http_response_code(400);
            die("Invalid XML file!");
        }

        $data = [];

        $account = $file->Zaglavlje->attributes()->IBANBroj;


        foreach ($file->Stavke as $transakcija) {
            $date = $transakcija->attributes()->DatumValute;
            $title = implode("\n", [$transakcija->attributes()->Opis, $transakcija->attributes()->Napomena]);

            $duguje = $transakcija->attributes()->Duguje;
            $potrazuje = $transakcija->attributes()->Potrazuje;

            if ($duguje != "0" && $potrazuje != "0") {
                http_response_code(400);
                die("U jednoj transakciji postoji i uplata i isplata!\n");
            }

            if ($duguje != 0) {
                $amount = $duguje;
            } else if ($potrazuje != 0) {
                $amount = $potrazuje;
            } else {
                http_response_code(400);
                die("Ne postoje ni uplata ni isplata");
            }

            $amount = floatval($amount) * $this->currencyMultiplier;

            array_push($data, [(string)$title, $amount, (string)$date, (string)$account]);
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
        if(!isset($file->Zaglavlje->attributes()->IBANBroj))
        {
            return false;
        }
        foreach ($file->Stavke as $transakcija) {
            if(!isset($transakcija->attributes()->DatumValute) || $transakcija->attributes()->DatumValute == "")
            {
                return false;
            }

            if(!isset($transakcija->attributes()->Duguje))
            {
                return false;
            }
            if(!isset($transakcija->attributes()->Potrazuje))
            {
                return false;
            }
        }

        return true;
    }
}
