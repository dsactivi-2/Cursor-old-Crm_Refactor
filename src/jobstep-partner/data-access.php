<?php
//Error log enabled
// error_reporting(E_ALL); // mogući NOTICE PHP-a uvode grešku u json
ini_set('display_errors', 0);
session_start();

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

ob_start();
include('includes/connect.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/env.php");
//PHPMailer
require 'mail/Exception.php';
require 'mail/PHPMailer.php';
require 'mail/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//MODULES
include('./modules/database/dbConfig.php');
require_once './modules/database/Database.php';
require_once './modules/user/UserService.php';
require_once './modules/user/UserController.php';
require_once './modules/candidates/CandidatesService.php';
require_once './modules/candidates/CandidatesController.php';
require_once './modules/companies/CompaniesService.php';
require_once './modules/companies/CompaniesController.php';
require_once './modules/recruitment_orders/RecruitmentOrderService.php';
require_once './modules/recruitment_orders/RecruitmentOrderController.php';
require_once './modules/notifications/NotificationsService.php';
require_once './modules/notifications/NotificationsController.php';
use modules\database\Database;
use modules\user\UserService;
use modules\user\UserController;
use modules\candidates\CandidatesService;
use modules\candidates\CandidatesController;
use modules\companies\CompaniesService;
use modules\companies\CompaniesController;
use modules\recruitment_orders\RecruitmentOrderService;
use modules\recruitment_orders\RecruitmentOrderController;
use modules\notifications\NotificationsService;
use modules\notifications\NotificationsController;
 function getPartnerIdFromToken($token){
    global $db;
    // POKUPI INFORMACIJU O IDU-U PARTNERA NA OSNOVU TOKENA
    $query_partner = $db->prepare("
                SELECT jp_id
                FROM idk_jobstep_partners
                WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token");

    $query_partner->execute(array(
        ':jp_mailconfirmation_token' => $token
    ));

    $row = $query_partner->fetch();

    return $row['jp_id'];
}

$request = "";

if (isset($_REQUEST["request"])) {
    $request = $_REQUEST["request"];
    $database = new Database($db_host, $db_name, $db_user, $db_pass);
    $userService = new UserService($database);
    $userController = new UserController($userService);
    $candidatesService = new CandidatesService($database, $userService);
    $candidatesController = new CandidatesController($candidatesService, $userService);
    $companiesService = new CompaniesService($database, $userService);
    $companiesController = new CompaniesController($companiesService, $userService);
    $recruitmentOrderService = new RecruitmentOrderService($database, $userService);
    $recruitmentOrderController = new RecruitmentOrderController($recruitmentOrderService, $userService);
    $notificationsService = new NotificationsService($database, $userService);
    $notificationsController = new NotificationsController($notificationsService, $userService);

    $userController->setLatestActivity($_SESSION['token']);

    switch ($request) {
        //USER    
        case "login":
            $userController->login();
        break; 

        case "onesignal":
            $userController->onesignalId();
        break;

        case "profile_info":
            $userController->profileInfo();
        break;

        case "edit_profile":
            $userController->updateProfile();
        break;

        case "update_app_language":
            $userController->updateAppLanguage();
        break;

        case "get_app_language":
            $userController->getAppLanguage();
        break;
        
        case "get_user_company":
            $userController->getUserCompany();
        break;
        
        case "get_appointments_list":
            $userController->appointmentsList();
        break;    
        
        case "get_support_chat":
            $userController->getSupportChat();
        break;    

        case "get_representative":
            $userController->getRepresentative();
        break;

        case "logout":
            $userController->logout();
        break;

        case "register":
            $userController->insertNewUser();
        break;

        case "activate_account":
            $userController->activateAccount();
        break;

        case "password_reset_code":
            $userController->passwordResetCode();
        break;

        case "confirm_reset_code":
            $userController->confirmResetCode();
        break;

        case "change_password":
            $userController->changePassword();
        break;

        case "update_password":
            $userController->updatePassword();
        break;

        case "deactivate_account":
            $userController->deactivateAccount();
        break;

        case "device_and_app_version":
            $userController->updateDeviceAndAppVersion();
        break;

        //CANDIDATES	
        case "candidate_profile": 
            $candidatesController->getCandidateProfile();
        break;

        case "candidate_profile_v2": 
            $candidatesController->getCandidateProfileV2();
        break;

        case "candidate_analytics":
            $candidatesController->getCandidateAnalytics();
        break;
        
        case "candidate_analytics_v2":
            $candidatesController->getCandidateAnalyticsV2();
        break;

        case "candidate_statuses":
            $candidatesController->getCandidateStatuses();
        break;

        case "number_of_candidates":
            $candidatesController->getNumberOfCandidates();
        break;

        case "number_of_candidates_v2":
            $candidatesController->getNumberOfCandidatesV2();
        break;

        case "candidates":
            $candidatesController->getCandidates();
        break;
        
        case "candidates_v2":
            $candidatesController->getCandidatesV2();
        break;
        
        //COMPANIES
        case "number_of_companies":
            $companiesController->getNumberOfCompanies();
        break;
        
        case "number_of_companies_v2":
            $companiesController->getNumberOfCompaniesV2();
        break;

        case "positions":
            $companiesController->getPositionsInfo();
        break;

        case "company":
            $companiesController->getCompanyById();
        break;

        case "company_list":
            $companiesController->getCompanies();
        break;    
        
        case "company_list_v2":
            $companiesController->getCompaniesV2();
        break;    

        //JOBS
        case "all_jobs":
            $recruitmentOrderController->getAllJobs();
        break;

        case "job_details":
            $recruitmentOrderController->getJobDetails();
        break;

        //LEADS
        case "leads":
            $candidatesController->getLeadCandidates();
        break;

        case "lead_analytics":
            $candidatesController->getLeadAnalytics();
        break;

        case "lead_profile":
            $candidatesController->getLeadProfile();
        break;

        case "lead_profile_v2":
            $candidatesController->getLeadProfileV2();
        break;

        case "lead_info":
            $candidatesController->getLeadInfo();
        break;

        case "lead_employer_info":
            $candidatesController->getLeadEmployerInfo();
        break;

        case "update_lead":
            $candidatesController->updateLead();
        break;
        
        case "lead_count":
            $candidatesController->getLeadCount();    
        break;

        //NOTIFICATIONS
        case "notifications":
            $notificationsController->getNotifications();
        break;

        case "toggle_notifications":
            $notificationsController->togglePartnerNotifications();
        break;

        case "update_notification_preferences":
            $notificationsController->updateNotificationPreferences();
        break;

        case "read_notification":
            $notificationsController->markNotificationAsRead();
        break;

        case "delete_notification":
            $notificationsController->markNotificationAsDeleted();
        break;

        case "get_notification_unread_count":
            $notificationsController->getNumberOfUnreadNotifications();
        break;

        case "get_notification_type_preferences":
            $notificationsController->getNotificationTypePreference();
        break;



        case "contact_us":

            $json = json_decode(file_get_contents('php://input'), true);

            $userLogged= "Benutzer angemeldet in Partner-App: <b> Ja </b>";
            if(!$_SESSION['token'])
            {
                $userLogged= "Benutzer angemeldet in Partner-App: <b> Nein </b>";
            }

            $infodata = array();
            $infodata['jp_imeprezime'] = $json['jp_imeprezime'];
            $infodata['jp_email'] = $json['jp_email'];
            $infodata['textContactUs'] = $json['textContactUs'];
            $infodata['subject'] = $json['subject'];

            $clickUpTicket="<h2>Benutzer hat eine Anfrage gesendet:</h2> <br>
                            <b>Email:</b> ".$infodata['jp_email']."<br> 
                            <b>Betreff:</b> ".$infodata['subject']."<br>
                            <b>Nachricht:</b> ".$infodata['textContactUs']."<br>
                            <b>Erstellungsdatum:</b> ".date('Y-m-d H:i:s')."<br> <br>
                            ".$userLogged."";
            
            $mailSentTo = "partner@job-step.com";
            if($envConfig->APP_ENV != "production"){
                $mailSentTo = "dev-test-pa@job-step.com";
            }

            $body = $clickUpTicket;
            
            $hostName = "smtp.gmail.com";
            $userName = "support@job-step.com";
            $password = "eooc nnxo aylp lqkh";
            $setFrom = "no-reply@job-step.com";

            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->Host = $hostName;
            $mail->SMTPAuth = true;
            $mail->Username = $userName;
            $mail->Password = $password;
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($setFrom);
            $mail->addAddress($mailSentTo);

            $mail->Subject = $infodata['subject'];
            $mail->Body    = $body;
            $mail->AltBody = "Novi upit";

            if(!$mail->send()) {
                echo json_encode('false');
            }else{
                echo json_encode('true');
            }

        break;


        case "add_company":
            if(!$_SESSION['token'])
            {
                http_response_code(401);
                echo json_encode(array("message" => "Unauthorized."));
                exit();
            }
            $json = json_decode(file_get_contents('php://input'), true);
            if(empty($json['company_name']) || empty($json['company_email']) || empty($json['company_phone'])){
                http_response_code(400);
                echo json_encode(array("message" => "Missing data."));
                exit();
            }
            $partner_id = getPartnerIdFromToken($_SESSION['token']);
                      
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
            $meeting_appointment = $json['meeting_appointment'];
            $meeting_type = 1;
            $total_workers_required =  $json['total_workers_required'];
            $company_professions_array =  $json['company_professions'];
            $company_professions;
            if(count($company_professions_array)>0){
                $company_professions = implode(',', $company_professions_array);
            }
            
            $other_professions = $json['other_professions'];
            $lang = $json['language'];
           
            $sql = "INSERT INTO idk_companies (js_partner_id, company_origin, company_name, company_size, company_zipcode, company_city, company_country, company_address, company_message, company_datetime,company_total_workers_required,company_professions,company_status)
                    VALUES (:js_partner_id, :company_origin, :company_name, :company_size, :compnay_zipcode, :company_city, :company_country, :company_address, :company_message, :company_datetime, :company_total_workers_required,:company_professions,:company_status)";
            $stmt = $db->prepare($sql);
            $stmt->execute(array(
                ':js_partner_id' => $partner_id,
                ':company_origin' => 5,
                ':company_name' => $company_name,
                ':company_size' => $company_size,
                ':compnay_zipcode' => $company_zip_code,
                ':company_city' => $company_city,
                ':company_country' => $company_country,
                ':company_address' => $company_address,
                ':company_message' => $message,
                ':company_datetime' => $date,
                ':company_status' => 2,
                ':company_total_workers_required' => $total_workers_required,
                ':company_professions'=>$company_professions
            ));

            $company_id = $db->lastInsertId();

            $profession_name_sql = "kp_ime_de";
            if($lang=='en'){
                $profession_name_sql = "kp_ime_en";
            }else{
                $profession_name_sql = "kp_ime_de";
            } 
            
            if($other_professions!=NULL){
                $professions_list = array();
                foreach($other_professions as $new_porofession){
                    $new_professions_sql = "INSERT INTO idk_kandidat_pozicija ($profession_name_sql, kp_source, kp_created_by) VALUES (:profession_name,2,:profession_created_by);";
                    $new_professions_stmt = $db->prepare($new_professions_sql);
                    $new_professions_stmt->execute(array(
                        ':profession_name'=>$new_porofession,
                        ':profession_created_by'=>$partner_id
                    )); 
                    $professions_list[] = $db->lastInsertId(); 

                }
                if(is_null($company_professions)){
                    $updated_professions = implode(',', $professions_list);
                }else{
                    $updated_professions = $company_professions . ',' . implode(',', $professions_list);
                }                     
                $update_company_professions_sql  = "UPDATE idk_companies SET company_professions = '$updated_professions' WHERE company_id = $company_id;";
                $update_company_professions_stmt = $db->prepare($update_company_professions_sql);
                $update_company_professions_stmt->execute(); 
            }


            $sql = "INSERT INTO idk_companies_info (comi_group, comi_title, comi_data, comi_primary, comi_companyid)
                    VALUES (:comi_group, :comi_title, :comi_data, :comi_primary, :comi_companyid)";
            $stmt = $db->prepare($sql);
            $stmt->execute(array(
                ':comi_group' => 1,
                ':comi_title' => 'Telefon',
                ':comi_data' => $company_phone,
                ':comi_primary' => 1,
                ':comi_companyid' => $company_id
            ));

            $sql = "INSERT INTO idk_companies_info (comi_group, comi_title, comi_data, comi_primary, comi_companyid)
                    VALUES (:comi_group, :comi_title, :comi_data, :comi_primary, :comi_companyid)";
            $stmt = $db->prepare($sql);
            $stmt->execute(array(
                ':comi_group' => 2,
                ':comi_title' => 'E-mail',
                ':comi_data' => $company_email,
                ':comi_primary' => 1,
                ':comi_companyid' => $company_id
            ));
            if(!empty($contact_person_fname)){
                $sql = "INSERT INTO idk_contacts (contact_firstname, contact_lastname, contact_companyid, contact_status, contact_job_title, contact_datetime)
                        VALUES (:contact_firstname, :contact_lastname, :contact_companyid, :contact_status, :contact_job_title, :contact_datetime)";
                $stmt = $db->prepare($sql);
                $stmt->execute(array(
                    ':contact_firstname' => $contact_person_fname,
                    ':contact_lastname' => $contact_person_lname,
                    ':contact_companyid' => $company_id,
                    ':contact_status' => 1,
                    ':contact_job_title' => $contact_job,
                    ':contact_datetime' => $date
                ));
            }
            
            //INSERT COMPANY INTO CLIENTS FOR SALE/FIRST CALL
            $sql = "INSERT INTO idk_clients (client_name, client_country, client_city, client_address, client_pp, client_telephone, client_email, client_origin, client_recommendation, client_recommendation_company, client_fc_or_sales, client_fc_status, client_sales_status, client_manager, client_sales_manager, client_contract, client_description) 
                    VALUES (:client_name, :client_country, :client_city, :client_address, NULL, :client_telephone, :client_email, 5, NULL, :client_recommendation_company, 1, 2, 0,NULL, NULL, 0, :client_description);";

            $stmt = $db->prepare($sql);
            $stmt->execute(array(
                ':client_name' => $company_name,
                ':client_country' => $company_country,
                ':client_city' => $company_city,
                ':client_address' => $company_address,
                ':client_telephone' => $company_phone,
                ':client_email' => $company_email,
                ':client_recommendation_company'=> $company_id,
                ':client_description' => $message,
            ));
            $employee_id_adnan = 493;//Za sada je receno da svi sastanci idu na adnana 
            //INSERT MEETING DETAILS
            $meeting_sql = "INSERT INTO idk_partner_meetings (meeting_appointment,meeting_type,meeting_employee,meeting_company_id) VALUES (:meeting_appointment , :meeting_type,:meeting_employee, :meeting_company_id);";

            $meeting_stmt = $db->prepare($meeting_sql);
            $meeting_stmt->execute(array(
                ':meeting_appointment'  => $meeting_appointment,
                ':meeting_type'         => $meeting_type,
                ':meeting_employee'     => $employee_id_adnan,
                ':meeting_company_id'   => $company_id
            ));

            $data = [];
            $sql_get_company = "SELECT 
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
                                    company_professions
                                FROM idk_companies
                                LEFT JOIN idk_contacts
                                ON idk_companies.company_id = idk_contacts.contact_companyid
                                WHERE company_id = :company_id";
            $stmt_get_company = $db->prepare($sql_get_company);
            $stmt_get_company->execute(array(
                ':company_id' => $company_id
            ));
            $company = $stmt_get_company->fetch();
            $data['company_id'] = $company_id;
            $data['company_name'] = $company['company_name'];
            $data['company_size'] = $company['company_size'];
            $data['company_country'] = $company['company_country'];
            $data['company_city'] = $company['company_city'];
            $data['company_zip_code'] = $company['company_zipcode'];
            $data['company_address'] = $company['company_address'];
            $data['message'] = $company['company_message'];
            $data['contact_fname'] = $company['contact_firstname'];
            $data['contact_lname'] = $company['contact_lastname'];
            $data['contact_job'] = $company['contact_job_title'];
            $data['total_workers_required'] = $company['company_total_workers_required'];

            $company_professions_list = $company['company_professions'];
            if(is_null($company_professions_list)){
                $company_professions_list = 0;
            }
            $all_professions_sql = "SELECT kp_id,$profession_name_sql as profession_name FROM idk_kandidat_pozicija WHERE kp_id IN ($company_professions_list)";
            $professions_stmt = $db->prepare($all_professions_sql);
            $professions_stmt->execute();
            $professions = $professions_stmt->fetchAll();
            $professions_string = '';
            $professions_names = [];
            if($professions!=NULL){
                foreach($professions as $profession){
                    $professions_data = [];
                    $professions_data['profession_id'] = $profession['kp_id'];
                    $professions_data['profession_name'] = $profession['profession_name'];
                    $data['professions'][] = $professions_data;
                    $professions_names[] = $profession['profession_name'];
                }
                $professions_string = implode(', ', $professions_names);
            } else {
                $data['professions'] = [];
                $professions_string = '';
            }

            $sql_get_company_info = "SELECT 
                                        comi_group, 
                                        comi_data
                                    FROM idk_companies_info
                                    WHERE comi_companyid = :comi_companyid";
            $stmt_get_company_info = $db->prepare($sql_get_company_info);
            $stmt_get_company_info->execute(array(
                ':comi_companyid' => $company_id
            ));
            while($info_result = $stmt_get_company_info->fetch()){
                if($info_result['comi_group'] == 1){
                    $data['company_phone'] = $info_result['comi_data'];
                } else if($info_result['comi_group'] == 2){
                    $data['company_email'] = $info_result['comi_data'];
                }
            }
            
            $get_meeting_sql = "SELECT meeting_appointment  FROM idk_partner_meetings WHERE meeting_company_id = :company_id AND meeting_type=1";
            $get_meeting = $db->prepare($get_meeting_sql);
            $get_meeting->execute(array(
                ':company_id'=>$company_id
            ));
            $meeting_details=$get_meeting->fetch();
            $data['meeting_appointment'] = $meeting_details['meeting_appointment'];
            $query_partner = $db->prepare("
                                        SELECT jp_imeprezime,jp_email
                                        FROM idk_jobstep_partners
                                        WHERE jp_id = :jp_id");

            $query_partner->execute(array(
                ':jp_id' => $partner_id
            ));
            $partner_details = $query_partner->fetch(); 
            $partner_name = $partner_details['jp_imeprezime'];
            $partner_mail = $partner_details['jp_email'];
            $mailSentTo = "partner@job-step.com";
            if($envConfig->APP_ENV != "production"){
                $mailSentTo = "dev-test-pa@job-step.com";
            }
            $mail_us_subject = "Neue Firma eingetragen: $company_name";
            $mail_us_body = "<h2>Der Benutzer hat folgende Informationen über das Unternehmen eingegeben: </h2> <br>
                            <h2><u>Firmeninfo</u></h2>
                            <b>Firmenname:</b> $company_name<br>
                            <b>Kontaktperson:</b> $contact_person_fname $contact_person_lname<br>
                            <b>Position der Kontaktperson:</b> $contact_job<br>
                            <b>Unternehmensgröße:</b> $company_size<br>
                            <b>E-Mail-Adresse des Unternehmens:</b> $company_email<br>
                            <b>Telefonnummer des Unternehmens:</b> $company_phone<br>
                            <b>Land:</b> $company_country<br>
                            <b>Stadt:</b> $company_city<br>
                            <b>Postleitzahl:</b> $company_zip_code<br>
                            <b>Adresse:</b> $company_address<br>
                            <b>Nachricht:</b> $message<br>
                            <b>Termin vereinbart:</b> $meeting_appointment<br>
                            <b>Gesamtzahl der benötigten Arbeitskräfte:</b> $total_workers_required<br>
                            <b>Berufe gefragt:</b> $professions_string<br>
                            <br> 
                            <h2><u>Partner info</u></h2>
                            <b>ID:</b> $partner_id<br>
                            <b>Name:</b> $partner_name<br>
                            <b>Email:</b> $partner_mail<br>
                            <b>Erstellungsdatum:</b> ".date('Y-m-d H:i:s');


            $hostName = "smtp.gmail.com";
            $userName = "no-reply@job-step.com";
            $password = "kfqa orsn epcl bxcw";
            
            $mail_us = new PHPMailer;
            $mail_us->isSMTP();
            $mail_us->Host = $hostName;
            $mail_us->SMTPAuth = true;
            $mail_us->Username = $userName;
            $mail_us->Password = $password;
            $mail_us->SMTPSecure = 'ssl';
            $mail_us->Port = 465;
            $mail_us->CharSet = 'UTF-8';
            $mail_us->setFrom($partner_details['jp_email']); 
            $mail_us->addAddress($mailSentTo);

            $mail_us->Subject = $mail_us_subject;
            $mail_us->Body    = $mail_us_body;
            $mail_us->AltBody = "Nova kompanija kreirana";

            if(!$mail_us->send()) {
                $mail_us_response = 'Support mail failed';
            }else{
                $mail_us_response = 'Support mail sent';
            }


            $mail_company_body = file_get_contents('mail-templates/create_company.html');   
            $mail_company_subject = 'Herzlich willkommen bei Jobstep!';

            $mail_company = new PHPMailer;
            $mail_company->isSMTP();
            $mail_company->Host = $hostName;
            $mail_company->SMTPAuth = true;
            $mail_company->Username = $userName;
            $mail_company->Password = $password;
            $mail_company->SMTPSecure = 'ssl';
            $mail_company->Port = 465;
            $mail_company->CharSet = 'UTF-8';

            $mail_company->setFrom('no-reply@job-step.com','Partner App');
            $mail_company->addAddress($company_email);
            $mail_company->isHTML(true);

            $mail_company->Subject = $mail_company_subject;
            $mail_company->Body    = $mail_company_body;
            $mail_company->AltBody = "Nova kompanija kreirana";


            if(!$mail_company->send()) {
                $mail_company_response = 'Company mail failed';
            }else{
                $mail_company_response = 'Company mail sent';
            }
            
            http_response_code(200);
            echo json_encode([
                'message' => 'Successfully added company. '.$mail_us_response.'. '.$mail_company_response,
                'data' => $data
            ]);
        break;

                
    }
}
