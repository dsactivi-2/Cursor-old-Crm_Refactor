<?php

include($_SERVER["DOCUMENT_ROOT"] . "/includes/functions.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/env.php");

use Dompdf\Dompdf;

$request = "";

if(isset($_REQUEST["request"])) {
	$request = $_REQUEST["request"];
    $lang = $_REQUEST["lang"];
    switch($lang){
        case "de":
            $full_name = "Vorname Nachname";
            $directive = "Direktive";
            $makler_id = "Makler ID";
            $register_date = "Registrierungsdatum";
            $first_login = "Erstes Login";
            $last_login = "Letztes Login";
            $acc_status = "Konto Status";
            $num_of_created_companies = "Anzahl der erstellten Unternehmen";
            $num_of_rejected = "Anzahl der abgelehnten";
            $num_of_contracts = "Anzahl der Verträge";
            $num_of_registered_candidates = "Anzahl der registrierten Kandidaten";
            $num_of_requested_candidates = "Anzahl der angeforderten Kandidaten";
        break;
        case "bs":
            $full_name = "Ime i Prezime";
            $directive = "Direktiva";
            $makler_id = "Makler ID";
            $register_date = "Datum registracije";
            $first_login = "Prva prijava";
            $last_login = "Zadnja prijava";
            $acc_status = "Status računa";
            $num_of_created_companies = "Broj kreiranih kompanija";
            $num_of_rejected = "Broj odbijenih";
            $num_of_contracts = "Broj ugovora";
            $num_of_registered_candidates = "Ukupan broj prijavljenih kandidata";
            $num_of_requested_candidates = "Ukupan broj traženih kandidata";
        break;
    }

    switch ($request){
        case "export_user_activity":
        ?>
        <div class="" style="height:0px;overflow:hidden;">
            <table class="table" id="CASTABLEmmds" style="100%;" class="display">
                <thead>
                    <tr>
                        <th style="width:150px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $full_name; ?></th>
                        <th style="width:70px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $directive; ?></th>
                        <th style="width:70px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $makler_id; ?></th>
                        <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $register_date; ?></th>
                        <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $first_login; ?></th>
                        <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $last_login; ?></th>
                        <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $acc_status; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT jp_ime, jp_prezime, jp_direction_number, jp_makler_id, jp_register_date, jp_first_login, jp_latest_activity, jp_confirmedaccount, jp_datum_deaktivacije
                            FROM idk_jobstep_partners
                            WHERE jp_user_type = 1
                            AND jp_partner_company = 3";  
                    $stmt = $db->prepare($sql);

                    $stmt->execute();
                    $result = $stmt->fetchAll();

                    foreach($result as $user){
                        $fullname = $user["jp_ime"] . " " . $user["jp_prezime"];
                        $directive = $user["jp_direction_number"];
                        $makler_id = $user["jp_makler_id"];
                        $register_date = $user["jp_register_date"];
                        $first_login = $user["jp_first_login"];
                        $last_login = $user["jp_latest_activity"];
                        $acc_status = $user["jp_confirmedaccount"];

                        if($acc_status == 1){
                            $acc_status = "Active";
                        } else {
                            $acc_status = "Deactivated";
                        }
                    ?>
                        <tr>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $fullname; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $directive; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $makler_id; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $register_date; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $first_login; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $last_login; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $acc_status; ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <script>
                $(document).ready(function(){
                    $("#CASTABLEmmds").table2excel({
                        exclude: ".noExl",
                        name: "userLogExport(<?php echo date("Y-m-d");?>)",
                        filename: "UserLogExport(<?php echo date("Y-m-d");?>)",
                        fileext: ".xls"
                    }); 	  
                })
            </script>	
        </div>
        <?php
        break;

        case "export_work_activity":
        ?>
        <div class="" style="height:0px;overflow:hidden;">
            <table class="table" id="CASTABLEmmds" style="100%;" class="display">
                <thead>
                    <tr>
                        <th style="width:150px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $full_name; ?></th>
                        <th style="width:70px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $directive; ?></th>
                        <th style="width:70px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $makler_id; ?></th>
                        <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $num_of_created_companies; ?></th>
                        <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $num_of_rejected; ?></th>
                        <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $num_of_contracts; ?></th>
                        <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $num_of_registered_candidates; ?></th>
                        <th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;"><?php echo $num_of_requested_candidates; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT
                                    jp_id,
                                    jp_imeprezime,
                                    jp_direction_number,
                                    jp_makler_id,
                                    br_kompanija.ukupan_broj_kompanija,
                                    br_odbijenih.broj_odbijenih,
                                    br_ugovora.broj_ugovora,
                                    br_prijavljenih_kandidata.prijavljeni_kandidati,
                                    COALESCE(SUM(CAST(company_total_workers_required AS UNSIGNED)), 0) AS ukupan_broj_trazenih_kandidata
                                FROM
                                    idk_jobstep_partners
                                JOIN
                                    (
                                        SELECT
                                            js_partner_id,
                                            count(company_id) as ukupan_broj_kompanija
                                        FROM
                                            idk_companies
                                        GROUP BY js_partner_id
                                    ) as br_kompanija
                                ON
                                    idk_jobstep_partners.jp_id = br_kompanija.js_partner_id
                                LEFT JOIN 
                                    (
                                        SELECT
                                            js_partner_id,
                                            count(company_id) as broj_odbijenih
                                        FROM
                                            idk_companies
                                        WHERE
                                            company_status = 5
                                        GROUP BY js_partner_id
                                    ) as br_odbijenih
                                ON
                                    idk_jobstep_partners.jp_id = br_odbijenih.js_partner_id
                                LEFT JOIN
                                    (
                                    SELECT 
                                        js_partner_id,
                                        count(company_id) as broj_ugovora
                                    FROM
                                            idk_companies
                                    WHERE
                                            company_status IN (1,3,6)
                                    GROUP BY js_partner_id
                                    ) as br_ugovora
                                ON
                                    idk_jobstep_partners.jp_id = br_ugovora.js_partner_id
                                JOIN
                                    (
                                    SELECT 
                                        kandidat_partnerid,
                                        count(kandidat_id) as prijavljeni_kandidati
                                    FROM
                                        idk_kandidati
                                    GROUP BY kandidat_partnerid
                                    ) as br_prijavljenih_kandidata
                                ON
                                    idk_jobstep_partners.jp_id = br_prijavljenih_kandidata.kandidat_partnerid
                                LEFT JOIN
                                    idk_companies 
                                ON 
                                    idk_companies.js_partner_id = idk_jobstep_partners.jp_id
                                WHERE
                                    jp_confirmedaccount = 1 AND jp_user_type = 1
                                GROUP BY jp_id";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetchAll();
                        foreach($result as $row){
                            $fullname = $row["jp_imeprezime"];
                            $directive = $row["jp_direction_number"];
                            $makler_id = $row["jp_makler_id"];
                            $num_of_created_companies = $row["ukupan_broj_kompanija"];
                            $num_of_rejected = $row["broj_odbijenih"];
                            $num_of_contracts = $row["broj_ugovora"];
                            $num_of_registered_candidates = $row["prijavljeni_kandidati"];
                            $num_of_requested_candidates = $row["ukupan_broj_trazenih_kandidata"];
                    ?>
                        <tr>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $fullname; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $directive; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $makler_id; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $num_of_created_companies; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $num_of_rejected; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $num_of_contracts; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $num_of_registered_candidates; ?></td>
                            <td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $num_of_requested_candidates; ?></td>
                        </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
            <script>
                $(document).ready(function(){
                    $("#CASTABLEmmds").table2excel({
                        exclude: ".noExl",
                        name: "userWorkActivityExport(<?php echo date("Y-m-d");?>)",
                        filename: "UserWorkActivityExport(<?php echo date("Y-m-d");?>)",
                        fileext: ".xls"
                    }); 	  
                })
            </script>	
        </div>
        <?php
        break;

    }
}
?>