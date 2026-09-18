<?php

class Atu{

    public function __construct(Gateway $gateway, $method)
    {
        $this->gateway = $gateway;
        $this->assertMethod($method);
    }

    public function sendFormAtu(){
        $data = json_decode(file_get_contents("php://input"), true);
        
        $params = $this->gateway->getAtuForm($data);

        $this->gateway->sendFormCurl($params);
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