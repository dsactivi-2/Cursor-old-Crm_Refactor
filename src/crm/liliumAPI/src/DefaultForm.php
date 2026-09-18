<?php
class DefaultForm
{
    public function __construct(Gateway $gateway, $method)
    {
        $this->gateway = $gateway;
        $this->assertMethod($method);
    }

    public function sendDefaultForm(){
        $data = json_decode(file_get_contents("php://input"), true);
        
        $params = $this->gateway->getDefaultForm($data);

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