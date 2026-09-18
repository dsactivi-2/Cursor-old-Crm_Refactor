<?php
namespace modules\recruitment_orders;
use modules\database\Database;
use modules\user\UserService;

class RecruitmentOrderService {
    public function __construct(Database $database, UserService $userService) 
    {
        $this->conn = $database->getConnection();
        $this->userService = $userService;
    }

    public function getAllJobs($lang){
        $user_id = $this->userService->getUserIdByToken();

        $sql = "SELECT nalog_id,lg_datetime, lg_link_prijave, idk_urlimg_prijave,lg_id, idk_nalozi_opis.no_nalognaziv ,nalog_partner_app_location,nalog_partner_app_salary
                FROM idk_nalozi	
                INNER JOIN idk_link_generator ON idk_nalozi.nalog_id = idk_link_generator.lg_nalogid
                INNER JOIN 	idk_nalozi_opis ON idk_nalozi.nalog_id = idk_nalozi_opis.no_nalogid
                WHERE nalog_partner_active = 1 AND idk_nalozi_opis.no_lang = '" . $lang . "'  AND (idk_urlimg_prijave IS NOT NULL AND idk_urlimg_prijave != 'none') AND lg_partner_app = 1
                GROUP BY nalog_id
                ORDER BY idk_link_generator.lg_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $linkEnv = "registration";
        Global $envConfig;
        if($envConfig->APP_ENV != "production"){
            $linkEnv = "registration2";
        }
        $data = [];
        foreach($result as $job){
            $nData = [];
            $nData['id'] = $job['nalog_id'];
            $nData['title'] = $job['no_nalognaziv'];
            $nData['location'] = $job['nalog_partner_app_location'];
            $nData['salary'] = $job['nalog_partner_app_salary'];
            $nData['link'] = "https://job-step.net/$linkEnv/".$job['lg_id']."/".$_SESSION['token']."/".$lang;
            $nData['thumbnail'] = $job['idk_urlimg_prijave'];
            $nData['job_created_at'] = $job['lg_datetime'];
            $data[] = $nData;
        }

        return $data;
    }

    public function getJobDetails($lang, $job_id){
        $sql = "SELECT
                    nalog_id,
                    idk_link_generator.lg_datetime,
                    nalog_potrebno_kandidata,
                    nalog_partner_app_until,
                    nalog_partner_app_location,
                    nalog_partner_app_salary,
                    no_nalognaziv,
                    no_nalogopis,
                    idk_link_generator.lg_id,
                    idk_link_generator.idk_urlimg_prijave,
                    COUNT(
                        CASE WHEN idk_kandidati.kandidat_status_prijave IN(
                            4,
                            7,
                            8,
                            9,
                            10,
                            12,
                            15,
                            18,
                            21,
                            24,
                            27
                        ) THEN 1
                    END
                ) AS found_candidates,
                CASE WHEN nalog_status IN(1, 2, 3, 4, 5, 6, 7, 9, 10) THEN 1 WHEN nalog_status IN(8) THEN 2 ELSE NULL
                END AS status_naloga
                FROM
                    idk_nalozi
                LEFT JOIN idk_nalozi_opis ON idk_nalozi.nalog_id = idk_nalozi_opis.no_nalogid
                LEFT JOIN idk_kandidati ON idk_nalozi.nalog_id = idk_kandidati.kandidat_nalog_id
                JOIN idk_link_generator ON idk_nalozi.nalog_id = idk_link_generator.lg_nalogid
                WHERE
                    nalog_id = :nalog_id AND no_lang = :lang AND nalog_status != 12 AND nalog_partner_active = 1 AND lg_partner_app = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nalog_id', $job_id);
        $stmt->bindParam(':lang', $lang);
        $stmt->execute();
        $result = $stmt->fetch();
        $data = [];

        $data['id'] = $result['nalog_id'];
        $data['title'] = $result['no_nalognaziv'];
        if($result['status_naloga'] == 1){
            if($lang == 'de'){
                $data['job_status'] = 'In Bearbeitung';
            } else if($lang == 'en'){
                $data['job_status'] = 'In progress';
            } else if($lang == 'bs'){
                $data['job_status'] = 'U obradi';
            }
        } else {
            if($lang == 'de'){
                $data['job_status'] = 'Abgeschlossen';
            } else if($lang == 'en'){
                $data['job_status'] = 'Completed';
            } else if($lang == 'bs'){
                $data['job_status'] = 'Završen';
            }
        }
        $linkEnv = "registration";
        Global $envConfig;
        if($envConfig->APP_ENV != "production"){
            $linkEnv = "registration2";
        }
        $data['location'] = $result['nalog_partner_app_location'];
        $data['until'] = date('Y-m-d', strtotime($result['nalog_partner_app_until']));
        $data['salary'] = $result['nalog_partner_app_salary'];
        $data['requested_candidates'] = $result['nalog_potrebno_kandidata'];
        $data['found_candidates'] = $result['found_candidates'];
        $data['link'] = "https://job-step.net/$linkEnv/".$result['lg_id']."/".$_SESSION['token']."/".$lang;
        $data['thumbnail'] = $result['idk_urlimg_prijave'];
        $data['description'] = $result['no_nalogopis'];
        $data['job_created_at'] = $result['lg_datetime'];
        
        return $data;
    }
}
