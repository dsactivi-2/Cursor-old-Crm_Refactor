<?php
namespace modules\companies;

use modules\user\UserService;

class CompaniesController {
    public function __construct(CompaniesService $companiesService, UserService $userService)
    {
        $this->companiesService = $companiesService;
        $this->userService = $userService;
    }

    public function getNumberOfCompanies(){
        $this->checkAuth();

        $data = $this->companiesService->getNumberOfCompanies();
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully fetched number of companies.",
            "data" => $data
        ]);
    }

    public function getNumberOfCompaniesV2(){
        $this->checkAuth();

        $data = $this->companiesService->getNumberOfCompaniesV2();
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully fetched number of companies.",
            "data" => $data
        ]);
    }

    public function getCompanyById(){
        $this->checkAuth();

        $company_id = $this->getCompanyIdParam();
        $lang = $this->getLangParam();
        $data = $this->companiesService->getCompanyById($company_id, $lang);

        $desc = "User otvorio detalje kompanije.";
        $this->userService->insertUserLog($desc);
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully fetched company.",
            "data" => $data
        ]);
    }

    public function getPositionsInfo(){
        $this->checkAuth();

        $lang = $this->getLangParam();
        $data = $this->companiesService->getPositionsInfo($lang);

        http_response_code(200);
        echo json_encode([
            "message" => "Successfully fetched positions.",
            "data" => $data
        ]);
    }

    public function getCompanies(){
        $this->checkAuth();

        $order = $this->getOrderParam();
        $desc = $this->getDescParam();
        $page = $this->getPageParam();
        $limit = $this->getLimitParam();
        $search = $this->getSearchParam();
        
        
        $data = $this->companiesService->getCompanies($order, $desc, $page, $limit, $search);
        $number_of_companies = $this->companiesService->getNumberOfCompanies($search);
        $total_items = $number_of_companies;
        $total_pages = $limit !== null ? ceil($total_items / $limit) : 1;
        $current_page = $page !== null ? $page : 1;
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully fetched companies.",
            "data" => $data,
            "total_items" => intval($total_items),
            "total_pages" => intval($total_pages),
            "current_page" => intval($current_page)
        ]);
    }
    
    public function getCompaniesV2(){
        $this->checkAuth();

        $order = $this->getOrderParam();
        $desc = $this->getDescParam();
        $page = $this->getPageParam();
        $limit = $this->getLimitParam();
        $search = $this->getSearchParam();
        
        
        $data = $this->companiesService->getCompaniesV2($order, $desc, $page, $limit, $search);
        $number_of_companies = $this->companiesService->getNumberOfCompaniesV2($search);
        $total_items = $number_of_companies;
        $total_pages = $limit !== null ? ceil($total_items / $limit) : 1;
        $current_page = $page !== null ? $page : 1;

        $log_desc = "User otvorio listu kompanija.";
        $this->userService->insertUserLog($log_desc);
        http_response_code(200);
        echo json_encode([
            "message" => "Successfully fetched companies.",
            "data" => $data,
            "total_items" => intval($total_items),
            "total_pages" => intval($total_pages),
            "current_page" => intval($current_page)
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

    private function getCompanyIdParam(){
        if(!isset($_GET['id'])){
            http_response_code(400);
            die("Missing company_id parameter.");
        }
        return $_GET['id'];
    }

    private function getLangParam(){
        if(!isset($_GET['lang'])){
            $lang = 'de';
        } else {
            $lang = $_GET['lang'];
        }
        return $lang;
    }

    private function getOrderParam(){
        if(!isset($_GET['order'])){
            $order = null;
        } else {
            $order = $_GET['order'];
        }
        return $order;
    }

    private function getDescParam(){
        if(!isset($_GET['desc'])){
            $desc = null;
        } else {
            $desc = $_GET['desc'];
        }
        return $desc;
    }
    
    private function getPageParam(){
        if(!isset($_GET['page'])){
            $page = null;
        } else {
            $page = $_GET['page'];
        }
        return $page;
    }

    private function getLimitParam(){
        if(!isset($_GET['limit'])){
            $limit = null;
        } else {
            $limit = $_GET['limit'];
        }
        return $limit;
    }

    private function getSearchParam(){
        if(!isset($_GET['search'])){
            $search = null;
        } else {
            $search = $_GET['search'];
        }
        return $search;
    }
}