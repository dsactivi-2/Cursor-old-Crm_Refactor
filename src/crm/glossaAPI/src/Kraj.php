<?php

class Kraj
{
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    function addKraj()
    {
        $data  = json_decode(file_get_contents("php://input"), true);
        $token = $this->assertToken($data);

        $this->gateway->checkClient($token);
        $this->checkNumberOfData($data);

        $level = $this->assertLangLvl($data);
        $this->checkLangLvl($level);

        $sub_level  = $this->getSubLvl($level); 
        $motive_lvl = $this->getMotiveLvl($level);
        $lang_level = $this->getLangLvl($level);
        $date       = $this->assertDate($data);
        $candidates = $this->assertCandidates($data);

        if($sub_level != NULL)
        {
            foreach($candidates as $candidate_id)
            {
                $this->gateway->addEndDate($date, $candidate_id, $lang_level, $sub_level);
            }
        }
        if($motive_lvl != NULL)
        {
            foreach($candidates as $candidate_id)
            {
                $this->gateway->addMotiveEndDate($date, $candidate_id, $lang_level);
            }
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
        if(count($data) != 4)
        {
            http_response_code(400);
            throw new Exception("Number of data is not satisfied!", 400);
        }
    }

    private function assertDate($data)
    {
        if(!isset($data["date"]))
        {
            http_response_code(400);
            throw new Exception("Required data is not set!", 400);
        }
        else
        {
            return $data["date"];
        }
    }
    
    private function assertCandidates($data)
    {
        if(!isset($data["candidates"]))
        {
            http_response_code(400);
            throw new Exception("Required data is not set!", 400);
        }
        else
        {

            $ids = [];
            foreach($data["candidates"] as $candidate)
            {
                $candidate_id = $this->gateway->checkCandidate($candidate);
                $ids[] = $candidate_id;
            }
            return $ids;
        }
    }

    private function assertLangLvl($data)
    {
        if(!isset($data["level"]))
        {
            http_response_code(400);
            throw new Exception("Required data is not set!", 400);
        }
        else
        {
            return $data["level"];
        }
    }

    private function checkLangLvl(string $level)
    {
        $approved_levels = ["A1.MOTIVE", "A2.MOTIVE", "A1.1", "A1.2", "A2.1", "A2.2", "B1.1", "B1.2", "B2.1", "B2.2", "C1.1", "C1.2", "C2.1", "C2.2"];
        
        if(!in_array($level, $approved_levels))
        {
            http_response_code(400);
            throw new Exception("Value of language level is not valid!", 400);
        }
    }

    private function getSubLvl(string $level)
    {
        $sub_level = explode(".", $level);
        if($sub_level[1] != "MOTIVE")
        {
            return $sub_level[1];
        }
    }

    private function getLangLvl(string $level)
    {
        $lang = explode(".", $level);
        return $lang[0];
    }

    private function getMotiveLvl(string $level)
    {
        $motive = explode(".", $level);
        if($motive[1] == "MOTIVE")
        {
            return $motive[1];
        }
    }
}