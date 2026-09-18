<?php

//PHPMailer
require $_SERVER['DOCUMENT_ROOT'].'/mail/Exception.php';
require $_SERVER['DOCUMENT_ROOT'].'/mail/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/mail/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$subject    = $_POST["subject"];
$body       = $_POST["body"];

$key = $_POST["key"];

if ($key != "QJEBlaAXcOVpfr9vhU6asUdD1QszaTty") {
    exit();
}

$mail_recipient = "support@job-step.com";
$mail_name = "Jobstep Support";

$mail_subject = $subject;
$mail_body = $body;
$mail_altbody = "alt";
$mail = new PHPMailer(true);					// Passing `true` enables exceptions
$mail->isSMTP();
try {
    //Server settings
    $mail->Host = 'smtp.gmail.com';  			// Specify main and backup SMTP servers
    $mail->SMTPAuth = true;						// Enable SMTP authentication
    $mail->Username = 'support@job-step.com';	// SMTP username
    $mail->Password = 'eooc nnxo aylp lqkh';		// SMTP password
    $mail->SMTPSecure = 'ssl';					// Enable TLS encryption, `ssl` also accepted
    $mail->Port = 465;							// TCP port to connect to
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('support@job-step.com', 'JobStep');

    //Recipients
    $mail->addAddress($mail_recipient, $mail_name);     // Add a recipient

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $mail_subject;
    $mail->Body    = $mail_body;
    $mail->AltBody = $mail_altbody;

    $mail->send();

}catch (Exception $e){
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}

?>