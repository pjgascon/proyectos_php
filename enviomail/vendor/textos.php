<?php
class Textos
{
    public static function textoMailMaat($mensaje): string
    {
        $texto = "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>MaatCRM | Correo Corporativo</title>
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
                            <td align='center' style='background-color: #48BA49; padding: 20px 0; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                    <tr>
                                        <td align='center' class='header-logo' style='padding: 0 20px;'>
                                            <a href='https://www.maatcrm.es' target='_blank' style='text-decoration: none;'>
                                                <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>MaatCRM</h1>
                                                </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td class='content-area' style='padding: 30px 40px; color: #333333;'>
                                <p style='font-size: 16px; line-height: 1.6;'>
                                    {$mensaje}
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td align='center' style='background-color: #f2f2f2; padding: 20px 40px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;'>
                                <p style='font-size: 12px; color: #777777; margin: 0 0 10px 0;'>
                                    &copy; MaatCRM. Todos los derechos reservados.
                                </p>
                                <p style='font-size: 12px; color: #777777; margin: 0;'>
                                    <a href='https://maatcrm.es' target='_blank' style='color: #48BA49; text-decoration: none;'>Acceder</a> 
                                </p>

                                <p style='font-size: 11px; color: #999999; margin: 15px 0 0 0; line-height: 1.5;'>
                                Liberi Software S.L.
                                </p>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>

        </body>
        </html>";
        return $texto;
    }

    public static function textoMailB2B($mensaje): string
    {
        $texto = "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Beetobee</title>
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
                            <td align='center' style='background-color: #FFD735; padding: 20px 0; border-top-left-radius: 8px; border-top-right-radius: 8px;'>
                                <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                    <tr>
                                        <td align='center' class='header-logo' style='padding: 0 20px;'>
                                            <a href='https://grupobeetobee.com' target='_blank' style='text-decoration: none;'>
                                                <h1 style='color: #000000; margin: 0; font-size: 24px;'>Beetobee</h1>
                                                </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td class='content-area' style='padding: 30px 40px; color: #333333;'>
                                <p style='font-size: 16px; line-height: 1.6;'>
                                   {$mensaje}
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td align='center' style='background-color: #f2f2f2; padding: 20px 40px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;'>
                                <p style='font-size: 12px; color: #777777; margin: 0 0 10px 0;'>
                                    &copy; Beetobee. Todos los derechos reservados.
                                </p>
                                <p style='font-size: 12px; color: #777777; margin: 0;'>
                                    <a href='https://grupobeetobee.com' target='_blank' style='color: #000000; text-decoration: none;'>Acceder</a> 
                                </p>

                                <p style='font-size: 11px; color: #999999; margin: 15px 0 0 0; line-height: 1.5;'>
                                Liberi Software S.L.
                                </p>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
        </table>

        </body>
        </html>";
        return $texto;
    }

    public static function configuracionMaat(&$mail): void
    {
        $mail->IsSMTP();

        $mail->CharSet = "UTF-8";
        $mail->Host = "smtp.serviciodecorreo.es";
        $mail->SMTPDebug = 0;
        $mail->Port = 587; //465 or 587

        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->IsHTML(true);

        $mail->setFrom("info@maatcrm.es", 'Comunicaciones MAAT Crm');

        //Authentication
        $mail->Username = "info@maatcrm.es";
        $mail->Password = "Coral18262202";
    }

    public static function configuracionB2B(&$mail): void
    {
        $mail->IsSMTP();

        $mail->CharSet = "UTF-8";
        $mail->Host = "smtp.strato.com";
        $mail->SMTPDebug = 0;
        $mail->Port = 587; //465 or 587

        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->IsHTML(true);

        $mail->setFrom("comunicaciones@grupobeetobee.com", 'Comunicaciones MAAT Crm');

        //Authentication
        $mail->Username = "comunicaciones@grupobeetobee.com";
        $mail->Password = "QQ@diV2mPBjQdFE";
    }
}
