<?php

class Procjena
{
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function addProcjena()
    {
        $data  = json_decode(file_get_contents("php://input"), true);
        $token = $this->assertToken($data);

        $this->gateway->checkClient($token);
        $this->checkNumberOfData($data);

        $id           = $this->assertCandidateID($data);
        $procjena     = $this->assertLangLvl($data);
        $candidate_id = $this->gateway->checkCandidate($id);

        $trimmed_procjena = $this->trimLangLevel($procjena);
        $this->checkLangLevel($trimmed_procjena);

        $language_id = $this->gateway->checkLanguage($candidate_id, $trimmed_procjena);
        if($language_id == 0)
        {
           $this->gateway->insertNewLanguage($candidate_id, $trimmed_procjena);
        }
        else
        {
            $this->gateway->updateExistingLanguage($language_id, $candidate_id);
        }
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
        if(count($data) != 3)
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
            throw new Exception("Allowed data is not set!", 400);
        }
        else
        {
            return $data["id"];
        }
    }

    private function assertLangLvl($data)
    {
        if(!isset($data["procjena"]))
        {
            http_response_code(400);
            throw new Exception("Allowed data is not set!", 400);
        }
        else
        {
            return $data["procjena"];
        }
    }

    private function checkLangLevel(string $level)
    {
        $approved_levels = ["A1", "A2", "B1", "B2", "C1", "C2"];

        if(!in_array($level, $approved_levels))
        {
            http_response_code(400);
            throw new Exception("Value of language level is not valid!", 400);
        }
    }

    private function checkLangSubLevel(string $sublevel)
    {
        $approved_levels = ["A1.1", "A1.2", "A2.1", "A2.2", "B1.1", "B1.2", "B2.1", "B2.2", "C1.1", "C1.2", "C2.1", "C2.2"];

        if(!in_array($sublevel, $approved_levels))
        {
            http_response_code(400);
            throw new Exception("Value of language sublevel is not valid! Example: A1.1!", 400);
        }
    }

    private function trimLangLevel(string $sublevel)
    {
        $this->checkLangSubLevel($sublevel);

        $level = strstr($sublevel, '.', true);

        return $level;
    }

}