<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);


require_once(getcwd() . '/vendor/Exception.php');
require_once(getcwd() . '/vendor/PHPMailer.php');
require_once(getcwd() . '/vendor/SMTP.php');
require_once(getcwd() . '/vendor/conexion_cronos.php');

$procesarCorreo = false;
$c = new Conexion();
$c->conectar();

$r = $c->query("call contabilidad.robot_obtener_mail(5);");
if ($r->num_rows > 0) {
    $r = $r->fetch_all(MYSQLI_ASSOC);
    $procesarCorreo = true;
    $c->next_result();
} else {
    $c->close();
    echo "Nada que hacer";
    return;
}

if ($procesarCorreo) {
    for ($i = 0; $i < count($r); $i++) {
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        try {

            $mail->IsSMTP();

            $mail->CharSet = "UTF-8";
            $mail->Host = "smtp.buzondecorreo.com";
            $mail->SMTPDebug = 0;
            $mail->Port = 587; //465 or 587

            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->IsHTML(true);

            //Authentication
            $mail->Username = "info@qrconta.com";
            $mail->Password = "Coral18262202#";

            //Set Params
            $mail->setFrom("info@qrconta.com", 'Comunicaciones QRConta');

            $mail->AddAddress($r[$i]["para"]);
            $mail->Subject = $r[$i]["asunto"];
            $mail->Body = $r[$i]["texto"];

            if (!is_null($r[$i]['adjunto'])) {
                if (strlen($r[$i]['adjunto']) > 4) {
                    if (is_file($r[$i]['adjunto'])) {
                        $mail->addAttachment($r[$i]['adjunto']);
                    }
                }
            }

            if (!$mail->Send()) {
                $c->query("call contabilidad.robot_modificar_estado(" . $r[$i]['id'] . ",0);");
                echo "Mailer Error: " . $mail->ErrorInfo;
            } else {
                $c->query("call contabilidad.robot_modificar_estado(" . $r[$i]['id'] . ",1);");
                echo "Correo " . $i . " enviado info@cronosapp.es\n\r";
            }
        } catch (Exception $e) {
            $c->query("call contabilidad.robot_modificar_estado(" . $r[$i]['id'] . ",0);");
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        usleep(2000000);
    }
    $c->close();
}
