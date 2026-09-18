<?php

class Form{

    public function __construct(Gateway $gateway, $method)
    {
        $this->gateway = $gateway;
        $this->assertMethod($method);
    }

    public function sendForm(){
        $data = json_decode(file_get_contents("php://input"), true);
        
        $params = $this->gateway->getForm($data);

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