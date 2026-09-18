<?php

class Controller
{
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function processRequest($method, $endpoint)
    {
        try {
            if($method != "POST")
            {
                http_response_code(405);
                throw new Exception("Method not allowed!", 400);
            }

            switch($endpoint)
            {
                case "procjena":
                    $procjena = new Procjena($this->gateway);
                    $procjena->addProcjena();
                break;

                case "pocetak":
                    $pocetak = new Pocetak($this->gateway);
                    $pocetak->addPocetak();
                break;

                case "kraj":
                    $kraj = new Kraj($this->gateway);
                    $kraj->addKraj();
                break;

                case "odustao":
                    $odustao = new Odustao($this->gateway);
                    $odustao->odustao();
                break;
            }
        }catch(Exception $e){
            http_response_code($e->getCode());
            echo $e->getMessage();
            return $e->getMessage();
        }
    }

}