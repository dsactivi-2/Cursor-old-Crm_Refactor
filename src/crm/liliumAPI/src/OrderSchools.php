<?php
class OrderSchools
{
    public function __construct(Gateway $gateway, $method)
    {
        $this->gateway = $gateway;
        $this->assertMethod($method);
    }

    public function getOrderSchools()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $schools = $this->gateway->getOrderSchools($data);
        if($schools == 1)
        {
            $professions = $this->gateway->getProfessions();
            $response = array();
            
            foreach($professions as $profession){
                $professionArray = array();

                $professionArray["id"]        = $profession["ss_id"];
                $professionArray["name"]      = $profession["ss_naziv"];
                $professionArray["school_id"] = $profession["ss_skola_id"];
                $professionArray["name_de"]   = $profession["ss_naziv_de"];
                $professionArray["name_en"]   = $profession["ss_naziv_en"];

                array_push($response,$professionArray);
                
                
            }    
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
        }
        else
        {
            $response = array();

            foreach($schools as $school)
            {
                $schoolArray = array();

                $schoolArray["id"]   = $school["smjer_id"];
                $schoolArray["name"] = $school["ss_naziv"];
                $schoolArray["name_de"] = $school["ss_naziv_de"];
                $schoolArray["name_en"] = $school["ss_naziv_en"];

                array_push($response,$schoolArray);
            }
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
        }
    }

    private function assertMethod($method)
    {
        if($method != "POST")
        {
            http_response_code(405);
            die("Method not allowed!");
        }
    }
}