<?php

class Controller
{
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function processRequest($endpoint){
        switch($endpoint){
            case "webhooks":
                $verification = new Verification($this->gateway);
                $verification->verifyAPI();
                break;
        }

    }
}