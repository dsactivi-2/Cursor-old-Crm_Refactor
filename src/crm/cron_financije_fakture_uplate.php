<?php 

    include("includes/functions.php");
    require 'mail/PHPMailerAutoload.php';

    /*
        Cron za obavijesti kasnjenja fakturisanja i uplaćivanja
    */

    /*
        Funkcije za cron
        */
            function getNalogNacinNostrifikacije_CFFU($vr) {
                //cffu - cron financije fakture uplate
                $vr = intval($vr);
                $result = '';
                switch ( $vr ) {
                    case 1: 
                        $result = 'Standardni';
                    break; 

                    case 2:
                        $result = 'Mjesečno';
                    break; 

                    default:
                        $result = 'Nedefinisano'; 
                    break;
                }
                return '<strong>'.$result.' - Nostrifikacija</strong>';
            }

            function getNalogFinancijeTipR_CFFU($vr){
                //cffu - cron financije fakture uplate
                $vr = intval($vr);
                $result = '';
                switch ( $vr ) {
                    case 1: 
                        $result = 'Avans';
                    break; 

                    case 2:
                        $result = 'Avans';
                    break; 

                    case 3:
                        $result = 'Rate'; 
                    break; 

                    case 4:
                        $result = 'Nostrifikacija'; 
                    break; 

                    default:
                        $result = 'Nedefinisano'; 
                    break;
                }
                return '<strong>'.$result.'</strong>';
            }

            function getNalogFinancijeR_CFFU($vr){
                //cffu - cron financije fakture uplate
                $vr = intval($vr);
                $result = '';
                switch ( $vr ) {
                    case 1: 
                        $result = 'Standardno';
                    break; 

                    case 2:
                        $result = 'Mjesečno';
                    break; 

                    case 3:
                        $result = 'Po plati'; 
                    break; 

                    default:
                        $result = 'Nedefinisano'; 
                    break;
                }
                return '<strong>'.$result.' - Nalog</strong>';
            }

            function getKandidatFinancijeTipR_CFFU($vr){
                //cffu - cron financije fakture uplate
                $vr = intval($vr);
                $result = '';
                switch ( $vr ) {
                    case 0:
                        $result = 'Nostrifikacija';
                    break;

                    case 1: 
                        $result = 'Ugovor';
                    break; 

                    case 2:
                        $result = 'Dobio vizu';
                    break; 

                    case 3:
                        $result = 'Početak rada'; 
                    break; 

                    case 4:
                        $result = 'Mjeseci nakon'; 
                    break;

                    default:
                        $result = 'Nedefinisano'; 
                    break;
                }
                return '<strong>'.$result.'</strong>';
            }
        /*
        Funkcije za cron
    */

    $employees_email = array('dz.komic@job-step.com');
    $getSiteUrl = getSiteUrlr();
    if ( count($employees_email) != 0 ) {

        /* 
               
            ****************************************************************************************
            *   FAKTURE                                                                            *
            ****************************************************************************************
            
            */

                /*
                    Rate za naloge
                    */
                        $table_nalog_faktura = ''; 
                        $query_nalog_faktura = $db->prepare("
                            SELECT 
                                n.nalog_id,
                                n.nalog_naziv, 
                                n.kompanija_id, 
                                n.nalog_broj,
                                c.company_name, 
                                n.nalog_financije, 
                                n.nalog_broj_rata,
                                n.nalog_nacin_nostrifikacije,
                                nf.nf_datum_aktiviranja, 
                                nf.nf_type, 
                                nf.nf_broj_rate, 
                                DATEDIFF(CURRENT_DATE(), nf.nf_datum_aktiviranja) AS broj_dana
                            FROM 
                                idk_nalozi n
                            JOIN 
                                idk_companies c
                            ON 
                                n.kompanija_id = c.company_id 
                            JOIN
                                idk_nalog_financije nf
                            ON 
                                n.nalog_id = nf.nf_nalog_id 
                            WHERE
                                nf.nf_placeno = 1
                                AND 
                                nf.nf_datum_aktiviranja <= CURRENT_DATE()
                            ORDER BY n.nalog_id, nf.nf_datum_aktiviranja
                        "); 
                        $query_nalog_faktura->execute();

                        if ( $query_nalog_faktura->rowCount() != 0 ) {
                            $table_rows_nalog_faktura = '';
                            while ( $row_nalog_faktura = $query_nalog_faktura->fetch() ) {
                                $table_row_nalog_faktura = '';

                                $nalog_id                   = intval($row_nalog_faktura["nalog_id"]);
                                $nalog_naziv                = $row_nalog_faktura["nalog_naziv"];
                                $kompanija_id               = intval($row_nalog_faktura["kompanija_id"]);
                                $nalog_broj                 = $row_nalog_faktura["nalog_broj"];
                                $kompanija_naziv            = $row_nalog_faktura["company_name"];
                                $nalog_financije            = intval($row_nalog_faktura["nalog_financije"]);
                                $nalog_broj_rata            = intval($row_nalog_faktura["nalog_broj_rata"]); 
                                $nalog_nacin_nostrifikacije = intval($row_nalog_faktura["nalog_nacin_nostrifikacije"]);
                                $nf_datum_aktiviranja       = date("d.m.Y", strtotime($row_nalog_faktura["nf_datum_aktiviranja"])); 
                                $nf_type                    = intval($row_nalog_faktura["nf_type"]);
                                $nf_type_ispis              = getNalogFinancijeTipR_CFFU($nf_type);
                                $nalog_financije_ispis      = ( ( $nf_type != 4) ? getNalogFinancijeR_CFFU($nalog_financije) : getNalogNacinNostrifikacije_CFFU($nalog_nacin_nostrifikacije) );
                                $nf_broj_rate               = ( ( $nf_type == 3 OR $nf_type == 4 ) ? intval($row_nalog_faktura["nf_broj_rate"]).'/'.$nalog_broj_rata : '-' ); 
                                $broj_dana                  = intval($row_nalog_faktura["broj_dana"]); 

                                $table_row_nalog_faktura = '
                                    <tr>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nalog_id.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$kompanija_naziv.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nalog_broj.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nalog_financije_ispis.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nf_type_ispis.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nf_broj_rate.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nf_datum_aktiviranja.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$broj_dana.'</td>
                                    </tr>
                                ';

                                $table_rows_nalog_faktura = $table_rows_nalog_faktura.''.$table_row_nalog_faktura;
                            }

                            $table_nalog_faktura = '
                                <table class="s-10 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                    <tbody>
                                        <tr>
                                        <td style="line-height: 40px; font-size: 40px; width: 100%; height: 40px; margin: 0;" align="left" width="100%" height="40">
                                            &#160;
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                    <table class="card" role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-radius: 6px; border-collapse: separate !important; width: 100%; overflow: hidden; border: 1px solid #e2e8f0;" bgcolor="#ffffff">
                                    <tbody>
                                        <tr>
                                        <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0;" align="left" bgcolor="#ffffff">
                                            <table class="card-body" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0; padding: 20px;" align="left">
                                                    <h3 class="h3" style="padding-top: 0; padding-bottom: 0; font-weight: 500; vertical-align: baseline; font-size: 28px; line-height: 33.6px; margin: 0;" align="left">Fakturisanje - Rate naloga</h3>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    <div class="">
                                                    <table class="table text-center table-bordered" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 100%; text-align: center !important; border: 1px solid #e2e8f0;">
                                                        <thead>
                                                        <tr>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Nalog ID</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Kompanija</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Nalog</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Način plaćanja</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Tip</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Broj rate</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Datum fakturisanja</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Broj dana kašnjenja</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        '.$table_rows_nalog_faktura.'
                                                        <tr>
                                                            <td colspan="8" style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">
                                                            <small>
                                                                U tabeli se nalaze rate koje su dospjele danas i koje kasne sa fakturisanjem.
                                                            </small>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    </div>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                </td>
                                                </tr>
                                            </tbody>
                                            </table>
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                    <table class="s-10 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                    <tbody>
                                        <tr>
                                        <td style="line-height: 40px; font-size: 40px; width: 100%; height: 40px; margin: 0;" align="left" width="100%" height="40">
                                            &#160;
                                        </td>
                                        </tr>
                                    </tbody>
                                </table>
                            ';
                        }
                    /*
                    Rate za naloge
                */

                /*
                    Rata za kandidate
                    */
                        $table_kandidat_faktura = ''; 
                        $query_kandidat_faktura = $db->prepare("
                            SELECT 
                                n.nalog_id,
                                n.nalog_naziv, 
                                n.kompanija_id, 
                                n.nalog_broj,
                                c.company_name, 
                                n.nalog_financije,
                                n.nalog_nacin_nostrifikacije,
                                kf.kf_datum, 
                                kf.kf_type, 
                                DATEDIFF(CURRENT_DATE(), kf.kf_datum) AS broj_dana, 
                                k.kandidat_id,
                                concat(k.kandidat_ime,' ', k.kandidat_prezime) AS kanidat_ime_prezime
                            FROM 
                                idk_nalozi n
                            JOIN 
                                idk_companies c
                            ON 
                                n.kompanija_id = c.company_id 
                            JOIN
                                idk_kandidat_financije kf
                            ON 
                                n.nalog_id = kf.nalog_id 
                            JOIN 
                                idk_kandidati k
                            ON 
                                k.kandidat_id = kf.kandidat_id
                            WHERE
                                kf.kf_placeno = 1
                                AND 
                                kf.kf_status = 1
                                AND
                                kf.kf_datum <= CURRENT_DATE()
                            ORDER BY n.nalog_id, k.kandidat_id, kf.kf_datum
                        "); 
                        $query_kandidat_faktura->execute();

                        if ( $query_kandidat_faktura->rowCount() != 0 ) {
                            $table_rows_kandidat_faktura = '';
                            while ( $row_kandidat_faktura = $query_kandidat_faktura->fetch() ) {
                                $table_row_kandidat_faktura = '';

                                $nalog_id                       = intval($row_kandidat_faktura["nalog_id"]);
                                $nalog_naziv                    = $row_kandidat_faktura["nalog_naziv"];
                                $kompanija_id                   = intval($row_kandidat_faktura["kompanija_id"]);
                                $nalog_broj                     = $row_kandidat_faktura["nalog_broj"];
                                $kompanija_naziv                = $row_kandidat_faktura["company_name"];
                                $nalog_financije                = intval($row_kandidat_faktura["nalog_financije"]);
                                $nalog_nacin_nostrifikacije     = intval($row_kandidat_faktura["nalog_nacin_nostrifikacije"]);
                                $kanidat_ime_prezime            = $row_kandidat_faktura["kanidat_ime_prezime"];
                                $kf_datum                       = date("d.m.Y", strtotime($row_kandidat_faktura["kf_datum"])); 
                                $kf_type                        = intval($row_kandidat_faktura["kf_type"]);
                                $kf_type_ispis                  = getKandidatFinancijeTipR_CFFU($kf_type); 
                                $nalog_financije_ispis          = ( ( $kf_type != 0) ? getNalogFinancijeR_CFFU($nalog_financije) : getNalogNacinNostrifikacije_CFFU($nalog_nacin_nostrifikacije) );
                                $broj_dana                      = intval($row_kandidat_faktura["broj_dana"]); 

                                $table_row_kandidat_faktura = '
                                    <tr>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nalog_id.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$kompanija_naziv.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nalog_broj.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nalog_financije_ispis.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$kanidat_ime_prezime.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$kf_type_ispis.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$kf_datum.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$broj_dana.'</td>
                                    </tr>
                                ';

                                $table_rows_kandidat_faktura = $table_rows_kandidat_faktura.''.$table_row_kandidat_faktura;
                            }

                            $table_kandidat_faktura = '
                                <table class="s-10 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                    <tbody>
                                        <tr>
                                        <td style="line-height: 40px; font-size: 40px; width: 100%; height: 40px; margin: 0;" align="left" width="100%" height="40">
                                            &#160;
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                    <table class="card" role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-radius: 6px; border-collapse: separate !important; width: 100%; overflow: hidden; border: 1px solid #e2e8f0;" bgcolor="#ffffff">
                                    <tbody>
                                        <tr>
                                        <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0;" align="left" bgcolor="#ffffff">
                                            <table class="card-body" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0; padding: 20px;" align="left">
                                                    <h3 class="h3" style="padding-top: 0; padding-bottom: 0; font-weight: 500; vertical-align: baseline; font-size: 28px; line-height: 33.6px; margin: 0;" align="left">Fakturisanje - Rate kandidata</h3>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    <div class="">
                                                    <table class="table text-center table-bordered" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 100%; text-align: center !important; border: 1px solid #e2e8f0;">
                                                        <thead>
                                                        <tr>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Nalog ID</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Kompanija</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Nalog</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Način plaćanja</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Kandidat</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Tip rate</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Datum fakturisanja</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Broj dana kašnjenja</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        '.$table_rows_kandidat_faktura.'
                                                        <tr>
                                                            <td colspan="8" style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">
                                                            <small>
                                                                U tabeli se nalaze rate od kandidata koje su dospjele danas i koje kasne sa fakturisanjem.
                                                            </small>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    </div>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                </td>
                                                </tr>
                                            </tbody>
                                            </table>
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                    <table class="s-10 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                    <tbody>
                                        <tr>
                                        <td style="line-height: 40px; font-size: 40px; width: 100%; height: 40px; margin: 0;" align="left" width="100%" height="40">
                                            &#160;
                                        </td>
                                        </tr>
                                    </tbody>
                                </table>
                            ';
                        }
                    /*
                    Rate za kandidate
                */

            /* 
               
            ****************************************************************************************
            *   FAKTURE                                                                            *
            ****************************************************************************************
            
        */

        /* 
               
            ****************************************************************************************
            *   UPLATE                                                                             *
            ****************************************************************************************
            
            */

                /*
                    Rate za naloge
                    */
                        $table_nalog_uplata = ''; 
                        $query_nalog_uplata = $db->prepare("
                            SELECT 
                                n.nalog_id,
                                n.nalog_naziv, 
                                n.kompanija_id, 
                                n.nalog_broj,
                                c.company_name, 
                                n.nalog_financije, 
                                n.nalog_broj_rata,
                                n.nalog_nacin_nostrifikacije,
                                DATE_ADD(nf.nf_datum_fakturisanja, INTERVAL n.nalog_dospijece DAY) AS datum_fakturisanja,
                                nf.nf_type, 
                                nf.nf_broj_rate, 
                                DATEDIFF(CURRENT_DATE(), DATE_ADD(nf.nf_datum_fakturisanja, INTERVAL n.nalog_dospijece DAY)) AS broj_dana
                            FROM 
                                idk_nalozi n
                            JOIN 
                                idk_companies c
                            ON 
                                n.kompanija_id = c.company_id 
                            JOIN
                                idk_nalog_financije nf
                            ON 
                                n.nalog_id = nf.nf_nalog_id 
                            WHERE
                                nf.nf_placeno = 2
                                AND 
                                DATE_ADD(nf.nf_datum_fakturisanja, INTERVAL n.nalog_dospijece DAY) <= CURRENT_DATE()
                            ORDER BY n.nalog_id, nf.nf_datum_fakturisanja
                        "); 
                        $query_nalog_uplata->execute();

                        if ( $query_nalog_uplata->rowCount() != 0 ) {
                            $table_rows_nalog_uplata = '';
                            while ( $row_nalog_uplata = $query_nalog_uplata->fetch() ) {
                                $table_row_nalog_uplata = '';

                                $nalog_id                   = intval($row_nalog_uplata["nalog_id"]);
                                $nalog_naziv                = $row_nalog_uplata["nalog_naziv"];
                                $kompanija_id               = intval($row_nalog_uplata["kompanija_id"]);
                                $nalog_broj                 = $row_nalog_uplata["nalog_broj"];
                                $kompanija_naziv            = $row_nalog_uplata["company_name"];
                                $nalog_financije            = intval($row_nalog_uplata["nalog_financije"]);
                                $nalog_broj_rata            = intval($row_nalog_uplata["nalog_broj_rata"]); 
                                $nalog_nacin_nostrifikacije = intval($row_nalog_uplata["nalog_nacin_nostrifikacije"]);
                                $nf_datum_fakturisanja      = date("d.m.Y", strtotime($row_nalog_uplata["datum_fakturisanja"])); 
                                $nf_type                    = intval($row_nalog_uplata["nf_type"]);
                                $nf_type_ispis              = getNalogFinancijeTipR_CFFU($nf_type);
                                $nalog_financije_ispis      = ( ( $nf_type != 4) ? getNalogFinancijeR_CFFU($nalog_financije) : getNalogNacinNostrifikacije_CFFU($nalog_nacin_nostrifikacije) );
                                $nf_broj_rate               = ( ( $nf_type == 3 OR $nf_type == 4 ) ? intval($row_nalog_uplata["nf_broj_rate"]).'/'.$nalog_broj_rata : '-' ); 
                                $broj_dana                  = intval($row_nalog_uplata["broj_dana"]); 

                                $table_row_nalog_uplata = '
                                    <tr>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nalog_id.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$kompanija_naziv.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nalog_broj.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nalog_financije_ispis.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nf_type_ispis.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nf_broj_rate.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nf_datum_fakturisanja.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$broj_dana.'</td>
                                    </tr>
                                ';

                                $table_rows_nalog_uplata = $table_rows_nalog_uplata.''.$table_row_nalog_uplata;
                            }

                            $table_nalog_uplata = '
                                <table class="s-10 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                    <tbody>
                                        <tr>
                                        <td style="line-height: 40px; font-size: 40px; width: 100%; height: 40px; margin: 0;" align="left" width="100%" height="40">
                                            &#160;
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                    <table class="card" role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-radius: 6px; border-collapse: separate !important; width: 100%; overflow: hidden; border: 1px solid #e2e8f0;" bgcolor="#ffffff">
                                    <tbody>
                                        <tr>
                                        <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0;" align="left" bgcolor="#ffffff">
                                            <table class="card-body" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0; padding: 20px;" align="left">
                                                    <h3 class="h3" style="padding-top: 0; padding-bottom: 0; font-weight: 500; vertical-align: baseline; font-size: 28px; line-height: 33.6px; margin: 0;" align="left">Uplate - Rate naloga</h3>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    <div class="">
                                                    <table class="table text-center table-bordered" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 100%; text-align: center !important; border: 1px solid #e2e8f0;">
                                                        <thead>
                                                        <tr>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Nalog ID</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Kompanija</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Nalog</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Način plaćanja</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Tip</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Broj rate</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Datum dospijeća uplate</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Broj dana kašnjenja</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        '.$table_rows_nalog_uplata.'
                                                        <tr>
                                                            <td colspan="8" style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">
                                                            <small>
                                                                U tabeli se nalaze rate koje su dospjele danas i koje kasne sa uplatom.
                                                            </small>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    </div>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                </td>
                                                </tr>
                                            </tbody>
                                            </table>
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                    <table class="s-10 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                    <tbody>
                                        <tr>
                                        <td style="line-height: 40px; font-size: 40px; width: 100%; height: 40px; margin: 0;" align="left" width="100%" height="40">
                                            &#160;
                                        </td>
                                        </tr>
                                    </tbody>
                                </table>
                            ';
                        }
                    /*
                    Rate za naloge
                */

                /*
                    Rata za kandidate
                    */
                        $table_kandidat_uplata = ''; 
                        $query_kandidat_uplata = $db->prepare("
                            SELECT 
                                n.nalog_id,
                                n.nalog_naziv, 
                                n.kompanija_id, 
                                n.nalog_broj,
                                c.company_name, 
                                n.nalog_financije,
                                n.nalog_nacin_nostrifikacije,
                                DATE_ADD(kf.kf_datum_fakturisanja, INTERVAL n.nalog_dospijece DAY) AS datum_fakturisanja,
                                kf.kf_type, 
                                DATEDIFF(CURRENT_DATE(), DATE_ADD(kf.kf_datum_fakturisanja, INTERVAL n.nalog_dospijece DAY)) AS broj_dana, 
                                k.kandidat_id,
                                concat(k.kandidat_ime,' ', k.kandidat_prezime) AS kanidat_ime_prezime
                            FROM 
                                idk_nalozi n
                            JOIN 
                                idk_companies c
                            ON 
                                n.kompanija_id = c.company_id 
                            JOIN
                                idk_kandidat_financije kf
                            ON 
                                n.nalog_id = kf.nalog_id 
                            JOIN 
                                idk_kandidati k
                            ON 
                                k.kandidat_id = kf.kandidat_id
                            WHERE
                                kf.kf_placeno = 2
                                AND 
                                DATE_ADD(kf.kf_datum_fakturisanja, INTERVAL n.nalog_dospijece DAY) <= CURRENT_DATE()
                            ORDER BY n.nalog_id, k.kandidat_id, kf.kf_datum_fakturisanja
                        "); 
                        $query_kandidat_uplata->execute();

                        if ( $query_kandidat_uplata->rowCount() != 0 ) {
                            $table_rows_kandidat_uplata = '';
                            while ( $row_kandidat_uplata = $query_kandidat_uplata->fetch() ) {
                                $table_row_kandidat_uplata = '';

                                $nalog_id                       = intval($row_kandidat_uplata["nalog_id"]);
                                $nalog_naziv                    = $row_kandidat_uplata["nalog_naziv"];
                                $kompanija_id                   = intval($row_kandidat_uplata["kompanija_id"]);
                                $nalog_broj                     = $row_kandidat_uplata["nalog_broj"];
                                $kompanija_naziv                = $row_kandidat_uplata["company_name"];
                                $nalog_financije                = intval($row_kandidat_uplata["nalog_financije"]);
                                $nalog_nacin_nostrifikacije     = intval($row_kandidat_uplata["nalog_nacin_nostrifikacije"]);
                                $kanidat_ime_prezime            = $row_kandidat_uplata["kanidat_ime_prezime"];
                                $kf_datum_fakturisanja          = date("d.m.Y", strtotime($row_kandidat_uplata["datum_fakturisanja"])); 
                                $kf_type                        = intval($row_kandidat_uplata["kf_type"]);
                                $kf_type_ispis                  = getKandidatFinancijeTipR_CFFU($kf_type); 
                                $nalog_financije_ispis          = ( ( $kf_type != 0) ? getNalogFinancijeR_CFFU($nalog_financije) : getNalogNacinNostrifikacije_CFFU($nalog_nacin_nostrifikacije) );
                                $broj_dana                      = intval($row_kandidat_uplata["broj_dana"]); 

                                $table_row_kandidat_uplata = '
                                    <tr>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nalog_id.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$kompanija_naziv.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nalog_broj.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$nalog_financije_ispis.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$kanidat_ime_prezime.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$kf_type_ispis.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$kf_datum_fakturisanja.'</td>
                                        <td style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$broj_dana.'</td>
                                    </tr>
                                ';

                                $table_rows_kandidat_uplata = $table_rows_kandidat_uplata.''.$table_row_kandidat_uplata;
                            }

                            $table_kandidat_uplata = '
                                <table class="s-10 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                    <tbody>
                                        <tr>
                                        <td style="line-height: 40px; font-size: 40px; width: 100%; height: 40px; margin: 0;" align="left" width="100%" height="40">
                                            &#160;
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                    <table class="card" role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-radius: 6px; border-collapse: separate !important; width: 100%; overflow: hidden; border: 1px solid #e2e8f0;" bgcolor="#ffffff">
                                    <tbody>
                                        <tr>
                                        <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0;" align="left" bgcolor="#ffffff">
                                            <table class="card-body" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0; padding: 20px;" align="left">
                                                    <h3 class="h3" style="padding-top: 0; padding-bottom: 0; font-weight: 500; vertical-align: baseline; font-size: 28px; line-height: 33.6px; margin: 0;" align="left">Uplate - Rate kandidata</h3>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    <div class="">
                                                    <table class="table text-center table-bordered" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 100%; text-align: center !important; border: 1px solid #e2e8f0;">
                                                        <thead>
                                                        <tr>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Nalog ID</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Kompanija</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Nalog</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Način plaćanja</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Kandidat</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Tip rate</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Datum dospijeća uplate</th>
                                                            <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">Broj dana kašnjenja</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        '.$table_rows_kandidat_uplata.'
                                                        <tr>
                                                            <td colspan="8" style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">
                                                            <small>
                                                                U tabeli se nalaze rate od kandidata koje su dospjele danas i koje kasne sa uplatom.
                                                            </small>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    </div>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                </td>
                                                </tr>
                                            </tbody>
                                            </table>
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                    <table class="s-10 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                    <tbody>
                                        <tr>
                                        <td style="line-height: 40px; font-size: 40px; width: 100%; height: 40px; margin: 0;" align="left" width="100%" height="40">
                                            &#160;
                                        </td>
                                        </tr>
                                    </tbody>
                                </table>
                            ';
                        }
                    /*
                    Rate za kandidate
                */

            /* 
            
            ****************************************************************************************
            *   UPLATE                                                                             *
            ****************************************************************************************
            
        */

        /* 
               
            ****************************************************************************************
            *   SUMA IZVJESTAJA                                                                    *
            ****************************************************************************************
            
            */
                $izvjestaji_sum = '
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                    <html>
                        <head>
                            <!-- Compiled with Bootstrap Email version: 1.3.1 --><meta http-equiv="x-ua-compatible" content="ie=edge">
                            <meta name="x-apple-disable-message-reformatting">
                            <meta name="viewport" content="width=device-width, initial-scale=1">
                            <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                            <style type="text/css">
                            body,table,td{font-family:Helvetica,Arial,sans-serif !important}.ExternalClass{width:100%}.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div{line-height:150%}a{text-decoration:none}*{color:inherit}a[x-apple-data-detectors],u+#body a,#MessageViewBody a{color:inherit;text-decoration:none;font-size:inherit;font-family:inherit;font-weight:inherit;line-height:inherit}img{-ms-interpolation-mode:bicubic}table:not([class^=s-]){font-family:Helvetica,Arial,sans-serif;mso-table-lspace:0pt;mso-table-rspace:0pt;border-spacing:0px;border-collapse:collapse}table:not([class^=s-]) td{border-spacing:0px;border-collapse:collapse}@media screen and (max-width: 600px){.w-full,.w-full>tbody>tr>td{width:100% !important}.w-32,.w-32>tbody>tr>td{width:128px !important}*[class*=s-lg-]>tbody>tr>td{font-size:0 !important;line-height:0 !important;height:0 !important}.s-4>tbody>tr>td{font-size:16px !important;line-height:16px !important;height:16px !important}.s-12>tbody>tr>td{font-size:48px !important;line-height:48px !important;height:48px !important}}
                            </style>
                        </head>
                        <body class="bg-light" style="outline: 0; width: 100%; min-width: 100%; height: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 24px; font-weight: normal; font-size: 16px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000000; margin: 0; padding: 0; border-width: 0;" bgcolor="#f7fafc">
                            <table class="bg-light body" valign="top" role="presentation" border="0" cellpadding="0" cellspacing="0" style="outline: 0; width: 100%; min-width: 100%; height: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 24px; font-weight: normal; font-size: 16px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000000; margin: 0; padding: 0; border-width: 0;" bgcolor="#f7fafc">
                            <tbody>
                                <tr>
                                <td valign="top" style="line-height: 24px; font-size: 16px; margin: 0;" align="left" bgcolor="#f7fafc">
                                    <table class="container" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                        <td align="center" style="line-height: 24px; font-size: 16px; margin: 0; padding: 0 16px;">
                                            <!--[if (gte mso 9)|(IE)]>
                                            <table align="center" role="presentation">
                                                <tbody>
                                                <tr>
                                                    <td width="600">
                                            <![endif]-->
                                            <table align="center" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin: 0 auto;">
                                            <tbody>
                                                <tr>
                                                <td style="line-height: 24px; font-size: 16px; margin: 0;" align="left">
                                                    <table class="s-12 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 48px; font-size: 48px; width: 100%; height: 48px; margin: 0;" align="left" width="100%" height="48">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    <table class="ax-center" role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 24px; font-size: 16px; margin: 0;" align="left">
                                                            <div class="">
                                                            <img class="img-fluid w-32" src="'.$getSiteUrl.'images/Jobstep_logo_new.png" alt="Some Image" style="height: auto; line-height: 100%; outline: none; text-decoration: none; display: block; max-width: 100%; width: 128px; border-style: none; border-width: 0;" width="128">
                                                            </div>
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    <table class="s-12 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 48px; font-size: 48px; width: 100%; height: 48px; margin: 0;" align="left" width="100%" height="48">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    '.( $table_nalog_faktura != '' ? $table_nalog_faktura : null ).' 
                                                    '.( $table_kandidat_faktura != '' ? $table_kandidat_faktura : null ).'
                                                    '.( $table_nalog_uplata != '' ? $table_nalog_uplata : null ).'
                                                    '.( $table_kandidat_uplata != '' ? $table_kandidat_uplata : null ).'
                                                    '.
                                                        ( 
                                                            ( $table_nalog_faktura == '' AND $table_kandidat_faktura == '' AND $table_nalog_uplata == '' AND $table_kandidat_uplata == '' ) ? 
                                                            '
                                                                <table class="card" role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-radius: 6px; border-collapse: separate !important; width: 100%; overflow: hidden; border: 1px solid #e2e8f0;" bgcolor="#ffffff">
                                                                    <tbody>
                                                                        <tr>
                                                                        <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0;" align="left" bgcolor="#ffffff">
                                                                            <table class="card-body" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                                                            <tbody>
                                                                                <tr>
                                                                                <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0; padding: 20px;" align="left">
                                                                                    <h5 class="text-teal-700" style="color: #13795b; padding-top: 0; padding-bottom: 0; font-weight: 500; vertical-align: baseline; font-size: 20px; line-height: 24px; margin: 0;" align="left">Nisu pronađene rate koje su dospjele ili kasne sa fakturisanjem ili uplatom!</h5>
                                                                                </td>
                                                                                </tr>
                                                                            </tbody>
                                                                            </table>
                                                                        </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            ' : 
                                                            '' 
                                                        )
                                                    .'
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    <table class="ax-left" role="presentation" align="left" border="0" cellpadding="0" cellspacing="0">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 24px; font-size: 16px; margin: 0;" align="left">
                                                            <div class="">
                                                            <small><strong>Izvje&#353;taj kreiran za: </strong>'.implode(", ", $employees_email).'</small><br>
                                                            <small><strong>Vrijeme kreiranja: </strong>'.date("d.m.Y H:i:s").'</small>
                                                            </div>
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                    <table class="s-4 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                                    <tbody>
                                                        <tr>
                                                        <td style="line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;" align="left" width="100%" height="16">
                                                            &#160;
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                </td>
                                                </tr>
                                            </tbody>
                                            </table>
                                            <!--[if (gte mso 9)|(IE)]>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                            <![endif]-->
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                </td>
                                </tr>
                            </tbody>
                            </table>
                        </body>
                    </html>
                ';
            /* 
               
            ****************************************************************************************
            *   SUMA IZVJESTAJA                                                                    *
            ****************************************************************************************
            
        */
        echo $izvjestaji_sum;
        /* 
               
            ****************************************************************************************
            *   MAIL                                                                               *
            ****************************************************************************************
            
            */
                $hostName                   = "smtp.gmail.com";
                $userName                   = "support@job-step.com";
                $password                   = "eooc nnxo aylp lqkh";
                $setFrom                    = "support@job-step.com";
                $naslovMail                 = "Obavijest za dospijeće i kašnjenje faktura i uplata - ".date("Y-m-d");           
                $textMail                   = $izvjestaji_sum;
                
                $mail                       = new PHPMailer;
                $mail->isSMTP();

                $mail->Host                 = $hostName;
                $mail->SMTPAuth             = true;
                $mail->Username             = $userName;
                $mail->Password             = $password;
                $mail->SMTPSecure           = 'ssl';
                $mail->Port                 = 465;
                $mail->CharSet              = 'UTF-8';

                $mail->setFrom($setFrom, 'JobStep');
                foreach ($employees_email as $employee_email) {
                    $mail->addAddress($employee_email);
                }
                $mail->isHTML(true);
                $mail->Subject = "".$naslovMail."";
                $mail->Body = "
                    ".$textMail."
                ";
                $mail->AltBody = "ALT";

                if(!$mail->send()) { 
                    $log_desc = 'IZVJEŠTAJ ZA KAŠNJENJE FAKTURISANJA I UPLAĆIVANJA - Nije poslan izvještaj na e-mail zaposlenicima: '.implode(", ", $employees_email).'. Mailer Error: ' . $mail->ErrorInfo;
                } else {
                    $log_desc = 'IZVJEŠTAJ ZA KAŠNJENJE FAKTURISANJA I UPLAĆIVANJA - Poslan izvještaj na e-mail zaposlenicima: '.implode(", ", $employees_email).'.';
                }
                
                $log_query = $db->prepare("
                    INSERT INTO idk_logs
                        (log_employeeid, log_desc, log_date, log_type)
                    VALUES
                        (:log_employeeid, :log_desc, :log_date, 4)
                ");
                $log_query->execute(array(
                    ':log_employeeid' => 139,
                    ':log_desc' => $log_desc,
                    ':log_date' => date("Y-m-d H:i:s")
                ));
            /* 
               
            ****************************************************************************************
            *   MAIL                                                                               *
            ****************************************************************************************
            
        */
    }
?>