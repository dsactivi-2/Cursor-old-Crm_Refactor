<?php

class Professions
{

    public function __construct(Gateway $gateway, $method)
    {
        $this->gateway = $gateway;
        $this->assertMethod($method);
    }

    public function getProfessions()
    {
        
        $professions = $this->gateway->getProfessions();
        $response = array();
        
        foreach($professions as $profession){
            $professionArray = array();

            $professionArray["id"]        = $profession["ss_id"];
            $professionArray["name"]      = $profession["ss_naziv"];
            $professionArray["school_id"] = $profession["ss_skola_id"];

            array_push($response,$professionArray);
            
            
        }    
        echo json_encode($response,JSON_UNESCAPED_UNICODE);
    }

    private function assertMethod($method)
    {
        if($method != "GET")
        {
            http_response_code(405);
            die("Method not allowed!");
        }
    }
}