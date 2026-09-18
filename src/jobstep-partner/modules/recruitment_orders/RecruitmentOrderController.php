<?php
namespace modules\recruitment_orders;

use modules\user\UserService;

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/env.php");

class RecruitmentOrderController {
    public function __construct(RecruitmentOrderService $recruitmentOrderService, UserService $userService) 
    {
        $this->recruitmentOrderService = $recruitmentOrderService;
        $this->userService = $userService;
    }

    public function getAllJobs(){
        Global $envConfig;
        $this->checkAuth();
        $lang = $this->getLangParam();
        $data = $this->recruitmentOrderService->getAllJobs($lang);
        $linkEnv = "registration";
        if($envConfig->APP_ENV != "production"){
            $linkEnv = "registration2";
        }

        $desc = "User otvorio listu naloga.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully fetched all jobs.",
            "data" => $data,
            "generic_link" => "https://job-step.net/$linkEnv/2040/".$_SESSION['token']."/".$lang
        ]);
    }

    public function getJobDetails(){
        $this->checkAuth();
        $lang = $this->getLangParam();
        $job_id = $this->getJobIdParam();
        $data = $this->recruitmentOrderService->getJobDetails($lang, $job_id);

        $desc = "User otvorio detalje naloga.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully fetched job details.",
            "data" => $data
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

    private function getLangParam(){
        if(!isset($_GET['lang'])){
            $lang = 'de';
        } else {
            $lang = $_GET['lang'];
        }
        return $lang;
    }

    private function getJobIdParam(){
        if(!isset($_GET['job_id'])){
            http_response_code(400);
            die("Missing job_id parameter.");
        } else {
            $job_id = $_GET['job_id'];
        }
        return $job_id;
    }
}
