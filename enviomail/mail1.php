<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);


require_once(getcwd() . '/vendor/Exception.php');
require_once(getcwd() . '/vendor/PHPMailer.php');
require_once(getcwd() . '/vendor/SMTP.php');
require_once(getcwd() . '/vendor/conexion.php');

$procesarCorreo = false;
$c = new Conexion();
$c->conectar();

$r = $c->query("call robot_obtener_mail(8);");
if ($r->num_rows > 0) {
    $r = $r->fetch_all(MYSQLI_ASSOC);
    $procesarCorreo = true;
    $c->next_result();
}else{
    $c->close();
    echo "Nada que hacer";
    return;
}

if ($procesarCorreo) {
    for($i = 0; $i < count($r); $i++){
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        try {

            $mail->IsSMTP();

            $mail->CharSet = "UTF-8";
            $mail->Host = "smtp.serviciodecorreo.es";
            $mail->SMTPDebug = 0;
            $mail->Port = 587; //465 or 587

            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->IsHTML(false);

            //Authentication
            $mail->Username = "correo@waspapp.es";
            $mail->Password = "Coral1826";

            //Set Params
            $mail->setFrom("correo@waspapp.es", 'Comunicaciones WaspApp');
            $mail->AddAddress($r[$i]["para"]);
            $mail->Subject = $r[$i]["asunto"];
            $mail->Body = $r[$i]["texto"];

            if(strlen($r[$i]['adjunto']) > 4){
                if(is_file($r[$i]['adjunto'])){
                    $mail->addAttachment($r[$i]['adjunto']);
                }
            }

            if (!$mail->Send()) {
                $c->query("call robot_modificar_estado(".$r[$i]['id'].",0);");
                echo "Mailer Error: " . $mail->ErrorInfo;
            } else {
                $c->query("call robot_modificar_estado(".$r[$i]['id'].",1);");  
                echo "Correo ".$i." enviado correo@waspapp.es\n\r";
            }


            //$c->next_result();

            //     //Server settings
            //     //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            //     $mail->isSMTP();                                            //Send using SMTP
            //     $mail->Host       = 'smtp.buzondecorreo.com';                     //Set the SMTP server to send through
            //     $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            //     $mail->Username   = 'hil580c';                     //SMTP username
            //     $mail->Password   = 'Coral1826';                               //SMTP password
            //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            //     $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //     //Recipients
            //     $mail->setFrom('info@connectcloud.es', 'Mailer');
            //     $mail->addAddress('pedrojose.gascon@gmail.com', 'Joe User');     //Add a recipient
            //     //$mail->addAddress('ellen@example.com');               //Name is optional
            //     $mail->addReplyTo('info@connectcloud.es', 'Information');
            //    //$mail->addCC('cc@example.com');
            //    // $mail->addBCC('bcc@example.com');

            //     //Attachments
            //    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //     //Content
            //     $mail->isHTML(true);                                  //Set email format to HTML
            //     $mail->Subject = 'Here is the subject';
            //     $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            //     $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            //     $mail->send();
            // echo 'Message has been sent';
        } catch (Exception $e) {
            $c->query("call robot_modificar_estado(".$r[$i]['id'].",0);");
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    $c->close();
}
