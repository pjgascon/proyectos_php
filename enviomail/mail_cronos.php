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

$r = $c->query("call robot_obtener_mail(5);");
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

            if ($r[$i]['tipologia'] == 1) {
                //Authentication
                $mail->Username = "info@cronosapp.es";
                $mail->Password = "Coral18262202";

                //Set Params
                $mail->setFrom("info@cronosapp.es", 'Comunicaciones CRONOS Crm');
                $mail->Body = $r[$i]["texto"];
                $mail->Subject = $r[$i]["asunto"];
            } elseif ($r[$i]['tipologia'] == 2) {
                //Authentication
                $mail->Username = "correo@fichabit.es";
                $mail->Password = "Coral19272202#";

                //Set Params
                $mail->setFrom("correo@fichabit.es", 'Fichabit Gestion Laboral');
                $cuerpo = str_replace("CRONOS CRM", "Fichabit Gestion Laboral", $r[$i]["texto"]);
                $mail->Body = "<!DOCTYPE html>
                                <html lang='es'>
                                <head>
                                    <meta charset='UTF-8'>
                                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                                    <title>Fichabit</title>
                                    <style>
                                        body { margin: 0; padding: 0; background-color: #f6f6f6; font-family: Arial, sans-serif; }
                                        table { border-collapse: collapse; }
                                        /* Media Query para móvil - ¡Muchos clientes lo ignoran! */
                                        @media only screen and (max-width: 600px) {
                                            .container { width: 100% !important; }
                                            .content-area { padding: 10px !important; }
                                            .header-logo img { max-width: 150px !important; height: auto !important; }
                                        }
                                    </style>
                                </head>
                                <body style='margin: 0; padding: 0; background-color: #f6f6f6; font-family: Arial, sans-serif;'>

                                <table border='0' cellpadding='0' cellspacing='0' width='100%' style='min-width: 320px;'>
                                    <tr>
                                        <td align='center' style='padding: 20px 0;'>
                                            <table border='0' cellpadding='0' cellspacing='0' width='600' class='container' style='width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>

                                                <tr>
                                                    <td align='center' style='background-color: #B3FF00; padding: 20px 0; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                                        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                                            <tr>
                                                                <td align='center' class='header-logo' style='padding: 0 20px;'>
                                                                    <a href='https://www.fichabit.es' target='_blank' style='text-decoration: none;'>
                                                                        <h1 style='color: #000000; margin: 0; font-size: 24px;'>Fichabit Gestión Laboral</h1>
                                                                        </a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class='content-area' style='padding: 30px 40px; color: #333333;'>
                                                        <p style='font-size: 16px; line-height: 1.6;'>
                                                            {$cuerpo}
                                                        </p>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td align='center' style='background-color: #f2f2f2; padding: 20px 40px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;'>
                                                        <p style='font-size: 12px; color: #777777; margin: 0 0 10px 0;'>
                                                            &copy; Liberi Software. Todos los derechos reservados.
                                                        </p>
                                                        <p style='font-size: 12px; color: #777777; margin: 0;'>
                                                            <a href='https://fichabit.es' target='_blank' style='color: #48BA49; text-decoration: none;'>Acceder</a> 
                                                        </p>
                                                    </td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                </body>
                                </html>";
                $mail->Subject = "Fichabit Gestion Laboral";
            }

            $mail->AddAddress($r[$i]["para"]);


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
                echo "Correo " . $i . " enviado info@cronosapp.es\n\r";
            }
        } catch (Exception $e) {
            $c->query("call robot_modificar_estado(" . $r[$i]['id'] . ",0);");
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
        usleep(2000000);
    }
    $c->close();
}

