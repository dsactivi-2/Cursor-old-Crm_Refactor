<?php
include_once 'env.php';
if($envConfig->APP_ENV != 'dev'){
  $env = parse_ini_file('.env');
  $app_id = $env["APP_ID"];
  $app_key = $env["APP_KEY_TOKEN"];
  $user_key = $env["USER_KEY_TOKEN"];

  function sendNotification($onesignal_id, $title, $content){
    Global $app_id;
    Global $app_key;
    Global $user_key;

    $data = array(
      "app_id"=> "$app_id",
      "include_subscription_ids" => array("$onesignal_id"),
      "target_channel" => "push",
      "headings" =>  array(
          "en" => "$title"
      ),
      "contents" =>  array(
          "en" => "$content"
      )
    );

    $url = "https://onesignal.com/api/v1/notifications";
    $data_json = json_encode($data);

    $headers = array( 
      'Authorization: Basic ' . $app_key,
      'Content-Type: application/json; charset=utf-8'
    );

    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $url);                                                                 
    curl_setopt($ch, CURLOPT_POST, 1);  
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, $data_json);  

    $result = curl_exec($ch);
  }

  function sendMultipleNotifications($onesignal_ids, $title, $content){
    Global $app_id;
    Global $app_key;
    Global $user_key;

    $data = array(
      "app_id"=> "$app_id",
      "include_subscription_ids" => $onesignal_ids,
      "target_channel" => "push",
      "headings" =>  array(
          "en" => "$title"
      ),
      "contents" =>  array(
          "en" => "$content"
      )
    );

    $url = "https://onesignal.com/api/v1/notifications";
    $data_json = json_encode($data);

    $headers = array( 
      'Authorization: Basic ' . $app_key,
      'Content-Type: application/json; charset=utf-8'
    );

    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $url);                                                                 
    curl_setopt($ch, CURLOPT_POST, 1);  
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, $data_json);  

    $result = curl_exec($ch);
    curl_close ($ch);

  }
}


