<?php

require_once $_SERVER['DOCUMENT_ROOT']."/intranet/controladores/Reportes/prepareExcel.php";
$mail = new \phpmailer;
$mail->IsSMTP(); // habilita SMTP
$mail->SMTPDebug = 1; // debugging: 1 = errores y mensajes, 2 = sólo mensajes
$mail->SMTPAuth = true; // auth habilitada
$mail->SMTPSecure = 'ssl'; // transferencia segura REQUERIDA para Gmail
$mail->Host = "smtp.gmail.com";
$mail->Port = 465; // or 587
$mail->IsHTML(true);
$mail->Username = "rhmatrix2019@gmail.com";
$mail->Password = "M@tr1x2017";
$mail->SetFrom("rhmatrix2019@gmail.com");
$mail->Subject = "Test";
$mail->Body = "hello";
$mail->AddAddress("sergio_estave@hotmail.com");

 if(!$mail->Send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
 } else {
    echo "Message has been sent";
 }
 ?>