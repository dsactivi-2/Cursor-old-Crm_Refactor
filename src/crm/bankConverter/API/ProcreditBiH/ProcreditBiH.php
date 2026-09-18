<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API" . "/functions.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API" . "/Bank/Bank.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ProcreditBiH extends Bank
{
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
        self::assertProcreditBiHValid($spreadsheet);

        // Prvi red je naslov
        $data = [];

        $account = $spreadsheet->getCell("U6")->getValue();

        // Na 10. redu počinju transakcije.
        $redTabele = 15;
        while ($spreadsheet->getCell("I{$redTabele}")->getValue() != "UKUPNO:") {
            // Izvuci datum, tekst, duguje, potrazuje.
            list($date, $title, $amount) = self::parseRow($spreadsheet, $redTabele);

            array_push($data, [(string)$title, (string)$amount, (string)$date, (string)$account]);

            $redTabele++;
        }

        return $data;
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

        if (!self::isSpreadSheetValid($spreadsheet)) {
            return false;
        }

        return true;
    }
    static function isSpreadSheetValid($spreadsheet)
    {
        // Provjeri očekivane vrijednosti.
        if (
            !isValueInCell($spreadsheet, 6, "T", "Račun broj:") ||
            !isValueInCell($spreadsheet, 14, "D", "Datum") ||
            !isValueInCell($spreadsheet, 14, "I", "Primalac/Nalogodavac") ||
            !isValueInCell($spreadsheet, 14, "N", "Isplate") ||
            !isValueInCell($spreadsheet, 14, "Q", "Uplate") ||
            !isValueInCell($spreadsheet, 14, "R", "Provizija") ||
            !isValueInCell($spreadsheet, 14, "S", "Opis transakcije")
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
                if ($cell->getValue() == "UKUPNO:" && $cell->getCoordinate()[0] == "I") {
                    $sentinelFound = true;
                }
            }
        }

        if (!$sentinelFound) {
            return false;
        }

        return true;
    }

    static function assertProcreditBiHValid($spreadsheet)
    {
        // Provjeri očekivane vrijednosti.
        assertCellValues($spreadsheet, 6, "T", "Račun broj:");
        assertCellValues($spreadsheet, 14, "D", "Datum");
        assertCellValues($spreadsheet, 14, "I", "Primalac/Nalogodavac");
        assertCellValues($spreadsheet, 14, "N", "Isplate");
        assertCellValues($spreadsheet, 14, "Q", "Uplate");
        assertCellValues($spreadsheet, 14, "R", "Provizija");
        assertCellValues($spreadsheet, 14, "S", "Opis transakcije");


        // Provjeriti da se u prvoj koloni postoji ćelija sa tekstom TOTAL SPALTE.
        // Ona označava kraj tabele, ako nje ne bude, nećemo znati gdje stati sa
        // čitanjem transakcija.

        $sentinelFound = false;
        foreach ($spreadsheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            //$cellIterator->setIterateOnlyExistingCells(true);
            foreach ($cellIterator as $cell) {
                if ($cell->getValue() == "UKUPNO:" && $cell->getCoordinate()[0] == "I") {
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
        $kolonaDuguje = "N";
        $kolonaPotrazuje = "Q";
        $kolonaProvizija = "R";

        $kolonaDatum = "D";

        $kolonaOpisa = "E";
        $kolonaOpisa1 = "I";

        $date = $spreadsheet->getCell("{$kolonaDatum}{$row}")->getValue();
        $title = $spreadsheet->getCell("{$kolonaOpisa}{$row}")->getValue() . "\n" . $spreadsheet->getCell("{$kolonaOpisa1}{$row}")->getValue();

        $duguje = $spreadsheet->getCell("{$kolonaDuguje}{$row}")->getValue();
        $potrazuje = $spreadsheet->getCell("{$kolonaPotrazuje}{$row}")->getValue();
        $provizija = $spreadsheet->getCell("{$kolonaProvizija}{$row}")->getValue();


        $amount = self::getAmount($duguje, $potrazuje, $provizija);

        return array($date, $title, $amount);
    }

    function getAmount($duguje, $potrazuje, $provizija)
    {
        $amount = "";
        if (($duguje != "0" && $duguje != "") && ($potrazuje != "0" && $potrazuje != "")) {
            http_response_code(400);
            die("U jednoj transakciji postoji i uplata i isplata!\n");
        }

        if ($duguje != "0" && $duguje != "") {
            $amount =  floatval($duguje) + floatval($provizija);
            $amount = $amount * (-1);
        } else if ($potrazuje != "0" && $potrazuje != "") {
            $amount = $potrazuje;
        } else if ($provizija != "0" && $provizija != "") {
            $amount = floatval($provizija) * -1;
        } else {
            http_response_code(400);
            die("Ne postoje ni uplata ni isplata ni provizija.");
        }

        return floatval($amount) * $this->currencyMultiplier;
    }
}
