<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/bankConverter/API" . "/functions.php";
// Uzimam sve redove koji imaju 6 kolona jer su to redovi koji 
// označavaju transakcije.
// Preskačemo prvi jer su to nazivi kolona.


class Postfinance extends Bank
{
    function parse($inputFileName)
    {
        if (!self::isFileValid($inputFileName)) {
            http_response_code(400);
            die("Nije validan CSV file!\n");
        }
        $skipColumns = true;

        $outputData = [];
        $account = "";
        if (($handle = fopen($inputFileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, null, ";")) !== FALSE) {

                $brojKolonaUnutarReda = count($data);

                if ($brojKolonaUnutarReda == 2 && $data[0] == "Konto:") {
                    // Nalazimo se na liniji na kojoj piše broj računa

                    // Razdvoji string po navodnim znacima, u sredini se nalazi broj računa.
                    $explodedRed = explode('"', $data[1]);
                    $account = $explodedRed[1];
                }
                if ($brojKolonaUnutarReda == 6) {
                    // Preskoči nazive kolona
                    if ($skipColumns) {
                        $skipColumns = false;
                        continue;
                    }

                    $date = $data[4];
                    $title = $data[1];

                    // Ako postoje i gutschrift i lastschrift, imamo problem.
                    if ($data[2] != "" && $data[3] != "") {
                        http_response_code(400);
                        die("U jednoj transakciji postoji i uplata i isplata!\n");
                    }

                    $amount = "";

                    if ($data[2] != "") {
                        $amount = $data[2];
                    } else if ($data[3] != "") {
                        $amount = $data[3];
                    } else {
                        // I lastschrift i gutschrift su prazni, imamo problem.
                        http_response_code(400);
                        die("Ne postoje ni uplata ni isplata");
                    }

                    $amount = floatval($amount) * $this->currencyMultiplier;

                    array_push($outputData, [(string)$title, $amount, (string)$date, $account]);
                }
            }
            fclose($handle);
            return $outputData;
        } else {
            http_response_code(400);
            die("Nije validan file!\n");
        }
    }

    static function isFileValid($inputFileName)
    {
        if (($handle = fopen($inputFileName, "r")) !== FALSE) {
            $countRowsOfSix = 0;
            while (($data = fgetcsv($handle, null, ";")) !== FALSE) {

                $brojKolonaUnutarReda = count($data);

                if ($brojKolonaUnutarReda == 6) {
                    $countRowsOfSix++;
                    if ($countRowsOfSix == 1) {
                        // Prvi red od šest kolona sadrži naslove kolona
                        if (
                            implode(";", $data) != "Buchungsdatum;Avisierungstext;Gutschrift in CHF;Lastschrift in CHF;Valuta;Saldo in CHF" &&
                            implode(";", $data) != "Buchungsdatum;Avisierungstext;Gutschrift in EUR;Lastschrift in EUR;Valuta;Saldo in EUR"
                        ) {
                            return false;
                        }
                    }
                    if ($data[2] != "" && $data[3] != "" && $countRowsOfSix > 1) {
                        return false;
                    }
                }
            }
            fclose($handle);

            if ($countRowsOfSix == 0) {
                return false;
            }

            // Ako smo došli do tu, svi testovi su dobro prošli
            return true;
        } else {
            return false;
        }
    }
}
