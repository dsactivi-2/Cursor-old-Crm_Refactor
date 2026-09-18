<?php 

class Schools
{
    public function __construct(Gateway $gateway, $method)
    {
        $this->gateway = $gateway;
        $this->assertMethod($method);
    }

    public function getSchoolsJSON () 
    {

        $schools = $this->gateway->getSchools();

        $response = array();

        foreach ($schools as $school) {

            $school_reformatted         = array();
            $school_reformatted['id']   = $school['skola_id'];
            $school_reformatted['name'] = $school['skola_naziv'];
            
            array_push($response, $school_reformatted);

        } 

        echo json_encode($response, JSON_UNESCAPED_UNICODE);

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