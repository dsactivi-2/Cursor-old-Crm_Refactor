<?php
class candidateAssessmentCalculation

{

    private $candidate_id;
    private $candidate_status;
    private $candidate_days_on_status;
    private $candidate_nostrification_status;
    private $candidate_days_on_nostrification_status;
    private $candidate_language_level;
    private $candidate_language_status;
    private $candidate_days_on_language_status;
    private $candidate_language_exam_date;
    private $candidate_language_sublevel_start_date_1;
    private $candidate_language_sublevel_end_date_1;
    private $candidate_language_sublevel_start_date_2;
    private $candidate_language_sublevel_end_date_2;
    private $candidate_language_last_updated;
    private $candidate_key;
    private $candidate_fullname;
    private $candidate_company_id;
    private $candidate_nalog_id;
    private $candidate_nalog_name;
    private $candidate_partner_id;
    private $candidate_partner_name;
    private $candidate_has_interview;
    private $candidate_dipl_id;
    private $candidate_is_glossa;
    private $candidate_days_on_language_for_course;
    private $candidate_agreed_start_date;
    private $candidate_potential_start_date;
    private $is_eu_candidate;
    private $candidate_departure_type;
    private $candidate_visa_appointment;
    private $candidate_motive_start_date;
    private $candidate_motive_end_date;

    private $default_duration;
    private $candidate_assessment;

    private $detailed_pre_contract_assessment = array();
    private $detailed_after_contract_assessment = array();
    private $detailed_nostrification_assessment = array();
    private $detailed_language_assessment = array();

    

    function __construct(
        $arg_id,
        $arg_status,
        $arg_days_on_status,
        $arg_nostrification_status,
        $arg_days_on_nostrification_status,
        $arg_language_level,
        $arg_language_status,
        $arg_days_on_language_status,
        $arg_language_exam_date,
        $arg_default_duration,
        $arg_candidate_key,
        $arg_candidate_fullname,
        $arg_company_id,
        $arg_company_name,
        $arg_nalog_id,
        $arg_nalog_name,
        $arg_partner_id,
        $arg_partner_name,
        $arg_has_interview,
        $arg_dipl_id,
        $candidate_is_glossa,
        $arg_candidate_language_sublevel_start_date_1,
        $arg_candidate_language_sublevel_end_date_1,
        $arg_candidate_language_sublevel_start_date_2,
        $arg_candidate_language_sublevel_end_date_2,
        $arg_candidate_language_last_updated,
        $arg_candidate_days_on_language_for_course,
        $arg_candidate_agreed_start_date,
        $arg_candidate_potential_start_date,
        $arg_is_eu_candidate,
        $arg_candidate_departure_type,
        $arg_candidate_visa_appointment,
        $arg_candidate_motive_start_date,
        $arg_candidate_motive_end_date
    ) {            

        $this->candidate_id                               = $arg_id;
        $this->candidate_status                           = $arg_status;
        $this->candidate_days_on_status                   = $arg_days_on_status;
        $this->candidate_nostrification_status            = $arg_nostrification_status;
        $this->candidate_days_on_nostrification_status    = $arg_days_on_nostrification_status;
        $this->candidate_language_level                   = $arg_language_level;
        $this->candidate_language_status                  = $arg_language_status;
        $this->candidate_days_on_language_status          = $arg_days_on_language_status;
        $this->candidate_language_exam_date               = $arg_language_exam_date;
        $this->default_duration                           = $arg_default_duration;

        $this -> candidate_key             = $arg_candidate_key;
        $this -> candidate_fullname        = $arg_candidate_fullname;
        $this -> candidate_company_id      = $arg_company_id;
        $this -> candidate_company_name    = $arg_company_name;
        $this -> candidate_nalog_id        = $arg_nalog_id;
        $this -> candidate_nalog_name      = $arg_nalog_name;
        $this -> candidate_partner_id      = $arg_partner_id;
        $this -> candidate_partner_name    = $arg_partner_name;
        $this -> candidate_has_interview   = $arg_has_interview;
        $this -> candidate_dipl_id         = $arg_dipl_id;
        $this -> candidate_is_glossa       = $candidate_is_glossa;


        $this -> candidate_language_sublevel_start_date_1     = $arg_candidate_language_sublevel_start_date_1;
        $this -> candidate_language_sublevel_end_date_1       = $arg_candidate_language_sublevel_end_date_1;
        $this -> candidate_language_sublevel_start_date_2     = $arg_candidate_language_sublevel_start_date_2;
        $this -> candidate_language_sublevel_end_date_2       = $arg_candidate_language_sublevel_end_date_2;
        $this -> candidate_language_last_updated              = $arg_candidate_language_last_updated;
        $this -> candidate_days_on_language_for_course        = $arg_candidate_days_on_language_for_course;

        $this -> candidate_agreed_start_date                  = $arg_candidate_agreed_start_date;
        $this -> candidate_potential_start_date               = $arg_candidate_potential_start_date;

        $this -> is_eu_candidate    = $arg_is_eu_candidate;
        
        $this -> candidate_departure_type       = $arg_candidate_departure_type;
        $this -> candidate_visa_appointment     = $arg_candidate_visa_appointment;

        $this -> candidate_motive_start_date    = $arg_candidate_motive_start_date;
        $this -> candidate_motive_end_date      = $arg_candidate_motive_end_date;        
    }

//GETTERS start
    public function getAgreedStartDate(){
        if(!is_null($this -> candidate_agreed_start_date)){
            return strtotime($this -> candidate_agreed_start_date);
        }
        return ($this -> candidate_agreed_start_date);
    }
    public function getPotentialStartDate(){
        if(!is_null($this -> candidate_potential_start_date)){
            return strtotime($this -> candidate_potential_start_date);
        }
        return ($this -> candidate_potential_start_date);
    }
    public function candidateHasInterview(){
        return $this -> candidate_has_interview;
    }
    public function getCandidateID(){
        return $this -> candidate_id;
    }
    public function getCandidateDIPLID(){
        return $this -> candidate_dipl_id;
    }
    public function getCandidateStatus(){
        return $this -> candidate_status;
    }
    public function getCandidateDaysOnStatus()
    {
        return $this->candidate_days_on_status;
    }
    public function getCandidateNostrificationStatus()
    {
        return $this->candidate_nostrification_status;
    }
    public function getCandidateDaysOnNostrificationStatus()
    {
        return $this->candidate_days_on_nostrification_status;
    }
    public function getCandidateLanguageLevel()
    {
        return $this->candidate_language_level;
    }
    public function getCandidateLanguageStatus()
    {
        return $this->candidate_language_status;
    }
    public function getCandidateDaysOnLanguageStatus()
    {
        return $this->candidate_days_on_language_status;
    }
    public function getCandidateCandidateLanguageExamDate()
    {
        return $this->candidate_language_exam_date;
    }
    public function getCandidateAssessment()
    {
        return $this->candidate_assessment;
    }
    public function getCandidateKey()
    {
        return $this->candidate_key;
    }
    public function getCandidateFullname()
    {
        return $this->candidate_fullname;
    }
    public function getCandidateCompanyID()
    {
        return $this->candidate_company_id;
    }
    public function getCandidateCompanyName()
    {
        return $this->candidate_company_name;
    }
    public function getCandidateNalogID()
    {
        return $this->candidate_nalog_id;
    }
    public function getCandidateNalogName()
    {
        return $this->candidate_nalog_name;
    }
    public function getCandidatePartnerID()
    {
        return $this->candidate_partner_id;
    }
    public function getCandidatePartnerName()
    {
        return $this->candidate_partner_name;
    }

    public function getDefaultDuration()
    {
        return $this->getTrueDurationOfNostrification();
    }

    public function getLanguageLevel()
    {
        return $this->candidate_language_level;
    }
    public function getCandidateStartMonth()
    {
        if(is_null($this -> getAgreedStartDate())){
            return date('n', $this->getCandidateProjectedDate());
        }
        return date('n', $this -> getAgreedStartDate());
    }
    public function getCandidateStartYear()
    {
        if(is_null($this -> getAgreedStartDate())){
            return date('Y', $this->getCandidateProjectedDate());
        }
        return date('Y', $this -> getAgreedStartDate());
    }
    public function getDetailedPreContractAssessment(){
        return $this -> detailed_pre_contract_assessment;
    }
    public function getDetailedAfterContractAssessment(){
        return $this -> detailed_after_contract_assessment;
    }
    public function getDetailedNostrificationAssessment(){
        return $this -> detailed_nostrification_assessment;
    }
    public function getDetailedLanguageAssessment(){
        return $this -> detailed_language_assessment;
    }
    public function isCandidateGlossa(){
        return $this -> candidate_is_glossa;
    }
//GETTERS end
    public function setDetailedPreContractAssessment(){
        $this -> detailed_pre_contract_assessment =  func_get_args();
    }
    public function overwriteDetailedPreContractAssessment($detailed_assessment){
        $this -> detailed_pre_contract_assessment = $detailed_assessment;
    }
    public function unshiftDetailedPreContractAssessment($detailed_assessment){
        if($detailed_assessment['type'] == 'current'){
            foreach($this -> detailed_pre_contract_assessment as &$status_data){
                if($status_data['type'] == 'current'){
                    $status_data['start_date']   = $detailed_assessment['start_date'];
                    $status_data['duration']    += $detailed_assessment['duration'];
                    return;
                }
            }
        }
        array_unshift($this -> detailed_pre_contract_assessment, $detailed_assessment);
    }
    public function setDetailedAfterContractAssessment(){
        $this -> detailed_after_contract_assessment = func_get_args();
    }
    public function overwriteDetailedAfterContractAssessment($detailed_assessment){
        $this -> detailed_after_contract_assessment = $detailed_assessment;
    }
    public function unshiftDetailedAfterContractAssessment($detailed_assessment){
        if($detailed_assessment['type'] == 'current'){
            foreach($this -> detailed_after_contract_assessment as &$status_data){
                if($status_data['type'] == 'current'){

                    if($status_data['is_final']){
                        $status_data['start_date']   = $detailed_assessment['start_date'];
                        $status_data['end_date']   = $detailed_assessment['end_date'];
                        $status_data['duration']    += $detailed_assessment['duration'];
                    }
                    else{
                        $status_data['start_date']   = $detailed_assessment['start_date'];
                        $status_data['duration']    += $detailed_assessment['duration'];
                    }
                    return;
                }
            }
        }
        array_unshift($this -> detailed_after_contract_assessment, $detailed_assessment);
    }
    public function setDetailedNostrificationAssessment(){
        $this -> detailed_nostrification_assessment =  func_get_args();
    }
    public function overwriteDetailedNostrificationAssessment($detailed_assessment){
        $this -> detailed_nostrification_assessment = $detailed_assessment;
    }
    public function unshiftDetailedNostrificationAssessment($detailed_assessment){
        if($detailed_assessment['type'] == 'current'){
            foreach($this -> detailed_nostrification_assessment as &$status_data){
                if($status_data['type'] == 'current'){
                    $status_data['start_date']   = $detailed_assessment['start_date'];
                    $status_data['duration']    += $detailed_assessment['duration'];
                    return;
                }
            }
        }
        array_unshift($this -> detailed_nostrification_assessment, $detailed_assessment);
    }
    public function setDetailedLanguageAssessment(){
        $this -> detailed_language_assessment = func_get_args();
    }
    public function overwriteDetailedLanguageAssessment($detailed_assessment){
        $this -> detailed_language_assessment = $detailed_assessment;
    }
    public function unshiftDetailedLanguageAssessment($detailed_assessment){
        if($detailed_assessment['type'] == 'current'){
            foreach($this -> detailed_language_assessment as &$status_data){
                if($status_data['type'] == 'current' AND date('d.m.Y', $status_data['start_date']) != date('d.m.Y')){
                    $status_data['start_date']   = $detailed_assessment['start_date'];
                    $status_data['duration']    += $detailed_assessment['duration'];
                    
                    return;
                }
            }
        }
        array_unshift($this -> detailed_language_assessment, $detailed_assessment);
    }
    private function getDaysRemainingOnCurrentStatus($days_on_current_status, $default_duration_of_status){

        $result = 0;
        if ($days_on_current_status < $default_duration_of_status) {
            $result = $default_duration_of_status - $days_on_current_status;
        }
        return $result;
    }
    public function getTrueDurationOfNostrification(){
        if($this -> is_eu_candidate == 1 OR $this -> candidate_departure_type == 2){
            return 0;
        }
        $duration_nostrification = 0;
        $duration_for_current_status = 0;
        
        switch($this -> getCandidateNostrificationStatus()){
            case 1:
            case 7:
                $duration_nostrification = (-1) * $this -> default_duration -> getDurationOfNostrification();

                $this -> setDetailedNostrificationAssessment(
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration   -> getDurationOfNDCollectingDocuments(),
                        'status_name' => $this -> default_duration   -> getDurationOfNDCollectingDocumentsStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfNDDocumentsSent(),
                        'status_name' => $this -> default_duration   -> getDurationOfNDDocumentsSentStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfNDInstitutionProcessing(),
                        'status_name' => $this -> default_duration -> getDurationOfNDInstitutionProcessingStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfNDRenewal(),
                        'status_name' => $this -> default_duration -> getDurationOfNDRenewalStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    )
                );
            break;
            
            case 2:
                $duration_nostrification = 
                    $this -> getDaysRemainingOnCurrentStatus(
                        $this                       -> getCandidateDaysOnNostrificationStatus(), 
                        $this -> default_duration   -> getDurationOfNDCollectingDocuments()
                    )                                                                   + 
                    $this -> default_duration -> getDurationOfNDDocumentsSent()         +
                    $this -> default_duration -> getDurationOfNDInstitutionProcessing() +
                    $this -> default_duration -> getDurationOfNDRenewal();
                
                $this -> setDetailedNostrificationAssessment(
                    array(
                        'type'  => 'current',
                        'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnNostrificationStatus(), $this -> default_duration   -> getDurationOfNDCollectingDocuments()),
                        'status_name' => $this -> default_duration -> getDurationOfNDCollectingDocumentsStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfNDDocumentsSent(),
                        'status_name' => $this -> default_duration -> getDurationOfNDDocumentsSentStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfNDInstitutionProcessing(),
                        'status_name' => $this -> default_duration -> getDurationOfNDInstitutionProcessingStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfNDRenewal(),
                        'status_name' => $this -> default_duration -> getDurationOfNDRenewalStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    )
                );
            break;
            case 3:
                $duration_nostrification = 
                    $this -> getDaysRemainingOnCurrentStatus(
                        $this                       -> getCandidateDaysOnNostrificationStatus(), 
                        $this -> default_duration   -> getDurationOfNDDocumentsSent()
                    )                                                                   + 
                    $this -> default_duration -> getDurationOfNDInstitutionProcessing() +
                    $this -> default_duration -> getDurationOfNDRenewal();

                $this -> setDetailedNostrificationAssessment(
                    array(
                        'type'  => 'current',
                        'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnNostrificationStatus(), $this -> default_duration   -> getDurationOfNDDocumentsSent()),
                        'status_name' => $this -> default_duration ->getDurationOfNDDocumentsSentStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfNDInstitutionProcessing(),
                        'status_name' => $this -> default_duration -> getDurationOfNDInstitutionProcessingStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfNDRenewal(),
                        'status_name' => $this -> default_duration -> getDurationOfNDRenewalStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    )
                );        
                
            break;
            case 4:
                $duration_nostrification = 
                    $this -> getDaysRemainingOnCurrentStatus(
                        $this                       -> getCandidateDaysOnNostrificationStatus(), 
                        $this -> default_duration   -> getDurationOfNDInstitutionProcessing()
                    )                                                                   + 
                    $this -> default_duration -> getDurationOfNDRenewal();
                $this -> setDetailedNostrificationAssessment(
                    array(
                        'type'  => 'current',
                        'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnNostrificationStatus(), $this -> default_duration   -> getDurationOfNDInstitutionProcessing()),
                        'status_name' => $this -> default_duration -> getDurationOfNDInstitutionProcessingStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfNDRenewal(),
                        'status_name' => $this -> default_duration -> getDurationOfNDRenewalStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    )
                );
            break;
            case 5:
                $duration_nostrification =
                    $this->getDaysRemainingOnCurrentStatus(
                        $this->getCandidateDaysOnNostrificationStatus(),
                        $this->default_duration->getDurationOfNDRenewal()
                    );

                $this -> setDetailedNostrificationAssessment(
                    array(
                        'type'  => 'current',
                        'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnNostrificationStatus(), $this -> default_duration   -> getDurationOfNDRenewal()),
                        'status_name' => $this -> default_duration -> getDurationOfNDRenewalStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    )
                );

            break;
            case 6:
                $duration_nostrification = 0;
                break;
        }
        return $duration_nostrification;
    }

    public function getDurationOfMotiveLanguageCourses($language_level){
        $duration_of_A1 = 0;
        $duration_of_A2 = 0;

        if($language_level == "A1"){
            $duration_of_A1 = max(0, floor((strtotime($this -> candidate_motive_end_date) - time())/(86400)));
            $duration_of_A2 = $this -> default_duration -> getDurationOfMotiveA2();
        }
        else if($language_level == "A2"){
            $language_of_A2 = max(0, floor((strtotime($this -> candidate_motive_end_date)- time())/(86400)));
        }
        return $duration_of_A1 + $duration_of_A2;
    }

    public function getDetailedDurationOfMotiveLanguageCourses($language_level){
        $default_duration_A2 = $this -> default_duration -> getDurationOfMotiveA2();
        
        $start_date = max(time(), strtotime($this -> candidate_motive_start_date));
        $end_date = strtotime($this -> candidate_motive_end_date);
        
        if($language_level == "A1"){

            $start_date_A2 = date('Y-m-d', $end_date);
            $end_date_A2 = date('Y-m-d', strtotime($start_date_A2. ' + '.$default_duration_A2.' weekdays'));
            return array(
                array(
                    'type'  => 'current',
                    'duration' => floor((strtotime($end_date) - strtotime($start_date))/(86000)),
                    'status_name' => $this -> default_duration -> getDurationOfMotiveStatusName()." A1",
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ),
                array(
                    'type'  => 'future',
                    'duration' => floor((strtotime($end_date_A2) - strtotime($start_date_A2))/(86400)),
                    'status_name' => $this -> default_duration -> getDurationOfMotiveStatusName()." A2",
                    'start_date' => strtotime($start_date_A2),
                    'end_date' => strtotime($end_date_A2)
                )
            ); 
        }
        else if($language_level == "A2"){
            return array(
                array(
                    'type'  => 'current',
                    'duration' => floor((strtotime($end_date) - strtotime($start_date))/(86000)),
                    'status_name' => $this -> default_duration -> getDurationOfMotiveStatusName()." A2",
                    'start_date' => $start_date,
                    'end_date' => $end_date
                )
            );
        }
    }

    public function getDurationOfLanguageCourses($language_level, $language_sublevel, $mode){
        $number_of_courses = 0;
        $language_status = $this -> getCandidateLanguageStatus();
        if($language_level == "A1" OR $language_level == "0" OR $language_level == "") {
            $number_of_courses = 4;
        } 
        else if ($language_level == "A2") {
            $number_of_courses = 2;
        }
        if ($language_sublevel == 2) {
            $number_of_courses = $number_of_courses - 1;
        }

        if ($mode == 0){
            return $number_of_courses * $this->default_duration->getDurationOfLanguageCourse();
        }
        else{
            $sublevel_start_1   = $this -> candidate_language_sublevel_start_date_1;
            $sublevel_end_1     = $this -> candidate_language_sublevel_end_date_1;
            $sublevel_start_2   = $this -> candidate_language_sublevel_start_date_2;
            $sublevel_end_2     = $this -> candidate_language_sublevel_end_date_2;
            
            $candidate_language_last_updated = $this -> candidate_language_last_updated;
            $default_duration = $this -> default_duration -> getDurationOfLanguageCourse();
            $duration_A1_1 = 0;
            $duration_A1_2 = 0;
            $duration_A2_1 = 0;
            $duration_A2_2 = 0;

            if($language_level == "A1"){
                if($language_sublevel == 1){
                    
                    if(is_null($sublevel_start_1) && is_null($sublevel_end_1)){ 
                        $duration_A1_1 = $default_duration;
                    }
                    else if(!is_null($sublevel_end_1)){
                        $duration_A1_1 = max(0, floor((strtotime($sublevel_end_1) - time())/(60*60*24)));
                    }
                    else if(!is_null($sublevel_start_1)){
                        $duration_A1_1 = max(0, floor((strtotime($sublevel_start_1) - time())/(60*60*24)) + $default_duration);
                    }
                    
                    if(!is_null($sublevel_end_2)){
                        $duration_A1_2 = max(0, floor((strtotime($sublevel_end_2) - strtotime($sublevel_end_1))/(60*60*24)));
                    }
                    else if(!is_null($sublevel_start_2)){
                        $duration_A1_2 = max(0, floor((strtotime($sublevel_start_2) - strtotime($sublevel_end_1))/(60*60*24)) + $default_duration);
                    }
                    else{
                        $duration_A1_2 = $default_duration;
                    }
                }
                else if($language_sublevel == 2){
                    $duration_A1_1 = 0;

                    if(is_null($sublevel_start_2) && is_null($sublevel_end_2)){ 
                        $duration_A1_2 = max(0, floor((strtotime($candidate_language_last_updated) - time())/(60*60*24)) + $default_duration);
                    }
                    else if(!is_null($sublevel_end_2)){
                        $duration_A1_2 = max(0, floor((strtotime($sublevel_end_2) - time())/(60*60*24)));
                    }
                    else if(!is_null($sublevel_start_2)){
                        $duration_A1_2 = max(0, floor((strtotime($sublevel_start_2) - time())/(60*60*24)) + $default_duration);
                    }
                }
                
                $duration_A2_1 = $default_duration;
                $duration_A2_2 = $default_duration;
            }

            else if($language_level == "A2"){
                $duration_A1_1 = 0;
                $duration_A1_2 = 0;

                if($language_sublevel == 1){
                    if(is_null($sublevel_start_1) && is_null($sublevel_end_1)){ 
                        $duration_A2_1 = $default_duration;
                    }
                    else if(!is_null($sublevel_end_1)){
                        $duration_A2_1 = max(0, floor((strtotime($sublevel_end_1) - time())/(60*60*24)));
                    }
                    else if(!is_null($sublevel_start_1)){
                        $duration_A2_1 = max(0, floor((strtotime($sublevel_start_1) - time())/(60*60*24)) + $default_duration);
                    }
                    
                    if(!is_null($sublevel_end_2)){
                        $duration_A2_2 = max(0, floor((strtotime($sublevel_end_2) - strtotime($sublevel_end_1))/(60*60*24)));
                    }
                    else if(!is_null($sublevel_start_2)){
                        $duration_A2_2 = max(0, floor((strtotime($sublevel_start_2) - strtotime($sublevel_end_1))/(60*60*24)) + $default_duration);
                    }
                    else{
                        $duration_A2_2 = $default_duration;
                    }
                }
                else if($language_sublevel == 2){
                    $duration_A1_1 = 0;

                    if(is_null($sublevel_start_2) && is_null($sublevel_end_2)){ 
                        $duration_A1_2 = $default_duration;
                    }
                    else if(!is_null($sublevel_end_2)){
                        $duration_A1_2 = max(0, floor((strtotime($sublevel_end_2) - time())/(60*60*24)));
                    }
                    else if(!is_null($sublevel_start_2)){
                        $duration_A1_2 = max(0, floor((strtotime($sublevel_start_2) - time())/(60*60*24)) + $default_duration);
                    }
                }
            }
           
            return $duration_A1_1 + $duration_A1_2 + $duration_A2_1 + $duration_A2_2; 
        }

    
        
    } 
    public function getDetailedDurationOfLanguageCourses($language_level, $language_status){
        $sublevel_start_date_1  = $this -> candidate_language_sublevel_start_date_1;
        $sublevel_end_date_1    = $this -> candidate_language_sublevel_end_date_1;
        $sublevel_start_date_2  = $this -> candidate_language_sublevel_start_date_2;
        $sublevel_end_date_2    = $this -> candidate_language_sublevel_end_date_2;

        
        if(!is_null($sublevel_start_date_1) AND strtotime($sublevel_start_date_1) < strtotime(date('Y-m-d'))){
            $sublevel_start_date_1 = date('Y-m-d');
        }

        if(!is_null($sublevel_start_date_2) AND strtotime($sublevel_start_date_2) < strtotime(date('Y-m-d'))){
            $sublevel_start_date_2 = date('Y-m-d');
        }
        $duration_of_sublevel_1 = $this -> default_duration -> getDurationOfLanguageCourse();
        $duration_of_sublevel_2 = $this -> default_duration -> getDurationOfLanguageCourse();

        if(!is_null($sublevel_start_date_1) AND !is_null($sublevel_end_date_1)){
            $duration_of_sublevel_1 = floor(strtotime($sublevel_start_date_1) - strtotime($sublevel_end_date_1)) / (60 * 60 * 24);
        }
        
        if(!is_null($sublevel_start_date_2) AND !is_null($sublevel_end_date_2)){
            $duration_of_sublevel_2 = floor(strtotime($sublevel_start_date_2) - strtotime($sublevel_end_date_2))/ (60 * 60 * 24);
        }


        if(is_null($sublevel_start_date_1)){
            $sublevel_start_date_1 = "";
        }
        else{
            $sublevel_start_date_1 = strtotime($sublevel_start_date_1);
        }
        
        if(is_null($sublevel_start_date_2)){
            $sublevel_start_date_2 = "";
        }
        else{
            $sublevel_start_date_2 = strtotime($sublevel_start_date_2);
        }

        if(is_null($sublevel_end_date_1)){
            $sublevel_end_date_1 = "";
        }
        else{
            $sublevel_end_date_1 = strtotime($sublevel_end_date_1);
        }
        
        if(is_null($sublevel_end_date_2)){
            $sublevel_end_date_2 = "";
        }
        else{
            $sublevel_end_date_2 = strtotime($sublevel_end_date_2);
        }


        $return_array = array();
        if($language_status == 1 OR $language_status == 2 OR is_null($language_status) OR $language_status == 12){
            if($language_level == "A1" OR $language_level == "0"){

                return array(
                    array(
                        'type'  => 'future',
                        'duration' => $duration_of_sublevel_1,
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A1.1",
                        'start_date' => $sublevel_start_date_1,
                        'end_date' => $sublevel_end_date_1
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $duration_of_sublevel_2,
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A1.2",
                        'start_date' => $sublevel_start_date_2,
                        'end_date' => $sublevel_end_date_2
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfLanguageCourse(),
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A2.1",
                        'start_date' => '',
                        'end_date' => ''
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfLanguageCourse(),
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A2.2",
                        'start_date' => '',
                        'end_date' => ''
                    )
                ); 
            }
            else if($language_level == "A2"){
                
                $return_array = array(
                    array(
                        'type'  => 'future',
                        'duration' => $duration_of_sublevel_1,
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A2.1",
                        'start_date' => $sublevel_start_date_1,
                        'end_date' => $sublevel_end_date_1
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $duration_of_sublevel_2,
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A2.2",
                        'start_date' => $sublevel_start_date_2,
                        'end_date' => $sublevel_end_date_2
                    )
                );
            }
        }
        else if($language_status == 3){

            if($sublevel_start_date_1 == ""){
                $sublevel_start_date_1 = time();
            }
            if($sublevel_start_date_2 == "" AND $sublevel_end_date_1 != ""){
                $sublevel_start_date_2 = max($sublevel_end_date_1, time());
            }
            if($sublevel_end_date_1 == "" AND $sublevel_start_date_1 != ""){
                $sublevel_end_date_1 = time();
            }

            if($language_level == "A1"){
                $return_array = array(
                    array(
                        'type'  => 'current',
                        'duration' => $duration_of_sublevel_1,
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A1.1",
                        'start_date' => $sublevel_start_date_1,
                        'end_date' => $sublevel_end_date_1
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $duration_of_sublevel_2,
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A1.2",
                        'start_date' => $sublevel_start_date_2,
                        'end_date' => $sublevel_end_date_2
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfLanguageCourse(),
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A2.1",
                        'start_date' => '',
                        'end_date' => ''
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfLanguageCourse(),
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A2.2",
                        'start_date' => '',
                        'end_date' => ''
                    )
                ); 
            }
            else if($language_level == "A2"){
                $return_array = array(
                    array(
                        'type'  => 'current',
                        'duration' =>  $duration_of_sublevel_1,
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A2.1",
                        'start_date' => $sublevel_start_date_1,
                        'end_date' => $sublevel_end_date_1
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $duration_of_sublevel_2,
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A2.2",
                        'start_date' => $sublevel_start_date_2,
                        'end_date' => $sublevel_end_date_2
                    )
                );
            }
        }
        else if($language_status == 4){
            if($sublevel_start_date_2 == ""){
                if($sublevel_end_date_1 == ""){
                    $sublevel_start_date_2 = time();
                }
                else{
                    $sublevel_start_date_2  = max(time(), $sublevel_start_date_2);
                }
            }
            if($sublevel_end_date_1 == "" AND $sublevel_start_date_1 != ""){
                $sublevel_end_date_1 = time();
            }

            if($language_level == "A1"){
              
                $return_array = array(
                    array(
                        'type'  => 'current',
                        'duration' =>  $duration_of_sublevel_2,
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A1.2",
                        'start_date' => $sublevel_start_date_2,
                        'end_date' => $sublevel_end_date_2
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfLanguageCourse(),
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A2.1",
                        'start_date' => max($sublevel_end_date_2, time()),
                        'end_date' => ''
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfLanguageCourse(),
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A2.2",
                        'start_date' => '',
                        'end_date' => ''
                    )
                ); 
            }
            else if($language_level == "A2"){
                
                $return_array = array(
                    array(
                        'type'  => 'current',
                        'duration' =>  $duration_of_sublevel_2,
                        'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName()." A2.2",
                        'start_date' => $sublevel_start_date_2,
                        'end_date' => $sublevel_end_date_2
                    )
                );
            }
        }
        return $return_array;
        
    }
    public function getTrueDurationOfLanguage(){

        if($this -> is_eu_candidate == 1 OR $this -> candidate_departure_type == 2){
            return 0;
        }

        $duration_language = 0;
        $duration_for_current_status = 0;
        $language_level = $this->getCandidateLanguageLevel();
        $language_status = $this->getCandidateLanguageStatus();

        $language_course_prep_duration      = 0;
        $language_course_prep_status_name   = "";
        if($this -> isCandidateGlossa()){
            $language_course_prep_duration = $this -> default_duration -> getDurationOfGlossaAssessment();
            $language_course_prep_status_name = $this -> default_duration -> getDurationOfGlossaAssessmentStatusName();
        }
        else{
            if(!is_null($language_status)){
                $language_course_prep_duration = $this -> default_duration -> getDurationBeforeLanguageCourse();
                $language_course_prep_status_name = $this -> default_duration -> getDurationBeforeLanguageCourseStatusName();
            }
            else{
                if(
                    $language_level == "B1" OR
                    $language_level == "B2" OR
                    $language_level == "C1" OR
                    $language_level == "C2" 
                )
                {
                    $language_course_prep_duration = 0;
                    $language_course_prep_status_name = "";
                }
            }
        }

        if (is_null($language_status) or $language_status == 1 or $language_status == 2 or $language_status == 12) {
            $duration_language = (-1) * (
                $language_course_prep_duration +
                $this->getDurationOfLanguageCourses($language_level, 1, 0)          +
                $this->default_duration->getDurationOfWaitingForExamDate()
            );
            $pre_course_details = array();
            $course_details = $this -> getDetailedDurationOfLanguageCourses($language_level, $language_status);
            if($language_course_prep_duration != 0){
                $pre_course_details = array(
                    'type'  => 'future',
                    'duration' => $language_course_prep_duration,
                    'status_name' => $language_course_prep_status_name,
                    'start_date' => '',
                    'end_date' => ''
                );
                array_unshift($course_details, $pre_course_details);
            }
            $after_course_details = array(
                'type'  => 'future',
                'duration' => $this -> default_duration -> getDurationOfWaitingForExamDate(),
                'status_name' => $this -> default_duration -> getDurationOfWaitingForExamDateStatusName(),
                'start_date' => '',
                'end_date' => ''
            );
           
            array_push($course_details, $after_course_details);

            $this -> setDetailedLanguageAssessment(
                ...$course_details
            );
        }
        else{
            switch($language_status){

                // case 14:
                //     $duration_language =
                //         $this->getDaysRemainingOnCurrentStatus(
                //             $this->getCandidateDaysOnLanguageStatus(),
                //             $this->default_duration->getDurationOfGlossaAssessment()
                //         )                                                                               +
                //         $this -> getDurationOfLanguageCourses($language_level, 1)                       +
                //         $this -> getDurationOfWaitingForExamDate();
                        
                //     $this -> setDetailedLanguageAssessment(
                //         array(
                //             'type'  => 'future',
                //             'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnLanguageStatus(), $this -> default_duration   -> getDurationOfGlossaAssessment()),
                //             'status_name' => $this -> default_duration -> getDurationOfGlossaAssessmentStatusName(),
                //             'start_date' => '',
                //             'end_date' => ''
                //         ),
                //         array(
                //             'type'  => 'future',
                //             'duration' => $this -> getDurationOfLanguageCourses($language_level, 1),
                //             'status_name' => $this -> default_duration -> getDurationOfLanguageCourseStatusName(),
                //             'start_date' => '',
                //             'end_date' => ''
                //         ),
                //         array(
                //             'type'  => 'future',
                //             'duration' => $this -> default_duration -> getDurationOfWaitingForExamDate(),
                //             'status_name' => $this -> default_duration -> getDurationOfWaitingForExamDateStatusName(),
                //             'start_date' => '',
                //             'end_date' => ''
                //         )
                //     );
                // break;

                case 3:
                case 4:
                    $duration_language =
                        $this->getDurationOfLanguageCourses($language_level, ($language_status - 2), 1)
                        + 
                        $this -> default_duration -> getDurationOfWaitingForExamDate();

                    $after_course_details = array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfWaitingForExamDate(),
                        'status_name' => $this -> default_duration -> getDurationOfWaitingForExamDateStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    );
                    $course_details = $this -> getDetailedDurationOfLanguageCourses($language_level, $language_status);
                    array_push($course_details, $after_course_details);
                    $this -> setDetailedLanguageAssessment(
                        ...$course_details
                    );
                break;
                
                case 14:
                    $duration_language =
                        $this->getDurationOfMotiveLanguageCourses($language_level)
                        + 
                        $this -> default_duration -> getDurationOfWaitingForExamDate();

                    $after_course_details = array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfWaitingForExamDate(),
                        'status_name' => $this -> default_duration -> getDurationOfWaitingForExamDateStatusName(),
                        'start_date' => '',
                        'end_date' => ''
                    );
                    $course_details = $this -> getDetailedDurationOfMotiveLanguageCourses($language_level);
                    array_push($course_details, $after_course_details);
                    $this -> setDetailedLanguageAssessment(
                        ...$course_details
                    );
                break;

                case 5:
                    $duration_language =
                        $this->getDaysRemainingOnCurrentStatus(
                            $this->getCandidateDaysOnLanguageStatus(),
                            $this->default_duration->getDurationOfWaitingForExamDate()
                        );

                    $this -> setDetailedLanguageAssessment(
                        array(
                            'type'  => 'current',
                            'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnLanguageStatus(), $this -> default_duration   -> getDurationOfWaitingForExamDate()),
                            'status_name' => $this -> default_duration -> getDurationOfWaitingForExamDateStatusName(),
                            'start_date' => '',
                            'end_date' => ''
                        )
                    );
                
                break;

                default:
                    $duration_language = 0;
                break;
            }
        }

        return $duration_language;
    }
    
    public function getTrueDurationOfPreSignedContract(){
        

        $duration_pre_contract_signed   = 0;
        $candidate_status               = $this -> getCandidateStatus();
        $candidate_days_on_status       = $this -> getCandidateDaysOnStatus();
        
        switch($candidate_status){
            case 0:
            case 1:
            case 2:
            case 5:
            case 6:
                $duration_pre_contract_signed = $this -> default_duration -> getDurationOfPreContractSigned();
                
                $this -> setDetailedPreContractAssessment(
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfPreInterview(),
                        'status_name' => $this -> default_duration -> getDurationOfPreInterviewStatusName(),
                        'start_date' => '',
                        'end_date' => '',
                        'is_final' => false
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfInterview(),
                        'status_name' => $this -> default_duration -> getDurationOfInterviewStatusName(),
                        'start_date' => '',
                        'end_date' => '',
                        'is_final' => false
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfWaitingContract(),
                        'status_name' => $this -> default_duration -> getDurationOfWaitingContractStatusName(),
                        'start_date' => '',
                        'end_date' => '',
                        'is_final' => false
                    ),
                    array(
                        'type'  => 'future',
                        'duration' => $this -> default_duration -> getDurationOfContractSent(),
                        'status_name' => $this -> default_duration -> getDurationOfContractSentStatusName(),
                        'start_date' => '',
                        'end_date' => '',
                        'is_final' => false
                    )
                );
            break;
            case 3:
                if($this -> candidateHasInterview()){
                    $duration_pre_contract_signed = $this -> getDaysRemainingOnCurrentStatus(
                        $this                       -> getCandidateDaysOnStatus(), 
                        $this -> default_duration   -> getDurationOfInterview()
                    )                                                                   +
                    $this -> default_duration   -> getDurationOfWaitingContract()       +
                    $this -> default_duration -> getDurationOfContractSent()
                    ;
                    $this -> setDetailedPreContractAssessment(
                        array(
                            'type'  => 'current',
                            'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnStatus(), $this -> default_duration   -> getDurationOfInterview()),
                            'status_name' => $this -> default_duration -> getDurationOfInterviewStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfWaitingContract(),
                            'status_name' => $this -> default_duration -> getDurationOfWaitingContractStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfContractSent(),
                            'status_name' => $this -> default_duration -> getDurationOfContractSentStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        )
                    );
                }
                else{
                    $duration_pre_contract_signed = $this -> getDaysRemainingOnCurrentStatus(
                        $this                       -> getCandidateDaysOnStatus(), 
                        $this -> default_duration   -> getDurationOfPreInterview()
                    )                                                                   +
                    $this -> default_duration   -> getDurationOfInterview()     +
                    $this -> default_duration   -> getDurationOfWaitingContract()       +
                    $this -> default_duration -> getDurationOfContractSent();

                    $this -> setDetailedPreContractAssessment(
                        array(
                            'type'  => 'current',
                            'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnStatus(), $this -> default_duration   -> getDurationOfPreInterview()),
                            'status_name' => $this -> default_duration -> getDurationOfPreInterviewStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfInterview(),
                            'status_name' => $this -> default_duration -> getDurationOfInterviewStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfWaitingContract(),
                            'status_name' => $this -> default_duration -> getDurationOfWaitingContractStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfContractSent(),
                            'status_name' => $this -> default_duration -> getDurationOfContractSentStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        )
                    );
                }
            break;
            case 7:
                $duration_pre_contract_signed = $this -> getDaysRemainingOnCurrentStatus(
                        $this                       -> getCandidateDaysOnStatus(), 
                        $this -> default_duration   -> getDurationOfWaitingContract()
                    )                                                                   +
                    $this -> default_duration -> getDurationOfContractSent();
                
                    $this -> setDetailedPreContractAssessment(
                        array(
                            'type'  => 'current',
                            'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnStatus(), $this -> default_duration   -> getDurationOfWaitingContract()),
                            'status_name' => $this -> default_duration -> getDurationOfWaitingContractStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfContractSent(),
                            'status_name' => $this -> default_duration -> getDurationOfContractSentStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        )
                    );
            break;

            case 8:
                $duration_pre_contract_signed = $this->getDaysRemainingOnCurrentStatus(
                    $this->getCandidateDaysOnStatus(),
                    $this->default_duration->getDurationOfContractSent()
                )                                                                   +
                $this -> default_duration -> getDurationOfContractSigned();

                $this -> setDetailedPreContractAssessment(
                    array(
                        'type'  => 'current',
                        'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnStatus(), $this -> default_duration   -> getDurationOfContractSent()),
                        'status_name' => $this -> default_duration -> getDurationOfContractSentStatusName(),
                        'start_date' => '',
                        'end_date' => '',
                        'is_final' => false
                    )
                );
            break;
        }
        return $duration_pre_contract_signed;
    }
    public function getTrueDurationOfAfterContractSigned(){
        $duration_after_contract_signed   = 0;
        $candidate_status               = $this -> getCandidateStatus();
        $candidate_days_on_status       = $this -> getCandidateDaysOnStatus();
        $candidate_agreed_start_date    = $this -> candidate_agreed_start_date;
        
        if($this -> is_eu_candidate == 1){
            $final_date = "";
            if(!is_null($candidate_agreed_start_date)){
                $final_date = strtotime($candidate_agreed_start_date);
            }
            switch($candidate_status){
                case 0:
                case 1:
                case 2:
                case 3:
                case 5:
                case 6:
                case 7:
                case 8:
                    $duration_after_contract_signed = $this -> default_duration-> getDurationOfRecievedVisa();
                    $this -> setDetailedAfterContractAssessment(
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration   -> getDurationOfRecievedVisa(),
                            'status_name' => 'Otkazni rok',
                            'start_date' => '',
                            'end_date' => $final_date,
                            'is_final' => false
                        )
                        );
                break;
                case 9:
                    $duration_after_contract_signed = 
                        $this -> getDaysRemainingOnCurrentStatus(
                            $this -> getCandidateDaysOnStatus(),
                            $this -> default_duration -> getDurationOfRecievedVisa()
                        );
                   
                    $this -> setDetailedAfterContractAssessment(
                        array(
                            'type'  => 'current',
                            'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnStatus(), $this -> default_duration   -> getDurationOfRecievedVisa()),
                            'status_name' => 'Otkazni rok',
                            'start_date' => '',
                            'end_date' => $final_date,
                            'is_final' => false
                        )
                    );
                break;
            }
        }
        else{
            switch($candidate_status){
                case 12:
                    $duration_of_waiting_for_visa_appointment = $this -> default_duration -> getDurationOfWaitingForAppointment();
                    if($this -> candidate_departure_type == 2 AND !is_null($this -> candidate_visa_appointment)){
                        $duration_of_waiting_for_visa_appointment = max(floor((strtotime($this -> candidate_visa_appointment) - time())/(86400)) - ( $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnStatus(), $this -> default_duration   -> getDurationOfCollectingDocuments())),0);
                    }

                    $duration_after_contract_signed =
                        $this->getDaysRemainingOnCurrentStatus(
                            $this->getCandidateDaysOnStatus(),
                            $this->default_duration->getDurationOfCollectingDocuments()
                        )
                        +
                        $duration_of_waiting_for_visa_appointment
                        +
                        $this->default_duration->getDurationOfWaitingForVisa()
                        +
                        $this -> default_duration -> getDurationOfRecievedVisa();
    
                        $this -> setDetailedAfterContractAssessment(
                            array(
                                'type'  => 'current',
                                'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnStatus(), $this -> default_duration   -> getDurationOfCollectingDocuments()),
                                'status_name' => $this -> default_duration -> getDurationOfCollectingDocumentsStatusName(),
                                'start_date' => '',
                                'end_date' => '',
                                'is_final' => false
                            ),
                            array(
                                'type'  => 'future',
                                'duration' => $duration_of_waiting_for_visa_appointment,
                                'status_name' => $this -> default_duration -> getDurationOfWaitingForAppointmentStatusName(),
                                'start_date' => '',
                                'end_date' => '',
                                'is_final' => false
                            ),
                            array(
                                'type'  => 'future',
                                'duration' => $this -> default_duration -> getDurationOfWaitingForVisa(),
                                'status_name' => $this -> default_duration -> getDurationOfWaitingForVisaStatusName(),
                                'start_date' => '',
                                'end_date' => '',
                                'is_final' => false
                            ),
                            array(
                                'type'  => 'future',
                                'duration' => $this -> default_duration -> getDurationOfRecievedVisa(),
                                'status_name' => $this -> default_duration -> getDurationOfRecievedVisaStatusName(),
                                'start_date' => '',
                                'end_date' => '',
                                'is_final' => true
                            )
                        );
                break;
    
                case 15:
                    $duration_of_waiting_for_visa_appointment =  $this->getDaysRemainingOnCurrentStatus($this->getCandidateDaysOnStatus(),$this -> default_duration -> getDurationOfWaitingForAppointment());
                    if($this -> candidate_departure_type == 2 AND !is_null($this -> candidate_visa_appointment)){
                        $duration_of_waiting_for_visa_appointment = max(floor((strtotime($this -> candidate_visa_appointment) - time())/(86400)), 0);
                    }

                    $duration_after_contract_signed =
                        $duration_of_waiting_for_visa_appointment
                        +
                        $this->default_duration->getDurationOfWaitingForVisa()
                        +
                        $this -> default_duration -> getDurationOfRecievedVisa();
                    
                    $this -> setDetailedAfterContractAssessment(
                        array(
                            'type'  => 'current',
                            'duration' => $duration_of_waiting_for_visa_appointment,
                            'status_name' => $this -> default_duration -> getDurationOfWaitingForAppointmentStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfWaitingForVisa(),
                            'status_name' => $this -> default_duration -> getDurationOfWaitingForVisaStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfRecievedVisa(),
                            'status_name' => $this -> default_duration -> getDurationOfRecievedVisaStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => true
                        )
                    );
                break;
    
                case 18: //ceka vizu
                    $duration_after_contract_signed =
                        $this->getDaysRemainingOnCurrentStatus(
                            $this->getCandidateDaysOnStatus(),
                            $this->default_duration->getDurationOfWaitingForVisa()
                        )
                        +
                        $this -> default_duration -> getDurationOfRecievedVisa();
    
                    $this -> setDetailedAfterContractAssessment(
                        array(
                            'type'  => 'current',
                            'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnStatus(), $this -> default_duration   -> getDurationOfWaitingForVisa()),
                            'status_name' => $this -> default_duration -> getDurationOfWaitingForVisaStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfRecievedVisa(),
                            'status_name' => $this -> default_duration -> getDurationOfRecievedVisaStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => true
                        )
                    );
                break;
    
                case 21:
                    $duration_after_contract_signed =
                        $this->getDaysRemainingOnCurrentStatus(
                            $this->getCandidateDaysOnStatus(),
                            $this->default_duration->getDurationOfRenewal()
                        )
                        +
                        $this->default_duration->getDurationOfWaitingForVisa()
                        +
                        $this -> default_duration -> getDurationOfRecievedVisa();
    
                    $this -> setDetailedAfterContractAssessment(
                        array(
                            'type'  => 'current',
                            'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnStatus(), $this -> default_duration   -> getDurationOfRenewal()),
                            'status_name' => $this -> default_duration -> getDurationOfRenewalStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfWaitingForVisa(),
                            'status_name' => $this -> default_duration -> getDurationOfWaitingForVisaStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfRecievedVisa(),
                            'status_name' => $this -> default_duration -> getDurationOfRecievedVisaStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => true
                        )
                    );
                break;
    
                case 24:
                    $duration_after_contract_signed =
                        $this->getDaysRemainingOnCurrentStatus(
                            $this->getCandidateDaysOnStatus(),
                            $this->default_duration->getDurationOfVisaRejected()
                        )
                        +
                        $this->default_duration->getDurationOfWaitingForVisa()
                        +
                        $this -> default_duration -> getDurationOfRecievedVisa();
    
                    $this -> setDetailedAfterContractAssessment(
                        array(
                            'type'  => 'current',
                            'duration' => $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnStatus(), $this -> default_duration   -> getDurationOfVisaRejected()),
                            'status_name' => $this -> default_duration -> getDurationOfVisaRejectedStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfWaitingForVisa(),
                            'status_name' => $this -> default_duration -> getDurationOfWaitingForVisaStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfRecievedVisa(),
                            'status_name' => $this -> default_duration -> getDurationOfRecievedVisaStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => true
                        )
                    );
                break;
    
                case 27:
                    $final_date = "";
                    $duration = $this -> getDaysRemainingOnCurrentStatus($this -> getCandidateDaysOnStatus(), $this -> default_duration   -> getDurationOfRecievedVisa());
                    $status_name = $this -> default_duration -> getDurationOfRecievedVisaStatusName();
                    if(!is_null($candidate_agreed_start_date)){
                        $final_date = strtotime($candidate_agreed_start_date);
                        $status_name = "Čeka početak rada";
                        $duration = max(0, floor(($final_date - time())/(86400)));
                    }
                    
                    $duration_after_contract_signed =
                        $this->getDaysRemainingOnCurrentStatus(
                            $this->getCandidateDaysOnStatus(),
                            $this->default_duration->getDurationOfRecievedVisa()
                        );
                    
                        $this -> setDetailedAfterContractAssessment(
                            array(
                                'type'  => 'current',
                                'duration' => $duration,
                                'status_name' => $status_name,
                                'start_date' => '',
                                'end_date' => '',
                                'is_final' => false
                            )
                        );
                break;
    
                case 10:
                    $duration_after_contract_signed = 0;
                    
                        $this -> setDetailedAfterContractAssessment(
                            array(
                                'type'  => 'current',
                                'duration' => 0,
                                'status_name' => 'Počeo sa radom',
                                'start_date' => '',
                                'end_date' => '',
                                'is_final' => true
                            )
                        );
                break;
    
                default:
                    $duration_after_contract_signed = $this -> default_duration -> getDurationOfAfterContractSigned();
    
                    $this -> setDetailedAfterContractAssessment(
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfCollectingDocuments(),
                            'status_name' => $this -> default_duration -> getDurationOfCollectingDocumentsStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfWaitingForAppointment(),
                            'status_name' => $this -> default_duration -> getDurationOfWaitingForAppointmentStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfWaitingForVisa(),
                            'status_name' => $this -> default_duration -> getDurationOfWaitingForVisaStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => false
                        ),
                        array(
                            'type'  => 'future',
                            'duration' => $this -> default_duration -> getDurationOfRecievedVisa(),
                            'status_name' => $this -> default_duration -> getDurationOfRecievedVisaStatusName(),
                            'start_date' => '',
                            'end_date' => '',
                            'is_final' => true
                        )
                    );
                break;
    
            }
        }
        
        return $duration_after_contract_signed;

    }

    private function calculateDetailedPreContractDates($start_date){
        $end_date = $start_date;
        $detailed_assessment = $this -> getDetailedPreContractAssessment();
        for ($i = 0; $i < count($detailed_assessment); $i++){
            $end_date = date('Y-m-d', strtotime($start_date. ' + '.$detailed_assessment[$i]['duration'].' days'));
            
            $detailed_assessment[$i]['start_date']   = strtotime($start_date);

            $detailed_assessment[$i]['end_date']     = strtotime($end_date);
            // $detailed_assessment[$i]['start_date']   = $start_date;
            // $detailed_assessment[$i]['end_date']     = $end_date;
            $start_date = $end_date;
        }
        $this -> overwriteDetailedPreContractAssessment($detailed_assessment);
        return $end_date;
    }
    private function calculateDetailedAfterContractDates($start_date){
        $end_date = $start_date;

        $detailed_assessment = $this -> getDetailedAfterContractAssessment();
        for ($i = 0; $i < count($detailed_assessment); $i++){
            $end_date = date('Y-m-d', strtotime($start_date. ' + '.$detailed_assessment[$i]['duration'].' days'));
            $detailed_assessment[$i]['start_date']   = strtotime($start_date);
            if($detailed_assessment[$i]['end_date'] == ""){
                $detailed_assessment[$i]['end_date']     = strtotime($end_date);
            }

            $start_date = $end_date;
        }
        $this -> overwriteDetailedAfterContractAssessment($detailed_assessment);
        return $end_date;
    }
    private function calculateDetailedNostrificationDates($start_date){
        $end_date = $start_date;
        $detailed_assessment = $this -> getDetailedNostrificationAssessment();
        for ($i = 0; $i < count($detailed_assessment); $i++){
            $end_date = date('Y-m-d', strtotime($start_date. ' + '.$detailed_assessment[$i]['duration'].' days'));
            
            $detailed_assessment[$i]['start_date']   = strtotime($start_date);
            $detailed_assessment[$i]['end_date']     = strtotime($end_date);

            $start_date = $end_date;
        }
        $this -> overwriteDetailedNostrificationAssessment($detailed_assessment);
        return $end_date;
    }
    private function calculateDetailedLanguageDates($start_date){
        $end_date = $start_date;
        $detailed_assessment = $this -> getDetailedLanguageAssessment();
        for ($i = 0; $i < count($detailed_assessment); $i++){
            
            if($detailed_assessment[$i]['end_date'] == ""){
                if($detailed_assessment[$i]['start_date'] != ""){
                    $start_date = date('Y-m-d', $detailed_assessment[$i]['start_date']);                    
                }
                $end_date = date('Y-m-d', strtotime($start_date. ' + '.$detailed_assessment[$i]['duration'].' days'));
                $detailed_assessment[$i]['start_date']   = strtotime($start_date);
                $detailed_assessment[$i]['end_date']     = strtotime($end_date);
                $start_date = $end_date;
            }
            else{
                $start_date = date('Y-m-d', $detailed_assessment[$i]['end_date']);
            }
        }
        $this -> overwriteDetailedLanguageAssessment($detailed_assessment);

        return $end_date;
    }
    private function get_max_date($date_1, $date_2){
        if ($date_1 > $date_2)
            return $date_1;
        else
            return $date_2;

    }
    public function getNostrificationHistory(){
        Global $db;
        $current_status = $this -> getCandidateNostrificationStatus();
        $timeline_type = "";

        $query_get_nostrification_status_history = $db -> prepare('

            SELECT 
                status_nd_kandidata, 
                date(vrijeme_promjene_statusa_nd_kandidata) as start_date, 
                CASE 
                    WHEN broj_dana_statusa_nd_kandidata IS NULL 
                    THEN DATEDIFF(date(now()), date(vrijeme_promjene_statusa_nd_kandidata)) 
                    ELSE broj_dana_statusa_nd_kandidata 
                END as broj_dana,
                CASE 
                    WHEN broj_dana_statusa_nd_kandidata IS NULL 
                    THEN date(now())
                    ELSE DATE_ADD(date(vrijeme_promjene_statusa_nd_kandidata), INTERVAL broj_dana_statusa_nd_kandidata DAY)
                END as end_date
            FROM 
                idk_nd_kandidata_status_log 
            WHERE 
                idd_broj_nd_kandidata = :candidate_id 
            AND status_nd_kandidata IN (2,3,4,5,6,7)
            ORDER BY status_nd_kandidata DESC
        ');
        
        $query_get_nostrification_status_history -> execute(array(':candidate_id' => $this -> getCandidateDIPLID()));
        $row_count = $query_get_nostrification_status_history -> rowCount();
        $number_of_days_on_status = 0;
        $end_date = 0;
        $is_done = false;
        while($row_get_nostrification_status_history = $query_get_nostrification_status_history -> fetch()){
            if($row_get_nostrification_status_history['status_nd_kandidata'] == $current_status){
                $timeline_type = 'current';
            }
            else{
                $timeline_type = 'past';
            }
            if(($row_count == 1 AND $row_get_nostrification_status_history['status_nd_kandidata'] == 7) OR $row_get_nostrification_status_history['status_nd_kandidata'] != 7){
                if($row_get_nostrification_status_history['status_nd_kandidata'] == 6 OR $row_get_nostrification_status_history['status_nd_kandidata'] == 7){
                    $number_of_days_on_status = 1;
                    $is_done = true;
                    $end_date = date('Y-m-d', strtotime($row_get_nostrification_status_history['start_date'] . " +$number_of_days_on_status days"));
                }
                else{
                    $is_done = false;
                    $number_of_days_on_status = $row_get_nostrification_status_history['broj_dana'];
                    $end_date = $row_get_nostrification_status_history['end_date'];
                }
                $this -> unshiftDetailedNostrificationAssessment(array(
                        'type'  => $timeline_type,
                        'duration' => $number_of_days_on_status,
                        'status_name' => $this -> default_duration ->getStatusNameByGroupStatus(2, $row_get_nostrification_status_history['status_nd_kandidata']),
                        'start_date' => strtotime($row_get_nostrification_status_history['start_date']),
                        'end_date' => strtotime($end_date),
                        'is_final' => $is_done
                ));
            }
        }
    }

    public function getContractHistroy(){
        Global $db;

        $current_status = $this -> getCandidateStatus();
        $timeline_type = "";

        $query_get_contract_status_history = $db -> prepare("
            SELECT
                ksp.redoslijed_statusa,
                CASE
                    WHEN (lsp.lsp_status_prijave_id = 3 AND pr.project_name LIKE ('%Intervju%')) OR lsp.lsp_status_prijave_id != 3
                    THEN lsp.lsp_status_prijave_id
                    ELSE 0
                END as status_prijave,
                CASE 
                    WHEN lsp.lsp_broj_dana IS NULL
                    THEN  DATEDIFF(date(now()), date(lsp.lsp_datetime))
                    ELSE lsp.lsp_broj_dana
                END as broj_dana,
                lsp.lsp_datetime as start_date,
                CASE 
                    WHEN lsp.lsp_broj_dana is NULL
                    THEN date(now())
                    ELSE DATE_ADD(date(lsp.lsp_datetime), INTERVAL lsp.lsp_broj_dana DAY)
                END as end_date,
                CASE 
                    WHEN lsp.lsp_status_prijave_id IN (3,7,8,9)
                    THEN 1
                    ELSE 0
                END as is_pre_contract
            FROM (
                SELECT 
                    sqlsp.lsp_status_prijave_id,
                    sqlsp.lsp_broj_dana,
                    sqlsp.lsp_datetime,
                       sqlsp.lsp_projekt_id
            
                FROM idk_log_statusi_prijave sqlsp
                WHERE sqlsp.lsp_kandidat_id = :candidate_id
                AND sqlsp.lsp_status_prijave_id IN (3,7,8,9,10,12,15,18,21,24,27)
            ) lsp
            JOIN (
                SELECT sqpr.project_id, sqpr.project_name 
                FROM idk_projects sqpr
                WHERE sqpr.project_nalogid = (
                    SELECT kan.kandidat_nalog_id
                    FROM idk_kandidati kan
                    WHERE kan.kandidat_id = :candidate_id
                )
            ) pr
            ON pr.project_id = lsp.lsp_projekt_id
            JOIN idk_kandidat_status_prijave ksp
            ON lsp.lsp_status_prijave_id = ksp.status_id
            ORDER BY ksp.redoslijed_statusa DESC, status_prijave DESC
        ");
        $query_get_contract_status_history -> execute(array(':candidate_id' => $this -> getCandidateID()));
        
        $end_date = date('Y-m-d');
        $is_done = false;
        $number_of_days_on_status = "0";
        while($row_get_contract_status_history = $query_get_contract_status_history -> fetch()){
            $status_name_pre_contract = $this -> default_duration ->getStatusNameByGroupStatus(1, $row_get_contract_status_history['status_prijave']);
            $status_name_after_contract = $this -> default_duration ->getStatusNameByGroupStatus(4, $row_get_contract_status_history['status_prijave']);
            if($row_get_contract_status_history['status_prijave'] == $current_status){
                $timeline_type = 'current';
            }
            else{
                $timeline_type = 'past';
            }
            if($row_get_contract_status_history['status_prijave'] == 9  OR $row_get_contract_status_history['status_prijave'] == 10){
                $number_of_days_on_status = "1";
                $is_done = true;
                $end_date = date('Y-m-d', strtotime($row_get_contract_status_history['start_date'] . " +$number_of_days_on_status days"));
            }
            else if($row_get_contract_status_history['status_prijave'] == 27){
                $status_name_after_contract = "Čeka početak rada";
                $number_of_days_on_status =  $row_get_contract_status_history['broj_dana'];
                $is_done = false;
            }
            else{
                $number_of_days_on_status = $row_get_contract_status_history['broj_dana'];
                $is_done = false;
                $end_date = $row_get_contract_status_history['end_date'];
            }

            if($row_get_contract_status_history['is_pre_contract']){
                $this -> unshiftDetailedPreContractAssessment(array(
                        'type'  => $timeline_type,
                        'duration' => $number_of_days_on_status,
                        'status_name' => $status_name_pre_contract,
                        'start_date' => strtotime($row_get_contract_status_history['start_date']),
                        'end_date' => strtotime($end_date),
                        'is_final' => $is_done
                ));
            }
            else{

                $this -> unshiftDetailedAfterContractAssessment(array(
                        'status_name' => $status_name_after_contract,
                        'type'  => $timeline_type,
                        'duration' =>  $number_of_days_on_status,
                        'start_date' => strtotime($row_get_contract_status_history['start_date']),
                        'end_date' => strtotime($end_date),
                        'is_final' => $is_done
                ));
            }
        }

        
    }
    public function getLanguageHistroy(){
        Global $db;
        $current_status = $this -> getCandidateLanguageStatus();
        $timeline_type = "";
        $query_get_language_status_history = $db -> prepare("
            SELECT
                cl.cll_id,
                cl.cll_status,
                cl.cll_date as start_date,
                CASE
                    WHEN cl.cll_status = 6
                    THEN cvl.cvl_exam_date
                    ELSE
                    CASE   
                        WHEN cl.cll_days_count IS NULL
                            THEN DATE(NOW()) 
                            ELSE DATE_ADD(date(cl.cll_date), INTERVAL cl.cll_days_count DAY)
                    END
                END as end_date,
                CASE 
                    WHEN cl.cll_days_count IS NULL
                    THEN DATEDIFF(date(now()), date(cl.cll_date))
                    ELSE cl.cll_days_count
                END as broj_dana,
                kj.kj_slusanje,
                cvl.cvl_course1_started,
                cvl.cvl_course1_ended,
                cvl.cvl_course2_started,
                cvl.cvl_course2_ended
            FROM idk_candidate_language_logs cl
            JOIN idk_kandidat_jezici kj
            ON kj.kj_id = cl.cll_cvl_id
            JOIN idk_candidate_verified_languages cvl
            ON cvl.cvl_id = kj.kj_id
            WHERE cl.cll_candidate_id = :candidate_id
            AND cl.cll_status IN (0,3,4,5,6,7,8,11,14)
            ORDER BY cl.cll_id DESC        
        ");
        $query_get_language_status_history -> execute(array(':candidate_id' => $this -> getCandidateID()));

        $start_date = "";
        while($row_get_language_status_history = $query_get_language_status_history -> fetch()){
            $start_date = $row_get_language_status_history['start_date'];
            $end_date  = $row_get_language_status_history['end_date'];
            $number_of_days_on_status = $row_get_language_status_history['broj_dana'];
            $is_done = false;

            if($row_get_language_status_history['cll_status'] == $current_status){
                $timeline_type = 'current';
                if($current_status == 8 AND (
                    $row_get_language_status_history['kj_slusanje'] == 'A2' OR
                    $row_get_language_status_history['kj_slusanje'] == 'B1' OR
                    $row_get_language_status_history['kj_slusanje'] == 'B2' OR
                    $row_get_language_status_history['kj_slusanje'] == 'C1' OR
                    $row_get_language_status_history['kj_slusanje'] == 'C2' 
                    )
                ){
                    $number_of_days_on_status = 1;
                    $end_date = date('Y-m-d', strtotime($row_get_language_status_history['start_date'] . " +$number_of_days_on_status days"));
                    $is_done = true;
                }
            }
            else{
                $timeline_type = 'past';
            }
           
            $this -> unshiftDetailedLanguageAssessment(array(
                    'type'  => $timeline_type,
                    'duration' => $number_of_days_on_status,
                    'status_name' => $this -> default_duration ->getStatusNameByGroupStatus(3, $row_get_language_status_history['cll_status']).", ".$row_get_language_status_history['kj_slusanje'],
                    'start_date' => strtotime($start_date),
                    'end_date' => strtotime($end_date),
                    'is_final' => $is_done
            ));
        }
    }
    // a - duration_pre_signed_contract
    // b - duration_language
    // c - duration_nostrification
    // d - duration_after_contract_signed
    // 1) Nostrifikacija i Jezik su započeti
    //      max(a,b,c) + d
    // 2) Nostrifikacija započeta, jezik nije
    //      a + max((c-a),b)+d
    // 3) Jezik započet, nostrifikacija nije
    //      a+max((b-a),c)+d
    // 4) Nostrifikacija i jezik nisu započeti
    //      a+max(b,c)+d
    public function getCandidateProjectedDate()
    {
        $duration_pre_signed_contract       = $this -> getTrueDurationOfPreSignedContract();
        $duration_language                  = $this -> getTrueDurationOfLanguage();
        $duration_nostrification            = $this -> getTrueDurationOfNostrification();
        $duration_after_contract_signed     = $this -> getTrueDurationOfAfterContractSigned();
        $true_start_day_in = 0;
        
        // 4)
        if ($duration_language < 0 and $duration_nostrification < 0) {
            $duration_language *= (-1);
            $duration_nostrification *= (-1);
            $true_start_day_in = $duration_pre_signed_contract + max($duration_language, $duration_nostrification) + $duration_after_contract_signed;

            $pre_contract_end_date = $this -> calculateDetailedPreContractDates(date("Y-m-d"));
            $this -> calculateDetailedAfterContractDates($this -> get_max_date($this -> calculateDetailedLanguageDates($pre_contract_end_date), $this -> calculateDetailedNostrificationDates($pre_contract_end_date)));
            $this -> getContractHistroy();
            $this -> getNostrificationHistory();
            $this -> getLanguageHistroy();
        }
        // 3)
        else if ($duration_nostrification < 0) {
            // var_dump($duration_pre_contract_signed);
            // var_dump($duration_language);
            // var_dump($duration_nostrification);
            // var_dump($duration_after_contract_signed);
            $duration_nostrification *= (-1);
            $true_start_day_in = $duration_pre_signed_contract + max(($duration_language - $duration_pre_signed_contract), $duration_nostrification) + $duration_after_contract_signed;

            $pre_contract_end_date = $this -> calculateDetailedPreContractDates(date("Y-m-d"));
            $language_end_date = $this -> calculateDetailedLanguageDates(date("Y-m-d"));

            $this -> calculateDetailedAfterContractDates($this -> get_max_date($language_end_date, $this -> calculateDetailedNostrificationDates($pre_contract_end_date)));
            $this -> getContractHistroy();
            $this -> getNostrificationHistory();
            $this -> getLanguageHistroy();

        }
        // 2)
        else if ($duration_language < 0) {
            $duration_language *= (-1);
            $true_start_day_in = $duration_pre_signed_contract + max($duration_language, ($duration_nostrification - $duration_pre_signed_contract)) + $duration_after_contract_signed;
        
            $pre_contract_end_date = $this -> calculateDetailedPreContractDates(date("Y-m-d"));
            $nostrification_end_date = $this -> calculateDetailedNostrificationDates(date("Y-m-d"));

            $this -> calculateDetailedAfterContractDates($this -> get_max_date($nostrification_end_date, $this -> calculateDetailedLanguageDates($pre_contract_end_date)));
            $this -> getContractHistroy();
            $this -> getNostrificationHistory();
            $this -> getLanguageHistroy();

        }
        // 1)
        else {
            $true_start_day_in = max($duration_language, $duration_nostrification, $duration_pre_signed_contract) + $duration_after_contract_signed;

            $pre_contract_end_date = $this -> calculateDetailedPreContractDates(date("Y-m-d"));
            $nostrification_end_date = $this -> calculateDetailedNostrificationDates(date("Y-m-d"));
            $language_end_date = $this -> calculateDetailedLanguageDates(date("Y-m-d"));
            $this -> calculateDetailedAfterContractDates($this -> get_max_date($this -> get_max_date($language_end_date, $nostrification_end_date), $pre_contract_end_date));
            $this -> getContractHistroy();
            $this -> getNostrificationHistory();
            $this -> getLanguageHistroy();
        }
        $today = date('d.m.y');
        // if(is_null($this -> getPotentialStartDate())){
            $start_date = date('d.m.Y', strtotime("$today + $true_start_day_in days"));
        // }else{
        //     $start_date = date('d.m.Y', $this->getPotentialStartDate());
        // }

        return strtotime($start_date);
        // return $true_start_day_in;
    }

    public function getGroupDurations()
    {
        $duration_pre_signed_contract       = $this->getTrueDurationOfPreSignedContract();
        $duration_language                  = $this->getTrueDurationOfLanguage();
        $duration_nostrification            = $this->getTrueDurationOfNostrification();
        $duration_after_contract_signed     = $this->getTrueDurationOfAfterContractSigned();

        $ret = [
            "recruiting" => abs($duration_pre_signed_contract),
            "language" =>  abs($duration_language),
            "dipl" => abs($duration_nostrification),
            "visa" => abs($duration_after_contract_signed)
        ];


        $true_start_day_in = 0;
        //4)
        if ($duration_language < 0 and $duration_nostrification < 0) {
            $ret["case"] = 4;

            $duration_language *= (-1);
            $duration_nostrification *= (-1);
            $true_start_day_in = $duration_pre_signed_contract + max($duration_language, $duration_nostrification) + $duration_after_contract_signed;
        }
        // 3)
        else if ($duration_nostrification < 0) {
            $ret["case"] = 3;

            $duration_nostrification *= (-1);
            $true_start_day_in = $duration_pre_signed_contract + max(($duration_language - $duration_pre_signed_contract), $duration_nostrification) + $duration_after_contract_signed;
        }
        // 2)
        else if ($duration_language < 0) {
            $ret["case"] = 2;

            $duration_language *= (-1);
            $true_start_day_in = $duration_pre_signed_contract + max($duration_language, ($duration_nostrification - $duration_pre_signed_contract)) + $duration_after_contract_signed;
        }
        // 1)
        else {
            $ret["case"] = 1;
            $true_start_day_in = max($duration_language, $duration_nostrification, $duration_pre_signed_contract) + $duration_after_contract_signed;
        }
        $today = date('d.m.y');
        $start_date = date('d.m.Y', strtotime("$today + $true_start_day_in days"));

        $ret["startDate"] = strtotime($start_date);
        return $ret;
        // return $true_start_day_in;
    }
}
