<?php
    include_once $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API" . "/functions.php";
    include_once $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API" . "/Bank/Bank.php";
    use PhpOffice\PhpSpreadsheet\Spreadsheet;

    class ProcreditSRB extends Bank{
        function parse($inputFileName)
        {
            $spreadsheet = null;

            try{
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($inputFileName)->getActiveSheet();
            }
            catch(Exception $e)
            {
                    http_response_code(400);
                    die("Neispravan Excel file!\n");
            }

            // Provjeri da li je format spreadsheeta ispravan, tj. da li je u očekivanom formatu.
            self::assertProcreditBiHValid($spreadsheet);

            // Prvi red je naslov
            $data=[];

            $account = self::getAccount($spreadsheet);
            // Na 23. redu počinju transakcije.
            $redTabele = 23;
            while($spreadsheet->getCell("A{$redTabele}")->getValue() != "")
            {
                // Izvuci datum, tekst, duguje, potrazuje.
                list($date,$title,$amount) = self::parseRow($spreadsheet, $redTabele);

                array_push($data, [(string)$title, (string)$amount, (string)$date, (string)$account]);

                $redTabele++;
            }
            return $data;
        }

        static function isFileValid($inputFileName)
        {
            $spreadsheet = null;

            try{
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($inputFileName)->getActiveSheet();
            }
            catch(Exception $e)
            {
                return false;
            }

            if(!self::isSpreadSheetValid($spreadsheet))
            {
                return false;
            }

            return true;
        }

        static function isSpreadSheetValid($spreadsheet)
        {
            if(
                !isValueInCell($spreadsheet, 22, "F", "broj računa")    || 
                !isValueInCell($spreadsheet, 22, "J", "datum prijema")  || 
                !isValueInCell($spreadsheet, 22, "N", "duguje\n")       || 
                !isValueInCell($spreadsheet, 22, "R", "potražuje")      || 
                !isValueInCell($spreadsheet, 22, "Z", "Svrha plaćanja") || 
                !isValueInCell($spreadsheet, 22, "A", "Redni  broj")
            ){
                return false;
            }

            $sentinelFound = false;
            foreach ($spreadsheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                //$cellIterator->setIterateOnlyExistingCells(true);
                foreach ($cellIterator as $cell) {
                    if (explode(":", $cell->getValue())[0] == "broj računa" && $cell->getCoordinate()[0] == "A") {
                        $sentinelFound = true;
                    }
                }
            }

            if(!$sentinelFound)
            {
                return false;
            }

            return true;
        }

        
        static function assertProcreditBiHValid($spreadsheet)
        {
            if(!self::isSpreadSheetValid($spreadsheet))
            {
                http_response_code(400);
                die("ProcreditSRB is not valid");
            }
        }

        function parseRow($spreadsheet, $row)
        {
            $kolonaDatum = "J";

            $kolonaOpisa = "Z";
            $kolonaOpisa1 = "F";

            $kolonaPotrazuje = "R";
            $kolonaDuguje = "N";

            $date = explode("\n", $spreadsheet->getCell("{$kolonaDatum}{$row}")->getValue())[1];
            $title = $spreadsheet->getCell("{$kolonaOpisa}{$row}")->getValue() . "\n" . $spreadsheet->getCell("{$kolonaOpisa1}{$row}")->getValue();

            $potrazuje = str_replace(",", "",$spreadsheet->getCell("{$kolonaPotrazuje}{$row}")->getValue());

            //U duguje cell se nalazi i dug i provizija
            $dugujeCellArray = explode("\n", $spreadsheet->getCell("{$kolonaDuguje}{$row}")->getValue());
            $duguje = str_replace(",", "", $dugujeCellArray[0]);
            $provizija = str_replace(",", "", explode(": ",$dugujeCellArray[1])[1]);

            $amount = self::getAmount(floatval($duguje),floatval($potrazuje), floatval($provizija));

            return array($date, $title, $amount);
        }

        function getAmount($duguje, $potrazuje, $provizija)
        {
            $amount = "";
            if($duguje != 0.0 && $potrazuje != 0.0)
            {
                http_response_code(400);
                die("U jednoj transakciji postoji i uplata i isplata!\n");
            }

            if($duguje != 0.0)
            {
                $amount =  $duguje + $provizija;
                $amount = $amount * (-1);
            }
            else if($potrazuje != 0.0)
            {
                $amount = $potrazuje;
            }
            else if ($provizija != 0.0)
            {
                $amount = floatval($provizija) * -1;
            }
            else
            {
                http_response_code(400);
                die("Ne postoje ni uplata ni isplata ni provizija.");
            }

            return floatval($amount) * $this->currencyMultiplier;
        }

        static function getAccount($spreadsheet)
        {
            foreach ($spreadsheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                //$cellIterator->setIterateOnlyExistingCells(true);
                foreach ($cellIterator as $cell) {
                    if (explode(":", $cell->getValue())[0] == "broj računa" && $cell->getCoordinate()[0] == "A") {
                        $redNaKojemJeBrojRacuna = explode("\n", $cell->getValue())[0];
                        return explode(":", $redNaKojemJeBrojRacuna)[1];
                    }
                }
            }
        }
    }
