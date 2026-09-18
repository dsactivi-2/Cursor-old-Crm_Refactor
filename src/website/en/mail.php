<?php
include('includes/functions.inc.php');
$form="";
if(isset($_REQUEST["form"])) {
	$form = $_REQUEST["form"];
    switch ($form)
    {
        case "footer_form":

            $firstName  = trim($_POST['firstName']);
            $lastName   = trim($_POST['lastName']);
            $email      = trim($_POST['email']);
            $phone      = trim($_POST['phone']);
            $subject    = trim($_POST['subject']);
            $message    = trim($_POST['message']);

            $body = '

                <div style="padding:20px 20px; width: 100%; color: #001E62; font-size: 14px; line-height: 14px;">
                Napravljen je novi upit sa Web stranice: <br><br>
                <p><b>Ime:</b> '. $firstName .'</p>
                <p><b>Prezime:</b> '. $lastName .'</p>
                <p><b>Email:</b> '. $email .'</p>
                <p><b>Telefon:</b> '. $phone .'</p>
                <p><b>Naslov:</b> '. $subject .'</p>
                <p><b>Poruka:</b> '. $message .'</p><br>
                </div>
            ';

            $params = [
                'subject' => 'Kontakt sa Web stranice',
                'body' => $body,
                'key' => 'QJEBlaAXcOVpfr9vhU6asUdD1QszaTty'
            ];
            $data = http_build_query($params);
            $url = $envConfig->CRM_URL . "mail_contact_form.php";
            
            $ch = curl_init();

            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($params));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);
            // var_dump($result);
            curl_close($ch);

            header("Location: " . getSiteUrlFront1() . "kontakt.php?mess=1");

        break;

        case "employeer_form":

            $nameSurname    = trim($_POST['nameSurname']);
            $companyName    = trim($_POST['companyName']);
            $email          = trim($_POST['email']);
            $phone          = trim($_POST['phone']);

            $body = '

                <div style="padding:20px 20px; width: 100%; color: #001E62; font-size: 14px; line-height: 14px;">
                Napravljen je novi upit sa Web stranice: <br><br>
                <p><b>Ime i prezime</b> '. $nameSurname .'</p>
                <p><b>Naziv kompanije:</b> '. $companyName .'</p>
                <p><b>Email:</b> '. $email .'</p>
                <p><b>Telefon:</b> '. $phone .'</p>
                </div>
            ';

            $params = [
                'subject' => 'Kontakt Poslodavca sa Web stranice',
                'body' => $body,
                'key' => 'QJEBlaAXcOVpfr9vhU6asUdD1QszaTty'
            ];
            $data = http_build_query($params);
            $url = $envConfig->CRM_URL . "mail_contact_form.php";
            
            $ch = curl_init();

            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, count($params));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);
            // var_dump($result);
            curl_close($ch);

            header("Location: " . getSiteUrlFront1() . "za-poslodavca?mess=1");

        break;
    }
}

?>