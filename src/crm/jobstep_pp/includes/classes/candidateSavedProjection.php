<?php 
class candidateSavedProjection {
    private $candidate_id;
    private $candidate_key;
    private $candidate_fullname;
    private $sp_status;
    private $nd_status;
    private $company_id;
    private $company_name;
    private $nalog_id;
    private $nalog_name;
    private $partner_id;
    private $partner_name;
    private $candidate_assessment;
    private $candidate_position;
    private $candidate_language_level;
    private $candidate_language_status;

    function __construct(
        $arg_candidate_id,
        $arg_candidate_key,
        $arg_candidate_fullname,
        $arg_sp_status,
        $arg_nd_status,
        $arg_company_id,
        $arg_company_name,
        $arg_nalog_id,
        $arg_nalog_name,
        $arg_partner_id,
        $arg_partner_name,
        $arg_candidate_assessment,
        $arg_candidate_position,
        $arg_candidate_language_level,
        $arg_candidate_language_status
    ){
        $this -> candidate_id = $arg_candidate_id;
        $this -> candidate_key = $arg_candidate_key;
        $this -> candidate_fullname = $arg_candidate_fullname;
        $this -> sp_status = $arg_sp_status;
        $this -> nd_status = $arg_nd_status;
        $this -> company_id = $arg_company_id;
        $this -> company_name = $arg_company_name;
        $this -> nalog_id = $arg_nalog_id;
        $this -> nalog_name = $arg_nalog_name;
        $this -> partner_id = $arg_partner_id;
        $this -> partner_name = $arg_partner_name;
        $this -> candidate_assessment = $arg_candidate_assessment;
        $this -> candidate_position = $arg_candidate_position;
        $this -> candidate_language_level = $arg_candidate_language_level;
        $this -> candidate_language_status = $arg_candidate_language_status;
    }

    public function getCandidateLanguageStatus(){
        return $this -> candidate_language_status;
    }
    public function getCandidateLanguageLevel(){
        return $this -> candidate_language_level;
    }
    public function getCandidateID(){
        return $this -> candidate_id;
    }
    public function getCandidateKey(){
        return $this -> candidate_key;
    }
    public function getCandidateFullname(){
        return $this -> candidate_fullname;
    }
    public function getCandidateStatus(){
        return $this -> sp_status;
    }
    public function getCandidateNostrificationStatus(){
        return $this -> nd_status;
    }
    public function getCandidateCompanyID(){
        return $this -> company_id;
    }
    public function getCandidateCompanyName(){
        return $this -> company_name;
    }
    public function getCandidateNalogID(){
        return $this -> nalog_id;
    }
    public function getCandidateNalogName(){
        return $this -> nalog_name;
    }
    public function getCandidatePartnerID(){
        return $this -> partner_id;
    }
    public function getCandidatePartnerName(){
        return $this -> partner_name;
    }
    public function getCandidatePosition(){
        return $this -> candidate_position;
    }
    public function getAgreedStartDate(){
        return $this -> candidate_assessment;
    }
    public function getCandidateStartMonth(){
        if(is_null($this -> getAgreedStartDate())){
            return date('n', $this->getCandidateProjectedDate());
        }
        return date('n', $this -> getAgreedStartDate());
    }
    public function getCandidateStartYear(){
        if(is_null($this -> getAgreedStartDate())){
            return date('Y', $this->getCandidateProjectedDate());
        }
        return date('Y', $this -> getAgreedStartDate());
    }
}