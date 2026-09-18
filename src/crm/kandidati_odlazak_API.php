<?php

include($_SERVER["DOCUMENT_ROOT"] . "/includes/functions.php");

$nalog_id = $_POST["nalog_id"];
$status_id = $_POST["status_id"];


//1-Slobodan, 2-U projektu NR, 3-Casting, 6-U projektu RZ  
$status_ids_precontract = array(1,2,3,6);
//4-Zaposlen, 7-Ceka ugovor, 8-Poslan ugovor, 10-Pocetak rada
$status_ids_contract = array(4,7,8,10);
//12-Prikupljanje dokumentacije, 15-Ceka termin, 18-Ceka vizu, 27-Dobio vizu
$status_ids_visa = array(12,15,18,27);

$query = $db->prepare(getQueryForOdlazakList($nalog_id, $status_id));
$query->execute();

echo '<table id="idk_table_status_prijave" class="display" cellspacing="0" width="100%">';

//LISTA KANDIDATA KOJI SU U STATUSIMA PRIJAVE VEZANE ZA SVE PRIJE UGOVORA OSIM ODBIJENIH ($status_ids_precontract - vec postoji komentar koji su to statusi kod definisanja niza)
    if(in_array($status_id, $status_ids_precontract)){
    
        echo '<thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Ime i Prezime</th>
                        <th class="text-center">Jezik</th>
                        <th class="text-center">Smjer</th>
                        <th class="text-center">Tip Obrazovanja</th>
                    </tr>
                </thead>
                <tbody>';
        while($result = $query->fetch()){
            $kandidat_id		 		= $result['kandidat_id'];
            $kandidat_full_name 		= $result['kandidat_full_name'];
            $kandidat_nivo_jezika		= $result['kj_slusanje'];
            $kandidat_tip_obrazovanja	= $result['ke_vrsta_obrazovanja'];
            $kandidat_smjer				= $result['ke_naziv_kvalifikacije'];

            $table_body = '<tr>
                                <td class="text-center">'. $kandidat_id .'</td>
                                <td class="text-center"><a href="'. getSiteUrlr() .'kandidati?page=open&id='. $kandidat_id .'">'. $kandidat_full_name .'</a></td>
                                <td class="text-center">'. $kandidat_nivo_jezika .'</td>
                                <td class="text-center">'. $kandidat_smjer .'</td>
                                <td class="text-center">'. $kandidat_tip_obrazovanja .'</td>
                            </tr>';
            echo $table_body;
        }
        echo '</tbody>';
    }

//LISTA KANDIDATA KOJI SU U STATUSIMA PRIJAVE VEZANE ZA UGOVOR ($status_ids_contract - vec postoji komentar koji su to statusi kod definisanja niza)
    elseif(in_array($status_id, $status_ids_contract)){

        echo '<thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Ime i Prezime</th>
                        <th class="text-center">Pozicija</th>
                        <th class="text-center">Grad</th>
                        <th class="text-center">Plata</th>
                        <th class="text-center">Početak rada</th>
                        <th class="text-center">Akcije</th>
                    </tr>
                </thead>
                <tbody>';
        while($result = $query->fetch()){
            $kandidat_id		 				= $result['kandidat_id'];
            $kandidat_full_name 				= $result['kandidat_full_name'];
            $kandidat_pp_lokacija				= $result['kandidat_pp_lokacija'];
            $kandidat_pp_pozicija				= $result['kandidat_pp_pozicija'];
            $kandidat_pp_plata					= $result['kandidat_pp_plata'];
            if($status_id == 4 OR $status_id == 10){
                $kandidat_pocetak_rada = date("d.m.Y", strtotime($result['kandidat_dogovoreni_pocetak_rada']));
            }else{
                $kandidat_pocetak_rada	= $result['kandidat_potencijalni_pocetak_rada'];
            }
            $kandidat_status_prijave			= $result['kandidat_status_prijave'] ?? null;
            $kandidat_ugovor					= $result['kc_file_name'] ?? null;
            $kandidat_ppa_partner_id			= $result['kandidat_ppa_partner_id'] ?? null;

            $table_body = '<tr>
                            <td class="text-center">'. $kandidat_id . '</td>
                            <td class="text-center"><a href="' . getSiteUrlr() .'kandidati?page=open&id='. $kandidat_id. '">'. $kandidat_full_name .'</a></td>
                            <td class="text-center">'. $kandidat_pp_pozicija .'</td>
                            <td class="text-center">'. $kandidat_pp_lokacija .'</td>
                            <td class="text-center">'. $kandidat_pp_plata .'</td>
                            <td class="text-center">'. $kandidat_pocetak_rada .'</td>
                            <td class="text-center">
                                <div class="btn-group material-btn-group">
                                    <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
                                        <i class="fa fa-cogs fa-lg" aria-hidden="true">
                                        </i> 
                                        <span class="caret material-btn__caret">
                                        </span>
                                    </button>
                                    <ul style = "top:32px; left: -40px; min-width: 180px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
                                        <li>
                                            <a href="#" data-toggle="modal" data-target="#modal_kandidat_odustao" class="material-dropdown-menu__link odustao" data-kandidat_id="'. $kandidat_id .'" onMouseDown="odustaoHandle();">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                                Odustao
                                            </a>
                                        </li>';
            if($kandidat_status_prijave == 7 or $kandidat_status_prijave == 8){
                $table_body .= '<li>
                                    <a 
                                        href="#" 
                                        data-toggle="modal" 
                                        data-target="#modal_kandidat_ugovor" 
                                        class="material-dropdown-menu__link upload" 
                                        data-kandidat_id="'. $kandidat_id .'" 
                                        data-ppa_partner_id="'.$kandidat_ppa_partner_id.'" 
                                        data-pp_pozicija="'.$kandidat_pp_pozicija.'"
                                        data-pp_lokacija="'.$kandidat_pp_lokacija.'"
                                        data-pp_plata="'.$kandidat_pp_plata.'"
                                        onMouseDown="uploadHandle();"
                                    >
                                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                        Upload dokument
                                    </a>
                                </li>
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#modal_poslan_ugovor_postom" class="material-dropdown-menu__link unesiDetaljePoste" data-kandidat_id="'. $kandidat_id .'" onMouseDown="poslanUgovorPostom();">
                                        <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                        Poslan ugovor poštom
                                    </a>
                                </li>';
            }
            if($kandidat_status_prijave == 8 or $kandidat_status_prijave == 9){
                $table_body .= '<li>
                                    <a href="'. getSiteUrlr() .'/jobstep_pp/files/candidate_contracts/'. $kandidat_ugovor .'"  target="_blank" class="material-dropdown-menu__link upload" data-kandidat_id="'. $kandidat_id .'">
                                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                        Preuzmi ugovor
                                    </a>
                                </li>';
            }
            $table_body .= '</ul>
                        </div>
                    </td>
                </tr>';

            echo $table_body;
        }
        echo '</tbody>';
    }
//LISTA KANDIDATA KOJI SU NA STATUSU Potpisan Ugovor
    elseif($status_id == 9){

        echo '<style>
                .linear {
                        animation: blink 2s linear infinite;
                }
                @keyframes blink{
                    0% {
                        opacity: .1;
                    }
                    50% {
                        opacity: .5;
                    }
                    100% {
                        opacity: 1;
                    }   
                }
            </style>';
        echo '<thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Ime i Prezime</th>
                        <th class="text-center">Dokaz o radnom iskustvu</th>
                        <th class="text-center">Dokaz o završenoj školi</th>
                        <th class="text-center">Pozicija</th>
                        <th class="text-center">Nostrifikacija</th>
                        <th class="text-center">Diploma</th>
                        <th class="text-center">Jezik</th>
                        <th class="text-center">Potencijalni pocetak rada</th>
                        <th class="text-center">Akcije</th>
                    </tr>
                </thead>
                <tbody>';
        while($result = $query->fetch()){
            $kandidat_id		 				= $result['kandidat_id'];
            $kandidat_dipl_id                   = $result['kandidat_dipl_id'];
            $kandidat_full_name 				= $result['kandidat_full_name'];
            $kandidat_pp_pozicija				= $result['kandidat_pp_pozicija'];
            $kandidat_nacin_odlaska             = $result['kandidat_nacin_odlaska'];
            $doc_dokaz_radno_iskustvo           = $result['doc_dokaz_radno_iskustvo'];
            $doc_dokaz_zavrsena_skola           = $result['doc_dokaz_zavrsena_skola'];
            $kandidat_status_dipl               = $result['dipl_status'];
            $kandidat_provjera_dipl             = $result['file_nd'];
            $kandidat_nivo_jezika               = $result['max_jezik'];
            $kandidat_status_jezik              = $result['cvl_status'];
            $kandidat_jezik_ustanova            = $result['kj_ustanova'];
            $kandidat_potencijalni_pocetak_rada	= $result['kandidat_potencijalni_pocetak_rada'];
            $kandidat_ugovor					= $result['kc_file_name'];
            $active_language                    = getActiveLanguage($kandidat_id);
            $ima_nostrifikaciju                 = $result['kandidat_ima_nostrifikaciju'];
            $required_languages                 = ["A2", "B1", "B2", "C1", "C2"];

            $status_jezik_ispis = '';
            if($kandidat_status_jezik==1) {
                if($kandidat_jezik_ustanova==0){
                    $status_jezik_ispis = 'Samoprocjena';
                }else if($kandidat_jezik_ustanova==1){
                    $status_jezik_ispis = 'Procjena Glose';
                }
            }
            else if($kandidat_status_jezik==2) {$status_jezik_ispis = 'Samostalno uči';}
            else if($kandidat_status_jezik==3) {$status_jezik_ispis = 'Pohađa kurs podnivo 1';}
            else if($kandidat_status_jezik==4) {$status_jezik_ispis = 'Pohađa kurs podnivo 2';}
            else if($kandidat_status_jezik==5) {$status_jezik_ispis = 'Čeka datum polaganja';}
            else if($kandidat_status_jezik==6) {$status_jezik_ispis = 'Čeka polaganje(ima termin)';}
            else if($kandidat_status_jezik==7) {$status_jezik_ispis = 'Čeka se rezultat';}
            else if($kandidat_status_jezik==8) {$status_jezik_ispis = 'Ima certfikat';}
            else if($kandidat_status_jezik==9) {$status_jezik_ispis = 'Certifikat istekao';}
            else if($kandidat_status_jezik==10) {$status_jezik_ispis = 'Napreduje na veći nivo';}
            else if($kandidat_status_jezik==11) {$status_jezik_ispis = 'Nije položio';}
            else if($kandidat_status_jezik==12) {$status_jezik_ispis = 'Odustao';}
            else if($kandidat_status_jezik==13) {$status_jezik_ispis = 'Arhiva';}
            else {$status_jezik_ispis = 'Nedefinisan';}

            if($active_language != NULL )
            {
                $jezik = $active_language;
                $lang_grow_class = "";
                $kandidat_tooltip = "Potvrdjen jezik kod kandidata";
                if(in_array($jezik, $required_languages) AND in_array($kandidat_status_jezik, array(6,7,8)))
                {
                    $kandidat_jezik = "<tool-tip>
                                        <span slot='content' class='label label-success material-label material-label_success main-container__column $lang_grow_class' role='status'> $jezik </span>
                                        <div slot='tooltip'>
                                            $kandidat_tooltip </br>
                                            <hr>
                                            Jezik ispunjava kriterij za nastavak procesa </br>
                                            <hr>
                                            $status_jezik_ispis
                                        </div>
                                    </tool-tip>";
                } 
                else 
                {
                    $kandidat_jezik = "<tool-tip>
                                        <span slot='content' class='label label-danger material-label material-label_danger main-container__column $lang_grow_class' role='status'> $jezik</span>
                                        <div slot='tooltip'>
                                            $kandidat_tooltip </br>
                                            <hr>
                                            Jezik ne ispunjava kriterij za nastavak procesa </br>
                                            <hr>
                                            $status_jezik_ispis
                                        </div>
                                    </tool-tip>";
                }
            } 
            else 
            {
                $jezik = $kandidat_nivo_jezika;
                $lang_grow_class = " linear";
                $kandidat_tooltip = "Potrebno je potvrditi jezik kandidata";
                if(in_array($jezik, $required_languages) AND in_array($kandidat_status_jezik, array(6,7,8)))
                {
                    $kandidat_jezik = "<tool-tip>
                                        <span slot='content' class='label label-success material-label material-label_success main-container__column $lang_grow_class' role='status'> $jezik </span>
                                        <div slot='tooltip'>
                                            $kandidat_tooltip </br>
                                            <hr>
                                            Jezik ispunjava kriterij za nastavak procesa </br>
                                            <hr>
                                            $status_jezik_ispis
                                        </div>
                                    </tool-tip>";
                } 
                else 
                {
                    $kandidat_jezik = "<tool-tip>
                                        <span slot='content' class='label label-danger material-label material-label_danger main-container__column $lang_grow_class' role='status'> $jezik</span>
                                        <div slot='tooltip'>
                                            $kandidat_tooltip </br>
                                            <hr>
                                            Jezik ne ispunjava kriterij za nastavak procesa </br>
                                            <hr>
                                            $status_jezik_ispis
                                        </div>
                                    </tool-tip>";
                }
            }

            if( $ima_nostrifikaciju == NULL ){
                switch($kandidat_status_dipl){
                    case "U pripremi":
                        $order = 3;
                        $dipl_status = "<span class='label label-warning material-label material-label_warning main-container__column'>" . $kandidat_status_dipl . "</span>";
                    break;
                    case "U procesu":
                        $order = 2;
                        $dipl_status = "<span class='label label-info material-label material-label_info main-container__column'>" . $kandidat_status_dipl . "</span>";
                    break;
                    case "Zavrsen":
                        $order = 1;
                        $dipl_status = "<span class='label label-success material-label material-label_success main-container__column'>" . $kandidat_status_dipl . "</span>";
                    break;
                    case "Nepoznato":
                        $order = 4;
                        $dipl_status = "<span class='label label-danger material-label material-label_danger main-container__column'>" . $kandidat_status_dipl . "</span>";
                    break;
                }
            } elseif($ima_nostrifikaciju == 0){
                $order = 5;
                $dipl_status = "<span class='label label-danger material-label material-label_danger main-container__column'>Nema nostrifikaciju</span>";
            } else {
                $order = 1;
                $dipl_status = "<span class='label label-success material-label material-label_success main-container__column'>Ima nostrifikaciju</span>";
            }
            if($order == 1){
                if($kandidat_provjera_dipl == NULL){
                    $dipl_order = 2;
                    $potvrđen_dipl = "<button class='label label-danger material-label material-label_warning main-container__column provjeri_dipl_kand' onclick='checkDipl()' data-toggle='modal' data-target='#provjeri_dipl_kand' data-dipl_id='$kandidat_dipl_id' data-kandidat_id='$kandidat_id'>🗎</button>";
                }else if($kandidat_provjera_dipl != NULL){
                    $dipl_order = 1;
                    $potvrđen_dipl = "<a href='$kandidat_provjera_dipl' target='_blank' class='label label-success material-label material-label_success main-container__column' data-toggle='tooltip' data-placement='top' title='Otvori dokument'>🗎</a>";

                }
            }else{
                $dipl_order = 3;
                $potvrđen_dipl = "<span class='label label-danger material-label material-label_secondary main-container__column'>🛇</span>";
            }

            /*
                Radno iskustvo i zavrsena skola - dokumenti
            */
            $explode_radno_iskustvo = '';
            $explode_radno_iskustvo_new = array();
            $explode_zavrsena_skola = '';
            $explode_zavrsena_skola_new = array();
            $implode_radno_iskustvo = '';
            $implode_zavrsena_skola = '';

            $button_add_radno_iskustvo = '<button onclick="addDocWE(this)" class="btn btn-primary material-btn material-btn_primary" data-candidate_id="'.$kandidat_id.'" data-doc_name="Dokazivo radno iskustvo" data-doc_type="1"><i class="fa fa-plus-square-o" aria-hidden="true"></i></button>';
            $button_add_zavrsena_skola = '<button onclick="addDocWE(this)" class="btn btn-primary material-btn material-btn_primary" data-candidate_id="'.$kandidat_id.'" data-doc_name="Dokaz o završenoj srednjoj školi" data-doc_type="2"><i class="fa fa-plus-square-o" aria-hidden="true"></i></button>'; 
            if ($kandidat_nacin_odlaska == 3) {
                if ($doc_dokaz_radno_iskustvo != null) {
                    $explode_radno_iskustvo = explode(',', $doc_dokaz_radno_iskustvo);
                    if (count($explode_radno_iskustvo) > 0){
                        foreach ($explode_radno_iskustvo AS $row_radno_iskustvo) {
                            array_push($explode_radno_iskustvo_new, '<a class="btn btn-success material-btn material-btn_success" href="'.getSiteUrlr().'files/kandidati_doc/'.$row_radno_iskustvo.'" target="_BLANK" ><i class="fa fa-file-o" aria-hidden="true"></i></a>');
                        }
                        //array_push($explode_radno_iskustvo_new, $button_add_radno_iskustvo);
                        $implode_radno_iskustvo = implode(' ', $explode_radno_iskustvo_new);
                    }
                } else {
                    $implode_radno_iskustvo = $button_add_radno_iskustvo;
                }
                if ($doc_dokaz_zavrsena_skola != null) {
                    $explode_zavrsena_skola = explode(',', $doc_dokaz_zavrsena_skola);
                    if (count($explode_zavrsena_skola) > 0){
                        foreach ($explode_zavrsena_skola AS $row_zavrsena_skola) {
                            array_push($explode_zavrsena_skola_new, '<a class="btn btn-success material-btn material-btn_success" href="'.getSiteUrlr().'files/kandidati_doc/'.$row_zavrsena_skola.'" target="_BLANK" ><i class="fa fa-file-o" aria-hidden="true"></i></a>');
                        }
                        //array_push($explode_zavrsena_skola_new, $button_add_zavrsena_skola);
                        $implode_zavrsena_skola = implode(' ', $explode_zavrsena_skola_new);
                    }
                } else {
                    $implode_zavrsena_skola = $button_add_zavrsena_skola;
                }

            } else {
                $implode_radno_iskustvo = '<i class="fa fa-info-circle text-danger" aria-hidden="true" title="Kandidatu nije potreban dokument jer ne ide preko načina odlaksa Radno iskustvo!"></i>';
                $implode_zavrsena_skola = '<i class="fa fa-info-circle text-danger" aria-hidden="true" title="Kandidatu nije potreban dokument jer ne ide preko načina odlaksa Radno iskustvo!"></i>';
            }

            unset($explode_radno_iskustvo);
            unset($explode_radno_iskustvo_new);
            unset($explode_zavrsena_skola);
            unset($explode_zavrsena_skola_new);

            $table_body = '<tr>
                            <td class="text-center">'. $kandidat_id . '</td>
                            <td class="text-center"><a href="' . getSiteUrlr() .'kandidati?page=open&id='. $kandidat_id. '">'. $kandidat_full_name .'</a></td>
                            <td class="text-center">'.$implode_radno_iskustvo.'</td>
                            <td class="text-center">'.$implode_zavrsena_skola.'</td>
                            <td class="text-center">'. $kandidat_pp_pozicija .'</td>
                            <td class="text-center" data-order=' . $order . '>'. $dipl_status .'</td>
                            <td class="text-center" data-order=' . $dipl_order .'>'.$potvrđen_dipl.'</td>
                            <td class="text-center">'. $kandidat_jezik .'</td>
                            <td class="text-center">'. $kandidat_potencijalni_pocetak_rada .'</td>
                            <td class="text-center">
                                <div class="btn-group material-btn-group">
                                    <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
                                        <i class="fa fa-cogs fa-lg" aria-hidden="true">
                                        </i> 
                                        <span class="caret material-btn__caret">
                                        </span>
                                    </button>
                                    <ul style = "top:32px; left: -40px; min-width: 180px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
                                        <li>
                                            <a href="#" data-toggle="modal" data-target="#modal_kandidat_odustao" class="material-dropdown-menu__link odustao" data-kandidat_id="'. $kandidat_id .'" onMouseDown="odustaoHandle();">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                                Odustao
                                            </a>
                                        </li>
                                        <li>
                                            <a href="'. getSiteUrlr() .'/jobstep_pp/files/candidate_contracts/'. $kandidat_ugovor .'"  target="_blank" class="material-dropdown-menu__link upload" data-kandidat_id="'. $kandidat_id .'">
                                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                Preuzmi ugovor
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>';
            unset($implode_radno_iskustvo);
            unset($implode_zavrsena_skola);

            echo $table_body;
        }
        echo '</tbody>';

    }

//LISTA KANDIDATA KOJI SU NA STATUSU PRIJAVE Prikupljanje Dokumentacije 
    elseif($status_id == 12){

        echo '<thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Ime i Prezime</th>
                        <th class="text-center">Datum prelaska na status</th>
                        <th class="text-center">Prikupljeni dokumenti</th>
                        <th class="text-center">Datum spremnih dokumenata</th>
                        <th class="text-center">Lista dokumenata</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>';
        while($result = $query->fetch()){
            $kandidat_id		 		= $result['kandidat_id'];
            $kandidat_full_name 		= $result['kandidat_full_name'];
            $date_status                = date("d.m.Y", strtotime($result['lsp_datetime']));

            $docs = getNalogDocs(getNalogIdByCandidateId($kandidat_id), $kandidat_id);
            addStatusesToDocument($docs, $kandidat_id);
            $broj_svih = count($docs);
            $broj_spremnih = count(array_filter($docs, function($n) { return $n->statuses["spreman"] != NULL; }));

            $table_body = '<tr>
                                <td class="text-center">'. $kandidat_id .'</td>
                                <td class="text-center"><a href="'. getSiteUrlr() .'kandidati?page=open&id='. $kandidat_id .'">'. $kandidat_full_name .'</a></td>
                                <td class="text-center">'. $date_status .'</td>
                                <td class="text-center">' . $broj_spremnih . ' / ' . $broj_svih . '</td>
                                <td class="text-center">'. allDocumentsReadyDate($kandidat_id, $nalog_id) .'</td>
                                <td class="text-center">
                                    <a 
                                        href="#" 
                                        class="material-dropdown-menu__link lista" 
                                        data-toggle="modal" 
                                        data-target="#modal_lista_dokumenata" 
                                        data-kandidat_id="'. $kandidat_id .'"
                                    >
                                        <i class="fa fa-list-ul" aria-hidden="true"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group material-btn-group">
                                        <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
                                            <i class="fa fa-cogs fa-lg" aria-hidden="true">
                                            </i> 
                                            <span class="caret material-btn__caret">
                                            </span>
                                        </button>
                                        <ul style = "top:32px; left: -40px; min-width: 180px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#modal_kandidat_odustao" class="material-dropdown-menu__link odustao" data-kandidat_id="'. $kandidat_id .'" onMouseDown="odustaoHandle();">
                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                    Odustao
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>';
            echo $table_body;
        }
        echo '</tbody>';
    }
//LISTA KANDIDATA KOJI SU NA STATUSIMA Ceka vizu I Ceka termin
    elseif($status_id == 18){

        echo '<thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Ime i Prezime</th>
                        <th class="text-center">Datum zakazivanja termina</th>
                        <th class="text-center">Datum termina</th>
                        <th class="text-center">Datum otkad ceka vizu</th>
                        <th class="text-center">Broj dana cekanja vize</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>';
        while($result = $query->fetch()){
            $kandidat_id		= $result['kandidat_id'];
            $kandidat_full_name = $result['kandidat_full_name'];
            $broj_dana_viza     = $result['broj_dana_viza'];
            $datum_termina      = date("d.m.Y", strtotime($result['datum_termina']));

            $table_body = '<tr>
                                <td class="text-center">'. $kandidat_id .'</td>
                                <td class="text-center"><a href="'. getSiteUrlr() .'kandidati?page=open&id='. $kandidat_id .'">'. $kandidat_full_name .'</a></td>
                                <td class="text-center">'. getLspDatetime($kandidat_id, 15) .'</td>
                                <td class="text-center">'. $datum_termina .'</td>
                                <td class="text-center">'. getLspDatetime($kandidat_id, 18) .'</td>
                                <td class="text-center">'. $broj_dana_viza .'</td>
                                <td class="text-center">
                                    <div class="btn-group material-btn-group">
                                        <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
                                            <i class="fa fa-cogs fa-lg" aria-hidden="true">
                                            </i> 
                                            <span class="caret material-btn__caret">
                                            </span>
                                        </button>
                                        <ul style = "top:32px; left: -40px; min-width: 180px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#modal_kandidat_odustao" class="material-dropdown-menu__link odustao" data-kandidat_id="'. $kandidat_id .'" onMouseDown="odustaoHandle();">
                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                    Odustao
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>';
            echo $table_body;
        }
        echo '</tbody>';
    }
//LISTA KANDIDATA KOJI SU NA STATUSU PRIJAVE Dopuna 
    elseif($status_id == 21){

        echo '<thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Ime i Prezime</th>
                        <th class="text-center">Datum prelaska na status</th>
                        <th class="text-center">Prikupljeni dokumenti</th>
                        <th class="text-center">Datum dopune</th>
                        <th class="text-center">Krajnji datum dopune</th>
                        <th class="text-center">Lista dokumenata</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>';
        while($result = $query->fetch()){
            $kandidat_id		        = $result['kandidat_id'];
            $kandidat_full_name         = $result['kandidat_full_name'];
            $date_status                = date("d.m.Y", strtotime($result['lsp_datetime']));
            $dopuna_date_kandidat       = $result["vi_date_received_candidate"];
            $dopuna_date_poslodavac     = $result["vi_date_received_employer"];
            $dopuna_deadline_kandidat   = $result["vi_deadline_date_candidate"];
            $dopuna_deadline_poslodavac = $result["vi_deadline_date_employer"];

            if($dopuna_date_kandidat == NULL){
                $dopuna_date = date("d.m.Y", strtotime($dopuna_date_poslodavac));
            } else {
                $dopuna_date = date("d.m.Y", strtotime($dopuna_date_kandidat));
            }
            
            if($dopuna_deadline_kandidat == NULL){
                $dopuna_deadline = date("d.m.Y", strtotime($dopuna_deadline_poslodavac));
            } else {
                $dopuna_deadline = date("d.m.Y", strtotime($dopuna_deadline_kandidat));
            }

            $docs = getKandidatDocs(getNalogIdByCandidateId($kandidat_id), $kandidat_id);
            addStatusesToDocument($docs, $kandidat_id);
            $broj_svih = count($docs);
            $broj_spremnih = count(array_filter($docs, function($n) { return $n->statuses["spreman"] != NULL; }));

            $table_body = '<tr>
                                <td class="text-center">'. $kandidat_id .'</td>
                                <td class="text-center"><a href="'. getSiteUrlr() .'kandidati?page=open&id='. $kandidat_id .'">'. $kandidat_full_name .'</a></td>
                                <td class="text-center">'. $date_status .'</td>
                                <td class="text-center">' . $broj_spremnih . ' / ' . $broj_svih . '</td>
                                <td class="text-center">'. $dopuna_date .'</td>
                                <td class="text-center">'. $dopuna_deadline .'</td>
                                <td class="text-center">
                                    <a 
                                        href="#" 
                                        class="material-dropdown-menu__link lista_dopuna" 
                                        data-toggle="modal" 
                                        data-target="#modal_lista_dokumenata_dopuna" 
                                        data-kandidat_id="'. $kandidat_id .'"
                                        data-dopuna_id="'. getActiveVisaIncompleteR($kandidat_id, 1).'"
                                    >
                                        <i class="fa fa-list-ul" aria-hidden="true"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group material-btn-group">
                                        <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
                                            <i class="fa fa-cogs fa-lg" aria-hidden="true">
                                            </i> 
                                            <span class="caret material-btn__caret">
                                            </span>
                                        </button>
                                        <ul style = "top:32px; left: -40px; min-width: 180px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#modal_kandidat_odustao" class="material-dropdown-menu__link odustao" data-kandidat_id="'. $kandidat_id .'" onMouseDown="odustaoHandle();">
                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                    Odustao
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>';
            echo $table_body;
        }
        echo '</tbody>';
    }
//LISTA KANDIDATA KOJI SU NA STATUSU PRIJAVE Odbijenica 
    elseif($status_id == 24){

        echo '<thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Ime i Prezime</th>
                        <th class="text-center">Datum prelaska na status</th>
                        <th class="text-center">Prikupljeni dokumenti</th>
                        <th class="text-center">Datum odbijenice</th>
                        <th class="text-center">Krajnji datum odbijenice</th>
                        <th class="text-center">Lista dokumenata</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>';
        while($result = $query->fetch()){
            $kandidat_id		            = $result['kandidat_id'];
            $kandidat_full_name             = $result['kandidat_full_name'];
            $date_status                    = date("d.m.Y", strtotime($result['lsp_datetime']));
            $odbijenica_date_kandidat       = $result["vi_date_received_candidate"];
            $odbijenica_date_poslodavac     = $result["vi_date_received_employer"];
            $odbijenica_deadline_kandidat   = $result["vi_deadline_date_candidate"];
            $odbijenica_deadline_poslodavac = $result["vi_deadline_date_employer"];

            if($odbijenica_date_kandidat == NULL){
                $odbijenica_date = date("d.m.Y", strtotime($odbijenica_date_poslodavac));
            } else {
                $odbijenica_date = date("d.m.Y", strtotime($odbijenica_date_kandidat));
            }
            
            if($odbijenica_deadline_kandidat == NULL){
                $odbijenica_deadline = date("d.m.Y", strtotime($odbijenica_deadline_poslodavac));
            } else {
                $odbijenica_deadline = date("d.m.Y", strtotime($odbijenica_deadline_kandidat));
            }

            $docs = getKandidatDocs(getNalogIdByCandidateId($kandidat_id), $kandidat_id);
            addStatusesToDocument($docs, $kandidat_id);
            $broj_svih = count($docs);
            $broj_spremnih = count(array_filter($docs, function($n) { return $n->statuses["spreman"] != NULL; }));

            $table_body = '<tr>
                                <td class="text-center">'. $kandidat_id .'</td>
                                <td class="text-center"><a href="'. getSiteUrlr() .'kandidati?page=open&id='. $kandidat_id .'">'. $kandidat_full_name .'</a></td>
                                <td class="text-center">'. $date_status .'</td>
                                <td class="text-center">' . $broj_spremnih . ' / ' . $broj_svih . '</td>
                                <td class="text-center">'. $odbijenica_date .'</td>
                                <td class="text-center">'. $odbijenica_deadline .'</td>
                                <td class="text-center">
                                    <a 
                                        href="#" 
                                        class="material-dropdown-menu__link lista_odbijenica" 
                                        data-toggle="modal" 
                                        data-target="#modal_lista_dokumenata_odbijenica" 
                                        data-kandidat_id="'. $kandidat_id .'"
                                        data-odbijenica_id="'. getActiveVisaIncompleteR($kandidat_id, 2).'"
                                    >
                                        <i class="fa fa-list-ul" aria-hidden="true"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group material-btn-group">
                                        <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
                                            <i class="fa fa-cogs fa-lg" aria-hidden="true">
                                            </i> 
                                            <span class="caret material-btn__caret">
                                            </span>
                                        </button>
                                        <ul style = "top:32px; left: -40px; min-width: 180px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#modal_kandidat_odustao" class="material-dropdown-menu__link odustao" data-kandidat_id="'. $kandidat_id .'" onMouseDown="odustaoHandle();">
                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                    Odustao
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>';
            echo $table_body;
        }
        echo '</tbody>';
    }

//LISTA KANDIDATA KOJI SU NA STATUSU Dobio vizu
    elseif($status_id == 27){

        echo '<thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Ime i Prezime</th>
                        <th class="text-center">Datum termina</th>
                        <th class="text-center">Datum dobijanja vize</th>
                        <th class="text-center">Datum otkad traje viza</th>
                        <th class="text-center">Datum do kad traje viza</th>
                        <th class="text-center">Pocetak rada</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>';
        while($result = $query->fetch()){
            $kandidat_id	    = $result['kandidat_id'];
            $kandidat_full_name = $result['kandidat_full_name'];
            $datum_termina      = date("d.m.Y", strtotime($result['datum_termina']));
            $date_status        = date("d.m.y", strtotime($result['lsp_datetime']));
            $pocetak_rada       = $result['kandidat_potencijalni_pocetak_rada'];
            $visa_start         = date("d.m.Y", strtotime($result["kandidat_viza_vrijedi_od"]));
            $visa_end           = date("d.m.Y", strtotime($result["kandidat_viza_vrijedi_do"]));

            $table_body = '<tr>
                                <td class="text-center">'. $kandidat_id .'</td>
                                <td class="text-center"><a href="'. getSiteUrlr() .'kandidati?page=open&id='. $kandidat_id .'">'. $kandidat_full_name .'</a></td>
                                <td class="text-center">'. $datum_termina .'</td>
                                <td class="text-center">'. $date_status .'</td>
                                <td class="text-center">'. $visa_start .'</td>
                                <td class="text-center">'. $visa_end .'</td>
                                <td class="text-center">'. $pocetak_rada .'</td>
                                <td class="text-center">
                                    <div class="btn-group material-btn-group">
                                        <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
                                            <i class="fa fa-cogs fa-lg" aria-hidden="true">
                                            </i> 
                                            <span class="caret material-btn__caret">
                                            </span>
                                        </button>
                                        <ul style = "top:32px; left: -40px; min-width: 180px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#modal_kandidat_odustao" class="material-dropdown-menu__link odustao" data-kandidat_id="'. $kandidat_id .'" onMouseDown="odustaoHandle();">
                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                    Odustao
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>';
            echo $table_body;
        }
        echo '</tbody>';
    }
echo '</table>';