<?php
namespace modules\user;

use modules\user\UserService;

class UserController{
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(){
        $json = json_decode(file_get_contents('php://input'), true);

        $email = $json['email']; 
        $pw = $json['password'];

        $data = $this->userService->checkUser($email, $pw); 

        if($data['user_type'] == 1){
            $session_expiration = time() + 2592000;
            setcookie(session_name(), session_id(), $session_expiration, "/");
            $_SESSION['token'] = $data['token'];
            $_SESSION['user_type'] = $data['user_type'];
            $_SESSION['user_permission'] = $data['user_permission'];

            $desc = "User se logirao.";
            $this->userService->insertUserLog($desc);
            http_response_code(200);
            echo json_encode(array("message" => "Login successful."));
        }

    }

    public function logout(){
        $desc = "User se izlogirao.";
        $this->userService->insertUserLog($desc);
        if(isset($_SESSION['token'])){
            unset($_SESSION['token']);
            unset($_SESSION['user_type']);
            unset($_SESSION['user_permission']);

            http_response_code(200);
            echo json_encode(array("message" => "Logout successful."));

        } else {
            http_response_code(401);
            die("User not logged in.");
        }
    }

    public function onesignalId(){
        $this->checkAuth();

        $json = json_decode(file_get_contents('php://input'), true);
        $onesignal_id = $json['onesignal_id'];

        if(is_null($onesignal_id) || $onesignal_id == ""){
            http_response_code(400);
            die("Onesignal ID is required.");
        }

        $this->userService->updateOnesignalId($onesignal_id);

        http_response_code(200);
        echo json_encode(array("message" => "Updated onesignal id for user"));
    }

    public function profileInfo(){
        $this->checkAuth();
        $data = $this->userService->getUserInfo();

        $desc = "User je otvorio svoj profil.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched profile info.',
            'data' => $data
        ]); 
    }
    
    public function appointmentsList(){
        $this->checkAuth();
        $data = $this->userService->appointmentsList();

        $desc = "User je otvorio svoje termine.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully appointments.',
            'data' => $data
        ]); 
    }

    public function updateProfile(){
        $this->checkAuth();

        $json = json_decode(file_get_contents('php://input'), true);
        $this->userService->updateProfile($json);

        $data = $this->userService->getUserInfo();

        $desc = "User je promenio svoje podatke.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully edited profile info.',
            'data' => $data
        ]);
    }

    public function updateAppLanguage(){
        $this->checkAuth();

        $json = json_decode(file_get_contents('php://input'), true);
        if(!$json["language"]){
            http_response_code(400);
            die("Missing parametar.");
        }
        $this->userService->updateAppLanguage($json);
        $data = $this->userService->getAppLanguage();

        $desc = "User je promenio jezik aplikacije.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully edited app language.',
            'data' => $data
        ]);
    }
    
    public function getAppLanguage(){
        $this->checkAuth();
        $data = $this->userService->getAppLanguage();
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched app language.',
            'data' => $data
        ]); 
    }
   
    public function getUserCompany(){
        $this->checkAuth();
        $data = $this->userService->getUserCompany();
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched user company.',
            'data' => $data
        ]); 
    }
    
    public function getSupportChat(){
        $this->checkAuth();

        if(isset($_GET['id']) AND !empty($_GET['id'])){
            $id = $_GET['id'];
            $data = $this->userService->getSupportChat($id);
        } else {
            $data = $this->userService->getSupportMainChat();
        }

        $desc = "User je otvorio chat sa podrškom.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched chat details.',
            'data' => $data
        ]); 
    }

    public function getRepresentative(){
        $this->checkAuth();
        $data = $this->userService->getRepresentative();

        $desc = "User je otvorio support.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched representative.',
            'data' => $data
        ]);
    }

    public function insertNewUser(){
        $first_name = $this->getFirstNameParam();
        $last_name = $this->getLastNameParam();
        $email = $this->getEmailParam();
        $makler_id = $this->getMaklerIdParam();
        $directorate_number = $this->getDirectorateNumberParam();
        $company_id = $this->getCompanyIdParam();
        if(is_null($company_id)){
            $company_id = 3;
        }

        [$is_sent, $message] = $this->userService->insertNewUser($first_name, $last_name, $email, $makler_id, $directorate_number, $company_id);

        if(!$is_sent){
            http_response_code(401);
        }
        else{
            http_response_code(200);
        }

        echo json_encode([
            'message' => $message
        ]);
    }

    public function activateAccount(){
        $token = $this->getTokenParam();

        [$is_sent, $message] = $this->userService->activateAccount($token);
        
        if(!$is_sent){
            http_response_code(401);
        }
        else{
            http_response_code(200);
        }

        echo ($message);
    }

    public function setLatestActivity($token){
        $this->userService->setLatestActivity($token);
    }

    public function passwordResetCode(){
        $json = json_decode(file_get_contents('php://input'), true);
        $email = $json['email'];

        $this->userService->passwordResetCode($email);
    }

    public function confirmResetCode(){
        $json = json_decode(file_get_contents('php://input'), true);
        $email = $json['email'];
        $code = $json['code'];

        $result = $this->userService->confirmResetCode($email, $code);

        if($result == 0){
            http_response_code(400);
            die("Password change declined.");
        }


        http_response_code(200);
        echo json_encode([
            'message' => 'Password change approved.'
        ]);
    }

    public function changePassword(){
        $json = json_decode(file_get_contents('php://input'), true);
        $email = $json['email'];
        $password = $json['password'];

        $this->userService->changePasswordFromPasswordResetCode($email, $password);

        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully changed password.'
        ]);
    }

    public function updatePassword(){
        $this->checkAuth();

        $json = json_decode(file_get_contents('php://input'), true);
        $old_password = $json['old_password'];
        $new_password = $json['new_password'];

        $this->userService->updatePassword($old_password, $new_password);

        $desc = "User je promenio lozinku.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully updated password.'
        ]);
    }

    public function deactivateAccount(){
        $this->checkAuth();


        $this->userService->deactivateAccount();

        $desc = "User je deaktivirao account.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully deleted account.'
        ]);
    }

    public function updateDeviceAndAppVersion(){
        $this->checkAuth();

        $json = json_decode(file_get_contents('php://input'), true);
        $device = $json['device'];
        $app_version = $json['app_version'];

        $this->userService->updateDeviceAndAppVersion($device, $app_version);

        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully updated device and app version.'
        ]);
    }

    private function checkAuth(){
        if(!isset($_SESSION['token'])){
            http_response_code(401);
            die("Unauthorized.");
        }
        else if(!$this->userService->isAccountActive(($_SESSION['token']))){
            http_response_code(401);
            die("Unauthorized.");
        }
    }

    private function getFirstNameParam(){
        if(!isset($_POST['first_name'])){
            http_response_code(400);
            die("Missing first_name parameter.");
        }
        return $_POST['first_name'];
    }

    private function getTokenParam(){
        if(!isset($_POST['token'])){
            http_response_code(400);
            die("Missing token parameter.");
        }
        return $_POST['token'];
    }

    private function getLastNameParam(){
        if(!isset($_POST['last_name'])){
            http_response_code(400);
            die("Missing last_name parameter.");
        }
        return $_POST['last_name'];
    }

    private function getEmailParam(){
        if(!isset($_POST['email'])){
            http_response_code(400);
            die("Missing email parameter.");
        }

        $email = $_POST['email'];
        $isDvag = $this -> isDvagEmailAddress($email);
        if(!$isDvag){
            http_response_code(400);
            die("is_not_dvag");
        }

        $isDuplicate = $this->userService->isDuplicateEmail($email);
        if($isDuplicate){
            http_response_code(400);
            die("account_exists");
        }
        
        return $email;
    }

    private function getMaklerIdParam(){
        if(!isset($_POST['makler_id'])){
            http_response_code(400);
            die("Missing makler_id parameter.");
        }
        return $_POST['makler_id'];
    }

    private function getDirectorateNumberParam(){
        if(!isset($_POST['directorate_number'])){
            http_response_code(400);
            die("Missing directorate_number parameter.");
        }
        return $_POST['directorate_number'];
    }

    private function getCompanyIdParam(){
        return $_POST['company_id'];
    }

    private function isDvagEmailAddress($email){
        if (strpos($email, '@dvag') !== false OR strpos($email, '@job-step.com') !== false ) {
            return 1;
        }
        return 0;
    }
}