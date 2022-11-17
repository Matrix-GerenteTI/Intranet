<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/intranet/lib/phpmailer/class.phpmailer.php';


class MailSender  
{
    public function send(  $params )
    {
        extract( $params );
        
        $emailsender = new phpmailer;
        $emailsender->isSMTP();
        $emailsender->SMTPDebug = 0;
        $emailsender->SMTPAuth = true;
        $emailsender->Port = 587;

        $emailsender->Host = 'mail.matrix.com.mx';
        $emailsender->Username = "no-responder@matrix.com.mx";
        $emailsender->Password = "M@tr1x2017";

        $emailsender->From ="no-responder@matrix.com.mx";
        $emailsender->FromName = $descripcionDestinatario;

        $emailsender->Subject ="$subject";
        $emailsender->Body = "<p>$mensaje</p>";

        $emailsender->AltBody = "...";

        if ( is_file($pathFile) ) {
            $emailsender->AddAttachment( $pathFile);
        }
        //sestrada
        foreach ($correos as $email) {
            $emailsender->AddAddress( $email );
        }
        /*$emailsender->AddAddress("luisimatrix@matrix.com");
            $emailsender->AddAddress("sestrada@matrix.com.mx");
            $emailsender->AddAddress("raulmatrixxx@hotmail.com");*/
            //$emailsender->AddAddress("auxsistemas@hotmail.com");
        
        $statusEnvio = $emailsender->Send();

        if ( $emailsender->ErrorInfo == "SMTP Error: Data not accepted") {
            $statusEnvio = true;
        } 

        if ( !$statusEnvio ) {
            return "[".$emailsender->ErrorInfo."] - Problemas enviando correo electrónico a ";
        } else {
            return "Enviado";
        }

    }
}
