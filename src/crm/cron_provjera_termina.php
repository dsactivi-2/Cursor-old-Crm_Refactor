<?php 
    
    include("includes/functions.php");
  
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    $today=date('Y-m-d');
    $query=$db->prepare("
        SELECT kandidat_id,kandidat_nalog_id,datum_termina,idk_nalozi.employee_id as employee_id,employee_firstname, employee_lastname, employee_email 
        FROM  idk_nalozi
        join  idk_kandidati
        on kandidat_nalog_id=nalog_id
        JOIN idk_employees on idk_nalozi.employee_id=idk_employees.employee_id
        where datum_termina=:today
    ");
    $query->execute(array(
        ':today'=>$today
    ));


    $select_rows=$query->fetchAll();

    foreach($select_rows as $select_row){
        $kandidat_id=$select_row['kandidat_id'];
        $kandidat_nalog_id=$select_row['kandidat_nalog_id'];
        $datum_termina=$select_row['datum_termina'];
        $employee_id=$select_row['employee_id'];
        $employee_firstname=$select_row['employee_firstname'];
        $employee_lastname=$select_row['employee_lastname'];
        $employee_email=$select_row['employee_email'];

        $query_user=$db->prepare("
                    SELECT kandidat_ime,kandidat_prezime FROM idk_kandidati where kandidat_id=:kandidat_id
        ");
        $query_user->execute(array(
                    ':kandidat_id'=>$kandidat_id
        ));
        $select_user=$query_user->fetch();
        $kandidat_ime=$select_user['kandidat_ime'];
        $kandidat_prezime=$select_user['kandidat_prezime'];

        
                $txtMail="Korisnik $kandidat_ime $kandidat_prezime  je danas imao termin, provjeri i unesi ishod termina na linku: 
                /kandidati.php?page=open&id=$kandidat_id#proces_odlaska ";
                
                $mail_email = "";
				$mail_name = "";
				$mail_subject = "";
				$mail_body = "";
				$mail_altbody = "";
                
				$mail_email = $employee_email;
				$mail_name = $employee_firstname." ".$employee_lastname;
				$mail_subject = "Provjera termina";
				$mail_body = $txtMail;
				$mail_altbody = $txtMail;

                    $mail = new PHPMailer;
                    $mail->setFrom('info@job-step.net');     		
                    $mail->addAddress($mail_email);
                    $mail->Subject ="Provjera termina kandidata";
                    $mail->Body    = $mail_body;
                    $mail->AltBody = $mail_altbody;
                    $mail->send();
       
    }

   







?>