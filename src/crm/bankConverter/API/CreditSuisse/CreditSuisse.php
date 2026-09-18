<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API" . "/functions.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API" . "/Bank/Bank.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CreditSuisse extends Bank
{
    static $incomeRules = [
        "Solutions30 Field Services Sud GmbH"   => "Solutions30 Field Services Sud GmbH",
        "JMF Consulting UG"                     => "JMF Consulting UG",
        "NKG Fiberservice GmbH"                 => "NKG Fiberservice GmbH",
        "ENPAL"                                 => "ENPAL",
        "E-CONNECT"                             => "E-CONNECT",
        "ENPAL S30"                             => "ENPAL S30",
        "AMA-INT "                              => "AMA-INT GmbH",
        "DAK"                                   => "DAK"
    ];

    static $outcomeRules = [
        "VIA MEDIA"                             => "VIA MEDIA",
        "GLOSSA CENTAR ZA NJEMACKI JEZIK"       => "GLOSSA CENTAR ZA NJEMAČKI JEZIK",
        "JMF Consulting UG"                     => "JMF Consulting UG",
        "Sunrise"                               => "Sunrise",
        "Swisscom"                              => "Swisscom",
        "CAMINADA"                              => "CAMINADA",
        "AKAD"                                  => "AKAD",
        "GO CLOUD"                              => "Go Cloud",
        "AGICAP"                                => "Agicap",
        "North Data"                            => "North Data",
        "EIDG. FINANZVERWALTUNG"                => "EIDG. FINANZVERWALTUNG"
    ];

    function parse($inputFileName)
    {
        $spreadsheet = null;

        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($inputFileName)->getActiveSheet();
        } catch (Exception $e) {
            http_response_code(400);
            die("Neispravan Excel file!\n");
        }

        // Provjeri da li je format spreadsheeta ispravan, tj. da li je u očekivanom formatu.
        self::assertCreditSuisseValid($spreadsheet);

        // Prvi red je naslov
        $data = [];

        $account = $spreadsheet->getCell("B5")->getValue();

        // Na 10. redu počinju transakcije.
        $redTabele = 10;
        while ($spreadsheet->getCell("A{$redTabele}")->getValue() != "TOTAL SPALTE") {
            // Izvuci datum, tekst, duguje, potrazuje.
            list($date, $title, $duguje, $potrazuje) = self::parseRow($spreadsheet, $redTabele);

            // Nađi koja od varijabli $duguje i $potrazuje nije 0, to je amount transakcije.
            $amount = self::getAmount($duguje, $potrazuje);

            $category = self::getCategory($title, $amount);

            array_push($data, [(string)$title, (string)$amount, (string)$date, (string)$account, (string)$category]);

            $redTabele++;
        }

        return $data;
    }

    static function getCategory($title, $amount)
    {
        $category = "";
        $numberOfMatches = 0;
        $dict = $amount > 0 ? self::$incomeRules : self::$outcomeRules;

        foreach ($dict as $key => $value) {
            if (strpos(mb_strtolower($title), mb_strtolower($key)) !== false) {
                $numberOfMatches++;
                $category = $value;
            }
        }

        // If there is more than one match, return uncategorized.
        if ($numberOfMatches == 1) {
            return $category;
        } else {
            return "Uncategorized";
        }
    }

    static function isFileValid($inputFileName)
    {
        $spreadsheet = null;

        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($inputFileName)->getActiveSheet();
        } catch (Exception $e) {
            return false;
        }

        if (!self::isSpreadSheetValid($spreadsheet)) {
            return false;
        }

        return true;
    }

    static function isSpreadSheetValid($spreadsheet)
    {
        // Provjeri očekivane vrijednosti.
        if (
            !isValueInCell($spreadsheet, 5, "A", "Konto") ||
            !isValueInCell($spreadsheet, 9, "A", "Buchungsdatum") ||
            !isValueInCell($spreadsheet, 9, "B", "Text") ||
            !isValueInCell($spreadsheet, 9, "C", "Belastung") ||
            !isValueInCell($spreadsheet, 9, "D", "Gutschrift")
        ) {
            return false;
        }


        // Provjeriti da se u prvoj koloni postoji ćelija sa tekstom TOTAL SPALTE.
        // Ona označava kraj tabele, ako nje ne bude, nećemo znati gdje stati sa
        // čitanjem transakcija.

        $sentinelFound = false;
        foreach ($spreadsheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            //$cellIterator->setIterateOnlyExistingCells(true);
            foreach ($cellIterator as $cell) {
                if ($cell->getValue() == "TOTAL SPALTE" && $cell->getCoordinate()[0] == "A") {
                    $sentinelFound = true;
                }
            }
        }

        if (!$sentinelFound) {
            return false;
        }

        return true;
    }

    static function assertCreditSuisseValid($spreadsheet)
    {
        // Provjeri očekivane vrijednosti.
        assertCellValues($spreadsheet, 5, "A", "Konto");
        assertCellValues($spreadsheet, 9, "A", "Buchungsdatum");
        assertCellValues($spreadsheet, 9, "B", "Text");
        assertCellValues($spreadsheet, 9, "C", "Belastung");
        assertCellValues($spreadsheet, 9, "D", "Gutschrift");

        // Provjeriti da se u prvoj koloni postoji ćelija sa tekstom TOTAL SPALTE.
        // Ona označava kraj tabele, ako nje ne bude, nećemo znati gdje stati sa
        // čitanjem transakcija.

        $sentinelFound = false;
        foreach ($spreadsheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            //$cellIterator->setIterateOnlyExistingCells(true);
            foreach ($cellIterator as $cell) {
                if ($cell->getValue() == "TOTAL SPALTE" && $cell->getCoordinate()[0] == "A") {
                    $sentinelFound = true;
                }
            }
        }

        if (!$sentinelFound) {
            http_response_code(400);
            die("Format nije ispravan! TOTAL SPALTE se ne nalazi na ispravnom mjestu!\n");
        }
    }

    static function parseRow($spreadsheet, $row)
    {
        $kolonaOpisa = "B";
        $kolonaDuguje = "C";
        $kolonaPotrazuje = "D";
        $kolonaDatum = "E";

        $date = $spreadsheet->getCell("{$kolonaDatum}{$row}")->getValue();
        $title = $spreadsheet->getCell("{$kolonaOpisa}{$row}")->getValue();
        $duguje = $spreadsheet->getCell("{$kolonaDuguje}{$row}")->getValue();
        $potrazuje = $spreadsheet->getCell("{$kolonaPotrazuje}{$row}")->getValue();

        return array($date, $title, $duguje, $potrazuje);
    }

    function getAmount($duguje, $potrazuje)
    {
        $amount = "";
        if (($duguje != "0" && $duguje != "") && ($potrazuje != "0" && $potrazuje != "")) {
            http_response_code(400);
            die("U jednoj transakciji postoji i uplata i isplata!\n");
        }

        if ($duguje != "0" && $duguje != "") {
            $amount = "-" . $duguje;
        } else if ($potrazuje != "0" && $potrazuje != "") {
            $amount =  $potrazuje;
        } else {
            http_response_code(400);
            die("Ne postoje ni uplata ni isplata");
        }

        return floatval($amount) * $this->currencyMultiplier;
    }

    static function assertCellValues($spreadsheet, $row, $column, $expectedValue)
    {
        if ($spreadsheet->getCell("{$column}{$row}")->getValue() != $expectedValue) {
            http_response_code(400);
            die("Format nije ispravan! $expectedValue se ne nalazi na ispravnom mjestu!\n");
        }
    }
}
