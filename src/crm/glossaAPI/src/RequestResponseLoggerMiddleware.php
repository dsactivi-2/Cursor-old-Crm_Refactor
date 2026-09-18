<?php
class RequestResponseLoggerMiddleware
{
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function logRequest()
    {
        return $data = [
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'request_uri' => $_SERVER['REQUEST_URI'],
            'request_headers' => getallheaders(),
            'request_body' => json_decode(file_get_contents("php://input")),
            'timestamp' => date("Y-m-d H:i:s"),
        ];
    }

    public function logResponse($request, $response)
    {
        $data = [
            'response_status_code' => http_response_code(),
            'response_body' => $response,
            'request_body' => $request,
            'timestamp' => date("Y-m-d H:i:s"),
        ];

        $this->saveToLog($data);
    }

    private function saveToLog($logData)
    {
        if($logData["response_status_code"] == 200 AND $logData["response_body"] == null){
            $response_body = "Success";
        }else{
            $response_body = json_encode($logData["response_body"]);
        }

        if($logData["request_body"]["request_body"]->id == null){
            $candidates = json_encode($logData["request_body"]["request_body"]->candidates);
        }else{
            $candidates = $logData["request_body"]["request_body"]->id;
        }

        $sql = "INSERT INTO 
                    idk_glossa_log 
                    (
                        request, 
                        response, 
                        code, 
                        candidate, 
                        request_endpoint 
                    )
                VALUES
                    (
                       :request, 
                       :response, 
                       :code, 
                       :candidate, 
                       :request_endpoint
                    )";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":request", json_encode($logData["request_body"]), PDO::PARAM_STR);
        $stmt->bindValue(":response", $response_body, PDO::PARAM_STR);
        $stmt->bindValue(":code", $logData["response_status_code"], PDO::PARAM_STR);
        $stmt->bindValue(":candidate", $candidates, PDO::PARAM_INT);
        $stmt->bindValue(":request_endpoint", $logData["request_body"]["request_uri"], PDO::PARAM_INT);

        $stmt->execute();

    }
}
