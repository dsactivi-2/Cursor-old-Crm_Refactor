<?php

class Odustao
{
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function odustao()
    {

        $data  = json_decode(file_get_contents("php://input"), true);
        $token = $this->assertToken($data);

        $this->gateway->checkClient($token);
        $this->checkNumberOfData($data);

        $id = $this->assertCandidateID($data);

        $this->gateway->candidateQuit($id);
    }

    private function assertToken($data)
    {
        if(!isset($data["token"]))
        {
            http_response_code(401);
            throw new Exception("Unauthorized!", 401);
        }
        else
        {
            return $data["token"];
        }
    }

    private function checkNumberOfData($data)
    {
        if(count($data) != 2)
        {
            http_response_code(400);
            throw new Exception("Number of data is not satisfied!", 400);
        }
    }

    private function assertCandidateID($data)
    {
        if(!isset($data["id"]))
        {
            http_response_code(400);
            throw new Exception("Required data is not set!", 400);
        }
        else
        {
            return $data["id"];
        }
    }
}