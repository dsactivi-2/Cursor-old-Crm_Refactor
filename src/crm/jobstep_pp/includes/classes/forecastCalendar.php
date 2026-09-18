<?php


class nalogData{
    
    public $min_year;
    public $max_year;
    public $table;
    public $nalog_id;
    public $nalog_name;
    public $partnersData            = array();
    public $nalogCountsByMonthYear  = array();

    function __construct($candidateProjections, $wanted_nalog_id, $year){
        // var_dump($year);

        $indexOfCurrentCount = 0;
        $flag_insert_new_count = 1;
        $flag_insert_new_partner = 1;
        
        $this -> nalog_id = $wanted_nalog_id;
        $this -> nalog_name = $this -> getNalogNameByNalogID($candidateProjections, $wanted_nalog_id);  

        if(is_null($this -> nalog_name)){
            $this -> nalog_name = $this -> getNalogNameByNalogIDFromDB($wanted_nalog_id);
        }
        // var_dump($this -> getNalogNameByNalogID($candidateProjections, $wanted_nalog_id));
        // exit();
        for($i = 0; $i < count($candidateProjections); $i++){
            $flag_insert_new_count = 1;
            $flag_insert_new_partner = 1;
            if($candidateProjections[$i]['nalog_id'] == $wanted_nalog_id){
                $candidate_month = date('n', $candidateProjections[$i]["candidate_assessment"]);
                $candidate_year = date('Y', $candidateProjections[$i]["candidate_assessment"]);
                $candidate_partner_id = $candidateProjections[$i]["partner_id"];
                $candidate_partner_name = $candidateProjections[$i]["partner_name"];
                if($candidate_year == $year OR $year == 0){
                    foreach($this -> nalogCountsByMonthYear as $currentCountData){
                        if($currentCountData -> checkMonthYear($candidate_year, $candidate_month)){
                            $flag_insert_new_count = 0;
                        }
                    }
                    if($flag_insert_new_count){
                        array_push($this -> nalogCountsByMonthYear, new nalogCountByMonthYear($candidate_year, $candidate_month));
                    }
                    
                    foreach($this -> partnersData as $currentPartner){
                        if($currentPartner -> checkPartners($candidate_partner_id)){
                            $flag_insert_new_partner = 0;
                        }
                    }
                    if($flag_insert_new_partner){
                        array_push($this -> partnersData, new partnerData($candidateProjections, $candidate_partner_id, $candidate_partner_name, $wanted_nalog_id, $year));
                    }
                    // var_dump($this -> partnersData);
                }
            }
        }
    }
    private function getNalogNameByNalogIDFromDB($nalog_id){
        Global $db;
        $query_get_nalog_name = $db -> prepare('
            SELECT nalog_naziv
            FROM idk_nalozi
            WHERE nalog_id = :nalog_id
        ');
        $query_get_nalog_name -> execute(array('nalog_id' => $nalog_id));
        $row_get_nalog_name = $query_get_nalog_name -> fetch();
        return $row_get_nalog_name['nalog_naziv'];
    }
    private function getNalogNameByNalogID($candidateProjections, $nalog_id){
        // $return_info = array(
        //     "candidate_projection" => $candidateProjections,
        //     "nalog_id" => $nalog_id
        // );
        // return json_encode($return_info);
        for($i = 0; $i < count($candidateProjections); $i++){
            if($candidateProjections[$i]['nalog_id'] == $nalog_id){
                return $candidateProjections[$i]['nalog_name'];
            }
        }
    }
    public function getNalogMinYear(){
        if(is_null($this -> nalogCountsByMonthYear[0])){
            $min_year =  3000;
        }
        else{
            $min_year = $this -> nalogCountsByMonthYear[0] -> getYear();
        }
        for($i = 1; $i < count($this -> nalogCountsByMonthYear); $i++){
            if(!is_null($this -> nalogCountsByMonthYear[$i])){
                $year_to_check = $this -> nalogCountsByMonthYear[$i] -> getYear();
                if($min_year > $year_to_check){
                    $min_year = $year_to_check;
                }
            }
        }
        return $min_year;
    }
    public function getNalogMaxYear(){
        if(is_null($this -> nalogCountsByMonthYear[0])){
            $max_year =  1000;
        }
        else{
            $max_year = $this -> nalogCountsByMonthYear[0] -> getYear();
        }
        for($i = 1; $i < count($this -> nalogCountsByMonthYear); $i++){
            if(!is_null($this -> nalogCountsByMonthYear[$i])){
                $year_to_check = $this -> nalogCountsByMonthYear[$i] -> getYear();
                if($max_year < $year_to_check){
                    $max_year = $year_to_check;
                }
            }
        }
        return $max_year;
    }
}
class nalogCountByMonthYear{
    public $month;
    public $year;
    public $count;

    function __construct($year, $month){
        $this -> year = $year;
        $this -> month = $month;
        $this -> count = 1;
    }
    public function incrementCount(){
        $this -> count++;
    }
    public function checkMonthYear($candidate_year, $candidate_month){
        if($this -> month == $candidate_month AND $this -> year == $candidate_year){
            $this -> incrementCount();
            return 1;
        }
        else{
            return 0;
        }
    }
    public function getYear(){

        return $this -> year;
    }
}

class partnerData{
    public $partner_id;
    public $partner_name;
    public $partnerCountByMonthYear = array();
    
    function __construct($candidateProjections, $partner_id, $partner_name, $wanted_nalog_id, $year){
        $this -> partner_id = $partner_id; 
        $this -> partner_name = $partner_name;

        for($i = 0; $i < count($candidateProjections); $i++){
            $flag_insert_new_count = 1;
            if($candidateProjections[$i]['nalog_id'] == $wanted_nalog_id AND $candidateProjections[$i]['partner_id'] == $partner_id){
                $candidate_month = date('n', $candidateProjections[$i]["candidate_assessment"]);
                $candidate_year = date('Y', $candidateProjections[$i]["candidate_assessment"]);
                $candidate_partner_id = $candidateProjections[$i]["partner_id"];
                $candidate_partner_name = $candidateProjections[$i]["partner_name"];
                if($candidate_year == $year OR $year == 0){
                    foreach($this -> partnerCountByMonthYear as $currentCountData){
                        if($currentCountData -> checkMonthYear($candidate_year, $candidate_month)){
                            $flag_insert_new_count = 0;
                        }
                    }
                    if($flag_insert_new_count){
                        array_push($this -> partnerCountByMonthYear, new nalogCountByMonthYear($candidate_year, $candidate_month));
                    }
                }
            }
        }
    }

    public function checkPartners($partner_id){
        if($this -> partner_id == $partner_id){
            return 1;
        }
        else{
            return 0;
        }
    }
}
class partnerCountByMonthYear{
    public $month;
    public $year;
    public $count;

    function __construct($year, $month){
        $this -> year = $year;
        $this -> month = $month;
        $this -> count = 1;
    }
    public function incrementCount(){
        $this -> count++;
    }
    public function checkMonthYear($candidate_year, $candidate_month){
        if($this -> month == $candidate_month AND $this -> year == $candidate_year){
            $this -> incrementCount();
            return 1;
        }
        else{
            return 0;
        }
    }
}


?>