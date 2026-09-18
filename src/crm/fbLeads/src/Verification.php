<?php

class Verification {
    public function __construct(Gateway $gateway){
        $this->gateway = $gateway;
    }


    public function verifyAPI(){
        $mode = $_GET['hub_mode'];
        $challenge = $_GET['hub_challenge'];
        $token = $_GET['hub_verify_token'];

        if($mode && $token && $challenge){
            echo $challenge;
        }

        $data  = json_decode(file_get_contents("php://input"), true);
        $lead_id = $data['entry'][0]['changes'][0]['value']['leadgen_id'];

        $lead = $this->gateway->getLead($lead_id);
        $lead_result = json_decode($lead, true);

        $kamp = "";
        $first_name = "";
        $last_name = "";
        $phone_number = "";
        $urlid = "";

        foreach($lead_result['field_data'] as $field){
            switch($field['name']){
                case 'kamp':
                    $kamp = $field['values'][0];
                    break;
                case 'urlid':
                    $urlid = $field['values'][0];
                    break;
                case 'first_name':
                    $first_name = $field['values'][0];
                    break;
                case 'last_name':
                    $last_name = $field['values'][0];
                    break;
                case 'phone_number':
                    $phone_number = $field['values'][0];
                    break;
            }
        }
        if($urlid !== ""){
            $this->gateway->saveLeadForm($urlid, $lead_result);
        } else {
            $this->gateway->saveLead($kamp, $first_name, $last_name, $phone_number);
        }
    }
}