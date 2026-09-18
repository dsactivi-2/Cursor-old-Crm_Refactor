<?php
namespace modules\candidates;
use modules\database\Database;
use modules\user\UserService;

class CandidatesService{
    public function __construct(Database $database, UserService $userService)
    {
        $this->conn = $database->getConnection();
        $this->userService = $userService;
    }

    public function getCandidateProfile($candidate_id, $lang){
        $sql = 'SELECT CASE
                    WHEN idk_kandidati.kandidat_status_prijave IN(10,4) THEN "Pocetak rada"
                    WHEN idk_kandidati.kandidat_status_prijave IN(9) THEN "Potpisan ugovor"
                    WHEN idk_kandidati.kandidat_status_prijave IN(8) THEN "Poslan ugovor"
                    WHEN idk_kandidati.kandidat_status_prijave IN(5) THEN "Odbijen"
                    WHEN idk_kandidati.kandidat_status_prijave IN(3) THEN "Casting"
                    WHEN idk_kandidati.kandidat_status_prijave IN(2,6) THEN "Prijava"
                    WHEN idk_kandidati.kandidat_status_prijave IN(7) THEN "Prihvacen"
                    WHEN idk_kandidati.kandidat_status_prijave IN(12,15,18,21,24,27) THEN "Ceka pocetak rada"
                END AS status_prijave,idk_kandidati.kandidat_id,idk_kandidati.kandidat_ime,idk_kandidati.kandidat_prezime,no_nalognaziv,idk_nalozi.nalog_kreirano FROM
                    idk_kandidati
                    LEFT JOIN idk_nalozi_opis ON idk_kandidati.kandidat_nalog_id = idk_nalozi_opis.no_nalogid
                    LEFT JOIN idk_nalozi ON idk_kandidati.kandidat_nalog_id = idk_nalozi.nalog_id
                WHERE
                    idk_kandidati.kandidat_id = :kandidat_id
                AND
                    idk_nalozi_opis.no_lang = :lang
                GROUP BY idk_kandidati.kandidat_id;
                ';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':kandidat_id', $candidate_id);
        $stmt->bindParam(':lang', $lang);
        $stmt->execute();
        $result = $stmt->fetch();

        if($result != null){
            $data = array();
            $data['id'] = $result['kandidat_id'];
            $data['first_name'] = $result['kandidat_ime'];
            $data['last_name'] = $result['kandidat_prezime'];
            $data['job_title'] = $result['no_nalognaziv'];
            $data['status'] = $result['status_prijave'];
            $data['order_created_at'] = $result['nalog_kreirano'];
            return $data;
        } else {
            http_response_code(404);
            die("Candidate not found.");
        }
    }

    public function getCandidateProfileV2($candidate_id, $lang){

        $sql = 'SELECT CASE
                    WHEN idk_kandidati.kandidat_status_prijave IN(10,4) THEN "Pocetak rada"
                    WHEN idk_kandidati.kandidat_status_prijave IN(9) THEN "Potpisan ugovor"
                    WHEN idk_kandidati.kandidat_status_prijave IN(8) THEN "Poslan ugovor"
                    WHEN idk_kandidati.kandidat_status_prijave IN(5) THEN "Odbijen"
                    WHEN idk_kandidati.kandidat_status_prijave IN(3) THEN "Casting"
                    WHEN idk_kandidati.kandidat_status_prijave IN(2,6) THEN "Prijava"
                    WHEN idk_kandidati.kandidat_status_prijave IN(7) THEN "Prihvacen"
                    WHEN idk_kandidati.kandidat_status_prijave IN(12,15,18,21,24,27) THEN "Ceka pocetak rada"
                END AS status_prijave,idk_kandidati.kandidat_id,idk_kandidati.kandidat_ime,idk_kandidati.kandidat_prezime,no_nalognaziv,idk_nalozi.nalog_kreirano FROM
                    idk_kandidati
                    LEFT JOIN idk_nalozi_opis ON idk_kandidati.kandidat_nalog_id = idk_nalozi_opis.no_nalogid
                    LEFT JOIN idk_nalozi ON idk_kandidati.kandidat_nalog_id = idk_nalozi.nalog_id
                WHERE
                    idk_kandidati.kandidat_id = :kandidat_id
                AND
                    ((idk_nalozi_opis.no_lang = :lang AND idk_kandidati.kandidat_nalog_id IS NOT NULL) OR idk_kandidati.kandidat_nalog_id IS NULL)
                GROUP BY idk_kandidati.kandidat_id;
                ';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':kandidat_id', $candidate_id);
        $stmt->bindParam(':lang', $lang);
        $stmt->execute();
        $result = $stmt->fetch();

        if($result != null){
            $data = array();
            $data['id'] = $result['kandidat_id'];
            $data['first_name'] = $result['kandidat_ime'];
            $data['last_name'] = $result['kandidat_prezime'];
            $data['job_title'] = $result['no_nalognaziv'];
            $data['status'] = $result['status_prijave'];
            $data['order_created_at'] = $result['nalog_kreirano'];
            return $data;
        } else {
            http_response_code(404);
            die("Candidate not found.");
        }
    }


    public function getCandidateAnalytics(){
        $user_id = $this->userService->getUserIdByToken();

        $sql = 'SELECT CASE
                    WHEN idk_kandidati.kandidat_status_prijave IN(10,4) THEN "Pocetak rada"
                    WHEN idk_kandidati.kandidat_status_prijave IN(9) THEN "Potpisan ugovor"
                    WHEN idk_kandidati.kandidat_status_prijave IN(8) THEN "Poslan ugovor"
                    WHEN idk_kandidati.kandidat_status_prijave IN(5) THEN "Odbijen"
                    WHEN idk_kandidati.kandidat_status_prijave IN(3) THEN "Casting"
                    WHEN idk_kandidati.kandidat_status_prijave IN(2,6) THEN "Prijava"
                    WHEN idk_kandidati.kandidat_status_prijave IN(7) THEN "Prihvacen"
                    WHEN idk_kandidati.kandidat_status_prijave IN(12,15,18,21,24,27) THEN "Ceka pocetak rada"
                END AS status_prijave,COUNT(idk_kandidati.kandidat_id) as "Broj kandidata" FROM
                    idk_kandidati
                WHERE
                    idk_kandidati.kandidat_partnerid = :partner_id  AND idk_kandidati.kandidat_nalog_id IS NOT NULL
                GROUP BY
                status_prijave
                ';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':partner_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $data = [];
        foreach($result as $item){
            $status = $item['status_prijave'];
            $broj_kandidata = $item['Broj kandidata'];

            if ($status === "Casting") {
                $data["Casting"] = $broj_kandidata;
            } elseif ($status === "Odbijen") {
                $data["Odbijen"] = $broj_kandidata;
            } elseif ($status === "Prijava") {
                $data["Prijava"] = $broj_kandidata;
            } elseif ($status === "Prihvacen") {
                $data["Prihvacen"] = $broj_kandidata;
            } elseif ($status === "Poslan ugovor") {
                $data["Poslan ugovor"] = $broj_kandidata;
            } elseif ($status === "Potpisan ugovor") {
                $data["Potpisan ugovor"] = $broj_kandidata;
            } elseif ($status === "Ceka pocetak rada") {
                $data["Ceka pocetak rada"] = $broj_kandidata;
            } elseif ($status === "Pocetak rada") {
                $data["Pocetak rada"] = $broj_kandidata;
            }
        }

        if($data == []){
            $data = (object) [];
        }

        return $data;
    }

    public function getCandidateAnalyticsV2(){
        $user_id = $this->userService->getUserIdByToken();

        $sql = 'SELECT CASE
                    WHEN idk_kandidati.kandidat_status_prijave IN(10,4) THEN "Pocetak rada"
                    WHEN idk_kandidati.kandidat_status_prijave IN(9) THEN "Potpisan ugovor"
                    WHEN idk_kandidati.kandidat_status_prijave IN(8) THEN "Poslan ugovor"
                    WHEN idk_kandidati.kandidat_status_prijave IN(5) THEN "Odbijen"
                    WHEN idk_kandidati.kandidat_status_prijave IN(3) THEN "Casting"
                    WHEN idk_kandidati.kandidat_status_prijave IN(2,6) THEN "Prijava"
                    WHEN idk_kandidati.kandidat_status_prijave IN(7) THEN "Prihvacen"
                    WHEN idk_kandidati.kandidat_status_prijave IN(12,15,18,21,24,27) THEN "Ceka pocetak rada"
                END AS status_prijave,COUNT(idk_kandidati.kandidat_id) as "Broj kandidata" FROM
                    idk_kandidati
                WHERE
                    idk_kandidati.assigned_to_makler = :partner_id
                GROUP BY
                status_prijave
                ';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':partner_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $data = [];
        foreach($result as $item){
            $status = $item['status_prijave'];
            $broj_kandidata = $item['Broj kandidata'];

            if ($status === "Casting") {
                $data["Casting"] = $broj_kandidata;
            } elseif ($status === "Odbijen") {
                $data["Odbijen"] = $broj_kandidata;
            } elseif ($status === "Prijava") {
                $data["Prijava"] = $broj_kandidata;
            } elseif ($status === "Prihvacen") {
                $data["Prihvacen"] = $broj_kandidata;
            } elseif ($status === "Poslan ugovor") {
                $data["Poslan ugovor"] = $broj_kandidata;
            } elseif ($status === "Potpisan ugovor") {
                $data["Potpisan ugovor"] = $broj_kandidata;
            } elseif ($status === "Ceka pocetak rada") {
                $data["Ceka pocetak rada"] = $broj_kandidata;
            } elseif ($status === "Pocetak rada") {
                $data["Pocetak rada"] = $broj_kandidata;
            }
        }

        if($data == []){
            $data = (object) [];
        }

        return $data;
    }

    public function getCandidateStatuses(){

        $sql = 'SELECT CASE WHEN idk_kandidat_status_prijave.status_id IN (2,6) THEN "1"
                    WHEN idk_kandidat_status_prijave.status_id IN (3) THEN "2"
                    WHEN idk_kandidat_status_prijave.status_id IN (5) THEN "3"
                    WHEN idk_kandidat_status_prijave.status_id IN (7) THEN "4"
                    WHEN idk_kandidat_status_prijave.status_id IN (8) THEN "5"
                    WHEN idk_kandidat_status_prijave.status_id IN (9) THEN "6"
                    WHEN idk_kandidat_status_prijave.status_id IN (12,15,18,21,24,27) THEN "7"
                    WHEN idk_kandidat_status_prijave.status_id IN (4,10) THEN "8"
                    END AS status_group,CASE WHEN idk_kandidat_status_prijave.status_id IN (2,6) THEN "Prijava"
                    WHEN idk_kandidat_status_prijave.status_id IN (3) THEN "Casting"
                    WHEN idk_kandidat_status_prijave.status_id IN (5) THEN "Odbijen"
                    WHEN idk_kandidat_status_prijave.status_id IN (7) THEN "Prihvacen"
                    WHEN idk_kandidat_status_prijave.status_id IN (8) THEN "Poslan ugovor"
                    WHEN idk_kandidat_status_prijave.status_id IN (9) THEN "Potpisan ugovor"
                    WHEN idk_kandidat_status_prijave.status_id IN (12,15,18,21,24,27) THEN "Ceka pocetak rada"
                    WHEN idk_kandidat_status_prijave.status_id IN (4,10) THEN "Pocetak rada"
                    END AS status_group_name  FROM idk_kandidat_status_prijave  GROUP BY status_group'
                ;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $data = [];
        foreach($result as $item){
            $nData = [];
            $nData['status_group'] = $item['status_group'];
            $nData['status_group_name'] = $item['status_group_name'];
            $data[] = $nData;
        }

        return $data;
    }

    public function getNumberOfCandidates($status = NULL){
        $user_id = $this->userService->getUserIdByToken();

        if($status != null){
            if($status == 1) $statuses = "2,6";
            elseif($status == 2) $statuses = "3";
            elseif($status == 3) $statuses = "5";
            elseif($status == 4) $statuses = "7";
            elseif($status == 5) $statuses = "8";
            elseif($status == 6) $statuses = "9";
            elseif($status == 7) $statuses = "12,15,18,21,24,27";
            elseif($status == 8) $statuses = "4,10";
            $status_where = "AND idk_kandidati.kandidat_status_prijave IN ($statuses)";
        } else {
            $status_where = " AND idk_kandidati.kandidat_status_prijave NOT IN (0,1)";
        }


        $sql = "SELECT COUNT(idk_kandidati.kandidat_id) as number_of_candidates FROM idk_kandidati WHERE idk_kandidati.kandidat_partnerid LIKE :partner_id AND idk_kandidati.kandidat_nalog_id IS NOT NULL AND idk_kandidati.kandidat_status_prijave IS NOT NULL ".$status_where;
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':partner_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['number_of_candidates'];
    }
    public function getNumberOfCandidatesV2($status = NULL, $search = NULL){
        if($status != null){
            if($status == 1) $statuses = "2,6";
            elseif($status == 2) $statuses = "3";
            elseif($status == 3) $statuses = "5";
            elseif($status == 4) $statuses = "7";
            elseif($status == 5) $statuses = "8";
            elseif($status == 6) $statuses = "9";
            elseif($status == 7) $statuses = "12,15,18,21,24,27";
            elseif($status == 8) $statuses = "4,10";
            $status_where = "AND idk_kandidati.kandidat_status_prijave IN ($statuses)";
        } else {
            $status_where = " AND idk_kandidati.kandidat_status_prijave NOT IN (0,1)";
        }

        $search_query = ""; 
        if($search != null){
            $search_query = ' AND (kandidat_ime LIKE "%'.$search.'%" OR kandidat_prezime LIKE "%'.$search.'%")';
        }

        $user_id = $this->userService->getUserIdByToken();
        $sql = "SELECT COUNT(idk_kandidati.kandidat_id) as number_of_candidates FROM idk_kandidati WHERE idk_kandidati.assigned_to_makler LIKE :partner_id AND idk_kandidati.kandidat_status_prijave IS NOT NULL $status_where $search_query";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':partner_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['number_of_candidates'];
    }

    public function getCandidates($status = null, $order = null, $desc = null, $page = null, $limit = null){
        if($status != null){
            if($status == 1) $statuses = "2,6";
            elseif($status == 2) $statuses = "3";
            elseif($status == 3) $statuses = "5";
            elseif($status == 4) $statuses = "7";
            elseif($status == 5) $statuses = "8";
            elseif($status == 6) $statuses = "9";
            elseif($status == 7) $statuses = "12,15,18,21,24,27";
            elseif($status == 8) $statuses = "4,10";
            $status_where = "AND idk_kandidati.kandidat_status_prijave IN ($statuses)";
        } else {
            $status_where = " AND 1";
        }

        $order_query = " ORDER BY kandidat_id";
        if($order != null){
            if($desc != null){
                if($desc == "true") $desc = "1";
                else $desc = "0";
            } else {
                $desc = "0";
            }

            if($order==='status' && $desc === '0'){
                $order_query = " ORDER BY idk_kandidat_status_prijave.redoslijed_statusa,kandidat_id ";
            } else if($order==='status' && $desc === '1'){
                $order_query = " ORDER BY idk_kandidat_status_prijave.redoslijed_statusa DESC,kandidat_id DESC ";
            } else if($order==='alphabetical' && $desc === '0'){
                $order_query = " ORDER BY kandidat_ime,kandidat_id ";
            } else if($order==='alphabetical' && $desc === '1'){
                $order_query = " ORDER BY kandidat_ime DESC,kandidat_id DESC ";
            }
        }

        $pagination_query = "";
        if($page != null && $limit != null){
            $offset = ($page - 1) * $limit;
            $pagination_query = " LIMIT $offset, $limit";
        }

        $user_id = $this->userService->getUserIdByToken();
        $sql = "SELECT kandidat_id, kandidat_ime, kandidat_prezime, CASE WHEN idk_kandidati.kandidat_status_prijave IN (2,6) THEN 'Prijava'
                    WHEN idk_kandidati.kandidat_status_prijave IN (3) THEN 'Casting'
                    WHEN idk_kandidati.kandidat_status_prijave IN (5) THEN 'Odbijen'
                    WHEN idk_kandidati.kandidat_status_prijave IN (7) THEN 'Prihvacen'
                    WHEN idk_kandidati.kandidat_status_prijave IN (8) THEN 'Poslan ugovor'
                    WHEN idk_kandidati.kandidat_status_prijave IN (9) THEN 'Potpisan ugovor'
                    WHEN idk_kandidati.kandidat_status_prijave IN (12,15,18,21,24,27) THEN 'Ceka pocetak rada'
                    WHEN idk_kandidati.kandidat_status_prijave IN (4,10) THEN 'Pocetak rada'
                    END AS status_group_name, no_nalognaziv
                FROM idk_kandidati
                LEFT JOIN idk_nalozi_opis
                ON idk_kandidati.kandidat_nalog_id = idk_nalozi_opis.no_nalogid
                JOIN idk_kandidat_status_prijave
                ON idk_kandidati.kandidat_status_prijave = idk_kandidat_status_prijave.status_id
                WHERE kandidat_partnerid = :kandidat_partnerid
                    AND idk_kandidati.kandidat_nalog_id IS NOT NULL
                    AND idk_kandidati.kandidat_status_prijave IS NOT NULL
                    AND idk_kandidati.kandidat_status_prijave NOT IN (0,1)
                    $status_where
                    GROUP BY kandidat_id
                    $order_query
                    $pagination_query";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':kandidat_partnerid', $user_id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $data = [];
        foreach($result as $item){
            $nData = [];
            $nData['id'] = $item['kandidat_id'];
            $nData['first_name'] = $item['kandidat_ime'];
            $nData['last_name'] = $item['kandidat_prezime'];
            $nData['status'] = $item['status_group_name'];
            $nData['job_title'] = $item['no_nalognaziv'];
            $data[] = $nData;
        }

        return $data;
    }

    public function getCandidatesV2($status = null, $order = null, $desc = null, $page = null, $limit = null, $search = null){
        if($status != null){
            if($status == 1) $statuses = "2,6";
            elseif($status == 2) $statuses = "3";
            elseif($status == 3) $statuses = "5";
            elseif($status == 4) $statuses = "7";
            elseif($status == 5) $statuses = "8";
            elseif($status == 6) $statuses = "9";
            elseif($status == 7) $statuses = "12,15,18,21,24,27";
            elseif($status == 8) $statuses = "4,10";
            $status_where = "AND idk_kandidati.kandidat_status_prijave IN ($statuses)";
        } else {
            $status_where = " AND 1";
        }

        $order_query = " ORDER BY kandidat_id";
        if($order != null){
            if($desc != null){
                if($desc == "true") $desc = "1";
                else $desc = "0";
            } else {
                $desc = "0";
            }

            if($order==='status' && $desc === '0'){
                $order_query = " ORDER BY idk_kandidat_status_prijave.redoslijed_statusa,kandidat_id ";
            } else if($order==='status' && $desc === '1'){
                $order_query = " ORDER BY idk_kandidat_status_prijave.redoslijed_statusa DESC,kandidat_id DESC ";
            } else if($order==='alphabetical' && $desc === '0'){
                $order_query = " ORDER BY kandidat_ime,kandidat_id ";
            } else if($order==='alphabetical' && $desc === '1'){
                $order_query = " ORDER BY kandidat_ime DESC,kandidat_id DESC ";
            }
        }

        $pagination_query = "";
        if($page != null && $limit != null){
            $offset = ($page - 1) * $limit;
            $pagination_query = " LIMIT $offset, $limit";
        }

        $search_query = ""; 
        if($search != null){
            $search_query = ' AND (kandidat_ime LIKE "%'.$search.'%" OR kandidat_prezime LIKE "%'.$search.'%")';
        }

        $user_id = $this->userService->getUserIdByToken();
        $sql = "SELECT kandidat_id, kandidat_ime, kandidat_prezime, CASE WHEN idk_kandidati.kandidat_status_prijave IN (2,6) THEN 'Prijava'
                    WHEN idk_kandidati.kandidat_status_prijave IN (3) THEN 'Casting'
                    WHEN idk_kandidati.kandidat_status_prijave IN (5) THEN 'Odbijen'
                    WHEN idk_kandidati.kandidat_status_prijave IN (7) THEN 'Prihvacen'
                    WHEN idk_kandidati.kandidat_status_prijave IN (8) THEN 'Poslan ugovor'
                    WHEN idk_kandidati.kandidat_status_prijave IN (9) THEN 'Potpisan ugovor'
                    WHEN idk_kandidati.kandidat_status_prijave IN (12,15,18,21,24,27) THEN 'Ceka pocetak rada'
                    WHEN idk_kandidati.kandidat_status_prijave IN (4,10) THEN 'Pocetak rada'
                    END AS status_group_name, no_nalognaziv
                FROM idk_kandidati
                LEFT JOIN idk_nalozi_opis
                ON idk_kandidati.kandidat_nalog_id = idk_nalozi_opis.no_nalogid
                JOIN idk_kandidat_status_prijave
                ON idk_kandidati.kandidat_status_prijave = idk_kandidat_status_prijave.status_id
                WHERE assigned_to_makler = :assigned_to_makler
                    AND idk_kandidati.kandidat_status_prijave IS NOT NULL
                    AND idk_kandidati.kandidat_status_prijave NOT IN (0,1)
                    $status_where
                    $search_query
                    GROUP BY kandidat_id
                    $order_query
                    $pagination_query";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':assigned_to_makler', $user_id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $data = [];
        foreach($result as $item){
            $nData = [];
            $nData['id'] = $item['kandidat_id'];
            $nData['first_name'] = $item['kandidat_ime'];
            $nData['last_name'] = $item['kandidat_prezime'];
            $nData['status'] = $item['status_group_name'];
            $nData['job_title'] = $item['no_nalognaziv'];
            $data[] = $nData;
        }

        return $data;
    }

    public function getLeadCandidates($status = null, $order = null, $desc = null, $page = null, $limit = null, $search = null){
        $user_id = $this->userService->getUserIdByToken();

        if(!is_null($status)){
            $status_where =  " AND kandidat_partner_lead_status IN ($status) ";
        } else {
            $status_where = " AND 1 ";
        }

        $search_query = ""; 
        if($search != null){
            $search_query = ' AND (kandidat_ime LIKE "%'.$search.'%" OR kandidat_prezime LIKE "%'.$search.'%")';
        }

        $order_query = " ORDER BY kandidat_id";
        if($order != null){
            if($desc != null){
                if($desc == "true") $desc = "1";
                else $desc = "0";
            } else {
                $desc = "0";
            }

            if($order==='status' && $desc === '0'){
                $order_query = " ORDER BY kandidat_partner_lead_status, kandidat_id ";
            } else if($order==='status' && $desc === '1'){
                $order_query = " ORDER BY kandidat_partner_lead_status DESC, kandidat_id DESC ";
            } else if($order==='alphabetical' && $desc === '0'){
                $order_query = " ORDER BY kandidat_ime ";
            } else if($order==='alphabetical' && $desc === '1'){
                $order_query = " ORDER BY kandidat_ime DESC ";
            } else if($order==='date' && $desc === '0'){
                $order_query = " ORDER BY zaduzen_makleru_datum ";
            } else if($order==='date' && $desc === '1'){
                $order_query = " ORDER BY zaduzen_makleru_datum DESC ";
            }
        }

        $pagination_query = "";
        if($page != null && $limit != null){
            $offset = ($page - 1) * $limit;
            $pagination_query = " LIMIT $offset, $limit";
        }

        $sql = "
            SELECT
                kandidat_id, kandidat_ime, kandidat_prezime, zaduzen_makleru_datum, kandidat_partner_lead_status
            FROM
                idk_kandidati
            WHERE zaduzeni_makler_id = :user_id
            $status_where
            $search_query
            $order_query
            $pagination_query
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $data = [];
        foreach($result as $item){
            $nData = [];
            $nData['id'] = $item['kandidat_id'];
            $nData['first_name'] = $item['kandidat_ime'];
            $nData['last_name'] = $item['kandidat_prezime'];
            $nData['status'] = $item['kandidat_partner_lead_status'];
            $nData['date'] = $item['zaduzen_makleru_datum'];
            $data[] = $nData;
        }

        return $data;

    }

    public function getLeadAnalytics(){
        $user_id = $this->userService->getUserIdByToken();

        $sql = "
            SELECT
                SUM(CASE WHEN kandidat_partner_lead_status = 1 THEN 1 ELSE 0 END) as 'lead',
                SUM(CASE WHEN kandidat_partner_lead_status = 2 THEN 1 ELSE 0 END) as 'client',
                SUM(CASE WHEN kandidat_partner_lead_status = 3 THEN 1 ELSE 0 END) as 'notInterested'
            FROM idk_kandidati
            WHERE zaduzeni_makler_id = :user_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch();

        $data[1] = intval($result['lead']);
        $data[2] = intval($result['client']);
        $data[3] = intval($result['notInterested']);

        return $data;
    }

    public function getLeadProfile($candidate_id){

        $main_data = $this->getLeadsMainData($candidate_id);
        $language_level_english = $this->getCandidatesLanguage($candidate_id, 'Engleski');
        $language_level_german = $this->getCandidatesLanguage($candidate_id, 'Njemacki');

        $data = [];
        $profile_image = $main_data['kandidat_slika'];
        if($profile_image == 'none' OR $profile_image == 'none.jpg'){
            $profile_image = 'image_none_partner_candidate.jpg';
        }

        $dob = $main_data['kandidat_datumrodjenja'];
        $reminder_date = $main_data['zaduzeni_makler_reminder_datum'];
        $start_of_work = $main_data['kandidat_dogovoreni_pocetak_rada'];

        if(!is_null($dob)){
            $dob = date('d/m/Y', strtotime($dob));
        }
        if(!is_null($reminder_date)){
            $reminder_date = date('d/m/Y', strtotime($reminder_date));
        }
        if(!is_null($start_of_work)){
            $start_of_work = date('d/m/Y', strtotime($start_of_work));
        }

        $data['id'] = $main_data['kandidat_id'];
        $data['first_name'] = $main_data['kandidat_ime'];
        $data['last_name'] = $main_data['kandidat_prezime'];
        $data['status'] = $main_data['kandidat_partner_lead_status'];
        $data['contact_phone'] = $main_data['kandidat_mobitel'];
        $data['contact_email'] = $main_data['kandidat_email'];
        $data['dob'] = $dob;
        $data['is_married'] = $main_data['kandidat_is_married'];
        $data['city'] = $main_data['destinacija_grad'];
        $data['plz'] = $main_data['kandidat_destinacija_postanski_broj'];
        $data['insurance_interest'] = $main_data['interestInInsurance'];
        $data['reminder_date'] =  $reminder_date;
        $data['language_level_german'] = $language_level_german;
        $data['language_level_english'] = $language_level_english;
        $data['note'] = $main_data['kandidat_partner_lead_biljeska'];
        $data['start_of_work'] =  $start_of_work;
        $data['profile_image'] = $profile_image;

        return $data;
    }

    public function getLeadProfileV2($candidate_id){

        $main_data = $this->getLeadsMainDataV2($candidate_id);

        $data = [];
        $profile_image = $main_data['kandidat_slika'];
        if($profile_image == 'none' OR $profile_image == 'none.jpg'){
            $profile_image = 'nonekandidati.jpg';
        }

        $data['id'] = $main_data['kandidat_id'];
        $data['first_name'] = $main_data['kandidat_ime'];
        $data['last_name'] = $main_data['kandidat_prezime'];
        $data['status'] = $main_data['kandidat_partner_lead_status'];
        $data['contact_phone'] = $main_data['kandidat_mobitel'];
        $data['insurance_interest'] = $main_data['interestInInsurance'];
        $data['note'] = $main_data['kandidat_partner_lead_biljeska'];
        $data['profile_image'] = $profile_image;

        return $data;
    }

    public function getLeadInfo($candidate_id) {

        $main_data = $this->getLeadInfoData($candidate_id);
        $language_level_english = $this->getCandidatesLanguage($candidate_id, 'Engleski');
        $language_level_german = $this->getCandidatesLanguage($candidate_id, 'Njemacki');

        $data = [];

        $dob = $main_data['kandidat_datumrodjenja'];

        if(!is_null($dob)){
            $dob = date('d/m/Y', strtotime($dob));
        }
        $data['contact_phone'] = $main_data['kandidat_mobitel'];
        $data['contact_email'] = $main_data['kandidat_email'];
        $data['dob'] = $dob;
        $data['is_married'] = $main_data['kandidat_is_married'];
        $data['city'] = $main_data['destinacija_grad'];
        $data['plz'] = $main_data['kandidat_destinacija_postanski_broj'];
        $data['language_levels'][] = [
            "language" => 'en',
            "level" => $language_level_english
        ];
        $data['language_levels'][] = [
            "language" => 'de',
            "level" => $language_level_german
        ];

        return $data;
    }

    public function getLeadEmployerInfo($candidate_id) {
        $main_data = $this->getLeadEmployerInfoData($candidate_id);

        $data = [];

        $start_of_work = $main_data['kandidat_dogovoreni_pocetak_rada'];
        if(!is_null($start_of_work)){
            $start_of_work = date('d/m/Y', strtotime($start_of_work));
        }

        $data['contact_phone'] = $main_data['kandidat_mobitel'];
        $data['name'] = $main_data['company_name'];
        $data['start_of_work'] = $start_of_work;
        $data['contact_person'] = $main_data['contact_name'];

        return $data;
    }

    private function getLeadEmployerInfoData($candidate_id){
        $sql = "
            SELECT
                idk_companies.company_name,
                CONCAT(idk_contacts.contact_firstname, ' ', idk_contacts.contact_lastname) AS contact_name,
                idk_kandidati.kandidat_dogovoreni_pocetak_rada
            FROM idk_kandidati
            JOIN idk_nalozi
            ON idk_kandidati.kandidat_nalog_id = idk_nalozi.nalog_id
            JOIN idk_companies
            ON idk_nalozi.kompanija_id = idk_companies.company_id
            LEFT JOIN idk_contacts
            ON idk_companies.company_id = idk_contacts.contact_companyid
            WHERE idk_kandidati.kandidat_id = :candidate_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':candidate_id', $candidate_id);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result;
    }

    private function getLeadInfoData($candidate_id){
        $sql = "
            SELECT
                idk_kandidati.kandidat_mobitel,
                idk_kandidati.kandidat_email,
                CASE
                    WHEN idk_kandidati.kandidat_destinacija_grad IS NULL THEN 'Nepoznato'
                    ELSE  idk_kandidati.kandidat_destinacija_grad
                END as destinacija_grad,
                idk_kandidati.kandidat_destinacija_postanski_broj,
                idk_kandidati.kandidat_datumrodjenja,
                CASE
                    WHEN idk_kandidati.kandidat_bracno_stanje = 0 THEN NULL
                    WHEN idk_kandidati.kandidat_bracno_stanje = 2 THEN true
                    WHEN idk_kandidati.kandidat_bracno_stanje IN (1,3,4)
                    THEN false
                END as kandidat_is_married
            FROM idk_kandidati
            WHERE idk_kandidati.kandidat_id = :candidate_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':candidate_id', $candidate_id);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result;
    }

    private function getLeadsMainData($candidate_id){
        $sql = "
            SELECT
                idk_kandidati.kandidat_id,
                idk_kandidati.kandidat_ime,
                idk_kandidati.kandidat_prezime,
                idk_kandidati.kandidat_mobitel,
                idk_kandidati.kandidat_email,
                idk_kandidati.kandidat_datumrodjenja,
                idk_kandidati.zaduzeni_makler_reminder_datum,
                idk_kandidati.kandidat_partner_lead_status,
                idk_kandidati.kandidat_slika,
                idk_kandidati.kandidat_destinacija_grad,
                idk_kandidati.kandidat_destinacija_postanski_broj,
                idk_kandidati.kandidat_dogovoreni_pocetak_rada,
                idk_kandidati.kandidat_partner_lead_biljeska,
                CASE
                    WHEN idk_kandidati.kandidat_bracno_stanje = 0 THEN NULL
                    WHEN idk_kandidati.kandidat_bracno_stanje = 2 THEN true
                    WHEN idk_kandidati.kandidat_bracno_stanje IN (1,3,4)
                    THEN false
                END as kandidat_is_married,
                CASE
                    WHEN idk_kandidati.kandidat_destinacija_grad IS NULL THEN 'Nepoznato'
                    ELSE idk_kandidati.kandidat_destinacija_grad
                END as destinacija_grad,
                CASE
                    WHEN idk_kandidati.kandidat_partner_lead_status = 1
                    THEN
                        CASE
                            WHEN idk_kandidati.zaduzeni_makler_reminder_datum IS NULL
                            THEN 'lead'
                            ELSE 'remindLater'
                        END
                    WHEN idk_kandidati.kandidat_partner_lead_status = 2 THEN 'client'
                    WHEN idk_kandidati.kandidat_partner_lead_status = 3 THEN 'notInterested'
                END as interestInInsurance

            FROM idk_kandidati
            WHERE idk_kandidati.kandidat_id = :candidate_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':candidate_id', $candidate_id);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result;
    }

    private function getLeadsMainDataV2($candidate_id){
        $sql = "
            SELECT
                idk_kandidati.kandidat_id,
                idk_kandidati.kandidat_ime,
                idk_kandidati.kandidat_prezime,
                idk_kandidati.kandidat_mobitel,
                idk_kandidati.kandidat_partner_lead_status,
                idk_kandidati.kandidat_slika,
                idk_kandidati.kandidat_partner_lead_biljeska,
                CASE
                    WHEN idk_kandidati.kandidat_partner_lead_status = 1
                    THEN
                        CASE
                            WHEN idk_kandidati.zaduzeni_makler_reminder_datum IS NULL
                            THEN 'lead'
                            ELSE 'remindLater'
                        END
                    WHEN idk_kandidati.kandidat_partner_lead_status = 2 THEN 'client'
                    WHEN idk_kandidati.kandidat_partner_lead_status = 3 THEN 'notInterested'
                END as interestInInsurance
            FROM idk_kandidati
            WHERE idk_kandidati.kandidat_id = :candidate_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':candidate_id', $candidate_id);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result;
    }

    private function getCandidatesLanguage($candidate_id, $language){
        $sql = "
            SELECT kj.kj_slusanje,
            CASE
                WHEN kj.kj_slusanje LIKE ('A1') THEN 1
                WHEN kj.kj_slusanje LIKE ('A2') THEN 2
                WHEN kj.kj_slusanje LIKE ('B1') THEN 3
                WHEN kj.kj_slusanje LIKE ('B2') THEN 4
                WHEN kj.kj_slusanje LIKE ('C1') THEN 5
                WHEN kj.kj_slusanje LIKE ('C2') THEN 6
                ELSE 0
            END as language_level
            FROM (
                SELECT *
                FROM idk_kandidat_jezici
                WHERE kj_kandidatid = :candidate_id
                AND kj_naziv LIKE ('$language')
            ) kj
            LEFT JOIN idk_candidate_verified_languages cvl
            ON cvl.cvl_id = kj.kj_id
            ORDER BY cvl.cvl_active DESC, kj.kj_ustanova DESC, language_level DESC
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':candidate_id', $candidate_id);
        $stmt->execute();
        $result = $stmt->fetch();

        $language_level = 'noKnowledge';
        if($result['language_level'] == 0){
            $language_level = 'unknown';
        }
        else{
            $language_level = $result['kj_slusanje'];
        }

        return $language_level;

    }

    public function getLeadCount($status = NULL, $search = NULL){
        $user_id = $this->userService->getUserIdByToken();

        $search_query = ""; 
        if($search != null){
            $search_query = ' AND (kandidat_ime LIKE "%'.$search.'%" OR kandidat_prezime LIKE "%'.$search.'%")';
        }

        $status_query = "";
        if(!is_null($status)){
            $status_query = " AND kandidat_partner_lead_status IN ($status) ";
        }

        $sql = "
            SELECT count(zaduzeni_makler_id) as lead_count
            FROM idk_kandidati
            WHERE zaduzeni_makler_id = :user_id
            $status_query
            $search_query
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch();
        return intval($result['lead_count']);
    }

    public function updateLead($candidate_id, $insurance_interest, $phone, $note){

        $current_status = $this->getCurrentLeadStatus($candidate_id);

        if($note == ''){
            $note = null;
        }
        $update_reminder_date_query = ", zaduzeni_makler_reminder_datum = NULL";
        if($insurance_interest == 'lead'){
            $update_to_status = 1;
        }
        else if($insurance_interest == 'client'){
            $update_to_status = 2;
        }
        else if($insurance_interest == 'notInterested'){
            $update_to_status = 3;
        }
        else{
            $update_to_status = 1;
            if(is_null($current_status['reminder_date'])){
                $update_reminder_date_query = ", zaduzeni_makler_reminder_datum = DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)";
            }else{
                $update_reminder_date_query = "";
            }
        }

        $sql = "
            UPDATE idk_kandidati
            SET kandidat_partner_lead_status = :update_to_status, kandidat_mobitel = :phone, kandidat_partner_lead_biljeska = :note $update_reminder_date_query
            WHERE kandidat_id = :candidate_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':candidate_id', $candidate_id);
        $stmt->bindParam(':update_to_status', $update_to_status);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':note', $note);

        $stmt->execute();

        $updated_profile =  $this->getLeadProfile($candidate_id);

        return $updated_profile;
    }

    private function getCurrentLeadStatus($candidate_id){
        $sql = "
            SELECT kandidat_partner_lead_status, zaduzeni_makler_reminder_datum
            FROM idk_kandidati
            WHERE kandidat_id = :candidate_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':candidate_id', $candidate_id);
        $stmt->execute();
        $result = $stmt->fetch();

        $data = [];
        $data['status'] = $result['kandidat_partner_lead_status'];
        $data['reminder_date'] = $result['zaduzeni_makler_reminder_datum'];
        return $data;
    }
}
