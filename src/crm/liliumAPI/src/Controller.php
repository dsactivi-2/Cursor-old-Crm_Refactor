<?php

class Controller
{
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function processRequest($method, $endpoint)
    {

        $token = $this->getBearerToken();
        $this->gateway->authorizeClient($token);

        $this->assertMethod($method);
        $this->assertEndpoint($endpoint);


        switch($endpoint)
        {
            case "Professions":

                $professions = new Professions($this->gateway, $method);
                $professions->getProfessions();

            break;

            case "Schools": 

                $schools = new Schools($this->gateway, $method);
                $schools->getSchoolsJSON();

            break;

            case "OrderSchools":

                $orderSchools = new OrderSchools($this->gateway, $method);
                $orderSchools->getOrderSchools();

            break;

            case "Groups":

                $grupe = new Groups($this->gateway, $method);
                $grupe->getGroups();

            break;

            case "Form":

                $forma = new Form($this->gateway, $method);
                $forma->sendForm();

            break;

            case "DefaultForm":

                $defaultForm = new DefaultForm($this->gateway, $method);
                $defaultForm->sendDefaultForm();

            break;

            case "Atu":

                $atu = new Atu($this->gateway, $method);
                $atu->sendFormAtu();

            break;

        }
    }

    private function assertMethod($method)
    {
        if($method != "GET" && $method != "POST")
        {
            http_response_code(405);
            die("Method not allowed!");
        }
    }

    private function assertEndpoint($endpoint)
    {
        $endpoints = ["Professions", "Schools", "Groups", "Form", "Atu", "OrderSchools", "DefaultForm"];

        if(!in_array($endpoint, $endpoints))
        {
            http_response_code(404);
            die("Endpoint not found!");
        }
    }

    private function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) 
        {
            $headers = trim($_SERVER["Authorization"]);
        } 
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) 
        { 
            //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } 
        elseif (function_exists('apache_request_headers')) 
        {
            $requestHeaders = apache_request_headers();

            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

            if (isset($requestHeaders['Authorization'])) 
            {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    private function getBearerToken()
    {
        $headers = $this->getAuthorizationHeader();

        // HEADER: Get the access token from the header
        if (!empty($headers)) 
        {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) 
            {
                return $matches[1];
            }
        }
        return null;
    }

}