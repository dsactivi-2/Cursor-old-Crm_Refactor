<?php

class Gateway
{
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function checkClient(string $token)
    {
        $sql = "SELECT client_id FROM idk_api_clients WHERE client_token = :token";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":token", $token, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch();
        
        if($result == NULL)
        {
            http_response_code(401);
            throw new Exception("Client not authorized. Check token!", 401);
        } 
    }

    public function checkCandidate(string $candidate_id)
    {
        $sql = "SELECT kandidat_id FROM idk_kandidati WHERE kandidat_check = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $candidate_id, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch();

        if($result != NULL)
        {
            return intval($result["kandidat_id"]);
        }
        else 
        {
            http_response_code(400);
            throw new Exception("Candidate does not exist!", 400);

        }
    }

    public function checkLanguage(int $candidate_id, string $procjena): int
    {
        $sql = "SELECT kj_id FROM idk_kandidat_jezici WHERE kj_naziv LIKE '%Njemacki%' AND kj_slusanje = :procjena AND kj_kandidatid = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":procjena", $procjena, PDO::PARAM_STR);
        $stmt->bindValue(":id", $candidate_id, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetch();

        return intval($result["kj_id"]);
    }

    public function insertNewLanguage(int $candidate_id, string $procjena): void
    {
        $sql = "INSERT INTO 
                    idk_kandidat_jezici 
                    (
                        kj_naziv, 
                        kj_slusanje, 
                        kj_citanje, 
                        kj_govorna_interakcija, 
                        kj_govorna_produkcija, 
                        kj_pisanje, 
                        kj_kandidatid, 
                        kj_ustanova
                    )
                VALUES
                    (
                       :kj_naziv, 
                       :kj_slusanje, 
                       :kj_citanje, 
                       :kj_govorna_interakcija, 
                       :kj_govorna_produkcija, 
                       :kj_pisanje, 
                       :kj_kandidatid, 
                       :kj_ustanova
                    )";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":kj_naziv", "Njemački", PDO::PARAM_STR);
        $stmt->bindValue(":kj_slusanje", $procjena, PDO::PARAM_STR);
        $stmt->bindValue(":kj_citanje", $procjena, PDO::PARAM_STR);
        $stmt->bindValue(":kj_govorna_interakcija", $procjena, PDO::PARAM_STR);
        $stmt->bindValue(":kj_govorna_produkcija", $procjena, PDO::PARAM_STR);
        $stmt->bindValue(":kj_pisanje", $procjena, PDO::PARAM_STR);
        $stmt->bindValue(":kj_kandidatid", $candidate_id, PDO::PARAM_INT);
        $stmt->bindValue(":kj_ustanova", 1, PDO::PARAM_INT);

        $stmt->execute();

        $language_id = $this->conn->lastInsertId();
        $cvl_id      = $this->getActiveVerifiedLanguage($candidate_id);

        if($cvl_id != 0)
        {
            $this->disableVerifiedLanguage($cvl_id);
        }

        $this->insertVerifiedLanguage($language_id);
        $this->updateCandidateProjectionAndInstallment($candidate_id);
    }

    public function updateExistingLanguage(int $language_id, int $candidate_id): void
    {
        $sql = "UPDATE idk_kandidat_jezici SET kj_ustanova = 1 WHERE kj_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $language_id, PDO::PARAM_INT);
        $stmt->execute();

        $cvl_id     = $this->findThisVerifiedLanguage($language_id);
        $cvl_active = $this->getActiveVerifiedLanguage($candidate_id);

        if($language_id == $cvl_id && $cvl_id == $cvl_active)
        {
            $this->updateVerifiedLanguage($language_id);
        }
        else if($language_id == $cvl_id && $cvl_id != $cvl_active)
        {
            $this->disableVerifiedLanguage($cvl_active);
            $this->updateVerifiedLanguage($language_id);
        }
        else
        {
            $this->disableVerifiedLanguage($cvl_active);
            $this->insertVerifiedLanguage($language_id);
        }
        $this->updateCandidateProjectionAndInstallment($candidate_id);
    }

    public function addStartDate($start_date, $candidate_id, $lang_level, $sub_level)
    {
        $cvl_id   = $this->getActiveVerifiedLanguage($candidate_id);
        if($cvl_id != 0)
        {
            $cvl_lvl  = $this->getCvlValue($cvl_id);
        }
        else
        {
            $cvl_lvl = "";
        }
        $compared = $this->compareLangLvls($cvl_lvl, $lang_level);
        
        if($compared)
        {
            //update existing cvl
            $this->updateSubLvlStartDate($sub_level, $cvl_id, $start_date);
        }
        else
        {
            //deactivate existing cvl and insert/update based on lang_level
            $this->newSubLevel($candidate_id, $cvl_id, $lang_level, $start_date, $sub_level);
        }

        $this->updateCandidateProjectionAndInstallment($candidate_id);
    }

    public function addStartDateMotive($start_date, $candidate_id, $lang_level)
    {
        $cvl_id   = $this->getActiveVerifiedLanguage($candidate_id);
        if($cvl_id != 0)
        {
            $cvl_lvl  = $this->getCvlValue($cvl_id);
        }
        else
        {
            $cvl_lvl = "";
        }
        $compared = $this->compareLangLvls($cvl_lvl, $lang_level);

        if($compared)
        {
            //update existing cvl
            $this->updateMotiveLvlStartDate($cvl_id, $start_date, $cvl_lvl);
        }
        else
        {
            //deactivate existing cvl and insert/update based on lang_level
            $this->newMotiveLvl($candidate_id, $cvl_id, $lang_level, $start_date);
        }
        $this->updateCandidateProjectionAndInstallment($candidate_id);
    }

    public function addMotiveEndDate($end_date, $candidate_id, $lang_level)
    {
        $cvl_id = $this->getActiveVerifiedLanguage($candidate_id);
        $cvl_lvl  = $this->getCvlValue($cvl_id);
        $compared = $this->compareLangLvls($cvl_lvl, $lang_level);

        if($compared == false)
        {
            http_response_code(400);
            throw new Exception("Sent data is not correct!", 400);
        }

        $this->updateMotiveEndDate($cvl_id, $end_date);
        $this->updateCandidateProjectionAndInstallment($candidate_id);
    }

    public function addEndDate($end_date, $candidate_id, $lang_level, $sub_level)
    {

        $cvl_id   = $this->getActiveVerifiedLanguage($candidate_id);
        $cvl_lvl  = $this->getCvlValue($cvl_id);
        $compared = $this->compareLangLvls($cvl_lvl, $lang_level);

        if($compared == false)
        {
            http_response_code(400);
            throw new Exception("Sent data is not correct!", 400);
        }

        $this->updateSubLvlEndDate($cvl_id, $sub_level, $end_date);

        $this->updateCandidateProjectionAndInstallment($candidate_id);
    }

    public function candidateQuit(string $candidate_id)
    {
        $id = $this->checkCandidate($candidate_id);
        $cvl_id = $this->getActiveVerifiedLanguage($id);

        if($cvl_id != 0)
        {
            $this->cvlQuit($cvl_id);
            $this->addNote($id);

            $this->updateCandidateProjectionAndInstallment($id);
        }
        else
        {
            http_response_code(400);
            throw new Exception("Candidate has no active verified language!", 400);
        }
    }

    /************************************************************** 
                //----START OF PRIVATE FUNCTIONS---//
    ***************************************************************/


    private function cvlQuit(int $cvl_id)
    {
        $updated_date = date("Y-m-d");

        $sql = "UPDATE idk_candidate_verified_languages SET cvl_status = 12, cvl_last_updated = :updated_date WHERE cvl_id = :cvl_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":cvl_id", $cvl_id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function addNote(int $candidate_id)
    {
        $note_txt      = "Kandidat odustao od pohadjanja kursa";
        $note_datetime = date('Y-m-d H:i:s');
        $note_group    = 2;
        $employee      = 139;

        $sql = "INSERT INTO 
                    idk_notes
						(
                            note_txt, 
                            note_datetime, 
                            note_group, 
                            note_dataid, 
                            note_employeeid
                        )
				VALUES
						(
                            :note_txt, 
                            :note_datetime, 
                            :note_group, 
                            :note_dataid, 
                            :note_employeeid
                        )";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":note_txt", $note_txt, PDO::PARAM_STR);
        $stmt->bindValue(":note_datetime", $note_datetime, PDO::PARAM_STR);
        $stmt->bindValue(":note_group", $note_group, PDO::PARAM_INT);
        $stmt->bindValue(":note_dataid", $candidate_id, PDO::PARAM_INT);
        $stmt->bindValue(":note_employeeid", $employee, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function getActiveVerifiedLanguage(int $candidate_id): int
    {
        $sql = "SELECT 
                    cvl_id 
                FROM 
                    idk_candidate_verified_languages 
                JOIN 
                    idk_kandidat_jezici 
                ON 
                    idk_candidate_verified_languages.cvl_id = idk_kandidat_jezici.kj_id 
                WHERE 
                    cvl_active = 1
                AND
                    kj_kandidatid = :kandidat_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":kandidat_id", $candidate_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return intval($result["cvl_id"]);
    }

    private function insertVerifiedLanguage(int $language_id): void
    {
        $updated_date = date("Y-m-d");

        $sql = "INSERT INTO idk_candidate_verified_languages (cvl_id, cvl_status, cvl_active, cvl_last_updated) VALUES (:cvl_id, 1, 1, :updated_date)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":cvl_id", $language_id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();

    }

    private function updateVerifiedLanguage(int $language_id): void
    {
        $updated_date = date("Y-m-d");

        $sql = "UPDATE idk_candidate_verified_languages SET cvl_status = 1, cvl_active = 1, cvl_last_updated = :updated_date WHERE cvl_id = :cvl_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":cvl_id", $language_id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function disableVerifiedLanguage(int $cvl_id): void
    {
        $updated_date = date("Y-m-d");

        $sql = "UPDATE 
                    idk_candidate_verified_languages 
                SET 
                    cvl_active = 0,
                    cvl_last_updated = :updated_date
                WHERE 
                    cvl_id IN (:cvl_id)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":cvl_id", $cvl_id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function findThisVerifiedLanguage(int $language_id): int
    {
        $sql = "SELECT cvl_id FROM idk_candidate_verified_languages WHERE cvl_id = :cvl_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":cvl_id", $language_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return intval($result["cvl_id"]);
    }


     ////////////////////////////////////////////////////////////////////
    ///----START OF PRIVATE FUNCTIONS USED FOR ENDPOINT "Pocetak"----/// 
   ////////////////////////////////////////////////////////////////////

    private function getCvlValue(int $cvl_id): string
    {
        $sql = "SELECT kj_slusanje FROM idk_kandidat_jezici WHERE kj_id = :kj_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":kj_id", $cvl_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result["kj_slusanje"];
    }

    private function compareLangLvls(string $cvl_lvl, string $lang_lvl)
    {
        if($cvl_lvl == $lang_lvl)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    private function updateSubLvlStartDate(int $sub_level, int $cvl_id, string $date)
    {
        if($sub_level == 1)
        {
            $this->updateSubLvlOneStartDate($date, $cvl_id);
        }
        else
        {
            $this->updateSubLvlTwoStartDate($date, $cvl_id);
        }
    }

    private function updateSubLvlOneStartDate(string $date, int $cvl_id): void
    {
        $updated_date = date("Y-m-d");

        $sql = "UPDATE idk_candidate_verified_languages SET cvl_course1_started = :date_start, cvl_status = 3, cvl_last_updated = :updated_date WHERE cvl_id = :cvl_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":date_start", $date, PDO::PARAM_STR);
        $stmt->bindValue(":cvl_id", $cvl_id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function updateSubLvlTwoStartDate(string $date, int $cvl_id): void
    {
        $updated_date = date("Y-m-d");

        $sql = "UPDATE idk_candidate_verified_languages SET cvl_course2_started = :date_start, cvl_status = 4, cvl_last_updated = :updated_date WHERE cvl_id = :cvl_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":date_start", $date, PDO::PARAM_STR);
        $stmt->bindValue(":cvl_id", $cvl_id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function getLanguageIdByLvl(string $lang_level, int $candidate_id): int
    {
        $sql = "SELECT kj_id FROM idk_kandidat_jezici WHERE kj_slusanje LIKE :lang_level AND kj_naziv LIKE '%Njemacki%' AND kj_kandidatid = :candidate_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":lang_level", $lang_level, PDO::PARAM_STR);
        $stmt->bindValue(":candidate_id", $candidate_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();

        if($result != null)
        {
            return intval($result["kj_id"]);
        }
        else 
        {
            return 0;
        }
    }

    private function newSubLevel(int $candidate_id, int $cvl_id, string $lang_level, string $date, int $sub_level)
    {
        $lang_id = $this->getLanguageIdByLvl($lang_level, $candidate_id);

        $this->disableVerifiedLanguage($cvl_id);

        if($lang_id != 0)
        {
            $this->updateSubLvlLanguage($lang_id);
            $new_cvl = $this->findThisVerifiedLanguage($lang_id);

            if($new_cvl != 0)
            {
                $this->updateCvlSubLvl($sub_level, $cvl_id, $date);
            }
            else
            {
                $this->insertCvlSubLvl($lang_id, $sub_level, $date);
            }
        }
        else
        {
            $this->insertLanguageSubLvl($candidate_id, $sub_level, $lang_level, $date);
        }
    }

    private function updateSubLvlLanguage(int $lang_id)
    {
        $sql  = "UPDATE idk_kandidat_jezici SET kj_ustanova = 1 WHERE kj_id = :kj_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":kj_id", $lang_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function updateCvlSubLvl(int $sub_level, int $cvl_id, string $start_date)
    {
        if($sub_level == 1)
        {
            $this->updateCvlSubLvlOneStart($cvl_id, $start_date);
        }
        else
        {
            $this->updateCvlSubLvlTwoStart($cvl_id, $start_date);
        }
    }

    private function updateCvlSubLvlOneStart(int $cvl_id, string $start_date)
    {
        $updated_date = date("Y-m-d");

        $sql = "UPDATE idk_candidate_verified_languages SET cvl_active = 1, cvl_status = 3, cvl_course1_started = :date_start, cvl_last_updated = :updated_date WHERE cvl_id = :cvl_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":date_start", $start_date, PDO::PARAM_STR);
        $stmt->bindValue(":cvl_id", $cvl_id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function updateCvlSubLvlTwoStart(int $cvl_id, string $start_date)
    {
        $updated_date = date("Y-m-d");

        $sql = "UPDATE idk_candidate_verified_languages SET cvl_active = 1, cvl_status = 4, cvl_course1_started = :date_start, cvl_last_updated = :updated_date WHERE cvl_id = :cvl_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":date_start", $start_date, PDO::PARAM_STR);
        $stmt->bindValue(":cvl_id", $cvl_id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function insertLanguageSubLvl(int $candidate_id, int $sub_level ,string $lang_level, string $start_date)
    {
        $sql = "INSERT INTO 
                    idk_kandidat_jezici 
                    (
                        kj_naziv, 
                        kj_slusanje, 
                        kj_citanje, 
                        kj_govorna_interakcija, 
                        kj_govorna_produkcija, 
                        kj_pisanje, 
                        kj_kandidatid, 
                        kj_ustanova
                    )
                VALUES
                    (
                       :kj_naziv, 
                       :kj_slusanje, 
                       :kj_citanje, 
                       :kj_govorna_interakcija, 
                       :kj_govorna_produkcija, 
                       :kj_pisanje, 
                       :kj_kandidatid, 
                       :kj_ustanova
                    )";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":kj_naziv", "Njemački", PDO::PARAM_STR);
        $stmt->bindValue(":kj_slusanje", $lang_level, PDO::PARAM_STR);
        $stmt->bindValue(":kj_citanje", $lang_level, PDO::PARAM_STR);
        $stmt->bindValue(":kj_govorna_interakcija", $lang_level, PDO::PARAM_STR);
        $stmt->bindValue(":kj_govorna_produkcija", $lang_level, PDO::PARAM_STR);
        $stmt->bindValue(":kj_pisanje", $lang_level, PDO::PARAM_STR);
        $stmt->bindValue(":kj_kandidatid", $candidate_id, PDO::PARAM_INT);
        $stmt->bindValue(":kj_ustanova", 1, PDO::PARAM_INT);

        $stmt->execute();

        $language_id = $this->conn->lastInsertId();

        $this->insertCvlSubLvl($language_id, $sub_level, $start_date);
    }

    private function insertCvlSubLvl(int $lang_id, int $sub_level, string $start_date)
    {
        if($sub_level == 1)
        {
            $this->insertCvlSubLvlOne($lang_id, $start_date);
        }
        else
        {
            $this->insertCvlSubLvlTwo($lang_id, $start_date);
        }
    }

    private function insertCvlSubLvlOne(int $language_id, string $start_date): void
    {
        $updated_date = date("Y-m-d");

        $sql = "INSERT INTO idk_candidate_verified_languages (cvl_id, cvl_status, cvl_active, cvl_course1_started, cvl_last_updated) VALUES (:cvl_id, 3, 1, :date_start, :updated_date)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":cvl_id", $language_id, PDO::PARAM_INT);
        $stmt->bindValue(":date_start", $start_date, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function insertCvlSubLvlTwo(int $language_id, string $start_date): void
    {
        $updated_date = date("Y-m-d");

        $sql = "INSERT INTO idk_candidate_verified_languages (cvl_id, cvl_status, cvl_active, cvl_course2_started, cvl_last_updated) VALUES (:cvl_id, 4, 1, :date_start, :updated_date)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":cvl_id", $language_id, PDO::PARAM_INT);
        $stmt->bindValue(":date_start", $start_date, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
    }
     /////////////////////////////////////////////////////////////////
    ///----END OF PRIVATE FUNCTIONS USED FOR ENDPOINT "Pocetak"---///
   /////////////////////////////////////////////////////////////////

     ////////////////////////////////////////////////////////////////////
    ///----START OF PRIVATE FUNCTIONS USED FOR ENDPOINT "Kraj"----/// 
   ////////////////////////////////////////////////////////////////////

   private function updateSubLvlEndDate(int $cvl_id, int $sub_level, string $end_date)
   {
        if($sub_level == 1)
        {
            $this->updateCvlSubLvlOneEnd($cvl_id, $end_date);
        }
        else
        {
            $this->updateCvlSubLvlTwoEnd($cvl_id, $end_date);
        }
   }

   private function updateCvlSubLvlOneEnd(int $cvl_id, string $end_date)
   {
        $updated_date = date("Y-m-d");

        $sql = "UPDATE idk_candidate_verified_languages SET cvl_active = 1, cvl_status = 3, cvl_course1_ended = :date_end, cvl_last_updated = :updated_date WHERE cvl_id = :cvl_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":date_end", $end_date, PDO::PARAM_STR);
        $stmt->bindValue(":cvl_id", $cvl_id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
   }

   private function updateCvlSubLvlTwoEnd(int $cvl_id, string $end_date)
   {
        $updated_date = date("Y-m-d");

        $sql = "UPDATE idk_candidate_verified_languages SET cvl_active = 1, cvl_status = 4, cvl_course2_ended = :date_end, cvl_last_updated = :updated_date WHERE cvl_id = :cvl_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":date_end", $end_date, PDO::PARAM_STR);
        $stmt->bindValue(":cvl_id", $cvl_id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
   }

     /////////////////////////////////////////////////////////////////
    ///----END OF PRIVATE FUNCTIONS USED FOR ENDPOINT "Kraj"---///
   /////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////
    ///----START OF PRIVATE FUNCTIONS USED FOR MOTIVE ----///
    ////////////////////////////////////////////////////////////////////

    private function updateMotiveLvlStartDate(int $cvl_id, string $date, string $cvl_lvl)
    {
        $updated_date = date("Y-m-d H:i:s");

        if($cvl_lvl == "A1")
        {
            $end_date = date('Y-m-d', strtotime($date. ' + 15 weekdays'));
        }
        else
        {
            $end_date = date('Y-m-d', strtotime($date. ' + 17 weekdays'));
        }

        $sql = "UPDATE idk_candidate_verified_languages SET cvl_motive_start = :date_start, cvl_motive_end = :end_date, cvl_status = 14, cvl_last_updated = :updated_date, cvl_active = 1 WHERE cvl_id = :cvl_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":date_start", $date, PDO::PARAM_STR);
        $stmt->bindValue(":end_date", $end_date, PDO::PARAM_STR);
        $stmt->bindValue(":cvl_id", $cvl_id, PDO::PARAM_INT);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function newMotiveLvl(int $candidate_id, int $cvl_id, string $lang_level, string $date)
    {
        $lang_id = $this->getLanguageIdByLvl($lang_level, $candidate_id);

        $this->disableVerifiedLanguage($cvl_id);

        if($lang_id != 0)
        {
            $this->updateSubLvlLanguage($lang_id);
            $new_cvl = $this->findThisVerifiedLanguage($lang_id);

            if($new_cvl != 0)
            {
                $this->updateMotiveLvlStartDate($new_cvl, $date, $lang_level);
            }
            else
            {
                $this->insertMotiveLvl($lang_id, $lang_level, $date);
            }
        }
        else
        {
            $this->insertLanguageMotiveLvl($candidate_id, $lang_level, $date);
        }
    }

    private function insertMotiveLvl(int $lang_id, string $lang_lvl, string $start_date)
    {
        $updated_date = date("Y-m-d H:i:s");

        if($lang_level == "A1")
        {
            $end_date = date('Y-m-d', strtotime($start_date. ' + 15 weekdays'));
        }
        else
        {
            $end_date = date('Y-m-d', strtotime($start_date. ' + 17 weekdays'));
        }

        $sql = "INSERT INTO idk_candidate_verified_languages (cvl_id, cvl_status, cvl_active, cvl_motive_start, cvl_motive_end, cvl_last_updated) VALUES (:cvl_id, 14, 1, :date_start, :end_date, :updated_date)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":cvl_id", $lang_id, PDO::PARAM_INT);
        $stmt->bindValue(":date_start", $start_date, PDO::PARAM_STR);
        $stmt->bindValue(":end_date", $end_date, PDO::PARAM_STR);
        $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function insertLanguageMotiveLvl(int $candidate_id, string $lang_level, $date)
    {
        $sql = "INSERT INTO 
                    idk_kandidat_jezici 
                    (
                        kj_naziv, 
                        kj_slusanje, 
                        kj_citanje, 
                        kj_govorna_interakcija, 
                        kj_govorna_produkcija, 
                        kj_pisanje, 
                        kj_kandidatid, 
                        kj_ustanova
                    )
                VALUES
                    (
                       :kj_naziv, 
                       :kj_slusanje, 
                       :kj_citanje, 
                       :kj_govorna_interakcija, 
                       :kj_govorna_produkcija, 
                       :kj_pisanje, 
                       :kj_kandidatid, 
                       :kj_ustanova
                    )";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":kj_naziv", "Njemački", PDO::PARAM_STR);
        $stmt->bindValue(":kj_slusanje", $lang_level, PDO::PARAM_STR);
        $stmt->bindValue(":kj_citanje", $lang_level, PDO::PARAM_STR);
        $stmt->bindValue(":kj_govorna_interakcija", $lang_level, PDO::PARAM_STR);
        $stmt->bindValue(":kj_govorna_produkcija", $lang_level, PDO::PARAM_STR);
        $stmt->bindValue(":kj_pisanje", $lang_level, PDO::PARAM_STR);
        $stmt->bindValue(":kj_kandidatid", $candidate_id, PDO::PARAM_INT);
        $stmt->bindValue(":kj_ustanova", 1, PDO::PARAM_INT);

        $stmt->execute();

        $language_id = $this->conn->lastInsertId();

        $this->insertMotiveLvl($language_id, $lang_level, $date);
    }

    private function updateMotiveEndDate(int $cvl_id, string $end_date)
    {
            $updated_date = date("Y-m-d H:i:s");

            $sql = "UPDATE idk_candidate_verified_languages SET cvl_active = 1, cvl_status = 14, cvl_motive_end = :date_end, cvl_last_updated = :updated_date WHERE cvl_id = :cvl_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":date_end", $end_date, PDO::PARAM_STR);
            $stmt->bindValue(":cvl_id", $cvl_id, PDO::PARAM_INT);
            $stmt->bindValue(":updated_date", $updated_date, PDO::PARAM_STR);
            $stmt->execute();
    }
    ////////////////////////////////////////////////////////////////////
    ///----END OF PRIVATE FUNCTIONS USED FOR MOTIVE ----///
    ////////////////////////////////////////////////////////////////////

    /************************************************************** 
                //----END OF PRIVATE FUNCTIONS---//
    ***************************************************************/
    
    private function candidateHasProjection($candidate_id){
        
        $sql = 'SELECT kandidat_id FROM idk_kandidat_projekcije WHERE kandidat_id = :candidate_id';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":candidate_id", $candidate_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();

        if(is_null($result['kandidat_id'])){
            return 0;
        }
        return 1;
    }
    
    
    private function getCandidateStatusPrijaveByCandidateId($candidate_id){

        $sql = 'SELECT kandidat_status_prijave FROM idk_kandidati WHERE kandidat_id = :candidate_id';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":candidate_id", $candidate_id, PDO::PARAM_INT);
    
        $result = $stmt->fetch();    
        return $result['kandidat_status_prijave'];
    }
    
    private function deleteCandidatesProjection($candidate_id){

        $sql = 'DELETE FROM idk_kandidat_projekcije WHERE kandidat_id = :candidate_id';
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":candidate_id", $candidate_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    private function getCandidatesQuickAccessProjection($candidate_id){
            
        $sql = 'SELECT proracunati_pocetak_rada FROM idk_kandidat_projekcije WHERE kandidat_id = :candidate_id';
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":candidate_id", $candidate_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch();
        
        return strtotime($result['proracunati_pocetak_rada']);
    }
    
    private function calculateCandidatesProjection($candidate_id){

        include_once($_SERVER["DOCUMENT_ROOT"] . '/includes/connect.php');
        include_once($_SERVER["DOCUMENT_ROOT"] . '/jobstep_pp/includes/classes/candidatesProjection.php');
        $durationPerStatus = new durationPerStatus();
    
        $candidates_projection = new candidatesProjection($durationPerStatus, $candidate_id);
        $projection_object       = $candidates_projection -> getCandidateProjectionRows();

        return $projection_object[0]['candidate_assessment'];
    }
    
    private function updateCandidatesQuickAccessProjection($candidate_id, $projection_date){
       
        $sql = 'UPDATE idk_kandidat_projekcije SET proracunati_pocetak_rada =  :projection_date WHERE kandidat_id = :candidate_id';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":candidate_id", $candidate_id, PDO::PARAM_INT);
        $stmt->bindValue(":projection_date", $projection_date, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    private function insertCandidateQuickAccessProjection($candidate_id, $projection_date){

        $sql = 'INSERT INTO idk_kandidat_projekcije (kandidat_id, proracunati_pocetak_rada) VALUES (:candidate_id, :projection_date)';
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":candidate_id", $candidate_id, PDO::PARAM_INT);
        $stmt->bindValue(":projection_date", $projection_date, PDO::PARAM_STR);
        $stmt->execute();
    }
    
    private function updateCandidateInstallments($candidate_id, $shift_dates){

        $sql = ' 
            UPDATE idk_kandidat_financije
            SET kf_datum = DATE_ADD(kf_datum, INTERVAL :shift_dates DAY), kf_datum_stvarni = DATE_ADD(kf_datum_stvarni, INTERVAL :shift_dates DAY)
            WHERE kandidat_id = :candidate_id
            AND kf_placeno = 0
            AND kf_type != 0

        ';
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":candidate_id", $candidate_id, PDO::PARAM_INT);
        $stmt->bindValue(":shift_dates", $shift_dates, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    private function updateCandidateProjectionAndInstallment($candidate_id){

        $shift_dates = 0;
    
        $candidate_status = $this -> getCandidateStatusPrijaveByCandidateId($candidate_id);
        $candidate_has_projection = $this -> candidateHasProjection($candidate_id);
    
        $negative_statuses = array(1,2,3,5,6);
        // $positive_statuses = array(4,7,8,9,10,12,15,18,21,24,27);
    
        if(in_array($candidate_status, $negative_statuses)){
            if($candidate_has_projection){
                $this -> deleteCandidatesProjection($candidate_id);
            }
            return;
        }
        else{
            $candidate_calculated_projection  = $this -> calculateCandidatesProjection($candidate_id);
    
            if($candidate_has_projection){
    
                $candidate_quick_access_projection = $this -> getCandidatesQuickAccessProjection($candidate_id);				
                if($candidate_quick_access_projection != $candidate_calculated_projection){
                    $shift_dates = floor(($candidate_quick_access_projection - $candidate_calculated_projection)/86400);
                    $this -> updateCandidatesQuickAccessProjection($candidate_id, date('Y-m-d', $candidate_calculated_projection));
                }
                else return;
            }
            else{
                $this -> insertCandidateQuickAccessProjection($candidate_id, date('Y-m-d', $candidate_calculated_projection));
            }
        }
    
        if($shift_dates != 0){
            $this -> updateCandidateInstallments($candidate_id, $shift_dates);
        }
    }
}