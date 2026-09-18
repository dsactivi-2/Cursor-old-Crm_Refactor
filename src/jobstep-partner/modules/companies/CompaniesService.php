<?php
namespace modules\companies;
use modules\database\Database;
use modules\user\UserService;

class CompaniesService {
    public function __construct(Database $database, UserService $userService)
    {
        $this->conn = $database->getConnection();
        $this->userService = $userService;
    }

    public function getNumberOfCompanies($search = null){
        $user_id = $this->userService->getUserIdByToken();

        $search_query = "";
        if($search != null){
            $search_query = " AND idk_companies.company_name LIKE '%$search%'";
        }

        $sql = "SELECT COUNT(idk_companies.company_id) as number_of_companies 
        FROM idk_companies 
        JOIN idk_clients ON idk_companies.company_id = idk_clients.client_recommendation_company
        WHERE idk_companies.js_partner_id = :partner_id AND idk_clients.client_sales_status!=4 $search_query";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':partner_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result['number_of_companies'];
    }
    
    public function getNumberOfCompaniesV2($search = null){
        $user_id = $this->userService->getUserIdByToken();

        $search_query = "";
        if($search != null){
            $search_query = " AND idk_companies.company_name LIKE '%$search%'";
        }

        $sql = "SELECT COUNT(idk_companies.company_id) as number_of_companies 
        FROM idk_companies 
        JOIN idk_clients ON idk_companies.company_id = idk_clients.client_recommendation_company
        WHERE idk_companies.js_partner_id = :partner_id AND (idk_companies.company_status IN (1,2,3,4,5,6) OR idk_companies.company_status IS NULL) $search_query";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':partner_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result['number_of_companies'];
    }

    public function getCompanyById($company_id, string $lang){
        $sql = "SELECT 
                    company_id,
                    company_name, 
                    company_size, 
                    company_country, 
                    company_city, 
                    company_zipcode, 
                    company_address, 
                    company_message, 
                    contact_firstname, 
                    contact_lastname, 
                    contact_job_title,
                    company_total_workers_required,
                    company_professions,
                    idk_partner_meetings.meeting_appointment
                FROM idk_companies
                LEFT JOIN idk_contacts
                ON idk_companies.company_id = idk_contacts.contact_companyid
                LEFT JOIN idk_partner_meetings 
                ON idk_companies.company_id = idk_partner_meetings.meeting_company_id
                WHERE company_id = :company_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':company_id', $company_id);
        $stmt->execute();
        $result = $stmt->fetch();
        
        $data = [];
        $data['company_id'] = $result['company_id'];
        $data['company_name'] = $result['company_name'];
        $data['company_size'] = $result['company_size'];
        $data['company_country'] = $result['company_country'];
        $data['company_city'] = $result['company_city'];
        $data['company_zip_code'] = $result['company_zipcode'];
        $data['company_address'] = $result['company_address'];
        $data['message'] = $result['company_message'];
        $data['contact_fname'] = $result['contact_firstname'];
        $data['contact_lname'] = $result['contact_lastname'];
        $data['contact_job'] = $result['contact_job_title'];
        $data['total_workers_required'] = $result['company_total_workers_required'];
        $data['meeting_appointment'] =  $result['meeting_appointment'];

        $profession_name_sql = ($lang == 'en') ? "kp_ime_en" : "kp_ime_de";
        $second_profession_name_sql = ($lang == 'en') ? "kp_ime_de" : "kp_ime_en";
        $company_professions = $result['company_professions'];
        if(is_null($company_professions)){
            $company_professions = 0;
        }
        $professions_sql = "SELECT kp_id, CASE WHEN $profession_name_sql IS NOT NULL THEN $profession_name_sql ELSE $second_profession_name_sql END AS profession_name FROM idk_kandidat_pozicija WHERE kp_id IN ($company_professions) AND kp_active = 1";
        $professions_stmt = $this->conn->prepare($professions_sql);
        $professions_stmt->execute();
        $professions = $professions_stmt->fetchAll();

        if($professions!=NULL){
            foreach($professions as $profession){
                $professions_data = [];
                $professions_data['profession_id'] = $profession['kp_id'];
                $professions_data['profession_name'] = $profession['profession_name'];
                $data['professions'][] = $professions_data;
            }
        } else {
            $data['professions'] = [];
        }

        $company_info = $this->getCompanyInfo($company_id);
        foreach($company_info as $info){
            if($info['comi_group'] == 1){
                $data['company_phone'] = $info['comi_data'];
            } else if($info['comi_group'] == 2){
                $data['company_email'] = $info['comi_data'];
            }
        }

        $jobs = [];

        $sql = "SELECT nalog_id, nalog_potrebno_kandidata, nalog_partner_app_until, no_nalognaziv,idk_link_generator.lg_id, 
                count(CASE WHEN idk_kandidati.kandidat_status_prijave IN (4, 7, 8, 9, 10, 12, 15, 18, 21, 24, 27) THEN 1 END ) as found_candidates,
                CASE WHEN nalog_status In (1,2,3,4,5,6,7,9,10) THEN 1
                WHEN nalog_status IN (8) THEN 2
                ELSE null
                END AS status_naloga
                FROM idk_nalozi 
                LEFT JOIN idk_nalozi_opis 
                ON idk_nalozi.nalog_id = idk_nalozi_opis.no_nalogid
                LEFT JOIN idk_kandidati
                ON idk_nalozi.nalog_id = idk_kandidati.kandidat_nalog_id
                JOIN idk_link_generator 
                ON idk_nalozi.nalog_id = idk_link_generator.lg_nalogid
                WHERE kompanija_id = :kompanija_id AND no_lang = :lang AND nalog_status != 12 AND nalog_partner_active = 1 AND lg_partner_app = 1 GROUP BY nalog_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':kompanija_id', $company_id);
        $stmt->bindParam(':lang', $lang);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if($result != null){
            foreach($result as $job){
                $job_data = [];
                $job_data['job_id'] = $job['nalog_id'];
                $job_data['job_title'] = $job['no_nalognaziv'];
                if($job['status_naloga'] == 1){
                        if($lang == 'de'){
                            $job_data['job_status'] = 'In Bearbeitung';
                        } else if($lang == 'en'){
                            $job_data['job_status'] = 'In progress';
                        } else if($lang == 'bs'){
                            $job_data['job_status'] = 'U obradi';
                        }
                } else {
                        if($lang == 'de'){
                            $job_data['job_status'] = 'Abgeschlossen';
                        } else if($lang == 'en'){
                            $job_data['job_status'] = 'Completed';
                        } else if($lang == 'bs'){
                            $job_data['job_status'] = 'Završen';
                        }
                }
                $job_data['job_until'] = $job['nalog_partner_app_until'];
                $job_data['requested_candidates'] = $job['nalog_potrebno_kandidata'];
                $job_data['found_candidates'] = $job['found_candidates'];
                $job_data['link'] = "https://job-step.net/registration/".$job['lg_id']."/".$_SESSION['token'];
                $data['jobs'][] = $job_data;
            }
        } else {
            $data['jobs'] = [];
        }
        
        return $data;
    }

    public function getPositionsInfo($lang){
        $profession_name_sql = ($lang == 'en') ? "kp_ime_en" : "kp_ime_de";
        $second_profession_name_sql = ($lang == 'en') ? "kp_ime_de" : "kp_ime_en";

        $sql = "SELECT kp_id, CASE WHEN $profession_name_sql IS NOT NULL THEN $profession_name_sql ELSE $second_profession_name_sql END AS profession_name FROM idk_kandidat_pozicija WHERE kp_active = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $data = [];
        foreach($result as $position){
            $nData = [];
            $nData['id'] = $position['kp_id'];
            $nData['name'] = $position['profession_name'];
            $data[] = $nData;
        }

        return $data;
    }

    public function getCompanyInfo($company_id){
        $sql = "SELECT 
                    comi_group, 
                    comi_data
                FROM idk_companies_info
                WHERE comi_companyid = :comi_companyid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':comi_companyid', $company_id);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }

      public function getCompanies($order = null, $desc = null, $page = null, $limit = null, $search = null){
        $user_id = $this->userService->getUserIdByToken();

        $order_query = " ORDER BY idk_companies.company_id ";
        if($order != null){
            if($desc === null){
                $desc = '0';
            } else {
                if($desc === "true"){
                    $desc = '1';
                }
            }
            if($order === 'alphabetical' && $desc === '0') {
                $order_query = " ORDER BY idk_companies.company_name,idk_companies.company_id ";
            }else if($order === 'alphabetical' && $desc === '1') {
                $order_query = " ORDER BY idk_companies.company_name DESC,idk_companies.company_id ";
            }else if($order === 'age' && $desc === '0') {
                $order_query = " ORDER BY idk_companies.company_id ";
            }else if($order === 'age' && $desc === '1') {
                $order_query = " ORDER BY idk_companies.company_id DESC ";
            }
        }

        $pagination_query = "";
        if($page != null && $limit != null){
            $offset = ($page - 1) * $limit;
            $pagination_query = " LIMIT $offset, $limit";
        }

        $search_query = "";
        if($search != null){
            $search_query = " AND idk_companies.company_name LIKE '%$search%'";
        }

        $sql = "SELECT
                    idk_companies.company_id,
                    idk_companies.company_name,
                    CASE WHEN idk_clients.client_sales_status IN (0,1,2) THEN 'Lead'
                    WHEN idk_clients.client_sales_status IN (3) THEN 'Active'
                    END AS company_status
                FROM
                    idk_companies
                JOIN idk_clients ON idk_companies.company_id = idk_clients.client_recommendation_company
                WHERE
                    idk_companies.js_partner_id = :partner_id AND idk_clients.client_sales_status!=4
                    $search_query
                    $order_query
                    $pagination_query";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':partner_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $data = [];
        foreach($result as $company){
            $nData = [];
            $nData['id'] = $company['company_id'];
            $nData['company_name'] = $company['company_name'];
            $nData['company_status'] = $company['company_status'];
            $data[] = $nData;
        }

        return $data;
    }

    public function getCompaniesV2($order = null, $desc = null, $page = null, $limit = null, $search = null){
        $user_id = $this->userService->getUserIdByToken();

        $order_query = " ORDER BY idk_companies.company_id ";
        if($order != null){
            if($desc === null){
                $desc = '0';
            } else {
                if($desc === "true"){
                    $desc = '1';
                }
            }
            if($order === 'alphabetical' && $desc === '0') {
                $order_query = " ORDER BY idk_companies.company_name,idk_companies.company_id ";
            }else if($order === 'alphabetical' && $desc === '1') {
                $order_query = " ORDER BY idk_companies.company_name DESC,idk_companies.company_id ";
            }else if($order === 'age' && $desc === '0') {
                $order_query = " ORDER BY idk_companies.company_id ";
            }else if($order === 'age' && $desc === '1') {
                $order_query = " ORDER BY idk_companies.company_id DESC ";
            }
        }

        $pagination_query = "";
        if($page != null && $limit != null){
            $offset = ($page - 1) * $limit;
            $pagination_query = " LIMIT $offset, $limit";
        }

        $search_query = "";
        if($search != null){
            $search_query = " AND idk_companies.company_name LIKE '%$search%'";
        }

        $sql = "SELECT
                    idk_companies.company_id,
                    idk_companies.company_name,
                    CASE WHEN idk_companies.company_status=2 OR idk_companies.company_status IS NULL THEN 'New'
                    WHEN idk_companies.company_status=3 THEN 'In progress'
                    WHEN idk_companies.company_status=4 THEN 'On hold'
                    WHEN idk_companies.company_status=5 THEN 'Rejected'
                    WHEN idk_companies.company_status=6 THEN 'Finished'
                    WHEN idk_companies.company_status=1 THEN 'Active'
                    END AS company_status
                FROM
                    idk_companies
                JOIN idk_clients ON idk_companies.company_id = idk_clients.client_recommendation_company
                WHERE
                    idk_companies.js_partner_id = :partner_id AND (idk_companies.company_status IN (1,2,3,4,5,6) OR idk_companies.company_status IS NULL)
                    $search_query
                    $order_query
                    $pagination_query";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':partner_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $data = [];
        foreach($result as $company){
            $nData = [];
            $nData['id'] = $company['company_id'];
            $nData['company_name'] = $company['company_name'];
            $nData['company_status'] = $company['company_status'];
            $data[] = $nData;
        }

        return $data;
    }

    public function addCompany($json){
        $user_id = $this->userService->getUserIdByToken();
        $company_name = $json['company_name'];
        $contact_person_fname = $json['contact_fname'];
        $contact_person_lname = $json['contact_lname'];
        $contact_job = $json['contact_job'];
        $company_size = $json['company_size'];
        $company_email = $json['company_email'];
        $company_phone = $json['company_phone'];   
        $company_country = $json['company_country'];
        $company_city = $json['company_city'];
        $company_zip_code = $json['company_zip_code'];
        $company_address = $json['company_address'];
        $message = $json['message'];
        $date = date("Y-m-d H:i:s");

        $sql = "INSERT INTO idk_companies (js_partner_id, company_origin, company_name, company_size, company_zipcode, company_city, company_country, company_address, company_message, company_datetime)
                VALUES (:js_partner_id, :company_origin, :company_name, :company_size, :compnay_zipcode, :company_city, :company_country, :company_address, :company_message, :company_datetime)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':js_partner_id', $user_id);
        $stmt->bindParam(':company_origin', 5);
        $stmt->bindParam(':company_name', $company_name);
        $stmt->bindParam(':company_size', $company_size);
        $stmt->bindParam(':compnay_zipcode', $company_zip_code);
        $stmt->bindParam(':company_city', $company_city);
        $stmt->bindParam(':company_country', $company_country);
        $stmt->bindParam(':company_address', $company_address);
        $stmt->bindParam(':company_message', $message);
        $stmt->bindParam(':company_datetime', $date);
        $stmt->execute();

        $company_id = $this->conn->lastInsertId();

        $sql = "INSERT INTO idk_companies_info (comi_group, comi_title, comi_data, comi_primary, comi_companyid)
                VALUES (:comi_group, :comi_title, :comi_data, :comi_primary, :comi_companyid)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':comi_group', 1);
        $stmt->bindParam(':comi_title', 'Telefon');
        $stmt->bindParam(':comi_data', $company_phone);
        $stmt->bindParam(':comi_primary', 1);
        $stmt->bindParam(':comi_companyid', $company_id);
        $stmt->execute();
        
        $sql = "INSERT INTO idk_companies_info (comi_group, comi_title, comi_data, comi_primary, comi_companyid)
                VALUES (:comi_group, :comi_title, :comi_data, :comi_primary, :comi_companyid)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':comi_group', 2);
        $stmt->bindParam(':comi_title', 'Email');
        $stmt->bindParam(':comi_data', $company_email);
        $stmt->bindParam(':comi_primary', 1);
        $stmt->bindParam(':comi_companyid', $company_id);
        $stmt->execute();

        if(!empty($contact_person_fname)){
            $sql = "INSERT INTO idk_contacts (contact_firstname, contact_lastname, contact_companyid, contact_status, contact_job_title, contact_datetime)
                    VALUES (:contact_firstname, :contact_lastname, :contact_companyid, :contact_status, :contact_job_title, :contact_datetime)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':contact_firstname', $contact_person_fname);
            $stmt->bindParam(':contact_lastname', $contact_person_lname);
            $stmt->bindParam(':contact_companyid', $company_id);
            $stmt->bindParam(':contact_status', 1);
            $stmt->bindParam(':contact_job_title', $contact_job);
            $stmt->bindParam(':contact_datetime', $date);
            $stmt->execute();
        }

        $sql = "INSERT INTO idk_clients (client_name, client_country, client_city, client_address, client_pp, client_telephone, client_email, client_origin, client_recommendation, client_recommendation_company, client_fc_or_sales, client_fc_status, client_sales_status, client_manager, client_sales_manager, client_contract, client_description) 
                VALUES (:client_name, :client_country, :client_city, :client_address, NULL, :client_telephone, :client_email, 5, NULL, :client_recommendation_company, 1, 2, 0,NULL, NULL, 0, :client_description);";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':client_name', $company_name);
        $stmt->bindParam(':client_country', $company_country);
        $stmt->bindParam(':client_city', $company_city);
        $stmt->bindParam(':client_address', $company_address);
        $stmt->bindParam(':client_telephone', $company_phone);
        $stmt->bindParam(':client_email', $company_email);
        $stmt->bindParam(':client_recommendation_company', $company_id);
        $stmt->bindParam(':client_description', $message);
        $stmt->execute();
        $data = [];
        $sql = "SELECT 
                    company_name, 
                    company_size, 
                    company_country, 
                    company_city, 
                    company_zipcode, 
                    company_address, 
                    company_message, 
                    contact_firstname, 
                    contact_lastname, 
                    contact_job_title
                FROM idk_companies
                LEFT JOIN idk_contacts
                ON idk_companies.company_id = idk_contacts.contact_companyid
                WHERE company_id = :company_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':company_id', $company_id);
        $stmt->execute();
        $result = $stmt->fetch();
        $data['company_id'] = $company_id;
        $data['company_name'] = $result['company_name'];
        $data['company_size'] = $result['company_size'];
        $data['company_country'] = $result['company_country'];
        $data['company_city'] = $result['company_city'];
        $data['company_zip_code'] = $result['company_zipcode'];
        $data['company_address'] = $result['company_address'];
        $data['message'] = $result['company_message'];
        $data['contact_fname'] = $result['contact_firstname'];
        $data['contact_lname'] = $result['contact_lastname'];
        $data['contact_job'] = $result['contact_job_title'];

        $sql = "SELECT 
                    comi_group, 
                    comi_data
                FROM idk_companies_info
                WHERE comi_companyid = :comi_companyid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':comi_companyid', $company_id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        foreach($result as $info_result){
            if($info_result['comi_group'] == 1){
                $data['company_phone'] = $info_result['comi_data'];
            } else if($info_result['comi_group'] == 2){
                $data['company_email'] = $info_result['comi_data'];
            }
        }
    }

}