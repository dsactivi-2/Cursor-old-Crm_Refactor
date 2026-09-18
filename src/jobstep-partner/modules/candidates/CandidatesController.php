<?php
namespace modules\candidates;

use modules\user\UserService;

class CandidatesController {
    public function __construct(CandidatesService $candidatesService, UserService $userService)
    {
        $this->candidatesService = $candidatesService;
        $this->userService = $userService;
    }

    public function getCandidateProfile(){
        $this->checkAuth();
        $lang = $this->getLangParam();
        $candidate_id = $this->getCandidateIdParam();
        $data = $this->candidatesService->getCandidateProfile($candidate_id, $lang);

        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched candidate profile',
            'data' => $data
        ]); 
    }

    public function getCandidateProfileV2(){
        $this->checkAuth();
        $lang = $this->getLangParam();
        $candidate_id = $this->getCandidateIdParam();
        $data = $this->candidatesService->getCandidateProfileV2($candidate_id, $lang);

        $desc = "User otvorio detalje kandidata.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched candidate profile',
            'data' => $data
        ]); 
    }

    public function getCandidateAnalytics(){
        $this->checkAuth();
        $data = $this->candidatesService->getCandidateAnalytics();

        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched candidates analytics',
            'data' => $data
        ]); 
    }

    public function getCandidateAnalyticsV2(){
        $this->checkAuth();
        $data = $this->candidatesService->getCandidateAnalyticsV2();

        $desc = "User otvorio analitiku kandidata.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched candidates analytics',
            'data' => $data
        ]); 
    }
    public function getCandidateStatuses(){
        $this->checkAuth();
        $data = $this->candidatesService->getCandidateStatuses();

        $desc = "User otvorio status kandidata.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched candidates statuses',
            'data' => $data
        ]);
    }

    public function getCandidates(){
        $this->checkAuth();
        $status = $this->getStatusParam();
        $order = $this->getOrderParam();
        $desc = $this->getDescParam();
        $page = $this->getPageParam();
        $limit = $this->getLimitParam();
        $search = $this->getSearchParam();

        $data = $this->candidatesService->getCandidates($status, $order, $desc, $page, $limit);
        $number_of_candidates = $this->candidatesService->getNumberOfCandidates($status);
        $total_items = $number_of_candidates;
        $total_pages = $limit !== null ? ceil($total_items / $limit) : 1;
        
        $current_page = $page !== null ? $page : 1;
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched candidates',
            'data' => $data,
            'total_items' => intval($total_items),
            'total_pages' => intval($total_pages),
            'current_page' => intval($current_page)
        ]);
    }

    public function getCandidatesV2(){
        $this->checkAuth();
        $status = $this->getStatusParam();
        $order = $this->getOrderParam();
        $desc = $this->getDescParam();
        $page = $this->getPageParam();
        $limit = $this->getLimitParam();
        $search = $this->getSearchParam();

        $data = $this->candidatesService->getCandidatesV2($status, $order, $desc, $page, $limit, $search);
        $number_of_candidates = $this->candidatesService->getNumberOfCandidatesV2($status, $search);
        $total_items = $number_of_candidates;
        $total_pages = $limit !== null ? ceil($total_items / $limit) : 1;
        
        $current_page = $page !== null ? $page : 1;

        $log_desc = "User otvorio listu kandidata.";
        $this->userService->insertUserLog($log_desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched candidates',
            'data' => $data,
            'total_items' => intval($total_items),
            'total_pages' => intval($total_pages),
            'current_page' => intval($current_page)
        ]);
    }

    public function getNumberOfCandidates(){
        $this->checkAuth();
        $data = $this->candidatesService->getNumberOfCandidates();

        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched number of candidates',
            'data' => $data
        ]);
    }

    public function getNumberOfCandidatesV2(){
        $this->checkAuth();
        $data = $this->candidatesService->getNumberOfCandidatesV2();

        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched number of candidates',
            'data' => $data
        ]);
    }
    
    public function getLeadCandidates(){
        $this->checkAuth();
        $status = $this->getStatusParam();
        $order = $this->getOrderParam();
        $desc = $this->getDescParam();
        $page = $this->getPageParam();
        $limit = $this->getLimitParam();
        $search = $this->getSearchParam();
        $data = $this->candidatesService->getLeadCandidates($status, $order, $desc, $page, $limit, $search);

        $total_items = $this->candidatesService->getLeadCount($status, $search);
        $total_pages = $limit !== null ? ceil($total_items / $limit) : 1;
        $current_page = $page !== null ? $page : 1;

        $log_desc = "User otvorio listu lead kandidata.";
        $this->userService->insertUserLog($log_desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched leads',
            'data' => $data,
            'total_items' => intval($total_items),
            'total_pages' => intval($total_pages),
            'current_page' => intval($current_page)
        ]);
    }

    public function getLeadAnalytics(){
        $this->checkAuth();

        $data = $this->candidatesService->getLeadAnalytics();

        $desc = "User otvorio analitiku lead kandidata.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched lead analytics',
            'data' => $data
        ]);
    }

    public function getLeadProfile(){
        $this->checkAuth();
        $candidate_id = $this->getCandidateIdParam();

        $data = $this->candidatesService->getLeadProfile($candidate_id);

        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched lead profile',
            'data' => $data
        ]);
    }

    public function getLeadProfileV2(): void {
        $this->checkAuth();
        $candidate_id = $this->getCandidateIdParam();

        $data = $this->candidatesService->getLeadProfileV2($candidate_id);

        $desc = "User otvorio detalje lead kandidata.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched lead profile',
            'data' => $data
        ]);
    }

    public function getLeadInfo(): void {
      $this->checkAuth();
      $candidate_id = $this->getCandidateIdParam();

      $data = $this->candidatesService->getLeadInfo($candidate_id);

      http_response_code(200);
      echo json_encode([
          'message' => 'Successfully fetched lead info',
          'data' => $data
      ]);
    }

    public function getLeadEmployerInfo(): void {
      $this->checkAuth();
      $candidate_id = $this->getCandidateIdParam();

      $data = $this->candidatesService->getLeadEmployerInfo($candidate_id);

      http_response_code(200);
      echo json_encode([
          'message' => 'Successfully fetched lead info',
          'data' => $data
      ]);
    }



    public function updateLead(){
        $this->checkAuth();
        $candidate_id = $this->getCandidateIdParam();
        $insurance_interest = $this->getInsuranceInterestParam();
        $phone = $this->getPhoneParam();
        $note = $this->getNoteParam();

        $data = $this->candidatesService->updateLead($candidate_id, $insurance_interest, $phone, $note);

        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully updated lead profile.',
            'data' => $data
        ]);
    }

    public function getLeadCount(){
        $this->checkAuth();

        $data = $this->candidatesService->getLeadCount();

        http_response_code(200);
        echo json_encode([
            'message' => 'Successfully fetched lead count',
            'data' => $data
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

    private function getCandidateIdParam(){
        if(isset($_GET['id'])){
            $candidate_id = $_GET['id'];
            return $candidate_id;
        } else {
            http_response_code(400);
            die("Candidate id is required.");
        }
    }
    
    private function getStatusParam(){
        if(isset($_GET['status'])){
            $status = $_GET['status'];
        } else {
            $status = null;
        }
        return $status;
    }

    private function getOrderParam(){
        if(isset($_GET['order'])){
            $order = $_GET['order'];
        } else {
            $order = null;
        }
        return $order;
    }

    private function getDescParam(){
        if(isset($_GET['desc'])){
            $desc = $_GET['desc'];
        } else {
            $desc = null;
        }
        return $desc;
    }
    
    private function getPageParam(){
        if(isset($_GET['page'])){
            $page = $_GET['page'];
        } else {
            $page = null;
        }
        return $page;
    }

    private function getLimitParam(){
        if(isset($_GET['limit'])){
            $limit = $_GET['limit'];
        } else {
            $limit = null;
        }
        return $limit;
    }

    private function getInsuranceInterestParam(){
        if(isset($_GET['insurance_interest'])){
            $insurance_interest = $_GET['insurance_interest'];
            if(!in_array($insurance_interest, ['lead', 'remindLater', 'client', 'notInterested'])){
                http_response_code(400);
                die($insurance_interest.' is not a valid action');
            }
            else return $insurance_interest;
        }
        else{
            http_response_code(400);
            die('insurance_interest is required');
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

    private function getPhoneParam(){
        if(isset($_GET['contact_phone'])){
            $phone = $_GET['contact_phone'];
        }
        else{
            http_response_code(400);
            die('phoneRequired');
        }
        return $phone;
    }

    private function getNoteParam(){
        if(isset($_GET['note'])){
            $note = $_GET['note'];
        }
        else{
            http_response_code(400);
            die('noteRequired');
        }
        return $note;
    }

    private function getSearchParam(){
        if(isset($_GET['search'])){
            $search = $_GET['search'];
        } else {
            $search = null;
        }
        return $search;
    }
}

