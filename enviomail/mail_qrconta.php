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

            $texto = "<!DOCTYPE html
    PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='es'>

<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <title>qrconta.com - Contacto Empresarial</title>
</head>

<body style='margin: 0; padding: 0; background-color: #f8f8f8;'>

    <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%'
        style='border-collapse: collapse; background-color: #f8f8f8;'>
        <tr>
            <td align='center' style='padding: 20px 0 30px 0;'>

                <table border='0' cellpadding='0' cellspacing='0' width='600'
                    style='border-collapse: collapse; background-color: #ffffff; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>

                    <tr>
                        <td align='left' style='padding: 30px 30px 10px 30px; border-bottom: 3px solid #008080;'>
                            <h1 style='color: #333333; font-family: Arial, sans-serif; font-size: 24px; margin: 0;'>
                                <span style='color: #008080;'>qrconta.com</span>
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style='padding: 30px 30px 20px 30px;'>
                            {$r[$i]["texto"]}
                        </td>
                    </tr>

                    <tr>
                        <td align='center' style='padding: 10px 30px 10px 30px;'>
                            <p style='margin: 0;'>
                                <a href='https://qrconta.com' target='_blank'
                                    style='background-color: #17a2b8; color: #ffffff; font-family: Arial, sans-serif; font-size: 16px; font-weight: bold; padding: 12px 25px; display: inline-block; text-decoration: none; border-radius: 5px;'>
                                    QRConta.com
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding: 30px; background-color: #f9f9f9; border-top: 1px solid #eeeeee;'>
                            <p
                                style='color: #333333; font-family: Arial, sans-serif; font-size: 15px; font-weight: bold; margin: 0;'>
                                Líberi Software S.L.
                            </p>                           
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>";

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
            $mail->Body = $texto;

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
