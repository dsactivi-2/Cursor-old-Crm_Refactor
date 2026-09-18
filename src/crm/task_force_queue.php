<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/connect.php");
include($_SERVER["DOCUMENT_ROOT"] . "/includes/functions.php");
// JSON header
header('Content-Type: application/json');

[$candidateId, $projectId, $nalogId, $vrstaId, $diplId, $categoryId] = getNextTFCandidate($logged_employee_id);
if ($candidateId === null) {
    http_response_code(500);
    echo json_encode(['message' => 'No candidate found']);
    exit;
}

http_response_code(200);
echo json_encode(["kandidat_id" => $candidateId, "projekt_id" => $projectId, "nalog_id" => $nalogId, "vrsta_id" => $vrstaId, "dipl_id" => $diplId, "category_id" => $categoryId]);


/* Functions */

/**
 * Returns an array of functions that return the next candidate for the agent
 * The order of the functions in the array determines the priority of the queue
 * @return array Array of functions
 */
function makePriorityQueue(): array
{
    $nalozi = getTFNalozi();
    
    $priorityQueue = [
                
        /* Posredovanje Inbound */
        function () use ($nalozi) { return getTFCandidateByTFStatus(43, $nalozi); },            /* Posredovanje Odbijen - Inbound */
        function () use ($nalozi) { return getTFCandidateByTFStatus(44, $nalozi); },            /* Posredovanje Ceka Ugovor - Inbound */
        function () use ($nalozi) { return getTFCandidateByTFStatus(45, $nalozi); },            /* Posredovanje Poslan Ugovor - Inbound */
        function () use ($nalozi) { return getTFCandidateByTFStatus(46, $nalozi); },            /* Posredovanje Poslan Ugovor TimeUp - Inbound */
        function () use ($nalozi) { return getTFCandidateByTFStatus(47, $nalozi); },            /* Posredovanje Potpisan Ugovor - Inbound */
        /* Jezici Inbound */
        function () use ($nalozi) { return getTFCandidateByTFStatus(140, $nalozi); },           /* Posredovanje - Jezik A1.1 Inbound */
        function () use ($nalozi) { return getTFCandidateByTFStatus(141, $nalozi); },           /* Posredovanje - Jezik A1.2 Inbound */
        function () use ($nalozi) { return getTFCandidateByTFStatus(142, $nalozi); },           /* Posredovanje - Jezik A2.1 Inbound */
        function () use ($nalozi) { return getTFCandidateByTFStatus(143, $nalozi); },           /* Posredovanje - Zavrsen kurs A2.2 */
        function () use ($nalozi) { return getTFCandidateByTFStatus(144, $nalozi); },           /* Posredovanje - Provjera izlaska na ispit */
        function () use ($nalozi) { return getTFCandidateByTFStatus(145, $nalozi); },           /* Posredovanje - Čeka rezultat */
        function () use ($nalozi) { return getTFCandidateByTFStatus(146, $nalozi); },           /*Posredovanje - Apliciraj za zapadni balkan */
        /* Casting Inbound */
        function () use ($nalozi) { return getTFCandidateByTFStatus(24, $nalozi); },            /* Casting - Inbound */                                                                                        /* Casting - Inbound */
        /* Posredovanje taskovi */
        function () use ($nalozi) { return getTFCandidateWestBalkan($nalozi, 9, 33); },         /* Posredovanje - Apliciraj za zapadni balkan */
        function () use ($nalozi) { return getTFCandidateByTFStatus(139, $nalozi); },           /* Posredovanje - Apliciraj za zapadni balkan - Nepotpuni podaci */
        function () use ($nalozi) { return getTFCandidateByStatusPrijave($nalozi, 9, 0, 11);},  /* Posredovanje - Potpisan ugovor */
        function () use ($nalozi) { return getTFCandidateByTFStatus(39, $nalozi); },            /* Posredovanje - Potpisan ugovor ne javlja se */
        function () use ($nalozi) { return getTFCandidateByStatusPrijave($nalozi, 7, 1, 8);},   /* Posredovanje - Čeka ugovor */
        function () use ($nalozi) { return getTFCandidateByStatusPrijave($nalozi, 5, 1, 7);},   /* Posredovanje - Odbijen */
        function () use ($nalozi) { return getTFCandidateByTFStatus(27, $nalozi); },            /* Posredovanje - Čeka ugovor ne javlja se */
        function () use ($nalozi) { return getTFCandidateByTFStatus(25, $nalozi); },            /* Posredovanje odbijen ne javlja se */
        function () use ($nalozi) { return getTFCandidateByStatusPrijave($nalozi, 8, 1, 9);},   /* Posredovanje - Poslan ugovor */
        function () use ($nalozi) { return getTFCandidateByTFStatus(31, $nalozi); },            /* Posredovanje poslan ugovor ne javlja se */
        function () use ($nalozi) { return getTFCandidateByStatusPrijave($nalozi, 8, 7, 10);},  /* Posredovanje - TimeUp for Poslan ugovor */
        function () use ($nalozi) { return getTFCandidateByTFStatus(35, $nalozi); },            /* Posredovanje - Time up for potpisan ugovor ne javlja se */
        function () use ($nalozi) { return getTFCandidateByTFStatus(36, $nalozi); },            /* Posredovanje - Time up for potpisan ugovor Obavijesten */
        function () use ($nalozi) { return getTFCandidateByTFStatus(37, $nalozi); },            /* Posredovanje - Time up for potpisan ugovor Obavijesten tekstom */
        function () use ($nalozi) { return getTFCandidateByLanguage($nalozi, "9,12,15,18,21,24", 2, 30, 'A2', 4);},  /* Posredovanje - Zavrsen kurs A2.2 */
        function () use ($nalozi) { return getTFCandidateByTFStatus(122, $nalozi); },           /* Posredovanje - Zavrsen kurs A2.2 - Ne javlja se */
        function () use ($nalozi) { return getTFCandidateByTFStatus(123, $nalozi); },           /* Posredovanje - Zavrsen kurs A2.2 - Ceka datum polaganja */
        function () use ($nalozi) { return getTFCandidateByTFStatus(125, $nalozi); },           /* Posredovanje - Zavrsen kurs A2.2 - Zeli jos da se spremi */
        function () use ($nalozi) { return getTFCandidateByLanguage($nalozi, "9,12,15,18,21,24", 1, 31, 'A2', 6);},  /* Posredovanje - Provjera izlaska na ispit */
        function () use ($nalozi) { return getTFCandidateByTFStatus(127, $nalozi); },           /* Posredovanje - Provjera izlaska na ispit - Ne javlja se */
        function () use ($nalozi) { return getTFCandidateByTFStatus(129, $nalozi); },           /* Posredovanje - Provjera izlaska na ispit - Nije izasao na ispit i treba novi termin */
        function () use ($nalozi) { return getTFCandidateByLanguage($nalozi, "9,12,15,18,21,24", 35, 32, 'A2', 7);},  /* Posredovanje - Čeka rezultat */
        function () use ($nalozi) { return getTFCandidateByTFStatus(131, $nalozi); },           /* Posredovanje - Čeka rezultat - Ne javlja se */
        function () use ($nalozi) { return getTFCandidateByTFStatus(133, $nalozi); },           /* Posredovanje - Čeka rezultat - Polozio dio ispita, treba novi termin */
        function () use ($nalozi) { return getTFCandidateByTFStatus(134, $nalozi); },           /* Posredovanje - Čeka rezultat - Nije polozio, treba novi termin */
        function () use ($nalozi) { return getTFCandidateByTFStatus(136, $nalozi); },           /* Posredovanje - Čeka rezultat - Nije još dobio rezultate */
        function () use ($nalozi) { return getTFCandidateByLanguage($nalozi, "9", 2, 27, 'A1', 3);},  /* Posredovanje - Jezik A1.1 */
        function () use ($nalozi) { return getTFCandidateByTFStatus(107, $nalozi); },           /* Posredovanje - Jezik A1.1 - Ne javlja se */
        function () use ($nalozi) { return getTFCandidateByTFStatus(109, $nalozi); },           /* Posredovanje - Jezik A1.1 - Nije položio završni test */
        function () use ($nalozi) { return getTFCandidateByLanguage($nalozi, "9", 2, 28, 'A1', 4);},  /* Posredovanje - Jezik A1.2 */
        function () use ($nalozi) { return getTFCandidateByTFStatus(112, $nalozi); },           /* Posredovanje - Jezik A1.2 - Ne javlja se */
        function () use ($nalozi) { return getTFCandidateByTFStatus(114, $nalozi); },           /* Posredovanje - Jezik A1.2 - Nije položio završni test */
        function () use ($nalozi) { return getTFCandidateByLanguage($nalozi, "9", 2, 29, 'A2', 3);},  /* Posredovanje - Jezik A2.1 */
        function () use ($nalozi) { return getTFCandidateByTFStatus(117, $nalozi); },           /* Posredovanje - Jezik A2.1 - Ne javlja se */
        function () use ($nalozi) { return getTFCandidateByTFStatus(119, $nalozi); },           /* Posredovanje - Jezik A2.1 - Nije položio završni test */
        /* Casting taskovi*/
        function () use ($nalozi) { return getTFCandidateByTFStatus(22, $nalozi); },                                    /* Casting - Nije došao */
        function () use ($nalozi) { return getTFCandidateByTFStatus(16, $nalozi); },                                    /* Casting - Pristao */
        function () use ($nalozi) { return getTFCandidateByTFStatus(12, $nalozi); },                                    /* Casting - Dopuna */
        function () use ($nalozi) { return getTFCandidateByTFStatus(14, $nalozi); },                                    /* Casting - Zainteresiran */
        function () use ($nalozi) { return getEUCandidate($nalozi); },                                                  /* EU kandidati */
        function () use ($nalozi) { return getProjectReservedCandidate($nalozi, '%BOT - ispunjava uslove%'); },         /* Casting - Bot ispunjava uslove */
        function () use ($nalozi) { return getProjectUnreservedCandidate($nalozi, '%Prijave%'); },                      /* Casting - Prijave*/
        function () use ($nalozi) { return getProjectUnreservedCandidate($nalozi, '%BOT - ne ispunjava uslove%'); },    /* Casting - Bot ne ispunjava uslove */ 
        function () use ($nalozi) { return getTFCandidateByTFStatus(2,  $nalozi); },                                    /* Casting - Neuspješna komunikacija */
        function () use ($nalozi) { return getProjectReservedCandidate($nalozi, '%Baza - odgovara za nalog%'); },       /* Casting - Baza odgovara za nalog */
        function () use ($nalozi) { return getTFCandidateByTFStatus(8,  $nalozi); },                                    /* Casting - Ispunjava uslove ne javlja se */
        /* Obrada Inbound */
        function () { return getDIPLCandidateByTFStatus(69); },                 /* Obrada Prebaciti na prikupljanje 100% Inbound */
        function () { return getDIPLCandidateByTFStatus(70); },                 /* Obrada Povezi sa ustanovom i posalji obavijest Inbound */
        function () { return getDIPLCandidateByTFStatus(89); },                 /* Obrada Cekamo uplatu takse-slanje dopune Inbound */
        function () { return getDIPLCandidateByTFStatus(79); },                 /* Obrada Javiti se ustanovi Inbound */
        function () { return getDIPLCandidateByTFStatus(88); },                 /* Obrada Cekamo taksu-dopunu Inbound*/
        function () { return getDIPLCandidateByTFStatus(90); },                 /* Obrada Javiti se ustanovi (zavrsetak procesa) Inbound */
        function () { return getDIPLCandidateByTFStatus(74); },                 /* Obrada Cekamo zahtjev Inbound */
        function () { return getDIPLCandidateByTFStatus(72); },                 /* Obrada Nepotpuna dokumentacija Inbound */
        function () { return getDIPLCandidateByTFStatus(78); },                 /* Obrada Dokumentacija kompletirana Inbound */
        function () { return getDIPLCandidateByTFStatus(73); },                 /* Obrada Posalji zahtjev HWK/ZAV/KMK Inbound */
        function () { return getDIPLCandidateByTFStatus(75); },                 /* Obrada Poslati postu Inbound */
        function () { return getDIPLCandidateByTFStatus(76); },                 /* Obrada Poslana posta Inbound */
        function () { return getDIPLCandidateByTFStatus(91); },                 /* Obrada Zavrsen proces Inbound */
        function () { return getDIPLCandidateByTFStatus(77); },                 /* Obrada Provjera da li je stigla dokumentacija Inbound */
        function () { return getDIPLCandidateByTFStatus(71); },                 /* Obrada Cekamo dokumentaciju Inbound */
        /* Obrada Taskovi */
        function () {return getDIPLCandidateForPrebacitiNaPrikupljanje(12); },  /* Obrada Prebaciti na prikupljanje 100% */
        function ()  { return getDIPLCandidateByStatus(2, 1, 0, 13); },         /* Obrada - Povezi sa ustanovom i posalji obavijest */
        function () { return getDIPLCandidateByTFStatus(51); },                 /* Obrada Povezi sa ustanovom i posalji obavijest - HWK/ZAV/KMK */
        function ()  { return getDIPLCandidateByStatus(4, 2, 0, 24); },         /* Obrada -  cekamo uplatu takse/ slanje dopune */
        function () { return getDIPLCandidateByTFStatus(82); },                 /* Obrada  cekamo uplatu takse/ slanje dopune - Obavijesten*/
        function () { return getDIPLCandidateByTFStatus(83); },                 /* Obrada cekamo uplatu takse/ slanje dopune - Obavijesten tekstom*/
        function ()  { return getDIPLCandidateByStatus(3, 2, 45, 22); },         /* Obrada -  Javiti se ustanovi */
        function () { return getDIPLCandidateByTFStatus(67); },                 /* Obrada -  Javiti se ustanovi - Ustanova kontaktirana*/
        function () { return getDIPLCandidateByTFStatus(68); },                 /* Obrada -  Javiti se ustanovi - Ustanova kontaktirana mailom*/
        function ()  { return getDIPLCandidateByStatus(4, 1, 20, 23); },         /* Obrada - Cekamo taksu-dopunu */
        function () { return getDIPLCandidateByTFStatus(80); },                 /* Obrada Cekamo taksu-dopunu - Ustanova kontaktirana*/
        function () { return getDIPLCandidateByTFStatus(81); },                 /* Obrada Cekamo taksu-dopunu - Ustanova kontaktirana mailom*/
        function ()  { return getDIPLCandidateByStatus(5, 1, 100, 25); },        /* Obrada -  Javiti se ustanovi (zavrsetak procesa) */
        function () { return getDIPLCandidateByTFStatus(84); },                 /* Obrada  Javiti se ustanovi (zavrsetak procesa) - Ustanova kontaktirana*/
        function () { return getDIPLCandidateByTFStatus(85); },                 /* Obrada Javiti se ustanovi (zavrsetak procesa) - Ustanova kontaktirana mailom*/
        function ()  { return getDIPLCandidateByStatus(2, 6, 7, 17); },         /* Obrada - Cekamo zahtjev */
        function () { return getDIPLCandidateByTFStatus(57); },                 /* Obrada Cekamo zahtjev - Obavijesten */
        function () { return getDIPLCandidateByTFStatus(58); },                 /* Obrada Cekamo zahtjev - Ne javlja se */
        function ()  { return getDIPLCandidateByStatus(2, 2, 0, 15); },         /* Obrada - Nepotpuna dokumentacija */
        function () { return getDIPLCandidateByTFStatus(54); },                 /* Obrada Nepotpuna dokumentacija - Obavijesten */
        function () { return getDIPLCandidateByTFStatus(55); },                 /* Obrada Nepotpuna dokumentacija - Ne javlja se */
        function ()  { return getDIPLCandidateByStatus(2, 4, 0, 21); },         /* Obrada - Dokumentacija kompletirana */
        function ()  { return getDIPLCandidateByStatus(2, 5, 0, 16); },         /* Obrada - Posalji zahtjev HWK/ZAV/KMK */
        function ()  { return getDIPLCandidateByStatus(2, 5, 3, 18); },         /* Obrada - Poslati postu */
        function ()  { return getDIPLCandidateByStatus(2, 7, 3, 18); },         /* Obrada - Poslati postu drugi uslov */
        function () { return getDIPLCandidateByTFStatus(60); },                 /* Obrada Poslati postu - Nije poslana posta */
        function ()  { return getDIPLCandidateByStatus(3, 1, 0, 19); },         /* Obrada - Poslana posta */
        function ()  { return getDIPLCandidateByStatus(6, 1, 0, 26); },         /* Obrada -  Zavrsen proces        */
        function () { return getDIPLCandidateByTFStatus(92); },                 /* Obrada Zavrsen proces - Cekamo postu iz Njemacke */
        function ()  { return getDIPLCandidateByStatus(3, 1, 10, 20); },        /* Obrada - Provjera da li je stigla dokumentacija */
        function () { return getDIPLCandidateByTFStatus(64); },                 /* Obrada Provjera da li je stigla dokumentacija - Dokumentacija nije stigla */
        function ()  { return getDIPLCandidateByStatus(2, 1, 7, 14); },         /* Obrada - Cekamo dokumentaciju */
        function () { return getDIPLCandidateByTFStatus(52); },                 /* Obrada Cekamo dokumentaciju - Obavijesten*/
        function () { return getDIPLCandidateByTFStatus(53); },                 /* Obrada Cekamo dokumentaciju - Ne javlja se */
        
    ];
    
    return $priorityQueue;
}
/**
 * Returns ID of the next candidate for the agent
 * @param int $agentId ID of the agent
 * @return array Array of candidate id, project id and nalog id
 */
function getNextTFCandidate(int $agentId): array
{
    // Check if agent already has a reserved candidate
    [$reservedCandidate, $reservedProjekt, $reservedNalog, $reservedVrsta, $reservedDipl, $categoryId] = getReservedCandidate($agentId);
    if ($reservedCandidate !== null) {
        return [$reservedCandidate, $reservedProjekt, $reservedNalog, $reservedVrsta, $reservedDipl, $categoryId];
    }
    
    // Loop through the queue of functions until a candidate is found
    $priorityQueue = makePriorityQueue();
    for ($i = 0; $i < count($priorityQueue); $i++) {
        [$candidateId, $projectId, $nalogId, $vrstaId, $diplId, $categoryId] = $priorityQueue[$i]();
        
        if ($candidateId === null) continue;

        /*
        Try to reserve the candidate. If it fails, it means that another agent
        has already reserved it while we were looping through the queue.
        If so, start over from the beginning of the queue.
        */
        try {
            reserveCandidate($agentId, $candidateId, $projectId, $nalogId, $vrstaId, $diplId, $categoryId);
            return [$candidateId, $projectId, $nalogId, $vrstaId, $diplId, $categoryId];
        } catch (PDOException $e) {
            $i = -1;
        }
    }

    // No candidate found
    return [null, null, null, null, null, null];
}

/**
 * Returns an array of ids of the nalozi that have a TF assigned
 * @return array Array of nalog ids
 */
function getTFNalozi(): array
{
    global $db;
    Global $logged_employee_id;

    // Ovo obrisati kad vidimo da je sve ok
    // $query = $db->prepare("SELECT nalog_id FROM idk_nalozi WHERE nalog_prioritet IS NOT NULL");
    // $query->execute();

    $query = $db->prepare("SELECT nalog_id 
                        FROM idk_nalozi
                        INNER JOIN idk_tf_agent_nalog ON idk_nalozi.nalog_id = idk_tf_agent_nalog.tfan_nalog 
                        WHERE nalog_prioritet IS NOT NULL AND tfan_agent = :tfan_agent
                        ORDER BY nalog_prioritet ASC");
                        
    $query->execute(array(
        ':tfan_agent' => $logged_employee_id
    ));

    $nalozi = $query->fetchAll(PDO::FETCH_COLUMN);
    return $nalozi;
}

/**
 * Returns a candidate from projekt rezervisani
 * @param array $nalozi Array of nalog ids
 * @param string $projekt Projekt name
 * @return array Array of candidate id, project id and nalog id
 */
function getProjectReservedCandidate(array $nalozi, string $projekt): array
{
    global $db;
    Global $logged_employee_id;
    $nalozi = implode(',', $nalozi);

    $isAgentDvag = isAgentDvag();

    if($isAgentDvag == 1){
        $sql_dvag_query = "";
    }else{
        $sql_dvag_query = " AND jp_id IS NULL";
    }

    $query = $db->prepare("SELECT   
        kan.kandidat_id,
        proj.project_id,
        kan.kandidat_nalog_id,
        1 AS vrsta_id,
        null AS dipl_id,
        1 AS category_id
    FROM
        idk_kandidati kan
    JOIN idk_project_kandidati projK ON
        kan.kandidat_id = projK.pk_kandidatid
    JOIN idk_projects proj ON
        projK.pk_projectid = proj.project_id
    JOIN (SELECT
            nal.nalog_id,
            nal.nalog_prioritet
        FROM
            idk_nalozi nal
        WHERE
            nal.nalog_prioritet IS NOT NULL) as nalozi ON proj.project_nalogid=nalozi.nalog_id
    JOIN idk_tf_agent_nalog ON kan.kandidat_nalog_id  = idk_tf_agent_nalog.tfan_nalog AND tfan_vrsta_id = 1 AND tfan_agent = $logged_employee_id   
    LEFT JOIN idk_jobstep_partners ON kan.kandidat_partnerid = idk_jobstep_partners.jp_id AND idk_jobstep_partners.jp_partner_company = 3
    WHERE
        kan.kandidat_nalog_id IN($nalozi)
        AND kan.kandidat_tf_status IS NULL
    AND (proj.project_nalogid = kan.kandidat_nalog_id AND proj.project_name LIKE '$projekt')
    AND(
        kan.kandidat_status_prijave IN(0, 1, 2, 5, 6) OR kan.kandidat_status_prijave IS NULL
    ) 
    AND kan.kandidat_id NOT IN (SELECT tfr.tf_kandidat_id FROM  idk_tf_reservations tfr)
    AND kan.kandidat_mobitel IS NOT NULL
    AND kan.kandidat_pogresan_broj = 0
    AND kan.kandidat_nedostupan = 0
    $sql_dvag_query
    GROUP BY
        kan.kandidat_id
    ORDER BY nalozi.nalog_prioritet ASC, idk_jobstep_partners.jp_partner_company DESC
    LIMIT 1
    ");

    $query->execute();

    if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_NUM);
        return $row;
    } else {
        return [null, null, null, null, null, null];
    }
}

/**
 * Returns a candidate from projekt nerezervisani
 * @param array $nalozi Array of nalog ids  
 * @param string $projekt Projekt name
 * @return array Array of candidate id, project id and nalog id
 */
function getProjectUnreservedCandidate(array $nalozi, string $projekt): ?array
{
    global $db;
    Global $logged_employee_id;
    $nalozi = implode(',', $nalozi);

    $isAgentDvag = isAgentDvag();

    if($isAgentDvag == 1){
        $sql_dvag_query = "";
    }else{
        $sql_dvag_query = " AND jp_id IS NULL";
    }

    $query = $db->prepare("SELECT
        kan.kandidat_id,
        proj.project_id,
        proj.project_nalogid,
        1 AS vrsta_id,
        null AS dipl_id,
        1 AS category_id
    FROM
        idk_kandidati kan
    JOIN idk_project_kandidati projK ON
        kan.kandidat_id = projK.pk_kandidatid
    JOIN idk_projects proj ON
        projK.pk_projectid = proj.project_id 
    JOIN (SELECT
            nal.nalog_id,
            nal.nalog_prioritet
        FROM
            idk_nalozi nal
        WHERE
            nal.nalog_prioritet IS NOT NULL) as nalozi ON proj.project_nalogid=nalozi.nalog_id
    JOIN idk_tf_agent_nalog ON proj.project_nalogid  = idk_tf_agent_nalog.tfan_nalog AND tfan_vrsta_id = 1 AND tfan_agent = $logged_employee_id    
    LEFT JOIN idk_jobstep_partners ON kan.kandidat_partnerid = idk_jobstep_partners.jp_id AND idk_jobstep_partners.jp_partner_company = 3
    WHERE kan.kandidat_tf_status IS NULL
        AND proj.project_nalogid IN ($nalozi)
            
    AND proj.project_name LIKE '$projekt'
    AND(
        kan.kandidat_status_prijave IN(0, 1, 2, 5, 6) OR kan.kandidat_status_prijave IS NULL
    ) 
    AND kan.kandidat_id NOT IN (SELECT tfr.tf_kandidat_id FROM idk_tf_reservations tfr)
    AND kan.kandidat_nalog_id IS NULL
    AND kan.kandidat_mobitel IS NOT NULL
    AND kan.kandidat_pogresan_broj = 0
    AND kan.kandidat_nedostupan = 0
    $sql_dvag_query
    GROUP BY
        kan.kandidat_id
    ORDER BY 
        nalozi.nalog_prioritet ASC, idk_jobstep_partners.jp_partner_company DESC
    LIMIT 1;
    ");

    $query->execute();

    if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_NUM);
        return $row;
    } else {
        return [null, null, null, null, null, null];
    }
}

/**
 * Returns a EU candidate
 * @param array $nalozi Array of nalog ids
 * @return array Array of candidate id, project id and nalog id
 */
function getEUCandidate(array $nalozi): array
{
    global $db;
    Global $logged_employee_id;
    $nalozi = implode(',', $nalozi);

    $isAgentDvag = isAgentDvag();

    if($isAgentDvag == 1){
        $sql_dvag_query = "";
    }else{
        $sql_dvag_query = " AND jp_id IS NULL";
    }

    $query = $db->prepare("SELECT
        kan.kandidat_id,
        proj.project_id,
        proj.project_nalogid,
        1 AS vrsta_id,
        null AS dipl_id,
        1 AS category_id
    FROM
        idk_kandidati kan
    JOIN idk_project_kandidati projK 
        ON kan.kandidat_id=projK.pk_kandidatid
    JOIN idk_projects proj
        ON projK.pk_projectid=proj.project_id
    JOIN ( SELECT
            nal.nalog_id,
            nal.nalog_prioritet
        FROM
            idk_nalozi nal
        WHERE
            nal.nalog_prioritet IS NOT NULL) as nalozi ON proj.project_nalogid=nalozi.nalog_id
    JOIN idk_tf_agent_nalog ON proj.project_nalogid  = idk_tf_agent_nalog.tfan_nalog AND tfan_vrsta_id = 1 AND tfan_agent = $logged_employee_id
    LEFT JOIN idk_jobstep_partners ON kan.kandidat_partnerid = idk_jobstep_partners.jp_id AND idk_jobstep_partners.jp_partner_company = 3
    WHERE
        proj.project_nalogid IN ( $nalozi )
        AND proj.project_name LIKE ('% - %')
        AND proj.project_name NOT LIKE ('%Nije zainteresiran%')
        AND proj.project_name NOT LIKE ('%dustao%')
        AND proj.project_name NOT LIKE ('%dbijen%')
        AND proj.project_name NOT LIKE ('%razgovor%')
        AND proj.project_name NOT LIKE ('%Nedostupan%')
        AND proj.project_name NOT LIKE ('%Ne ispunjava uslove za nalog%')
        AND (kan.kandidat_nalog_id IN ( $nalozi ) OR kan.kandidat_nalog_id IS NULL)
        AND kan.kandidat_tf_status IS NULL
        AND kan.kandidat_drzavljanstvo_vrsta='EU državljanin'
        AND (kan.kandidat_status_prijave IN (0,1,2,5,6) OR kan.kandidat_status_prijave is null)
        AND kan.kandidat_id NOT IN (SELECT tfr.tf_kandidat_id FROM idk_tf_reservations tfr)
        AND kan.kandidat_mobitel IS NOT NULL
        AND kan.kandidat_pogresan_broj = 0
        AND kan.kandidat_nedostupan = 0
        $sql_dvag_query
    GROUP BY kan.kandidat_id
    ORDER BY nalozi.nalog_prioritet ASC, idk_jobstep_partners.jp_partner_company DESC
    LIMIT 1;
    ");

    $query->execute();

    if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_NUM);
        return $row;
    } else {
        return [null, null, null, null, null, null];
    }
}

/**
 * Returns a TF candidate
 * @param int $tfStatus TF status id
 * @param array $nalozi Array of nalog ids
 * @return array Array of candidate id, project id and nalog id
 */
function getTFCandidateByTFStatus(int $tfStatus, array $nalozi): ?array
{
    global $db;
    Global $logged_employee_id;
    $nalozi = implode(',', $nalozi);
    $status_vrsta = getStatusVrsta($tfStatus);
    $vrsta_category = getVrstaCategory($status_vrsta);

    $isAgentDvag = isAgentDvag();

    if($isAgentDvag == 1){
        $sql_dvag_query = "";
    }else{
        $sql_dvag_query = " AND jp_id IS NULL";
    }

    if($status_vrsta != null){
        $query_primary = $db->prepare("SELECT
            tf_candidate_id,
            tf_project_id,
            tf_nalog_id,
            tf_vrsta_id AS vrsta_id,
            null AS dipl_id,
            $vrsta_category
        FROM
            idk_task_force tf
        JOIN idk_tf_statusi tfs ON
            tf.tf_status_id = tfs.tfs_id
        JOIN(
            SELECT
                nal.nalog_id,
                nal.nalog_prioritet
            FROM
                idk_nalozi nal
            WHERE
                nal.nalog_prioritet IS NOT NULL
        ) AS nalozi
        ON
            tf.tf_nalog_id = nalozi.nalog_id
        JOIN idk_kandidati ON
            kandidat_id = tf_candidate_id
        LEFT JOIN idk_jobstep_partners ON idk_kandidati.kandidat_partnerid = idk_jobstep_partners.jp_id AND idk_jobstep_partners.jp_partner_company = 3
        WHERE
            (tf.tf_call_appointment<CURRENT_TIMESTAMP OR (tf.tf_call_appointment IS NULL AND tf.tf_status_id IN (24, 43, 44, 45, 46, 47, 140, 141, 142, 143, 144, 145, 146)))
            AND tf.tf_last_active_task = 1 AND tf.tf_candidate_id NOT IN(
            SELECT
                tfr.tf_kandidat_id
            FROM
                idk_tf_reservations tfr
        )
        AND tf.tf_status_id=:tf_status_id
        AND tf_reserved_agent = :tf_reserved_agent
        AND idk_kandidati.kandidat_pogresan_broj = 0
        AND idk_kandidati.kandidat_nedostupan = 0
        $sql_dvag_query
        ORDER BY
            nalozi.nalog_prioritet ASC, tf.tf_call_appointment ASC, idk_jobstep_partners.jp_partner_company DESC 
        LIMIT 1     
        ");

        $query_primary->execute(array(
            ':tf_status_id' => $tfStatus,
            ':tf_reserved_agent' => $logged_employee_id
        ));

        if ($query_primary->rowCount() > 0) {
            $row_primary = $query_primary->fetch(PDO::FETCH_NUM);
            return $row_primary;
        } else {

            $query = $db->prepare("SELECT
                tf_candidate_id,
                tf_project_id,
                tf_nalog_id,
                tf_vrsta_id AS vrsta_id,
                null AS dipl_id,
                $vrsta_category
            FROM
                idk_task_force tf
            JOIN idk_tf_statusi tfs ON
                tf.tf_status_id = tfs.tfs_id
            JOIN(
                SELECT
                    nal.nalog_id,
                    nal.nalog_prioritet
                FROM
                    idk_nalozi nal
                WHERE
                    nal.nalog_prioritet IS NOT NULL
            ) AS nalozi
            ON
                tf.tf_nalog_id = nalozi.nalog_id
            JOIN idk_kandidati ON
                kandidat_id = tf_candidate_id
            JOIN idk_tf_agent_nalog ON tf.tf_nalog_id  = idk_tf_agent_nalog.tfan_nalog AND tfan_agent = $logged_employee_id AND tfan_vrsta_id = $status_vrsta 
            LEFT JOIN idk_jobstep_partners ON idk_kandidati.kandidat_partnerid = idk_jobstep_partners.jp_id AND idk_jobstep_partners.jp_partner_company = 3
            WHERE
                tf_nalog_id IN ($nalozi)
                AND (tf.tf_call_appointment<CURRENT_TIMESTAMP OR (tf.tf_call_appointment IS NULL AND tf.tf_status_id IN (24, 43, 44, 45, 46, 47, 140, 141, 142, 143, 144, 145, 146)))
                AND tf.tf_last_active_task = 1 AND tf.tf_candidate_id NOT IN(
                SELECT
                    tfr.tf_kandidat_id
                FROM
                    idk_tf_reservations tfr
            )
            AND tf.tf_status_id=:tf_status_id
            AND tf_reserved_agent is null
            AND idk_kandidati.kandidat_pogresan_broj = 0
            AND idk_kandidati.kandidat_nedostupan = 0
            $sql_dvag_query
            ORDER BY
                nalozi.nalog_prioritet ASC, tf.tf_call_appointment ASC, idk_jobstep_partners.jp_partner_company DESC
            LIMIT 1     
            ");
            $query->execute([':tf_status_id' => $tfStatus]);

            if ($query->rowCount() > 0) {
                $row = $query->fetch(PDO::FETCH_NUM);
                return $row;
            } else {
                return [null, null, null, null, null, null];
            }
        }
    }
}

/**
 * Returns a candidate from projekt rezervisani
 * @param array $nalozi Array of nalog ids
 * @param int $statusPrijave ID
 * @param int $day Day after status change
 * @param int $statusVrsta ID
 * @return array Array of candidate id, project id and nalog id
 */
function getTFCandidateByStatusPrijave(array $nalozi, int $statusPrijave, int $dayTrigger, int $statusVrsta): array
{
    global $db;
    Global $logged_employee_id;

    $isAgentDvag = isAgentDvag();

    if($isAgentDvag == 1){
        $sql_dvag_query = "";
    }else{
        $sql_dvag_query = " AND jp_id IS NULL";
    }

    if($statusVrsta == 9){
        $task_force_agent_connection = "(tfan_vrsta_id = 9 OR tfan_vrsta_id = 10)";
        $task_force_candidate_connection = "(idk_task_force.tf_vrsta_id = 9 OR idk_task_force.tf_vrsta_id = 10)";
    }else{
        $task_force_agent_connection = "tfan_vrsta_id = $statusVrsta";
        $task_force_candidate_connection = "idk_task_force.tf_vrsta_id = $statusVrsta";
    }

    $nalozi = implode(',', $nalozi);

    $query = $db->prepare("SELECT
                        kandidat_id,
                        lsp_projekt_id,
                        project_nalogid,
                        CASE 
                            WHEN kandidat_status_prijave = 5 THEN 7 /* Posredovanje-Odbijen (Status prijave Odbijen)*/
            				WHEN kandidat_status_prijave = 7 THEN 8 /* Posredovanje-Ceka ugovor (Status prijave Ceka ugovor)*/
            				WHEN kandidat_status_prijave = 8 AND DATE_FORMAT(lsp_datetime, '%Y-%M-%d') < DATE(NOW() - INTERVAL 1 DAY) AND DATE_FORMAT(lsp_datetime, '%Y-%M-%d') > DATE(NOW() - INTERVAL 7 DAY) THEN 9 /* Posredovanje-Poslan ugovor (Status prijave Poslan ugovor poslije 1 dan)*/
            				WHEN kandidat_status_prijave = 8 AND DATE_FORMAT(lsp_datetime, '%Y-%M-%d') <= DATE(NOW() - INTERVAL 7 DAY) THEN 10 /* Posredovanje-Time Up za Potpisan ugovor (Status prijave Poslan ugovor poslije 7 dana)*/
            				WHEN kandidat_status_prijave = 9 THEN 11 /* Posredovanje-Potpisan Ugovor (Status prijave Potpisan ugovor)*/
       					END AS vrsta_id,
                        null AS dipl_id,
                        2 AS category_id
    FROM
        `idk_kandidati`
    INNER JOIN `idk_log_statusi_prijave` ON `idk_kandidati`.`kandidat_id` = `idk_log_statusi_prijave`.`lsp_kandidat_id` AND idk_log_statusi_prijave.lsp_status_prijave_id = $statusPrijave AND idk_log_statusi_prijave.lsp_broj_dana IS NULL
    INNER JOIN idk_projects ON idk_log_statusi_prijave.lsp_projekt_id = idk_projects.project_id AND idk_projects.project_nalogid IN ($nalozi)
    INNER JOIN idk_nalozi ON idk_projects.project_nalogid = idk_nalozi.nalog_id
    INNER JOIN idk_tf_agent_nalog ON idk_projects.project_nalogid  = idk_tf_agent_nalog.tfan_nalog AND tfan_agent = $logged_employee_id AND $task_force_agent_connection
    LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND $task_force_candidate_connection AND idk_projects.project_nalogid = idk_task_force.tf_nalog_id 
    LEFT JOIN idk_jobstep_partners ON idk_kandidati.kandidat_partnerid = idk_jobstep_partners.jp_id AND idk_jobstep_partners.jp_partner_company = 3
    WHERE
        `kandidat_status_prijave` = $statusPrijave 
        AND DATE(`lsp_datetime`) <= DATE(NOW() - INTERVAL $dayTrigger DAY) 
        AND (kandidat_nalog_id IN ($nalozi) OR kandidat_nalog_id IS NULL)
        AND idk_task_force.tf_candidate_id IS NULL
        AND idk_kandidati.kandidat_id NOT IN(SELECT tf_kandidat_id FROM idk_tf_reservations)
        AND idk_kandidati.kandidat_pogresan_broj = 0
        AND idk_kandidati.kandidat_nedostupan = 0
        $sql_dvag_query
    GROUP BY
        kandidat_id
    ORDER BY idk_nalozi.nalog_prioritet ASC, idk_jobstep_partners.jp_partner_company DESC
    LIMIT 1
    ");

    $query->execute();

    if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_NUM);
        return $row;
    } else {
        return [null, null, null, null, null, null];
    }
}

/**
 * Returns a candidate accordin to language levels and statuses
 * @param array $nalozi Array of nalog ids
 * @param string $statusPrijave IDs
 * @param int $day Day after status change
 * @param int $statusVrsta ID
 * @param string $language level string A1,A2
 * @param int $language status (cvl status)
 * @return array Array of candidate id, project id and nalog id
 */
function getTFCandidateByLanguage(array $nalozi, string $statusPrijave, int $dayTrigger, int $statusVrsta, string $languageLevel, int $languageStatus): array
{
    global $db;
    Global $logged_employee_id;

    $isAgentDvag = isAgentDvag();

    if($isAgentDvag == 1){
        $sql_dvag_query = "";
    }else{
        $sql_dvag_query = " AND jp_id IS NULL";
    }

    if($languageStatus == 3){
        $date_query = "AND DATE(`cvl_course1_ended`) <= DATE(NOW() - INTERVAL $dayTrigger DAY)";
    }else if($languageStatus == 4){
        $date_query = "AND DATE(`cvl_course2_ended`) <= DATE(NOW() - INTERVAL $dayTrigger DAY)";
    }else if($languageStatus == 6){
        $date_query = "AND DATE(`cvl_exam_date`) <= DATE(NOW() - INTERVAL $dayTrigger DAY) AND cvl_exam_date IS NOT NULL";
    }else if($languageStatus == 7){
        $date_query = "AND DATE(`cvl_last_updated`) <= DATE(NOW() - INTERVAL $dayTrigger DAY) AND cvl_exam_date IS NOT NULL";
    }

    $nalozi = implode(',', $nalozi);

    $query = $db->prepare("SELECT
                        kandidat_id,
                        lsp_projekt_id,
                        idk_projects.project_nalogid,
                        $statusVrsta AS vrsta_id,
                        null AS dipl_id,
                        2 AS category_id
    FROM
        `idk_kandidati`
    INNER JOIN `idk_log_statusi_prijave` ON `idk_kandidati`.`kandidat_id` = `idk_log_statusi_prijave`.`lsp_kandidat_id` AND idk_log_statusi_prijave.lsp_status_prijave_id IN ($statusPrijave) AND idk_log_statusi_prijave.lsp_broj_dana IS NULL
    INNER JOIN idk_projects ON idk_log_statusi_prijave.lsp_projekt_id = idk_projects.project_id AND idk_projects.project_nalogid IN ($nalozi)
    INNER JOIN idk_nalozi ON idk_projects.project_nalogid = idk_nalozi.nalog_id
    INNER JOIN idk_kandidat_jezici ON idk_kandidati.kandidat_id = idk_kandidat_jezici.kj_kandidatid AND kj_naziv = 'Njemacki' AND kj_slusanje = '$languageLevel' AND kj_ustanova IN (1,2,3,4)
    INNER JOIN idk_candidate_verified_languages ON idk_kandidat_jezici.kj_id = idk_candidate_verified_languages.cvl_id AND idk_candidate_verified_languages.cvl_active = 1 AND idk_candidate_verified_languages.cvl_status = $languageStatus
    INNER JOIN idk_tf_agent_nalog ON idk_projects.project_nalogid  = idk_tf_agent_nalog.tfan_nalog AND tfan_agent = $logged_employee_id AND tfan_vrsta_id = $statusVrsta
    LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = $statusVrsta AND idk_projects.project_nalogid = idk_task_force.tf_nalog_id 
    LEFT JOIN idk_jobstep_partners ON idk_kandidati.kandidat_partnerid = idk_jobstep_partners.jp_id AND idk_jobstep_partners.jp_partner_company = 3
    WHERE
        `kandidat_status_prijave` IN ($statusPrijave)
        $date_query
        AND (kandidat_nalog_id IN ($nalozi) OR kandidat_nalog_id IS NULL)
        AND idk_task_force.tf_candidate_id IS NULL
        AND idk_kandidati.kandidat_id NOT IN(SELECT tf_kandidat_id FROM idk_tf_reservations)
        AND idk_kandidati.kandidat_pogresan_broj = 0
        AND idk_kandidati.kandidat_nedostupan = 0
        $sql_dvag_query
    GROUP BY
        kandidat_id
    ORDER BY idk_nalozi.nalog_prioritet ASC, idk_jobstep_partners.jp_partner_company DESC
    LIMIT 1
    ");

    $query->execute();

    if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_NUM);
        return $row;
    } else {
        return [null, null, null, null, null, null];
    }
}

/**
 * Returns a candidate for West Balkan application
 * @param array $nalozi Array of nalog ids
 * @param string $statusPrijave IDs
 * @param int $day Day after status change
 * @param int $statusVrsta ID
 * @return array Array of candidate id, project id and nalog id
 */
function getTFCandidateWestBalkan(array $nalozi, int $statusPrijave, int $statusVrsta): array
{
    global $db;
    Global $logged_employee_id;

    $isAgentDvag = isAgentDvag();

    if($isAgentDvag == 1){
        $sql_dvag_query = "";
    }else{
        $sql_dvag_query = " AND jp_id IS NULL";
    }

    $nalozi = implode(',', $nalozi);

    $query = $db->prepare("SELECT
                        kandidat_id,
                        lsp_projekt_id,
                        idk_projects.project_nalogid,
                        $statusVrsta AS vrsta_id,
                        null AS dipl_id,
                        2 AS category_id
    FROM
        `idk_kandidati`
    INNER JOIN `idk_log_statusi_prijave` ON `idk_kandidati`.`kandidat_id` = `idk_log_statusi_prijave`.`lsp_kandidat_id` AND idk_log_statusi_prijave.lsp_status_prijave_id = 9 AND idk_log_statusi_prijave.lsp_broj_dana IS NULL
    INNER JOIN idk_projects ON idk_log_statusi_prijave.lsp_projekt_id = idk_projects.project_id AND idk_projects.project_nalogid IN ($nalozi)
    INNER JOIN idk_nalozi ON idk_projects.project_nalogid = idk_nalozi.nalog_id
    INNER JOIN idk_tf_agent_nalog ON idk_projects.project_nalogid  = idk_tf_agent_nalog.tfan_nalog AND tfan_agent = $logged_employee_id AND tfan_vrsta_id = $statusVrsta
    LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = $statusVrsta AND (idk_task_force.tf_status_id = 139 OR (YEAR(idk_task_force.tf_doe) = YEAR(CURRENT_DATE) AND MONTH(idk_task_force.tf_doe) = MONTH(CURRENT_DATE))) AND idk_projects.project_nalogid = idk_task_force.tf_nalog_id 
    LEFT JOIN idk_jobstep_partners ON idk_kandidati.kandidat_partnerid = idk_jobstep_partners.jp_id AND idk_jobstep_partners.jp_partner_company = 3
    WHERE
        `kandidat_status_prijave` = $statusPrijave
        AND ((kandidat_nacin_odlaska = 2 AND datum_termina IS NULL) OR (kandidat_nacin_odlaska = 0 AND kandidat_paralelno_zb = 1))
        AND kandidat_drzavljanstvo_vrsta != 'EU državljanin'
        AND (kandidat_nalog_id IN ($nalozi) OR kandidat_nalog_id IS NULL)
        AND idk_task_force.tf_candidate_id IS NULL
        AND idk_kandidati.kandidat_id NOT IN(SELECT tf_kandidat_id FROM idk_tf_reservations)
        AND idk_kandidati.kandidat_pogresan_broj = 0
        AND idk_kandidati.kandidat_nedostupan = 0
        $sql_dvag_query
    GROUP BY
        kandidat_id
    ORDER BY idk_nalozi.nalog_prioritet ASC, idk_jobstep_partners.jp_partner_company DESC
    LIMIT 1
    ");

    $query->execute();

    if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_NUM);
        return $row;
    } else {
        return [null, null, null, null, null, null];
    }
}

/**
 * Returns a candidate from projekt rezervisani
 * @param int $status
 * @param int $podstatus
 * @param int $dayTrigger Day after status change
 * @param int $statusVrsta ID
 * @return array Array of candidate id, project id and nalog id
 */
function getDIPLCandidateByStatus(int $status, int $podstatus, int $dayTrigger, int $statusVrsta): array
{
    global $db;
    Global $logged_employee_id;

    $hasObradaVrsta = hasObradaVrsta($statusVrsta);

    if($statusVrsta == 14){
        $dodatni_uslov_join = " JOIN idk_task_force tf2 ON tf2.tf_candidate_id = `idk_kandidati`.kandidat_id AND tf2.tf_vrsta_id = 13 AND tf2.tf_status_id = 50";
    }else{
        $dodatni_uslov_join = "";
    }

    if($statusVrsta == 16){
        $dodatni_where = " AND (idk_nd_kandidata.idd_ustanova_nd = 75 OR idk_nd_kandidata.idd_ustanova_nd = 69 OR idk_nd_ustanove.naziv_ustanove_nd LIKE '%Handwerkskammer%') ";
    }elseif($statusVrsta == 18){
        if($podstatus == 5){
            $dodatni_where = " AND idk_nd_kandidata.idd_ustanova_nd = 1 ";
        }else{
            $dodatni_where = "";
        }
    }else{
        $dodatni_where = "";
    }

    if($statusVrsta == 26){
        $dodati_where_zavrsen = " AND vrijeme_promjene_statusa_nd_kandidata > '2023-11-20'";
    }else{
        $dodati_where_zavrsen = "";
    }

    
    if($hasObradaVrsta){
        $query = $db->prepare("SELECT
                kandidat_id,
                0 AS lsp_projekt_id,
                0 AS project_nalogid,
                $statusVrsta AS vrsta_id,
                kandidat_dipl_id AS dipl_id,
                3 AS category_id
            FROM
            `idk_kandidati`
            INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
            INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.status_nd_kandidata = $status AND idk_nd_kandidata_status_log.pstatus_nd_kandidata = $podstatus AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL $dodati_where_zavrsen
            LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = $statusVrsta
            LEFT JOIN idk_nd_ustanove ON idk_nd_kandidata.idd_ustanova_nd = idk_nd_ustanove.id_ustanove_nd
            $dodatni_uslov_join
            WHERE
            idk_nd_kandidata.status_nd_kandidata = $status AND idk_nd_kandidata.pstatus_nd_kandidata = $podstatus
            AND DATE(`vrijeme_promjene_statusa_nd_kandidata`) <= DATE(NOW() - INTERVAL $dayTrigger DAY) 
            AND idk_task_force.tf_candidate_id IS NULL
            AND idk_kandidati.kandidat_tf_status IS NULL
            AND idk_kandidati.kandidat_id NOT IN(SELECT tf_kandidat_id FROM idk_tf_reservations)
            AND idk_kandidati.kandidat_pogresan_broj = 0
            AND idk_kandidati.kandidat_nedostupan = 0
            $dodatni_where
            GROUP BY
            kandidat_id
            ORDER BY idk_kandidati.kandidat_id ASC
            LIMIT 1
        ");

        $query->execute();

        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_NUM);
            return $row;
        } else {
         return [null, null, null, null, null, null];
        }
    }else{
        return [null, null, null, null, null, null];
    }
    
}

/**
 * Returns a candidate for task Prebaciti na prikupljanje 100%
 * @param int $status
 * @param int $podstatus
 * @param int $dayTrigger Day after status change
 * @param int $statusVrsta ID
 * @return array Array of candidate id, project id and nalog id
 */
function getDIPLCandidateForPrebacitiNaPrikupljanje(int $statusVrsta): array
{
    global $db;
    Global $logged_employee_id;

    $hasObradaVrsta = hasObradaVrsta($statusVrsta);
    
    if($hasObradaVrsta){
        $query = $db->prepare("SELECT
                kandidat_id,
                0 AS lsp_projekt_id,
                0 AS project_nalogid,
                12 AS vrsta_id,
                kandidat_dipl_id AS dipl_id,
                3 AS category_id
            FROM
            `idk_kandidati`
            INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
			INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
			INNER JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 11 AND idk_task_force.tf_status_id IN (40, 41)
            WHERE
                idk_kandidati.kandidat_status_prijave = 9
                AND idk_nd_kandidata.status_nd_kandidata IN (1,7)
                AND idk_kandidati.kandidat_dipl_id IS NOT NULL
                AND idk_kandidati.kandidat_nacin_odlaska = 0
                AND idk_kandidati.kandidat_drzavljanstvo_vrsta NOT LIKE  ('EU državljanin')
                AND idk_kandidati.kandidat_id NOT IN(SELECT tf_kandidat_id FROM idk_tf_reservations)
                AND idk_kandidati.kandidat_pogresan_broj = 0
                AND idk_kandidati.kandidat_nedostupan = 0
            GROUP BY
            kandidat_id
            ORDER BY idk_kandidati.kandidat_id ASC
            LIMIT 1
        ");

        $query->execute();

        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_NUM);
            return $row;
        } else {
         return [null, null, null, null, null, null];
        }
    }else{
        return [null, null, null, null, null, null];
    }
    
}

/**
 * Returns a TF candidate
 * @param int $tfStatus TF status id
 * @return array Array of candidate id, project id and nalog id
 */
function getDIPLCandidateByTFStatus(int $tfStatus): ?array
{
    global $db;
    Global $logged_employee_id;
    $status_vrsta = getStatusVrsta($tfStatus);

    if($status_vrsta != null){

        $query = $db->prepare("SELECT
            tf_candidate_id,
            0,
            0,
            tf_vrsta_id AS vrsta_id,
            kandidat_dipl_id AS dipl_id,
            3 AS category_id
        FROM
            idk_task_force tf
        JOIN idk_tf_statusi tfs ON
            tf.tf_status_id = tfs.tfs_id
        JOIN idk_tf_vrste tfv ON
        	tfs.tfs_vrsta_id = tfv.id
        JOIN idk_kandidati ON
            kandidat_id = tf_candidate_id
        JOIN idk_tf_agent_nalog ON 
            tf.tf_vrsta_id = idk_tf_agent_nalog.tfan_vrsta_id AND idk_tf_agent_nalog.tfan_agent = $logged_employee_id
        WHERE
            (tf.tf_call_appointment<CURRENT_TIMESTAMP OR (tf.tf_call_appointment IS NULL AND tf.tf_status_id IN (69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 88, 89, 90, 91) ))
            AND tf.tf_last_active_task = 1 AND tf.tf_candidate_id NOT IN(
            SELECT
                tfr.tf_kandidat_id
            FROM
                idk_tf_reservations tfr
        )
        AND tf.tf_status_id=:tf_status_id
        AND tf_reserved_agent is null
        AND tfv.category = 3
        AND idk_kandidati.kandidat_pogresan_broj = 0
        AND idk_kandidati.kandidat_nedostupan = 0
        ORDER BY
            tf.tf_call_appointment ASC  
        LIMIT 1 
        ");
        $query->execute([':tf_status_id' => $tfStatus]);

        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_NUM);
            return $row;
        } else {
            return [null, null, null, null, null, null];
        }
    }
}

/**
 * Reserves a candidate for the agent
 * @param int $agentId ID of the agent
 * @param int $candidateId ID of the candidate
 * @param int $projektId ID of the project
 * @param int $nalogId ID of the nalog
 * @param $diplId ID of the dipl
 * @return void
 * @throws PDOException
 */
function reserveCandidate(int $agentId, int $candidateId, int $projektId, int $nalogId, int $vrstaId, $diplId, int $categoryId): void
{
    global $db;
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $query = $db->prepare("INSERT INTO idk_tf_reservations (tf_kandidat_id, tf_agent_id, tf_projekt_id, tf_nalog_id, tf_vrsta_id, tf_dipl_id, tf_category_id) VALUES (:candidate_id, :agent_id, :projekt_id, :nalog_id, :vrsta_id, :tf_dipl_id, :tf_category_id)");
    $query->execute([':agent_id' => $agentId, ':candidate_id' => $candidateId, ':projekt_id' => $projektId, ':nalog_id' => $nalogId, ':vrsta_id' => $vrstaId, ':tf_dipl_id' => $diplId, ':tf_category_id' => $categoryId]);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
}

/**
 * Returns ID of the candidate reserved for the agent
 * @param int $agentId ID of the agent
 * @return ?int ID of the candidate or null if no candidate reserved
 */
function getReservedCandidate(int $agentId): array
{
    global $db;
    $query = $db->prepare("SELECT tf_kandidat_id, tf_projekt_id, tf_nalog_id, tf_vrsta_id, tf_dipl_id, tf_category_id FROM idk_tf_reservations WHERE tf_agent_id = :agent_id");
    $query->execute([':agent_id' => $agentId]);

    if ($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_NUM);
        return $row;
    } else {
        return [null, null, null, null, null, null];
    }
}

/**
 * Returns status vrsta
 * @param int $tf_status_id
 * @return ?int status vrsta
 */
function getStatusVrsta(int $tf_status_id): int
{
    global $db;
    $query = $db->prepare("SELECT tfs_vrsta_id FROM idk_tf_statusi WHERE tfs_id  = :tfs_id");
    $query->execute([':tfs_id' => $tf_status_id]);

    if ($query->rowCount() > 0) {
        $row = $query->fetch();
        return $row["tfs_vrsta_id"];
    } else {
        return null;
    }
}

/**
 * Returns vrsta category
 * @param int $tf_vrsta_id
 * @return ?int status category
 */
function getVrstaCategory(int $tf_vrsta_id): int
{
    global $db;
    $query = $db->prepare("SELECT category FROM idk_tf_vrste WHERE id = :id");
    $query->execute([':id' => $tf_vrsta_id]);

    if ($query->rowCount() > 0) {
        $row = $query->fetch();
        return $row["category"];
    } else {
        return null;
    }
}

/**
 * Returns true if employee has this task for obrada checked
 * @param int $vrsta_id
 * @return ?bool status vrsta
 */
function hasObradaVrsta(int $vrsta_id)
{
    global $db;
    Global $logged_employee_id;

    $query = $db->prepare("SELECT tfan_id FROM idk_tf_agent_nalog WHERE tfan_vrsta_id  = :tfan_vrsta_id AND tfan_agent = $logged_employee_id");
    $query->execute([':tfan_vrsta_id' => $vrsta_id]);

    if ($query->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Returns info if agent calls dvag
 * @return ?int
 */
function isAgentDvag(): int
{
    global $db;
    Global $logged_employee_id;

    $query = $db->prepare("SELECT employee_dvag FROM idk_employees WHERE employee_id = :employee_id");
    $query->execute([':employee_id' => $logged_employee_id]);

    if ($query->rowCount() > 0) {
        $row = $query->fetch();
        return $row["employee_dvag"];
    } else {
        return 0;
    }
}