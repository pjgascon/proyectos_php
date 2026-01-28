<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);


require_once(getcwd() . '/vendor/Exception.php');
require_once(getcwd() . '/vendor/PHPMailer.php');
require_once(getcwd() . '/vendor/SMTP.php');
require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/textos.php');

$procesarCorreo = false;
$c = new Conexion();
$c->conectar();

$r = $c->query("call robot_obtener_mail(2);");
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
            if ($r[$i]["emisor"] == "info@maatcrm.es") {
                $texto = Textos::textoMailMaat($r[$i]["texto"]);
                Textos::configuracionMaat($mail);
            } elseif ($r[$i]["emisor"] == "comunicaciones@grupobeetobee.com") {
                $texto = Textos::textoMailB2B($r[$i]["texto"]);
                Textos::configuracionB2B($mail);
            }

            //Set Params           
            $mail->AddAddress($r[$i]["para"]);
            $mail->Subject = $r[$i]["asunto"];
            $mail->Body = $texto;

            if (!is_null($r[$i]['adjunto'])) {
                if (strlen($r[$i]['adjunto']) > 4) {
                    if (is_file($r[$i]['adjunto'])) {
                        $mail->addAttachment($r[$i]['adjunto']);
                    }
                }
            }

            if (!$mail->Send()) {
                $c->query("call robot_modificar_estado(" . $r[$i]['id'] . ",0);");
                echo "Mailer Error: " . $mail->ErrorInfo;
            } else {
                $c->query("call robot_modificar_estado(" . $r[$i]['id'] . ",1);");
                echo "Correo " . $i . " enviado info@waspapp.net\n\r";
            }
        } catch (Exception $e) {
            $c->query("call robot_modificar_estado(" . $r[$i]['id'] . ",0);");
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        usleep(2000000);
    }
    $c->close();
}
