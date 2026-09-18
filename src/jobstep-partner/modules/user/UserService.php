<?php
namespace modules\user;
use modules\database\Database;

//PHPMailer
// require 'mail/Exception.php';
// require 'mail/PHPMailer.php';
// require 'mail/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// include("includes/connect.php");

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/env.php");

class UserService{
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function checkUser(string $email, string $password){
        $sql = "SELECT jp_id, jp_mailconfirmation_token, jp_user_type, jp_user_permission
                FROM idk_jobstep_partners
                WHERE jp_email = :jp_email AND jp_password = :jp_password AND jp_confirmedaccount = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':jp_email', $email);
        $stmt->bindParam(':jp_password', md5($password));
        $stmt->execute();
        $result = $stmt->fetch();
        if($result != null){
            $data = array();
            $data['token'] = $result['jp_mailconfirmation_token'];
            $data['user_type'] = $result['jp_user_type'];
            $data['user_permission'] = $result['jp_user_permission'];
            $this -> setActivityLog($result['jp_id']);
            return $data;
        }else{
            http_response_code(404);
            die("User not found.");
        }
    }

    private function setActivityLog($id){
        $sql = "
            SELECT jp_first_login
            FROM idk_jobstep_partners
            WHERE jp_id = :jp_id
        ";
        $stmt = $this -> conn -> prepare($sql);
        $stmt->bindParam(':jp_id', $id);
        $stmt->execute();
        $result = $stmt->fetch();

        $query_first_login = "";
        if(is_null($result['jp_first_login'])){
            $query_first_login = ", jp_first_login = now() ";
        }

        $query_activity_log = "
            UPDATE idk_jobstep_partners
            SET jp_latest_login = now()".$query_first_login."
            WHERE jp_id = :jp_id
        ";

        $stmt = $this -> conn -> prepare($query_activity_log);
        $stmt->bindParam(':jp_id', $id);
        $stmt->execute();
    }
    
    public function updateOnesignalId(string $onesignal_id){
        $sql = "UPDATE idk_jobstep_partners SET jp_fcmtoken = :onesignal_id WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':onesignal_id', $onesignal_id);
        $stmt->bindParam(':jp_mailconfirmation_token', $_SESSION['token']);
        $stmt->execute();
    }

    public function getUserInfo(){
        $sql = "SELECT jp_id, jp_ime, jp_prezime, jp_email, jp_brtelefona, jp_makler_id,jp_region_de,jp_spoken_languages, jp_direction_number FROM idk_jobstep_partners WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':jp_mailconfirmation_token', $_SESSION['token']);
        $stmt->execute();
        $result = $stmt->fetch();
        if($result != null){
            $data = array();
            $data['id'] = $result['jp_id'];
            $data['first_name'] = $result['jp_ime'];
            $data['last_name'] = $result['jp_prezime'];
            $data['email'] = $result['jp_email'];
            $data['phone_number'] = $result['jp_brtelefona'];
            $data['makler_id'] = $result['jp_makler_id'];
            $data['direction_number'] = $result['jp_direction_number'];
            $data['region_de'] = $result['jp_region_de'];
            if(isset($result['jp_spoken_languages'])){
                $data['spoken_languages'] = explode( ',' , $result['jp_spoken_languages']);
            }else{
                $data['spoken_languages'] = [];
            }
            return $data;
        } else {
            http_response_code(404);
            die("User not found.");
        }
    }

    public function updateProfile($data){
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $phone_number = $data['phone_number'];
        $email = $data['email'];
        $makler_id = $data['makler_id'];
        $jp_region_de = $data['region_de'];
        $jp_spoken_languages_array = $data['spoken_languages'];
        if (empty($jp_spoken_languages_array)) {
            $jp_spoken_languages = null;
        } else {
            $jp_spoken_languages = implode(',', $jp_spoken_languages_array);
        }
        $sql = "UPDATE idk_jobstep_partners 
        SET jp_ime = :first_name, 
            jp_prezime = :last_name, 
            jp_brtelefona = :phone_number, 
            jp_email = :email, 
            jp_makler_id = :makler_id, 
            jp_region_de = :jp_region_de, 
            jp_spoken_languages = :jp_spoken_languages 
        WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':makler_id', $makler_id);
        $stmt->bindParam(':jp_region_de', $jp_region_de);
        $stmt->bindParam(':jp_spoken_languages', $jp_spoken_languages);
        $stmt->bindParam(':jp_mailconfirmation_token', $_SESSION['token']);
        $stmt->execute();

    }

    public function updateAppLanguage($data){
        $jp_lang = $data['language'];
        $sql = "UPDATE idk_jobstep_partners 
        SET  
            jp_lang = :jp_lang 
        WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':jp_lang', $jp_lang);
        $stmt->bindParam(':jp_mailconfirmation_token', $_SESSION['token']);
        $stmt->execute();

    }

    public function getAppLanguage(){
        $sql = "SELECT jp_lang FROM idk_jobstep_partners WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':jp_mailconfirmation_token', $_SESSION['token']);
        $stmt->execute();
        $result = $stmt->fetch();
        if($result != null){
            $data = array();
            $data['language'] = $result['jp_lang'];
            return $data;
        } else {
            http_response_code(404);
            die("User not found.");
        }
    }

    public function getUserIdByToken(){
        $sql = "SELECT jp_id FROM idk_jobstep_partners WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':jp_mailconfirmation_token', $_SESSION['token']);
        $stmt->execute();
        $result = $stmt->fetch();

        if($result != null){
            return $result['jp_id'];
        } else {
            http_response_code(404);
            die("User not found.");
        }
    }

    public function getUserCompany(){
        
        $sql = "SELECT pc_name,pc_primary_color,pc_logo 
                    FROM idk_jobstep_partners 
                    JOIN idk_partner_companies 
                    ON jp_partner_company = idk_partner_companies.pc_id 
                WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':jp_mailconfirmation_token', $_SESSION['token']);
        $stmt->execute();
        $result = $stmt->fetch();

        if($result != null){
            $data = array();
            $data['name'] = $result['pc_name'];
            $data['primary_color'] = json_decode($result['pc_primary_color']);
            $data['logo'] = $result['pc_logo'];
            return $data;
        } else {
            http_response_code(404);
            die("Data not found.");
        }
    }

    public function getSupportChat($id){
        $lang = $this->getAppLanguage()['language'];
        
        $getAnswerStmt = $this->conn->prepare("SELECT id,question,answer,parentQuestion FROM idk_chat_question WHERE id = :id AND deletedAt IS NULL");
        $getAnswerStmt->bindParam(':id', $id);
        $getAnswerStmt->execute();
        $getAnswer = $getAnswerStmt->fetch();
        
        $answer_json = json_decode($getAnswer['answer'], true);
        $question_json = json_decode($getAnswer['question'], true);
        if ($lang == "en") {
            $answerText = $answer_json['en'];
            $questionText = $question_json['en'];
        } else {
            $answerText = $answer_json['de'];
            $questionText = $question_json['de'];
        }

        $data = [];
        $data['question'] =  $questionText;
        $data['answer'] =  $answerText;
        $data["subquestions"]=[];
        $data['parentQuesiton'] = $getAnswer['parentQuestion'];

        $getSubquestionsSql = "SELECT id, question_order, question FROM idk_chat_question WHERE FIND_IN_SET('1', idk_chat_question.type) > 0 AND parentQuestion = :id AND deletedAt IS NULL";
        $getSubquestionsStmt = $this->conn->prepare($getSubquestionsSql);
        $getSubquestionsStmt->bindParam(':id', $id);
        $getSubquestionsStmt->execute();
        $getSubquestions = $getSubquestionsStmt->fetchAll();
        
        foreach($getSubquestions as $getSubquestion){
            $question_json = json_decode($getSubquestion['question'], true);
            if($lang == "en"){
                $question = $question_json['en'];
            } else {
                $question = $question_json['de'];
            }
            $data["subquestions"][] = [
                'id' => $getSubquestion['id'],
                'question' => $question
            ];
        }

        if($getAnswerStmt->rowCount()>0 OR $getSubquestionsStmt->rowCount()>0){
            return $data;
        } else {
            http_response_code(404);
            die("Data not found.");
        }
    }
     public function getSupportMainChat()
    {
        $lang = $this->getAppLanguage()['language'];
        
        $getAnswerStmt = $this->conn->prepare("SELECT id, question, answer, parentQuestion FROM idk_chat_question WHERE FIND_IN_SET('1', idk_chat_question.type) > 0  AND parentQuestion IS NULL AND deletedAt IS NULL");
        $getAnswerStmt->execute();
        $getAnswers = $getAnswerStmt->fetchAll();
        $data = [];
        $data['question'] =  "";
        $data['answer'] =  "";
        $data["subquestions"] = [];
        $data['parentQuesiton'] = NULL;

        foreach ($getAnswers as $getSubquestion) {
            $question_json = json_decode($getSubquestion['question'], true);
            if ($lang == "en") {
                $question = $question_json['en'];
            } else {
                $question = $question_json['de'];
            }
            $data["subquestions"][] = [
                'id' => $getSubquestion['id'],
                'question' => $question
            ];
        }

        if (!empty($data)) {
            return $data;
        } else {
            http_response_code(404);
            die("Data not found.");
        }
    } 

    public function appointmentsList(){
        $sql = "SELECT meeting_appointment FROM idk_partner_meetings WHERE meeting_appointment > DATE_ADD(CURDATE(),INTERVAL 1 DAY);";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        if($result != null){
            $data = array();
            foreach($result as $meeting)
            {
                $nData = [];
                $nData['meeting_appointment'] = $meeting['meeting_appointment'];
                $data[] = $nData;
            }
            return $data;
        } else {
            http_response_code(404);
            die("Appointments not found.");
        }
    }

    public function getRepresentative(){
        $representative_id = $this->getRepresentativeId();

        $sql = "SELECT CONCAT(employee_firstname, ' ', employee_lastname) AS employee_name, employee_email, employee_image, employee_phone FROM idk_employees WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':employee_id', $representative_id);
        $stmt->execute();
        $result = $stmt->fetch();
        if($result != null){
            $data = [];
            $data['full_name'] = $result['employee_name'];
            $data['email'] = $result['employee_email'];
            $data['phone_number'] = $result['employee_phone'];
            $data['profile_image'] = $result['employee_image'];
            return $data;
        } else {
            http_response_code(404);
            die("Representative not found.");
        }
    }

    private function getRepresentativeId(){
        $sql = "SELECT jp_representative_employee_id FROM idk_jobstep_partners WHERE jp_mailconfirmation_token = :jp_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':jp_id', $_SESSION['token']);
        $stmt->execute();
        $result = $stmt->fetch();
        if($result != null){
            return $result['jp_representative_employee_id'];
        } else {
            http_response_code(404);
            die("Representative not found.");
        }
    }

    public function insertNewUser($first_name, $last_name, $email, $makler_id, $directorate_number, $company_id){
        Global $envConfig;

        $token = $this->generateToken();
        $full_name = $first_name.' '.$last_name;
        $language = 'de';
        $representative_id = $this->getDefaultRepresentativeEmployeeId();

        $sql = "
            INSERT INTO idk_jobstep_partners (jp_ime, jp_prezime, jp_email, jp_lang, jp_mailconfirmation_token, jp_password, jp_confirmedaccount, jp_position, jp_source, jp_user_type, jp_partner_company, jp_makler_id, jp_register_date, jp_imeprezime, jp_direction_number, jp_representative_employee_id)
		    VALUES (:first_name, :last_name, :email, :language, :token, 0, 0, 1, 1, 1, :company_id, :makler_id, now(), :full_name, :directorate_number, :representative_id)
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':language', $language);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':makler_id', $makler_id);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':directorate_number', $directorate_number);
        $stmt->bindParam(':representative_id', $representative_id);
        $stmt->bindParam(':company_id', $company_id);
        $stmt->execute();

        return $this->sendConfirmationViaEmail($email, $token, $full_name, $language, $company_id);        
    }

    public function activateAccount($token){
        Global $envConfig;

        $sql = "
        SELECT jp_confirmedaccount, TIMESTAMPDIFF(HOUR, jp_register_date, NOW()) AS hours_ago 
        FROM idk_jobstep_partners
        WHERE jp_mailconfirmation_token= :token
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $result = $stmt->fetch();
        $is_confirmed = $result['jp_confirmedaccount'];
        $hours_ago = $result['hours_ago'];

        if($is_confirmed){
            return [0, "account_already_active"];
        }
        else if(!$is_confirmed AND $hours_ago > 24){
            return [0, "link_expired"];
        }
        else{
            [$password, $hashed_password] = $this->generatePassword();
            $sql = "
                UPDATE idk_jobstep_partners
                SET jp_password = :hashed_password, jp_confirmedaccount = 1
                WHERE jp_mailconfirmation_token= :token
            ";
    
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':hashed_password', $hashed_password);
            $stmt->execute();
    


            $sql = "
                SELECT jp_id, jp_email, jp_ime, jp_prezime, jp_lang, jp_partner_company, jp_makler_broj_direkcije, jp_representative_employee_id, jp_makler_id
                FROM idk_jobstep_partners
                WHERE jp_mailconfirmation_token = :token
            ";
    
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':token', $token);
    
            $stmt->execute();
            $result = $stmt->fetch();
    
            $email = $result['jp_email'];
            $first_name = $result['jp_ime'];
            $last_name = $result['jp_prezime'];
            $language = $result['jp_lang'];
            $company_id = $result['jp_partner_company'];
            $directorate_number = $result['jp_makler_broj_direkcije'];
            $employee_id = $result['jp_representative_employee_id'];
            $makler_id = $result['jp_makler_id'];
            $partner_id = $result['jp_id'];

            $full_name = $first_name.' '.$last_name;
    
        $sql = "
            SELECT partner_id 
            FROM idk_partner_personal_notifications_preferences 
            WHERE partner_id = :partner_id
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':partner_id', $partner_id);
        $stmt->execute();
        $result = $stmt->fetch();


        if($result['partner_id'] == NULL){
            $sql = "
                INSERT INTO idk_partner_personal_notifications_preferences (partner_id) 
                VALUES (:partner_id)
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':partner_id', $partner_id);
            $stmt->execute();
        }


            if($envConfig->APP_ENV == 'production'){
    
                $url = 'https://staging.crm.job-step.com/do.php?form=copy_new_partner_account_to_stage';
        
                $data = array(
                    'makler_firstname' => $first_name,
                    'makler_lastname' => $last_name,
                    'makler_email' => $email,
                    'makler_language' => $language,
                    'hashed_token' => $token,
                    'makler_company' => $company_id,
                    'hashed_password' => $hashed_password,
                    'makler_fullname' => $full_name,
                    'makler_broj_direkcije' => $directorate_number,
                    'logged_employee_id' => $employee_id,
                    'makler_id' => $makler_id,
                );
        
                $ch = curl_init($url);
        
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
                curl_exec($ch);
            }
    
            return $this->sendCredentialsViaEmail($email, $password, $full_name);
        }
    }

    private function generateToken(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $token = '';
    
        for ($i = 0; $i < 8; $i++) {
            $token .= $characters[rand(0, strlen($characters) - 1)];
        }
    
        $hashed_token = md5($token);

        return $hashed_token;
    }

    private function generatePassword(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $password = '';
    
        for ($i = 0; $i < 8; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
    
        $hashed_password = md5($password);

        return [$password, $hashed_password];
    }

    private function sendCredentialsViaEmail($email, $password, $full_name){
        Global $envConfig;
        $mail_subject = "Ihr neues Passwort";
        $mail_body = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/mail-templates/account_password.html');
        
        $tutorial_path_ios = 'https://jobstep-public-assets.s3.eu-west-1.amazonaws.com/instructions/partnerapp/Installationsanleitung+Partner-App+(iPhone).pdf';
        $tutorial_path_android = 'https://jobstep-public-assets.s3.eu-west-1.amazonaws.com/instructions/partnerapp/Installationsanleitung+Partner-App+(Android-Handys)+.pdf';

        $placeholders = array(
            '{user_fullname}' => $full_name,
            '{password}' => $password,
            '{email}' => $email,
            '{android_link}' => $tutorial_path_android,
            '{ios_link}' => $tutorial_path_ios,
        );

        $mail_body = str_replace(array_keys($placeholders), array_values($placeholders), $mail_body);


        $mail_host_name = "smtp.gmail.com";
        $mail_user_name = "no-reply@job-step.com";
        $mail_password = "kfqa orsn epcl bxcw";
        
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = $mail_host_name;
        $mail->SMTPAuth = true;
        $mail->Username = $mail_user_name;
        $mail->Password = $mail_password;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('no-reply@job-step.com','Partner App');
    
        $mail->addAddress($email);
    
        $mail->Subject = $mail_subject;
        $mail->Body    = $mail_body;
        $mail->AltBody = "Password";
    
        if(!$mail->send()) {
            return [0, "email_not_sent"];
        }else{
            return [1, "email_sent"];
        }
    }

    private function sendConfirmationViaEmail($email, $token, $full_name, $language, $company_id){
        $mail_subject = "Accountaktivierung";
        
        $mail_body = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/mail-templates/account_confirmation.html');
        $placeholders = array(
            '{user_fullname}' => $full_name,
            '{activation_link}' => $this->getJoinUrl().'partner/confirmationsuccess/'.$token.'/'.$company_id.'/'.$language,
        );
        $mail_body = str_replace(array_keys($placeholders), array_values($placeholders), $mail_body);
        $mail_host_name = "smtp.gmail.com";
        $mail_user_name = "no-reply@job-step.com";
        $mail_password = "kfqa orsn epcl bxcw";
        
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = $mail_host_name;
        $mail->SMTPAuth = true;
        $mail->Username = $mail_user_name;
        $mail->Password = $mail_password;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('no-reply@job-step.com','Partner App');
    
        $mail->addAddress($email);

        $mail->Subject = $mail_subject;
        $mail->Body    = $mail_body;
        $mail->AltBody = "Password";
    
        if(!$mail->send()) {
            return [0, "mail_not_sent"];
        }else{
            return [1, "success"];
        }
    }
    
    public function isDuplicateEmail($email){
        $sql = "
            SELECT count(jp_email) as cnt
            FROM idk_jobstep_partners
            WHERE jp_email LIKE (:email)
            AND jp_user_type = 1
        ";

        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch();

        if($result['cnt']){
            return 1;
        }
        return 0;
    }

    private function getJoinUrl(){
        Global $envConfig;

        if($envConfig->APP_ENV == 'production'){
            return "https://join.job-step.com/";
        }
        else if($envConfig->APP_ENV == 'staging'){
            return "https://staging.join.job-step.com/";
        }
        else{
            return "http://dev.join.job-step.com/";
        }
    }

    private function getDefaultRepresentativeEmployeeId(){
        $sql = "
            SELECT employee_id
            FROM idk_employees
            WHERE employee_makler_representative_status = 2
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();

        return $result['employee_id'];
    }

    public function setLatestActivity($token){
        if(!is_null($token)){
            $sql = "
                UPDATE idk_jobstep_partners
                SET jp_latest_activity = now()
                WHERE jp_mailconfirmation_token = :token
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
        }
    }

    public function isAccountActive($token){
        if(!is_null($token)){
            $sql = "
                SELECT jp_confirmedaccount
                FROM idk_jobstep_partners
                WHERE jp_mailconfirmation_token = :token
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->execute();

            $result = $stmt->fetch();

            return $result['jp_confirmedaccount'];
        }
    }

    public function deactivateAccount(){
        $sql = "SELECT jp_email FROM idk_jobstep_partners WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':jp_mailconfirmation_token', $_SESSION['token']);
        $stmt->execute();
        $result = $stmt->fetch();

        $full_name = "Deactivated User";
        $first_name = "Deactivated";
        $last_name = "User";
        $email = $result['jp_email'];
        $email_replacement = "deactivated.user";

        $new_email = preg_replace('/^[^@]+/', $email_replacement, $email);

        $sql = "UPDATE idk_jobstep_partners SET jp_confirmedaccount = 2, jp_imeprezime = '$full_name', jp_ime = '$first_name', jp_prezime = '$last_name', jp_email = '$new_email', jp_brtelefona = NULL, jp_fcmtoken = NULL WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':jp_mailconfirmation_token', $_SESSION['token']);
        $stmt->execute();

        session_destroy();
    }

    public function passwordResetCode($email){
        $reset_code = $this->generatePasswordResetCode();
        $date = date('Y-m-d H:i:s');
        $sql = "
            SELECT jp_imeprezime
            FROM idk_jobstep_partners
            WHERE jp_email = :email
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch();

        if($result == null){
            http_response_code(404);
            die("User not found.");
        }
        $full_name = $result['jp_imeprezime'];

        $insert_reset_code = "
            UPDATE idk_jobstep_partners
            SET jp_password_reset_code = :reset_code, jp_password_reset_datetime = :date
            WHERE jp_email = :email
        ";

        $stmt = $this->conn->prepare($insert_reset_code);
        $stmt->bindParam(':reset_code', $reset_code);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $mail_subject = "Passwort ändern";
        
        $mail_body = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/mail-templates/change_password.html');
        $placeholders = array(
            '{user_fullname}' => $full_name,
            '{code}' => $reset_code,
        );
        $mail_body = str_replace(array_keys($placeholders), array_values($placeholders), $mail_body);
        $mail_host_name = "smtp.gmail.com";
        $mail_user_name = "no-reply@job-step.com";
        $mail_password = "kfqa orsn epcl bxcw";
        
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = $mail_host_name;
        $mail->SMTPAuth = true;
        $mail->Username = $mail_user_name;
        $mail->Password = $mail_password;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('no-reply@job-step.com','Partner App');
    
        $mail->addAddress($email);

        $mail->Subject = $mail_subject;
        $mail->Body    = $mail_body;
        $mail->AltBody = "Password";

        if(!$mail->send()) {
            return [0, "mail_not_sent"];
        }else{
            return [1, "success"];
        }
    }

    public function confirmResetCode($email, $code){

        $get_num_of_tries = "
            SELECT jp_password_reset_tries AS num_of_tries
            FROM idk_jobstep_partners
            WHERE jp_email = :email
        ";
        $stmt = $this->conn->prepare($get_num_of_tries);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch();
        //increment num of tries
        $num_of_tries = intval($result['num_of_tries']) + 1;

        $increment_num_of_tries = "
            UPDATE idk_jobstep_partners
            SET jp_password_reset_tries = :num_of_tries
            WHERE jp_email = :email
        ";
        $stmt = $this->conn->prepare($increment_num_of_tries);
        $stmt->bindParam(':num_of_tries', $num_of_tries);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $sql = "
            SELECT jp_password_reset_code, TIMESTAMPDIFF(MINUTE, jp_password_reset_datetime, NOW()) AS minutes_ago
            FROM idk_jobstep_partners
            WHERE jp_email = :email
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch();


        if($result['jp_password_reset_code'] == $code AND $result['minutes_ago'] < 10 AND $num_of_tries <= 4){
            return 1;
        }else{
            return 0;
        }
    }

    public function changePasswordFromPasswordResetCode($email, $password){
        $hashed_password = md5($password);
        $sql = "
            UPDATE idk_jobstep_partners
            SET jp_password = :hashed_password, jp_password_reset_code = NULL, jp_password_reset_datetime = NULL, jp_password_reset_tries = 0
            WHERE jp_email = :email
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':hashed_password', $hashed_password);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    }

    public function updatePassword($old_pw, $new_pw){
        $hashed_old_pw = md5($old_pw);
        $hashed_new_pw = md5($new_pw);
        $sql = "
            SELECT jp_id
            FROM idk_jobstep_partners
            WHERE jp_mailconfirmation_token = :token AND jp_password = :hashed_old_pw
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':token', $_SESSION['token']);
        $stmt->bindParam(':hashed_old_pw', $hashed_old_pw);
        $stmt->execute();
        $result = $stmt->fetch();

        if($result == null){
            http_response_code(404);
            die("User not found.");
        }

        $sql = "
            UPDATE idk_jobstep_partners
            SET jp_password = :hashed_new_pw
            WHERE jp_mailconfirmation_token = :token
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':hashed_new_pw', $hashed_new_pw);
        $stmt->bindParam(':token', $_SESSION['token']);
        $stmt->execute();
    }

    private function generatePasswordResetCode(){
        $length = 6;
        $numberString = '';
            for ($i = 0; $i < $length; $i++) {
                $numberString .= rand(0, 9);
            }
        return $numberString;
    }

    public function insertUserLog($desc){
        $user_id = $this->getUserIdByToken();
        $sql = "INSERT INTO idk_partner_user_log (user_id, log_desc) VALUES (:user_id, :desc)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':desc', $desc);
        $stmt->execute();
    }

    public function updateDeviceAndAppVersion($device, $app_version){
        $sql = "UPDATE idk_jobstep_partners SET jp_device = :device, jp_app_version = :app_version WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':device', $device);
        $stmt->bindParam(':app_version', $app_version);
        $stmt->bindParam(':jp_mailconfirmation_token', $_SESSION['token']);
        $stmt->execute();
    }
}