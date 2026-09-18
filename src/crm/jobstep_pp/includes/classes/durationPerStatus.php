<?php

class durationPerStatus{
    private $duration_pre_casting;
        private $duration_pre_interview;
        private $duration_interview;
        private $duration_waiting_contract;
        private $duration_contract_sent;
        private $duration_contract_signed;

        private $duration_pre_interview_status_name;
        private $duration_interview_status_name;
        private $duration_waiting_contract_status_name;
        private $duration_contract_sent_status_name;
        private $duration_contract_signed_status_name;
    
    private $duration_after_casting;
        private $duration_collecting_documents;
        private $duration_waiting_appointment;
        private $duration_waiting_visa;
        private $duration_renewal;
        private $duration_visa_rejected;
        private $duration_recieved_visa;
        
        private $duration_collecting_documents_status_name;
        private $duration_waiting_appointment_status_name;
        private $duration_waiting_visa_status_name;
        private $duration_renewal_status_name;
        private $duration_visa_rejected_status_name;
        private $duration_recieved_visa_status_name;
        
    private $duration_nostrification;
        private $duration_nd_lead;
        private $duration_nd_collecting_documents;
        private $duration_nd_documents_sent;
        private $duration_nd_institution_proccessing;
        private $duration_nd_renewal;
        
        private $duration_nd_lead_status_name;
        private $duration_nd_collecting_documents_status_name;
        private $duration_nd_documents_sent_status_name;
        private $duration_nd_institution_proccessing_status_name;
        private $duration_nd_renewal_status_name;
    
    private $duration_language;
        private $duration_language_course;
        private $duration_waiting_for_exam_date;
        private $duration_glossa_assessment;
        
        private $duration_language_course_status_name;
        private $duration_waiting_for_exam_date_status_name;
        private $duration_glossa_assessment_status_name;

        private $duration_motive_A1 = 15; 
        private $duration_motive_A2 = 17; 
        private $duration_motive_status_name = "Motive";
        // Novi status -> koliko kandidat ćeka da počne sa kursom nakon procjene glose.

    function __construct() {

        Global $db;
        
        $query_get_status_durations = $db -> prepare('
            SELECT dpr_group, dpr_status, dpr_duration, dpr_status_name
            FROM idk_duration_per_status
        ');

        $query_get_status_durations -> execute();


        while($row_get_status_durations     = $query_get_status_durations -> fetch()){
            $dpr_group          = $row_get_status_durations['dpr_group'];
            $dpr_status         = $row_get_status_durations['dpr_status'];
            $dpr_duration       = $row_get_status_durations['dpr_duration'];
            $dpr_status_name    = $row_get_status_durations['dpr_status_name'];


            switch($dpr_group){
                case 1:
                    switch($dpr_status){
                        case "0":
                            $this -> duration_pre_interview                 = $dpr_duration;
                            $this -> duration_pre_interview_status_name     = $dpr_status_name;
                        break;

                        case "3":
                            $this -> duration_interview                     = $dpr_duration;
                            $this -> duration_interview_status_name         = $dpr_status_name;
                            break;
                            
                        case "7":
                            $this -> duration_waiting_contract              = $dpr_duration;
                            $this -> duration_waiting_contract_status_name  = $dpr_status_name;
                        break;

                        case "8":
                            $this -> duration_contract_sent                 = $dpr_duration; 
                            $this -> duration_contract_sent_status_name     = $dpr_status_name; 
                        break;
                    }
                break;

                case 2:
                    switch($dpr_status){
                        case "0":
                            $this -> duration_nd_lead                                   = $dpr_duration;
                            $this -> duration_nd_lead_status_name                       = $dpr_status_name;
                        break;

                        case "2":
                            $this -> duration_nd_collecting_documents                   = $dpr_duration;
                            $this -> duration_nd_collecting_documents_status_name       = $dpr_status_name;
                        break;

                        case "3":
                            $this -> duration_nd_documents_sent                         = $dpr_duration;
                            $this -> duration_nd_documents_sent_status_name             = $dpr_status_name;
                        break;

                        case "4":
                            $this -> duration_nd_institution_proccessing                = $dpr_duration;
                            $this -> duration_nd_institution_proccessing_status_name    = $dpr_status_name;

                        break;
                        
                        case "5":
                            $this -> duration_nd_renewal                                = $dpr_duration;
                            $this -> duration_nd_renewal_status_name                    = $dpr_status_name;
                        break;
                    }
                break;

                case 3:
                    switch($dpr_status){
                        case "0":
                            $this -> duration_before_language_course                    = $dpr_duration;
                            $this -> duration_before_language_course_status_name        = $dpr_status_name;
                        break;

                        case "3,4":
                            $this -> duration_language_course                           = $dpr_duration;
                            $this -> duration_language_course_status_name               = $dpr_status_name;
                        break;

                        case "5":
                            $this -> duration_waiting_for_exam_date                     = $dpr_duration;
                            $this -> duration_waiting_for_exam_date_status_name         = $dpr_status_name;
                        break;
                        case "14":
                            $this -> duration_glossa_assessment                         = $dpr_duration;
                            $this -> duration_glossa_assessment_status_name             = $dpr_status_name;
                        break;
                    }
                break;

                case 4:
                    switch($dpr_status){
                        case "12":
                            $this -> duration_collecting_documents              = $dpr_duration;
                            $this -> duration_collecting_documents_status_name  = $dpr_status_name;
                        break;
                            
                        case "15":
                            $this -> duration_waiting_appointment               = $dpr_duration;
                            $this -> duration_waiting_appointment_status_name   = $dpr_status_name;
                        break;

                        case "18":
                            $this -> duration_waiting_visa                      = $dpr_duration;
                            $this -> duration_waiting_visa_status_name          = $dpr_status_name;
                        break;

                        case "21":
                            $this -> duration_renewal                           = $dpr_duration;
                            $this -> duration_renewal_status_name               = $dpr_status_name;
                        break;

                        case "24":
                            $this -> duration_visa_rejected                     = $dpr_duration;
                            $this -> duration_visa_rejected_status_name         = $dpr_status_name;
                        break;

                        case "27":
                            $this -> duration_recieved_visa                     = $dpr_duration;
                            $this -> duration_recieved_visa_status_name         = "Dobio vizu";
                        break;
                    }
                break;
            }
        }

        $this -> duration_pre_contract_signed   = $this -> duration_pre_interview + $this -> duration_interview + $this -> duration_waiting_contract + $this -> duration_contract_sent + $this -> duration_contract_signed;
        $this -> duration_nostrification        = $this -> duration_nd_lead + $this -> duration_nd_collecting_documents + $this -> duration_nd_documents_sent + $this -> duration_nd_institution_proccessing + $this -> duration_nd_renewal;
        $this -> duration_after_casting         = $this -> duration_collecting_documents + $this -> duration_waiting_appointment + $this -> duration_waiting_visa + $this -> duration_recieved_visa;

    }

// GETTERS START

    public function getDurationOfMotiveA1(){
        return $this -> duration_motive_A1;
    }
    public function getDurationOfMotiveA2(){
        return $this -> duration_motive_A2;
    }
    public function getDurationOfMotiveStatusName(){
        return $this -> duration_motive_status_name;
    }
    public function getDurationOfPreContractSigned(){
        return $this -> duration_pre_contract_signed;
    }
    public function getDurationOfPreInterview(){
        return $this -> duration_pre_interview;
    }
    public function getDurationOfInterview(){
        return $this -> duration_interview;
    }
    public function getDurationOfWaitingContract(){
        return $this -> duration_waiting_contract;
    }
    public function getDurationOfContractSent(){
        return $this -> duration_contract_sent;
    }
    public function getDurationOfContractSigned(){
        return $this -> duration_contract_signed;
    }
    public function getDurationOfNostrification(){
        return $this -> duration_nostrification;
    }
    public function getDurationOfNDLead(){
        return $this -> duration_nd_lead;
    }
    public function getDurationOfNDCollectingDocuments(){
        return $this -> duration_nd_collecting_documents;
    }
    public function getDurationOfNDDocumentsSent(){
        return $this -> duration_nd_documents_sent;
    }
    public function getDurationOfNDInstitutionProcessing(){
        return $this -> duration_nd_institution_proccessing;
    }
    public function getDurationOfNDRenewal(){
        return $this -> duration_nd_renewal;
    }
    public function getDurationOfLanguage(){
        return $this -> duration_language;
    }
    public function getDurationOfLanguageCourse(){
        return $this -> duration_language_course;
    }
    public function getDurationBeforeLanguageCourse(){
        return $this -> duration_before_language_course;
    }
    public function getDurationOfGlossaAssessment(){
        return $this -> duration_glossa_assessment;
    }
    public function getDurationOfWaitingForExamDate(){
        return $this -> duration_waiting_for_exam_date;
    }
    public function getDurationOfCollectingDocuments(){
        return $this -> duration_collecting_documents;
    }
    public function getDurationOfWaitingForAppointment(){
        return $this -> duration_waiting_appointment;
    }
    public function getDurationOfWaitingForVisa(){
        return $this -> duration_waiting_visa;
    }
    public function getDurationOfRenewal(){
        return $this -> duration_renewal;
    }
    public function getDurationOfVisaRejected(){
        return $this -> duration_visa_rejected;
    }
    public function getDurationOfRecievedVisa(){
        return $this -> duration_recieved_visa;
    }
    public function getDurationOfAfterContractSigned(){
        return $this -> duration_after_casting;
    }
    public function getDurationOfPreContractSignedStatusName(){
        return $this -> duration_pre_contract_signed_status_name;
    }
    public function getDurationOfPreInterviewStatusName(){
        return $this -> duration_pre_interview_status_name;
    }
    public function getDurationOfInterviewStatusName(){
        return $this -> duration_interview_status_name;
    }
    public function getDurationOfWaitingContractStatusName(){
        return $this -> duration_waiting_contract_status_name;
    }
    public function getDurationOfContractSentStatusName(){
        return $this -> duration_contract_sent_status_name;
    }
    public function getDurationOfContractSignedStatusName(){
        return $this -> duration_contract_signed_status_name;
    }
    public function getDurationOfNostrificationStatusName(){
        return $this -> duration_nostrification_status_name;
    }
    public function getDurationOfNDLeadStatusName(){
        return $this -> duration_nd_lead_status_name;
    }
    public function getDurationOfNDCollectingDocumentsStatusName(){
        return $this -> duration_nd_collecting_documents_status_name;
    }
    public function getDurationOfNDDocumentsSentStatusName(){
        return $this -> duration_nd_documents_sent_status_name;
    }
    public function getDurationOfNDInstitutionProcessingStatusName(){
        return $this -> duration_nd_institution_proccessing_status_name;
    }
    public function getDurationOfNDRenewalStatusName(){
        return $this -> duration_nd_renewal_status_name;
    }
    public function getDurationOfLanguageStatusName(){
        return $this -> duration_language_status_name;
    }
    public function getDurationOfLanguageCourseStatusName(){
        return $this -> duration_language_course_status_name;
    }
    public function getDurationBeforeLanguageCourseStatusName(){
        return $this -> duration_before_language_course_status_name;
    }
    public function getDurationOfGlossaAssessmentStatusName(){
        return $this -> duration_glossa_assessment_status_name;
    }
    public function getDurationOfWaitingForExamDateStatusName(){
        return $this -> duration_waiting_for_exam_date_status_name;
    }
    public function getDurationOfCollectingDocumentsStatusName(){
        return $this -> duration_collecting_documents_status_name;
    }
    public function getDurationOfWaitingForAppointmentStatusName(){
        return $this -> duration_waiting_appointment_status_name;
    }
    public function getDurationOfWaitingForVisaStatusName(){
        return $this -> duration_waiting_visa_status_name;
    }
    public function getDurationOfRenewalStatusName(){
        return $this -> duration_renewal_status_name;
    }
    public function getDurationOfVisaRejectedStatusName(){
        return $this -> duration_visa_rejected_status_name;
    }
    public function getDurationOfRecievedVisaStatusName(){
        return $this -> duration_recieved_visa_status_name;
    }
    public function getDurationOfAfterContractSignedStatusName(){
        return $this -> duration_after_casting_status_name;
    }
    public function getStatusNameByGroupStatus($group_id, $status_id){
        switch($group_id){
            case 1:
                switch($status_id){
                    case "0":
                        return $this -> getDurationOfPreInterviewStatusName();
                    break;

                    case "3":
                        return $this -> getDurationOfInterviewStatusName();
                    break;
                        
                    case "7":
                        return $this -> getDurationOfWaitingContractStatusName();
                    break;

                    case "8":
                        return $this -> getDurationOfContractSentStatusName();
                    break;
                    
                    case "9":
                        return "Potpisan ugovor";
                    break;
                }
            break;

            case 2:
                switch($status_id){
                    case "0":
                        return $this -> getDurationOfNDLeadStatusName();
                    break;

                    case "2":
                        return $this -> getDurationOfNDCollectingDocumentsStatusName();
                    break;

                    case "3":
                        return $this -> getDurationOfNDDocumentsSentStatusName();
                    break;

                    case "4":
                        return $this -> getDurationOfNDInstitutionProcessingStatusName();
                    break;
                    
                    case "5":
                        return $this -> getDurationOfNDRenewalStatusName();
                    break;
                    case "6":
                        return "Završena nostrifikacija";
                    break;
                    case "7":
                        return "Nostrificirana negdje drugo";
                    break;
                }
            break;

            case 3:
                switch($status_id){
                    case "0":
                        return $this -> getDurationBeforeLanguageCourseStatusName();
                    break;

                    case "3":
                        return $this -> getDurationOfLanguageCourseStatusName()." 1";
                    break;
                    
                    case "4":
                        return $this -> getDurationOfLanguageCourseStatusName()." 2";
                    break;
                        
                    case "5":
                        return $this -> getDurationOfWaitingForExamDateStatusName();
                    break;

                    case "6":
                        return "Čeka Polaganje";
                    break;
                    case "7":
                        return "Čeka Rezultat";
                    case "8":
                        return "Položio";
                    break;
                    case "11":
                        return "Nije Položio";
                    break;
                        
                    case "14":
                        return $this -> getDurationOfGlossaAssessmentStatusName();
                    break;
                }
            break;

            case 4:
                switch($status_id){
                    case "12":
                        return $this -> getDurationOfCollectingDocumentsStatusName();
                    break;
                        
                    case "15":
                        return $this -> getDurationOfWaitingForAppointmentStatusName();
                    break;

                    case "18":
                        return $this -> getDurationOfWaitingForVisaStatusName();
                    break;

                    case "21":
                        return $this -> getDurationOfRenewalStatusName();
                    break;

                    case "24":
                        return $this -> getDurationOfVisaRejectedStatusName();
                    break;
                    case "27":
                        return "Dobio vizu";
                    break;
                }
            break;
        }
    }
//GETTERS END
}

?>