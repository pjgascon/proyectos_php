<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

require_once("otp.php");

// $hostname = '{imap.ionos.es:993/notls}INBOX';
// $hostname = '{imap.ionos.es:993/ssl}INBOX';
// $username = 'info@waspapp.net';
// $password = 'Coral1826illaph';
$hostname = '{imap.strato.com:993/ssl}INBOX';
$username = 'firma.digital@lynks-tic.es';
$password = '!e6FJ422nQ#3g!3';
// $hostname = '{outlook.office365.com:993/ssl}INBOX';
// $username = 'firma.digital@lynks-tic.com';
// $password = 'Dub93312';

$filename = "";
$asunto = "";
$idMensaje = null;

$inbox = imap_open($hostname, $username, $password) or die('Ha fallado la conexión: ' . imap_last_error());

$emails = imap_search($inbox, 'ALL');

if ($emails) {
    foreach ($emails as $email_number) {
        $idMensaje = $email_number;
        $overview = imap_fetch_overview($inbox, $email_number, 0);
        $structure = imap_fetchstructure($inbox, $email_number);
        $header = imap_headerinfo($inbox, $email_number);

        $attachments = array();
        if (isset($structure->parts) && count($structure->parts)) {
            for ($i = 0; $i < count($structure->parts); $i++) {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );
                if ($structure->parts[$i]->ifdparameters) {
                    foreach ($structure->parts[$i]->dparameters as $object) {
                        if (strtolower($object->attribute) == 'filename') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }

                if ($structure->parts[$i]->ifparameters) {
                    foreach ($structure->parts[$i]->parameters as $object) {
                        if (strtolower($object->attribute) == 'name') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }

                if ($attachments[$i]['is_attachment']) {
                    $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i + 1);

                    /* 4 = QUOTED-PRINTABLE encoding */
                    if ($structure->parts[$i]->encoding == 3) {
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                    /* 3 = BASE64 encoding */ elseif ($structure->parts[$i]->encoding == 4) {
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }

                foreach ($attachments as $attachment) {
                    if ($attachment['is_attachment'] == 1) {
                        echo $attachment['filename'];
                        $filename = $attachment['name'];
                        if (empty($filename)) $filename = $attachment['filename'];

                        if (empty($filename)) $filename = time() . ".dat";

                        /* prefix the email number to the filename in case two emails
                 * have the attachment with the same file name.
                 */
                        $fp = fopen($filename, "w+");
                        fwrite($fp, $attachment['attachment']);
                        fclose($fp);
                    }
                }
            }
        }

        if (strlen($filename) > 0) $asunto = mb_decode_mimeheader($header->subject);
     
        if (strpos($asunto, "Contratos para firma") !== false)
            break;
    }
}

if (strlen($filename) > 0 && strlen($asunto) > 0) {
    $otp = new OTP();
    $b = $otp->obtenerCodigo($asunto);
    if ($b) {
        $subirArchivo = $otp->subirContrato($filename, $otp->getCrm(), $otp->getOportunidad());
        if ($subirArchivo) {
            //Compruebo que existe
            $verificado = ($otp->verificarSubida($filename)) ? true : false;
            if ($verificado) {
                $actualizadoEstado = $otp->actualizarEstado($otp->getCrm(), $otp->getOportunidad(), $filename);
                if ($actualizadoEstado) {
                    sleep(5);
                    //Borro el contrato descargado
                    //unlink($filename);
                    //Borro el correo
                    if (!is_null($idMensaje)) {
                        imap_delete($inbox, $idMensaje);
                        imap_expunge($inbox);
                    }
                    echo "Finalizado";
                } else {
                    echo "Error al actualizar el estado";
                }
            } else {
                echo "Error al validar la subida del archivo";
            }
        } else {
            echo "Error al subir el archivo";
        }
    }
    imap_close($inbox);
}
