<?php
include("candidateAssessmentCalculation.php");
include("durationPerStatus.php");
include("candidateSavedProjection.php");
class candidatesProjection{

    private $candidatesProjectionDate = array();
    public $default_durations;
    public $load_projection_from_database;

    function __construct($default_duration, $candidate_id = NULL, $load_projection_from_database = FALSE) {
        $this -> default_durations = $default_duration;
        $this -> load_projection_from_database = $load_projection_from_database;

        $this -> populateCandidates($default_duration, $candidate_id);
    }

    public function getGroupDurations(){
        if(count($this->candidatesProjectionDate) === 1){
            return $this->candidatesProjectionDate[0]->getGroupDurations();
        }
    }
    public function getCandidateProjectionsForFinances(){
        $i = 0;
        foreach($this -> candidatesProjectionDate as $candidate){
            $start_date = $candidate -> getAgreedStartDate();
            if(is_null($start_date)){
                $potential_start_date = $candidate -> getPotentialStartDate();
                if(is_null($potential_start_date)){
                    $calculated_start_date = $candidate -> getCandidateProjectedDate();
                }else{
                    $calculated_start_date = $potential_start_date;
                }
               
                $return_array[$i]["candidate_id"]           = $candidate -> getCandidateID();
                $return_array[$i]["candidate_assessment"]   = date('Y-m-d', $calculated_start_date);
                $i++;
            }
        }
        return $return_array;
    }

    public function getCandidateProjectionRows(){

        $return_array = array();

        $i = 0;
        foreach($this -> candidatesProjectionDate as $candidate){
            $start_date = $candidate -> getAgreedStartDate();
            if(is_null($start_date)){
                $calculated_start_date = $candidate -> getCandidateProjectedDate();
                $start_date = $calculated_start_date;
            }
            $return_array[$i]["language_status"]        = $candidate -> getCandidateLanguageStatus();
            $return_array[$i]["language_level"]         = $candidate -> getCandidateLanguageLevel();
            $return_array[$i]["candidate_id"]           = $candidate -> getCandidateID();
            $return_array[$i]["candidate_key"]          = $candidate -> getCandidateKey();
            $return_array[$i]["candidate_assessment"]   = $start_date;
            $return_array[$i]["candidate_fullname"]     = $candidate -> getCandidateFullname();
            $return_array[$i]["sp_status"]              = $candidate -> getCandidateStatus();
            $return_array[$i]["nd_status"]              = $candidate -> getCandidateNostrificationStatus();
            $return_array[$i]["company_id"]             = $candidate -> getCandidateCompanyID();
            $return_array[$i]["company_name"]           = $candidate -> getCandidateCompanyName();
            $return_array[$i]["nalog_id"]               = $candidate -> getCandidateNalogID();
            $return_array[$i]["nalog_name"]             = $candidate -> getCandidateNalogName();
            $return_array[$i]["partner_id"]             = $candidate -> getCandidatePartnerID();
            $return_array[$i]["partner_name"]           = $candidate -> getCandidatePartnerName();
            $return_array[$i]["visa_acquired"]          = $start_date - (intval($this -> default_durations -> getDurationOfRecievedVisa()) * 86400);
            $return_array[$i]["candidate_position"]     = $this->getCandidatePosition($candidate -> getCandidateID());
            $i++;
        }
        
        return $return_array;

    }

    public function getCandidatePosition($candidate_id){
        Global $db;

        $query = $db -> prepare("
        SELECT 
        kandidat_pp_pozicija
        FROM
            idk_kandidati
        WHERE 
            kandidat_id = $candidate_id 
        ");

        $query -> execute();
        $row = $query -> fetch();

        return $row["kandidat_pp_pozicija"];
    }

    public function getCandidateProjectionRowsFiltered($nalog_id, $month, $year, $partner_id = NULL){
       
        $return_array = array();
        // var_dump($partner_id);
        // exit();
        $i = 0;
        foreach($this -> candidatesProjectionDate as $candidate){
            
            if((($candidate -> getCandidateNalogID() == $nalog_id OR $nalog_id == 0) AND $candidate -> getCandidateStartMonth() == $month AND $candidate -> getCandidateStartYear() == $year) AND ($candidate -> getCandidatePartnerID() == $partner_id OR is_null($partner_id))){
            //     var_dump('test');
            // exit();
                $start_date = $candidate -> getAgreedStartDate();
                if(is_null($start_date)){
                    $start_date = date('d.m.Y', $candidate -> getCandidateProjectedDate());
                }
                else{
                    $start_date = date('d.m.Y', $start_date);
                }
                $return_array[$i]["language_status"]        = $candidate -> getCandidateLanguageStatus();
                $return_array[$i]["language_level"]         = $candidate -> getCandidateLanguageLevel();
                $return_array[$i]["candidate_id"]           = $candidate -> getCandidateID();
                $return_array[$i]["candidate_key"]          = $candidate -> getCandidateKey();
                $return_array[$i]["candidate_assessment"]   = $start_date;
                $return_array[$i]["candidate_fullname"]     = $candidate -> getCandidateFullname();
                $return_array[$i]["sp_status"]              = $candidate -> getCandidateStatus();
                $return_array[$i]["nd_status"]              = $candidate -> getCandidateNostrificationStatus();
                $return_array[$i]["company_id"]             = $candidate -> getCandidateCompanyID();
                $return_array[$i]["company_name"]           = $candidate -> getCandidateCompanyName();
                $return_array[$i]["nalog_id"]               = $candidate -> getCandidateNalogID();
                $return_array[$i]["nalog_name"]             = $candidate -> getCandidateNalogName();
                $return_array[$i]["partner_id"]             = $candidate -> getCandidatePartnerID();
                $return_array[$i]["partner_name"]           = $candidate -> getCandidatePartnerName();
                $i++;
            }
        }
        return $return_array;

    }
    //NOT IN USE ANYWHERE
    public function getCandidateProjectionColumns(){

        $arr_candidate_id           = array();
        $arr_candidate_key          = array();
        $arr_candidate_assessment   = array();
        $arr_candidate_fullname     = array();
        $arr_sp_status              = array();
        $arr_nd_status              = array();
        $arr_language_status        = array();
        $arr_language_level         = array();
        $arr_company_id             = array();
        $arr_company_name           = array();
        $arr_nalog_id               = array();
        $arr_nalog_name             = array();
        $arr_partner_id             = array();
        $arr_partner_name           = array();

        foreach($this -> candidatesProjectionDate as $candidate){
            array_push($arr_candidate_id,           $candidate -> getCandidateID());
            array_push($arr_candidate_key,          $candidate -> getCandidateKey());
            array_push($arr_candidate_assessment,   $candidate -> getCandidateProjectedDate());
            array_push($arr_candidate_fullname,     $candidate -> getCandidateFullname());
            array_push($arr_sp_status,              $candidate -> getCandidateStatus());
            array_push($arr_nd_status,              $candidate -> getCandidateNostrificationStatus());
            array_push($arr_language_status,        $candidate -> getCandidateLanguageStatus());
            array_push($arr_language_level,         $candidate -> getCandidateLanguageLevel());
            array_push($arr_company_id,             $candidate -> getCandidateCompanyID());
            array_push($arr_company_name,           $candidate -> getCandidateCompanyName());
            array_push($arr_nalog_id,               $candidate -> getCandidateNalogID());
            array_push($arr_nalog_name,             $candidate -> getCandidateNalogName());
            array_push($arr_partner_id,             $candidate -> getCandidatePartnerID());
            array_push($arr_partner_name,           $candidate -> getCandidatePartnerName());
            
        }
        $return_array = array(
            "candidate_id"          =>  $arr_candidate_id,
            "candidate_key"         =>  $arr_candidate_key, 
            "candidate_assessment"  =>  $arr_candidate_assessment,
            "candidate_fullname"    =>  $arr_candidate_fullname,
            "sp_status"             =>  $arr_sp_status,
            "nd_status"             =>  $arr_nd_status,
            "language_status"       =>  $arr_language_status,
            "language_level"        =>  $arr_language_level,
            "company_id"            =>  $arr_company_id,
            "company_name"          =>  $arr_company_name,
            "nalog_id"              =>  $arr_nalog_id,
            "nalog_name"            =>  $arr_nalog_name,
            "partner_id"            =>  $arr_partner_id,
            "partner_name"          =>  $arr_partner_name
        );

        return $return_array;
    }
    public function test(){
        $return_val = "
            <table>
                <tr>
                    <td>ID</td>
                    <td>Prije potpisa ug</td>
                    <td>Nivo jezika</td>
                    <td>Jezik</td>
                    <td>Nostrifikacija</td>
                    <td>Status Nostrifikacije</td>
                    <td>Nakon potpisa</td>
                    <td>Status prijave</td>
                    <td>Pocetak rada za</td>
                    <td>Pocetak rada datum</td>
                    <td>nalog</td>
                    <td>partner</td>
                </tr>
        ";
        foreach($this -> candidatesProjectionDate as $test){
            $return_val .= "
                <tr>
                    <td>".$test -> getCandidateID()."</td>
                    <td>".$test -> getTrueDurationOfPreSignedContract()."</td>
                    <td>".$test -> getLanguageLevel()."</td>
                    <td>".$test -> getTrueDurationOfLanguage()."</td>
                    <td>".$test -> getTrueDurationOfNostrification()."</td>
                    <td>".$test -> getCandidateNostrificationStatus()."</td>
                    <td>".$test -> getTrueDurationOfAfterContractSigned()."</td>
                    <td>".$test -> getCandidateStatus()."</td>
                    <td>".$test -> getCandidateProjectedDate()."</td>
                    <td>".date("d.m.Y", $test -> getCandidateProjectedDate())."</td>
                    <td>".$test -> getCandidateNalogId()."</td>
                    <td>".$test -> getCandidatePartnerId()."</td>
                </tr>
            ";
        }
        $return_val .= "</table>";
        return $return_val;
    }
    public function getDetailedAssessment(){
        $return_val = array();
        foreach($this -> candidatesProjectionDate as $test){
            $projected_date = $test -> getCandidateProjectedDate();

            $return_val[1] =  $test -> getDetailedPreContractAssessment();
            $return_val[2] =  $test -> getDetailedNostrificationAssessment();
            $return_val[3] =  $test -> getDetailedLanguageAssessment();
            $return_val[4] =  $test -> getDetailedAfterContractAssessment();
            $start_date = $test -> getAgreedStartDate();
            if(is_null($start_date)){
                $start_date = date('d.m.Y', $projected_date);
            }
            else{
                $start_date = date('d.m.Y', $start_date);
            }
            $return_val[5] = $start_date;
        }
        return $return_val;
    }
    private function populateCandidates($default_duration, $candidate_id){
        Global $db;
        if($this -> load_projection_from_database){

            $candidates_all                     = array();
            $candidates_exist_in_cvl            = array();
            $candidates_doesnt_exist_in_kj      = array();
            $candidates_to_find_max_language    = array();
    
            $query_get_needed_candidates = $db -> prepare("
                SELECT 
                    kan.kandidat_id, 
                    cvl.exists_in_cvl, 
                    kj.exists_in_kj
                FROM(
                    SELECT sqkan.kandidat_id
                    FROM idk_kandidati sqkan
                    WHERE kandidat_status_prijave IN (7,8,9,12,15,18,21,24,27)
                )kan 
                
                LEFT JOIN (
                    SELECT sqkj.kj_kandidatid, 1 as exists_in_cvl
                    FROM idk_kandidat_jezici sqkj
                    WHERE sqkj.kj_id IN (
                        SELECT cvl.cvl_id
                        FROM idk_candidate_verified_languages cvl
                        WHERE cvl.cvl_active = 1
                    )
                )cvl
                ON 
                    cvl.kj_kandidatid = kan.kandidat_id
    
                LEFT JOIN (
                    SELECT ssqkj.kj_kandidatid, 1 as exists_in_kj
                        FROM idk_kandidat_jezici ssqkj
                    WHERE ssqkj.kj_naziv LIKE ('Njemački')
                )kj
                ON 
                    kj.kj_kandidatid = kan.kandidat_id
                AND 
                    cvl.exists_in_cvl IS NULL  
            ");
    
            $query_get_needed_candidates -> execute();
            while($row_get_needed_candidates = $query_get_needed_candidates -> fetch()){
                $cte_candidate_id   = $row_get_needed_candidates['kandidat_id'];
                $cte_exists_in_cvl  = $row_get_needed_candidates['exists_in_cvl'];
                $cte_existnt_in_kj  = $row_get_needed_candidates['exists_in_kj'];
                array_push($candidates_all, $cte_candidate_id);
                if($cte_exists_in_cvl == 1){
                    array_push($candidates_exist_in_cvl, $cte_candidate_id);
                }
                else if(is_null($cte_existnt_in_kj)){
                    array_push($candidates_doesnt_exist_in_kj, $cte_candidate_id);
                }
            
            }
    
            $candidates_to_find_max_language = array_diff($candidates_all, $candidates_exist_in_cvl, $candidates_doesnt_exist_in_kj);
    
            $condition_all                  = "";
            $condition_exists_in_cvl        = "";
            $condition_existnt_in_kj        = "";
            $condition_find_max_language    = "";
    
            if(!count($candidates_all)){
                array_push($candidates_all, '-1');
            }
            if(!count($candidates_exist_in_cvl)){
                array_push($candidates_exist_in_cvl, '-1');
            }
            if(!count($candidates_doesnt_exist_in_kj)){
                array_push($candidates_doesnt_exist_in_kj, '-1');
            }
            if(!count($candidates_to_find_max_language)){
                array_push($candidates_to_find_max_language, '-1');
            }
    

            $query_get_candidates = $db -> prepare("
                SELECT 
                    kan.kandidat_id, 
                    kan.kandidat_check,
                    CONCAT(kan.kandidat_ime , ' ', kan.kandidat_prezime) as candidate_fullname,
                    kan.kandidat_status_prijave,
                    nal.nalog_id, 
                    nal.nalog_naziv, 
                    comp.company_id, 
                    comp.company_name, 
                    partner.ppa_id as partner_id, 
                    partner.company_name as partner_name, 
                    kan.kandidat_pp_pozicija, 
                    CASE 
                        WHEN kan.kandidat_ima_nostrifikaciju = 1
                        THEN 6
                        ELSE
                        CASE 
                            WHEN ndkan.status_nd_kandidata IN(1,7) OR ndkan.status_nd_kandidata IS NULL
                            THEN 1
                            ELSE ndkan.status_nd_kandidata
                        END
                    END AS status_nd_kandidata,
                    CASE
                        WHEN kan.kandidat_dogovoreni_pocetak_rada IS NOT NULL
                        THEN kan.kandidat_dogovoreni_pocetak_rada
                        ELSE 
                            CASE 
                                WHEN kan.kandidat_potencijalni_pocetak_rada IS NOT NULL AND kan.kandidat_potencijalni_pocetak_rada != ''
                                THEN 
                                    CASE
                                        WHEN kan.kandidat_potencijalni_pocetak_rada LIKE '%to%'
                                        THEN STR_TO_DATE(CONCAT('1.', SUBSTRING_INDEX(kan.kandidat_potencijalni_pocetak_rada, 'to ', -1)), '%d.%m.%Y')
                                        ELSE STR_TO_DATE(CONCAT('1.',kan.kandidat_potencijalni_pocetak_rada), '%d.%m.%Y')
                                    END
                                ELSE projection.proracunati_pocetak_rada 
                            END
                    END as candidate_projection,
                    CASE 
                        WHEN table_languages.language_level = 0
                        THEN 0
                        WHEN table_languages.language_level = 1
                        THEN 'A1'
                        WHEN table_languages.language_level = 2
                        THEN 'A2'
                        WHEN table_languages.language_level = 3
                        THEN 'B1'
                        WHEN table_languages.language_level = 4
                        THEN 'B2'
                        WHEN table_languages.language_level = 5
                        THEN 'C1'
                        WHEN table_languages.language_level = 6
                        THEN 'C2'
                        ELSE 0
                    END as candidate_language_level,
                    table_languages.cvl_status

                FROM idk_kandidati kan
                LEFT JOIN idk_nd_kandidata ndkan
                ON ndkan.id_broj_nd_kandidata = kan.kandidat_dipl_id
                JOIN idk_nalozi nal
                ON nal.nalog_id = kan.kandidat_nalog_id
                JOIN idk_companies comp
                ON comp.company_id = nal.kompanija_id
                LEFT JOIN (
                    SELECT * 
                    FROM idk_pp_partners sqpp
                    JOIN idk_companies sqcomp
                    ON sqpp.ppa_company_id = sqcomp.company_id
                ) partner
                ON kan.kandidat_ppa_partner_id = partner.ppa_id
                LEFT JOIN idk_kandidat_projekcije projection
                ON projection.kandidat_id = kan.kandidat_id
                
                JOIN (
                    SELECT
                        set1_kj.kj_kandidatid,
                        set1_kj.language_level,
                        set1_kj.is_glossa as is_glossa,
                        set1_cvl.cvl_status,
                        set1_cvl.cvl_exam_date,
                        set1_cvl.jezik_dana_na_statusu,
                        set1_cvl.jezik_dana_na_statusu_za_kurs,
                        set1_cvl.cvl_last_updated,
                        set1_cvl.cvl_course1_started,
                        set1_cvl.cvl_course1_ended,
                        set1_cvl.cvl_course2_started,
                        set1_cvl.cvl_course2_ended,
                        set1_cvl.cvl_motive_start,
                        set1_cvl.cvl_motive_end

                    FROM(
                        SELECT
                            set1_sqcvl.cvl_id,
                            set1_sqcvl.cvl_status,
                            set1_sqcvl.cvl_exam_date,
                            set1_sqcvl.cvl_course1_started,
                            set1_sqcvl.cvl_course1_ended,
                            set1_sqcvl.cvl_course2_started,
                            set1_sqcvl.cvl_course2_ended,
                            set1_sqcvl.cvl_motive_start,
                            set1_sqcvl.cvl_motive_end,
                            CASE 
                                WHEN set1_sqcvl.cvl_status = 3  AND set1_sqcvl.cvl_course1_started IS  NOT NULL 
                                THEN  
                                    CASE 
                                        WHEN set1_sqcvl.cvl_course1_started  < CURRENT_TIME()
                                        THEN  DATEDIFF(CURRENT_TIME(), set1_sqcvl.cvl_course1_started)
                                        ELSE 0
                                    END

                                WHEN set1_sqcvl.cvl_status = 4  AND set1_sqcvl.cvl_course2_started IS  NOT NULL 
                                THEN
                                    CASE 
                                        WHEN set1_sqcvl.cvl_course2_started  < CURRENT_TIME()
                                        THEN  DATEDIFF(CURRENT_TIME(), set1_sqcvl.cvl_course2_started)
                                        ELSE 0
                                    END

                                ELSE DATEDIFF(CURRENT_TIME(), set1_sqcvl.cvl_last_updated)
                            END as jezik_dana_na_statusu,
                            DATEDIFF(CURRENT_TIME(), set1_sqcvl.cvl_last_updated) as jezik_dana_na_statusu_za_kurs,
                            set1_sqcvl.cvl_last_updated
                        FROM
                            idk_candidate_verified_languages set1_sqcvl
                        WHERE
                            set1_sqcvl.cvl_active = 1
                    ) set1_cvl
                    JOIN(
                        SELECT set1_sqkj.kj_id,
                            set1_sqkj.kj_kandidatid,
                            CASE 
                                WHEN set1_sqkj.kj_slusanje = 'C2'
                                THEN 6
                                WHEN set1_sqkj.kj_slusanje = 'C1'
                                THEN 5
                                WHEN set1_sqkj.kj_slusanje = 'B2'
                                THEN 4
                                WHEN set1_sqkj.kj_slusanje = 'B1'
                                THEN 3
                                WHEN set1_sqkj.kj_slusanje = 'A2'
                                THEN 2
                                WHEN set1_sqkj.kj_slusanje = 'A1'
                                THEN 1
                                ELSE 0
                            END as language_level,
                            set1_sqkj.kj_ustanova as is_glossa
                        FROM
                            idk_kandidat_jezici set1_sqkj
                        WHERE
                            set1_sqkj.kj_naziv LIKE('Njemački')
                        AND set1_sqkj.kj_kandidatid IN (".implode(',', $candidates_exist_in_cvl).")
                    ) set1_kj
                    ON
                        set1_kj.kj_id = set1_cvl.cvl_id

                    UNION

                    SELECT 
                        set2_kj.kj_kandidatid, 
                        max(set2_kj.language_level) as language_level, 
                        NULL as cvl_status, 
                        NULL as cvl_exam_date, 
                        NULL as jezik_dana_na_statusu,
                        NULL as jezik_dana_na_statusu_za_kurs,
                        NULL as cvl_last_updated,
                        set2_kj.is_glossa as is_glossa,
                        NULL as cvl_course1_started,
                        NULL as cvl_course1_ended,
                        NULL as cvl_course2_started,
                        NULL as cvl_course2_ended,
                        NULL as cvl_motive_start,
                        NULL as cvl_motive_end

                    FROM(
                        SELECT 
                            set2_sqkj.kj_id,
                            set2_sqkj.kj_kandidatid,
                            CASE 
                                WHEN set2_sqkj.kj_slusanje = 'C2'
                                THEN 6
                                WHEN set2_sqkj.kj_slusanje = 'C1'
                                THEN 5
                                WHEN set2_sqkj.kj_slusanje = 'B2'
                                THEN 4
                                WHEN set2_sqkj.kj_slusanje = 'B1'
                                THEN 3
                                WHEN set2_sqkj.kj_slusanje = 'A2'
                                THEN 2
                                WHEN set2_sqkj.kj_slusanje = 'A1'
                                THEN 1
                                ELSE 0
                            END as language_level,
                            set2_sqkj.kj_ustanova as is_glossa
                        FROM 
                            idk_kandidat_jezici set2_sqkj
                        WHERE 
                            set2_sqkj.kj_naziv LIKE ('Njemački')
                        AND set2_sqkj.kj_kandidatid IN (".implode(',', $candidates_to_find_max_language).")
                    )set2_kj
                    GROUP BY 
                        set2_kj.kj_kandidatid

                    UNION

                    SELECT 
                        set3_kan.kandidat_id, 
                        0 as language_level, 
                        NULL as cvl_status, 
                        NULL as cvl_exam_date, 
                        NULL as jezik_dana_na_statusu,
                        NULL as jezik_dana_na_statusu_za_kurs,
                        NULL as cvl_last_updated,
                        0 as is_glossa,
                        NULL as cvl_course1_started,
                        NULL as cvl_course1_ended,
                        NULL as cvl_course2_started,
                        NULL as cvl_course2_ended,
                        NULL as cvl_motive_start,
                        NULL as cvl_motive_end
                    FROM 
                        idk_kandidati set3_kan
                    WHERE 
                        set3_kan.kandidat_id IN(".implode(',', $candidates_doesnt_exist_in_kj).")
                ) table_languages

                ON table_languages.kj_kandidatid = kan.kandidat_id

                WHERE kan.kandidat_status_prijave IN (7,8,9,12,15,18,21,24,27)
                AND nal.pristup_poslodavcima = 1
            ");
            $query_get_candidates -> execute();
            
            while($row_get_candidates = $query_get_candidates -> fetch()){

                array_push($this -> candidatesProjectionDate, new candidateSavedProjection(
                    $row_get_candidates['kandidat_id'],
                    $row_get_candidates['kandidat_check'],
                    $row_get_candidates['candidate_fullname'],
                    $row_get_candidates['kandidat_status_prijave'],
                    $row_get_candidates['status_nd_kandidata'],
                    $row_get_candidates['company_id'],
                    $row_get_candidates['company_name'],
                    $row_get_candidates['nalog_id'],
                    $row_get_candidates['nalog_naziv'],
                    $row_get_candidates['partner_id'],
                    $row_get_candidates['partner_name'],
                    strtotime($row_get_candidates['candidate_projection']),
                    $row_get_candidates['kandidat_pp_pozicija'],
                    $row_get_candidates['candidate_language_level'],
                    $row_get_candidates['cvl_status']
                ));
            }
        }

        else{
            $condition = "";
            if(is_null($candidate_id)){
                $condition  = " 
                    WHERE sqkan.kandidat_status_prijave IN (7,8,9,12,15,18,21,24,27) 
                ";
            }
            else{
                $condition  = "WHERE sqkan.kandidat_id = ".$candidate_id;
            }
    
            $candidates_all                     = array();
            $candidates_exist_in_cvl            = array();
            $candidates_doesnt_exist_in_kj      = array();
            $candidates_to_find_max_language    = array();
    
            $query_get_needed_candidates = $db -> prepare("
                SELECT 
                    kan.kandidat_id, 
                    cvl.exists_in_cvl, 
                    kj.exists_in_kj
                FROM(
                    SELECT sqkan.kandidat_id
                    FROM idk_kandidati sqkan
                    ".$condition."                    
                )kan 
                
                LEFT JOIN (
                    SELECT sqkj.kj_kandidatid, 1 as exists_in_cvl
                    FROM idk_kandidat_jezici sqkj
                    WHERE sqkj.kj_id IN (
                        SELECT cvl.cvl_id
                        FROM idk_candidate_verified_languages cvl
                        WHERE cvl.cvl_active = 1
                    )
                )cvl
                ON 
                    cvl.kj_kandidatid = kan.kandidat_id
    
                LEFT JOIN (
                    SELECT ssqkj.kj_kandidatid, 1 as exists_in_kj
                        FROM idk_kandidat_jezici ssqkj
                    WHERE ssqkj.kj_naziv LIKE ('Njemački')
                )kj
                ON 
                    kj.kj_kandidatid = kan.kandidat_id
                AND 
                    cvl.exists_in_cvl IS NULL  
            ");
    
            $query_get_needed_candidates -> execute();
            while($row_get_needed_candidates = $query_get_needed_candidates -> fetch()){
                $cte_candidate_id   = $row_get_needed_candidates['kandidat_id'];
                $cte_exists_in_cvl  = $row_get_needed_candidates['exists_in_cvl'];
                $cte_existnt_in_kj  = $row_get_needed_candidates['exists_in_kj'];
                array_push($candidates_all, $cte_candidate_id);
                if($cte_exists_in_cvl == 1){
                    array_push($candidates_exist_in_cvl, $cte_candidate_id);
                }
                else if(is_null($cte_existnt_in_kj)){
                    array_push($candidates_doesnt_exist_in_kj, $cte_candidate_id);
                }
            
            }
    
            $candidates_to_find_max_language = array_diff($candidates_all, $candidates_exist_in_cvl, $candidates_doesnt_exist_in_kj);
    
            $condition_all                  = "";
            $condition_exists_in_cvl        = "";
            $condition_existnt_in_kj        = "";
            $condition_find_max_language    = "";
    
            if(!count($candidates_all)){
                array_push($candidates_all, '-1');
            }
            if(!count($candidates_exist_in_cvl)){
                array_push($candidates_exist_in_cvl, '-1');
            }
            if(!count($candidates_doesnt_exist_in_kj)){
                array_push($candidates_doesnt_exist_in_kj, '-1');
            }
            if(!count($candidates_to_find_max_language)){
                array_push($candidates_to_find_max_language, '-1');
            }
    
            $query_get_candidates = $db -> prepare("
                SELECT 	
                    table_to_filter.*,
                    DATEDIFF(CURRENT_TIME(), ndlog.vrijeme_promjene_statusa_nd_kandidata) as nd_dana_na_statusu,
                    CASE 
                        WHEN table_to_filter.kandidat_ima_nostrifikaciju = 1
                        THEN 6
                        ELSE
                            CASE 
                                WHEN nd.status_nd_kandidata IN(1,7) OR nd.status_nd_kandidata IS NULL
                                THEN 1
                                ELSE nd.status_nd_kandidata
                            END
                    END AS status_nd_kandidata
                FROM(
                    SELECT
                        kan.kandidat_id,
                        kan.kandidat_dipl_id,
                        kan.kandidat_ima_nostrifikaciju,
                        kan.kandidat_status_prijave,
                        kan.kandidat_check,
                        DATEDIFF(CURRENT_TIME(), kan.kandidat_latest_reserved_time) as sp_dana_na_statusu,
                        kan.candidate_fullname,
                        kan.kandidat_nalog_id,
                        kan.company_id,
                        kan.company_name,
                        kan.candidate_partner_id,
                        kan.candidate_partner_name,
                        kan.nalog_id,
                        kan.nalog_naziv,
                        kan.kandidat_dogovoreni_pocetak_rada,
                        kan.potencijalni_pocetak_rada_formatted,
                        kan.kandidat_nacin_odlaska,
                        kan.datum_termina,
                        CASE
                            WHEN kan.kandidat_drzavljanstvo_vrsta LIKE ('EU državljanin')
                            THEN 1
                            ELSE 0
                        END as is_eu_candidate,
    
                        CASE 
                            WHEN table_languages.language_level = 0
                            THEN 0
                            WHEN table_languages.language_level = 1
                            THEN 'A1'
                            WHEN table_languages.language_level = 2
                            THEN 'A2'
                            WHEN table_languages.language_level = 3
                            THEN 'B1'
                            WHEN table_languages.language_level = 4
                            THEN 'B2'
                            WHEN table_languages.language_level = 5
                            THEN 'C1'
                            WHEN table_languages.language_level = 6
                            THEN 'C2'
                            ELSE 0
                        END as candidate_language_level,
    
                        table_languages.cvl_status, 
                        table_languages.cvl_exam_date,
                        table_languages.jezik_dana_na_statusu,
                        table_languages.jezik_dana_na_statusu_za_kurs,
                        table_languages.cvl_last_updated,
                        table_languages.is_glossa,
                        table_languages.cvl_course1_started,
                        table_languages.cvl_course1_ended,
                        table_languages.cvl_course2_started,
                        table_languages.cvl_course2_ended,
                        table_languages.cvl_motive_start,
                        table_languages.cvl_motive_end,
                        kan.has_interview
    
                    FROM(
                        SELECT
                            sqkan.kandidat_id,
                            sqkan.kandidat_status_prijave,
                            sqkan.kandidat_dipl_id,
                            sqkan.kandidat_ima_nostrifikaciju,
                            sqkan.kandidat_latest_reserved_time,
                            sqkan.kandidat_check,
                            sqkan.kandidat_nalog_id,
                            sqkan.kandidat_dogovoreni_pocetak_rada,
                            CASE 
                                WHEN sqkan.kandidat_potencijalni_pocetak_rada IS NOT NULL 
                                THEN 
                                    CASE
                                        WHEN sqkan.kandidat_potencijalni_pocetak_rada LIKE '%to%'
                                        THEN STR_TO_DATE(CONCAT('1.', SUBSTRING_INDEX(sqkan.kandidat_potencijalni_pocetak_rada, 'to ', -1)), '%d.%m.%Y')
                                        ELSE STR_TO_DATE(CONCAT('1.',sqkan.kandidat_potencijalni_pocetak_rada), '%d.%m.%Y')
                                    END
                                ELSE null
                            END as potencijalni_pocetak_rada_formatted,
                            sqkan.kandidat_drzavljanstvo_vrsta,
                            sqkan.kandidat_nacin_odlaska,
                            sqkan.datum_termina,
                            CONCAT(sqkan.kandidat_ime , ' ', sqkan.kandidat_prezime) as candidate_fullname,
                            com.company_id,
                            com.company_name,
                            partner_company.candidate_partner_id,
                            partner_company.candidate_partner_name,
                            nal.nalog_id,
                            nal.nalog_naziv,
                            CASE 
                                WHEN pr.project_id IS NULL
                                THEN 0
                                ELSE 1
                            END as has_interview 
    
                        FROM
                            idk_kandidati sqkan
                        LEFT JOIN (
                            SELECT 
                                sqn.nalog_id, 
                                sqn.kompanija_id, 
                                sqn.nalog_naziv
                            FROM 
                                idk_nalozi sqn
                        )nal
                        ON 
                            nal.nalog_id = sqkan.kandidat_nalog_id
                        LEFT JOIN (
                            SELECT sqlsp.lsp_kandidat_id, sqlsp.lsp_projekt_id
                            FROM idk_log_statusi_prijave sqlsp
                            WHERE sqlsp.lsp_broj_dana  IS NULL
                        )lsp
                        ON lsp.lsp_kandidat_id = sqkan.kandidat_id
                        LEFT JOIN (
                            SELECT sqpr.project_id
                            FROM idk_projects sqpr
                            WHERE sqpr.project_name LIKE ('%Intervju%')
                        )pr
                        ON pr.project_id = lsp.lsp_projekt_id
                        LEFT JOIN (
                            SELECT sqcom.company_id, sqcom.company_name
                            FROM idk_companies sqcom
                        )com
                        ON 
                            com.company_id = nal.kompanija_id
                        
                        LEFT JOIN (
                            SELECT sqppa.ppa_id, sqppa.ppa_company_id
                            FROM idk_pp_partners sqppa
                        )ppa
                        ON 
                            ppa.ppa_id = sqkan.kandidat_ppa_partner_id
                        
                        LEFT JOIN (
                            SELECT sqppacom.company_id as candidate_partner_id, sqppacom.company_name as candidate_partner_name
                            FROM idk_companies sqppacom
                        )partner_company
                        ON 
                            ppa.ppa_company_id = partner_company.candidate_partner_id
                        
                        
                        WHERE
                            sqkan.kandidat_id IN (".implode(',', $candidates_all).")                
                    ) kan
                    JOIN (
                        SELECT
                            set1_kj.kj_kandidatid,
                            set1_kj.language_level,
                            set1_kj.is_glossa as is_glossa,
                            set1_cvl.cvl_status,
                            set1_cvl.cvl_exam_date,
                            set1_cvl.jezik_dana_na_statusu,
                            set1_cvl.jezik_dana_na_statusu_za_kurs,
                            set1_cvl.cvl_last_updated,
                            set1_cvl.cvl_course1_started,
                            set1_cvl.cvl_course1_ended,
                            set1_cvl.cvl_course2_started,
                            set1_cvl.cvl_course2_ended,
                            set1_cvl.cvl_motive_start,
                            set1_cvl.cvl_motive_end
    
                        FROM(
                            SELECT
                                set1_sqcvl.cvl_id,
                                set1_sqcvl.cvl_status,
                                set1_sqcvl.cvl_exam_date,
                                set1_sqcvl.cvl_course1_started,
                                set1_sqcvl.cvl_course1_ended,
                                set1_sqcvl.cvl_course2_started,
                                set1_sqcvl.cvl_course2_ended,
                                set1_sqcvl.cvl_motive_start,
                                set1_sqcvl.cvl_motive_end,
                                CASE 
                                    WHEN set1_sqcvl.cvl_status = 3  AND set1_sqcvl.cvl_course1_started IS  NOT NULL 
                                    THEN  
                                        CASE 
                                            WHEN set1_sqcvl.cvl_course1_started  < CURRENT_TIME()
                                            THEN  DATEDIFF(CURRENT_TIME(), set1_sqcvl.cvl_course1_started)
                                            ELSE 0
                                        END
    
                                    WHEN set1_sqcvl.cvl_status = 4  AND set1_sqcvl.cvl_course2_started IS  NOT NULL 
                                    THEN
                                        CASE 
                                            WHEN set1_sqcvl.cvl_course2_started  < CURRENT_TIME()
                                            THEN  DATEDIFF(CURRENT_TIME(), set1_sqcvl.cvl_course2_started)
                                            ELSE 0
                                        END
    
                                    ELSE DATEDIFF(CURRENT_TIME(), set1_sqcvl.cvl_last_updated)
                                END as jezik_dana_na_statusu,
                                DATEDIFF(CURRENT_TIME(), set1_sqcvl.cvl_last_updated) as jezik_dana_na_statusu_za_kurs,
                                set1_sqcvl.cvl_last_updated
                            FROM
                                idk_candidate_verified_languages set1_sqcvl
                            WHERE
                                set1_sqcvl.cvl_active = 1
                        ) set1_cvl
                        JOIN(
                            SELECT set1_sqkj.kj_id,
                                set1_sqkj.kj_kandidatid,
                                CASE 
                                    WHEN set1_sqkj.kj_slusanje = 'C2'
                                    THEN 6
                                    WHEN set1_sqkj.kj_slusanje = 'C1'
                                    THEN 5
                                    WHEN set1_sqkj.kj_slusanje = 'B2'
                                    THEN 4
                                    WHEN set1_sqkj.kj_slusanje = 'B1'
                                    THEN 3
                                    WHEN set1_sqkj.kj_slusanje = 'A2'
                                    THEN 2
                                    WHEN set1_sqkj.kj_slusanje = 'A1'
                                    THEN 1
                                    ELSE 0
                                END as language_level,
                                set1_sqkj.kj_ustanova as is_glossa
                            FROM
                                idk_kandidat_jezici set1_sqkj
                            WHERE
                                set1_sqkj.kj_naziv LIKE('Njemački')
                            AND set1_sqkj.kj_kandidatid IN (".implode(',', $candidates_exist_in_cvl).")
                        ) set1_kj
                        ON
                            set1_kj.kj_id = set1_cvl.cvl_id
    
                        UNION
    
                        SELECT 
                            set2_kj.kj_kandidatid, 
                            max(set2_kj.language_level) as language_level, 
                            NULL as cvl_status, 
                            NULL as cvl_exam_date, 
                            NULL as jezik_dana_na_statusu,
                            NULL as jezik_dana_na_statusu_za_kurs,
                            NULL as cvl_last_updated,
                            set2_kj.is_glossa as is_glossa,
                            NULL as cvl_course1_started,
                            NULL as cvl_course1_ended,
                            NULL as cvl_course2_started,
                            NULL as cvl_course2_ended,
                            NULL as cvl_motive_start,
                            NULL as cvl_motive_end
    
                        FROM(
                            SELECT 
                                set2_sqkj.kj_id,
                                set2_sqkj.kj_kandidatid,
                                CASE 
                                    WHEN set2_sqkj.kj_slusanje = 'C2'
                                    THEN 6
                                    WHEN set2_sqkj.kj_slusanje = 'C1'
                                    THEN 5
                                    WHEN set2_sqkj.kj_slusanje = 'B2'
                                    THEN 4
                                    WHEN set2_sqkj.kj_slusanje = 'B1'
                                    THEN 3
                                    WHEN set2_sqkj.kj_slusanje = 'A2'
                                    THEN 2
                                    WHEN set2_sqkj.kj_slusanje = 'A1'
                                    THEN 1
                                    ELSE 0
                                END as language_level,
                                set2_sqkj.kj_ustanova as is_glossa
                            FROM 
                                idk_kandidat_jezici set2_sqkj
                            WHERE 
                                set2_sqkj.kj_naziv LIKE ('Njemački')
                            AND set2_sqkj.kj_kandidatid IN (".implode(',', $candidates_to_find_max_language).")
                        )set2_kj
                        GROUP BY 
                            set2_kj.kj_kandidatid
    
                        UNION
    
                        SELECT 
                            set3_kan.kandidat_id, 
                            0 as language_level, 
                            NULL as cvl_status, 
                            NULL as cvl_exam_date, 
                            NULL as jezik_dana_na_statusu,
                            NULL as jezik_dana_na_statusu_za_kurs,
                            NULL as cvl_last_updated,
                            0 as is_glossa,
                            NULL as cvl_course1_started,
                            NULL as cvl_course1_ended,
                            NULL as cvl_course2_started,
                            NULL as cvl_course2_ended,
                            NULL as cvl_motive_start,
                            NULL as cvl_motive_end
                        FROM 
                            idk_kandidati set3_kan
                        WHERE 
                            set3_kan.kandidat_id IN(".implode(',', $candidates_doesnt_exist_in_kj).")
                    ) table_languages
                    ON 
                        table_languages.kj_kandidatid = kan.kandidat_id
                ) table_to_filter
    
                LEFT JOIN idk_nd_kandidata nd
                ON 
                    table_to_filter.kandidat_dipl_id = nd.id_broj_nd_kandidata
    
                LEFT JOIN (
                    SELECT 
                        sqndlog.idd_broj_nd_kandidata, 
                        sqndlog.vrijeme_promjene_statusa_nd_kandidata
                    FROM 
                        idk_nd_kandidata_status_log sqndlog
                    WHERE 
                        sqndlog.broj_dana_statusa_nd_kandidata IS NULL
                    ORDER BY(sqndlog.id_log_status_nd_kandidata) DESC
                )ndlog
                ON ndlog.idd_broj_nd_kandidata = nd.id_broj_nd_kandidata
                GROUP BY table_to_filter.kandidat_id
            ");

            $query_get_candidates -> execute();
            // var_dump($this -> load_projection_from_database);
            while($row_get_candidates = $query_get_candidates -> fetch()){

                array_push($this -> candidatesProjectionDate, new candidateAssessmentCalculation(
                    $row_get_candidates['kandidat_id'],
                    $row_get_candidates['kandidat_status_prijave'],
                    $row_get_candidates['sp_dana_na_statusu'],
                    $row_get_candidates['status_nd_kandidata'],
                    $row_get_candidates['nd_dana_na_statusu'],
                    $row_get_candidates['candidate_language_level'],
                    $row_get_candidates['cvl_status'],
                    $row_get_candidates['jezik_dana_na_statusu'],
                    $row_get_candidates['cvl_exam_date'],
                    $default_duration,
                    $row_get_candidates['kandidat_check'],
                    $row_get_candidates['candidate_fullname'],
                    $row_get_candidates['company_id'],
                    $row_get_candidates['company_name'],
                    $row_get_candidates['nalog_id'],
                    $row_get_candidates['nalog_naziv'],
                    $row_get_candidates['candidate_partner_id'],
                    $row_get_candidates['candidate_partner_name'],
                    $row_get_candidates['has_interview'],
                    $row_get_candidates['kandidat_dipl_id'],
                    $row_get_candidates['is_glossa'],
                    $row_get_candidates['cvl_course1_started'],
                    $row_get_candidates['cvl_course1_ended'],
                    $row_get_candidates['cvl_course2_started'],
                    $row_get_candidates['cvl_course2_ended'],
                    $row_get_candidates['cvl_last_updated'],
                    $row_get_candidates['jezik_dana_na_statusu_za_kurs'],
                    $row_get_candidates['kandidat_dogovoreni_pocetak_rada'],
                    $row_get_candidates['potencijalni_pocetak_rada_formatted'],
                    $row_get_candidates['is_eu_candidate'],
                    $row_get_candidates['kandidat_nacin_odlaska'],
                    $row_get_candidates['datum_termina'],
                    $row_get_candidates['cvl_motive_start'],
                    $row_get_candidates['cvl_motive_end']
    
                ));
            }
        }}
       

}
// $durationPerStatus = new durationPerStatus();


//ZA PROJEKCIJU JEDNOG KANDIDATA
//$candidatesProjection = new candidatesProjection($durationPerStatus, 10083);

//ZA PROJEKCIJU SVIH KANDIDATA ZA KANDIDATE OD STATUSA ČEKA UGOVOR PA DALJE U NALOZIMA KOJI SU NA PPu 
//$candidatesProjection = new candidatesProjection($durationPerStatus);

//ZA VRAĆANJE OBJEKTA SA REDOVIMA REZULTATA (mirza ova fja tebi treba)
//$candidatesProjection -> getCandidateProjectionRows();

//ZA VRAĆANJE OBJEKTA SA KOLONAMA REZULTATA (mozda ni meni ne treba al neka je)
//$candidatesProjection -> getCandidateProjectionRows();

// ZA VRAĆANJE FILTRIRANE LISTE PO nalog_id, month, year I OPCIONALNO partner_id
//$candidatesProjection -> getCandidateProjectionRowsFiltered(nalog_id, month, year I OPCIONALNO partner_id);

//ZA TABELARNI ISPIS ZA TESTIRANJE
// $candidatesProjection -> test();
?>
