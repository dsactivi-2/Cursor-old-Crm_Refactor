<?php

    class Groups{

        public function __construct(Gateway $gateway, $method)
        {
            $this->gateway = $gateway;
            $this->assertMethod($method);
        }

        public function getGroups()
        {

            $data  = json_decode(file_get_contents("php://input"), true);
            $urlid = $this->assertUrlid($data);

            $groups = $this->gateway->getGroups($urlid);
            $response = array();
            
            foreach($groups as $group){
                $groupArray = array();

                $groupArray["id"]     = $group["kg_id"];
                $groupArray["name"]   = $group["kg_title"];

                array_push($response,$groupArray);
                
                
            }    
            echo json_encode($response,JSON_UNESCAPED_UNICODE);
        }

        private function assertUrlid($data)
        {
            if(!isset($data["urlid"]))
            {
                http_response_code(400);
                die("Required data is not set!");
            }
            else
            {
                return $data["urlid"];
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