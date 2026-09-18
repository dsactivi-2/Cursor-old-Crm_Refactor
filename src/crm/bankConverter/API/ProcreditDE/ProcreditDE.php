<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API" . "/functions.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API" . "/Bank/Bank.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ProcreditDE extends Bank
{
    static $incomeRules = [
        "A.T.U GMBH + CO. KG"                   => "A.T.U GMBH ",
        "Solutions 30 Operations GmbH"          => "Solutions 30 Operations GmbH",
        "Ulmer Fleisch GmbH"                    => "Ulmer Fleisch GmbH",
        "Solutions30 Field Services Süd GmbH"   => "Solutions30 Field Services Süd GmbH",
        "JobStep Int GmbH"                      => "JobStep Int GmbH",
        "Hammer "                               => "Hammer",
        "NKG Fiberservice GmbH"                 => "NKG Fiberservice GmbH"
    ];

    static $outcomeRules = [
        "1+1 Telecom GmbH"                  => "1+1 Telecom GmbH",
        "1u1 Telecom GmbH"                  => "1+1 Telecom GmbH",
        "AOK Baden-Wuerttemberg"            => "AOK Baden-Wuerttemberg",
        "DAK - Gesundheit"                  => "DAK-Gesundheit",
        "STRATO AG"                         => "STRATO AG",
        "Telekom Deutschland GmbH"          => "Telekom Deutschland GmbH",
        "Finanzamt Nuertingen"              => "Finanzamt Nuertingen",
        "Nufin"                             => "Nufin",
        "Acconsis GmbH"                     => "Acconsiss",
        "OFFICE + SERVICE GmbH"             => "OFFICE + SERVICE GmbH",
        "Kuenstlersozialkasse"              => "Kuenstlersozialkasse",
        "DINO KLEPO"                        => "DINO KLEPO",
        "DKV Euro Service GmbH Co"          => "DKV Euro Service GmbH Co",
        "Stadtkasse Esslingen am Neckar"    => "Stadtkasse Esslingen am Neckar"
    ];

    function parse($inputFileName)
    {
        $spreadsheet = null;

        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($inputFileName)->getActiveSheet();
        } catch (Exception $e) {
            http_response_code(400);
            die("Neispravan Excel file!\n");
        }

        // Provjeri da li je format spreadsheeta ispravan, tj. da li je u očekivanom formatu.
        self::assertProcreditDEValid($spreadsheet);

        $data = [];

        $account = explode(" ", $spreadsheet->getCell("B7")->getValue())[1];

        // Na 10. redu počinju transakcije.
        $redTabele = 16;
        while ($spreadsheet->getCell("B{$redTabele}")->getValue() != "Anfangssaldo") {
            // Izvuci datum, tekst, duguje, potrazuje.
            list($date, $title, $amount) = self::parseRow($spreadsheet, $redTabele);

            $category = self::getCategory($title, $amount);
            array_push($data, [(string)$title, (string)$amount, (string)$date, (string)$account, (string)$category]);

            $redTabele += 2;
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
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($inputFileName)->getActiveSheet();
        } catch (Exception $e) {
            return false;
        }

        if (!self::isSpreadsheetValid($spreadsheet)) {
            return false;
        }


        return true;
    }
    static function isSpreadsheetValid($spreadsheet)
    {
        $kontoCell = $spreadsheet->getCell("B7")->getValue();
        if (explode(" ", $kontoCell)[0] != "Kontonummer:") {
            return false;
        }

        $datesCell = $spreadsheet->getCell("B15")->getValue();
        $dates = explode("\n", $datesCell);

        if ($dates[0] != "Buchungsdatum" || $dates[1] != "Wertstellungsdatum") {
            return false;
        }

        if (!isValueInCell($spreadsheet, 15, "C", "Buchungsinformation")) {
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
                if ($cell->getValue() == "Anfangssaldo" && $cell->getCoordinate()[0] == "B") {
                    $sentinelFound = true;
                }
            }
        }

        if (!$sentinelFound) {
            return false;
        }

        return true;
    }
    static function assertProcreditDEValid($spreadsheet)
    {
        // Provjeri očekivane vrijednosti.

        $kontoCell = $spreadsheet->getCell("B7")->getValue();
        if (explode(" ", $kontoCell)[0] != "Kontonummer:") {
            http_response_code(400);
            die("Format nije ispravan! Kontonummer se ne nalazi na ispravnom mjestu!\n");
        }

        $datesCell = $spreadsheet->getCell("B15")->getValue();
        $dates = explode("\n", $datesCell);

        if ($dates[0] != "Buchungsdatum" || $dates[1] != "Wertstellungsdatum") {
            http_response_code(400);
            die("Format nije ispravan! Datumi se ne nalaze na ispravnom mjestu!\n");
        }

        assertCellValues($spreadsheet, 15, "C", "Buchungsinformation");


        // Provjeriti da se u prvoj koloni postoji ćelija sa tekstom TOTAL SPALTE.
        // Ona označava kraj tabele, ako nje ne bude, nećemo znati gdje stati sa
        // čitanjem transakcija.

        $sentinelFound = false;
        foreach ($spreadsheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            //$cellIterator->setIterateOnlyExistingCells(true);
            foreach ($cellIterator as $cell) {
                if ($cell->getValue() == "Anfangssaldo" && $cell->getCoordinate()[0] == "B") {
                    $sentinelFound = true;
                }
            }
        }

        if (!$sentinelFound) {
            http_response_code(400);
            die("Format nije ispravan! TOTAL SPALTE se ne nalazi na ispravnom mjestu!\n");
        }
    }

    function parseRow($spreadsheet, $row)
    {
        $kolonaOpisa = "C";
        $kolonaIznos = "G";
        $kolonaDatum = "B";

        $nextRow = $row + 1;

        $date = $spreadsheet->getCell("{$kolonaDatum}{$row}")->getValue();
        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format("d.m.Y");

        $title = $spreadsheet->getCell("{$kolonaOpisa}{$row}")->getValue() . "\n" . $spreadsheet->getCell("{$kolonaOpisa}{$nextRow}")->getValue();
        $amount = $spreadsheet->getCell("{$kolonaIznos}{$row}")->getValue();

        $amount = str_replace(".", "", $amount);
        $amount = str_replace(",", ".", $amount);

        $amount = floatval($amount) * $this->currencyMultiplier;

        return array($date, $title, $amount);
    }
}
