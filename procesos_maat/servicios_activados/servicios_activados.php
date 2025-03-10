<?php

$debug = false;

if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('error_reporting', E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/util.php');
require_once(getcwd() . "/vendor/phpspreadsheet/autoload.php");
require_once(getcwd() . '/vendor/phpmailer/Exception.php');
require_once(getcwd() . '/vendor/phpmailer/PHPMailer.php');
require_once(getcwd() . '/vendor/phpmailer/SMTP.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\IOFactory;
// use \PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (file_exists("servicios_activados.xlsx"))
    unlink("servicios_activados.xlsx");

$c = new Conexion();
$c->conectar();

$r = $c->query("call enertel.robot_inf_comisiones();");

$datos = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC) : null;

if (!is_null($datos)) {
    $spread = new Spreadsheet();
    $spread
        ->getProperties()
        ->setCreator("Liberi Software")
        ->setTitle('Informe servicios activados')
        ->setSubject('Excel')
        ->setDescription('Informe servicios activados');

    $sheet = $spread->getActiveSheet();
    $sheet->setTitle("Informe servicios activados");
    $sheet->setCellValueByColumnAndRow(1, 1, "Distribuidor");
    $sheet->setCellValueByColumnAndRow(2, 1, "Comercial");
    $sheet->setCellValueByColumnAndRow(3, 1, "Cliente");
    $sheet->setCellValueByColumnAndRow(4, 1, "CIF");
    $sheet->setCellValueByColumnAndRow(5, 1, "Tarifa/Servicio");
    $sheet->setCellValueByColumnAndRow(6, 1, "Familia");
    $sheet->setCellValueByColumnAndRow(7, 1, "Cantidad");
    $sheet->setCellValueByColumnAndRow(8, 1, "Precio");
    $sheet->setCellValueByColumnAndRow(9, 1, "Precio Dto");
    $sheet->setCellValueByColumnAndRow(10, 1, "Fecha Activacion");
    $sheet->setCellValueByColumnAndRow(11, 1, "Upfront");

    for ($i = 0; $i < count($datos); $i++) {
        $sheet->setCellValueByColumnAndRow(1, $i + 2, $datos[$i]['nombre_distribuidor']);
        $sheet->setCellValueByColumnAndRow(2, $i + 2, $datos[$i]['comercial']);
        $sheet->setCellValueByColumnAndRow(3, $i + 2, $datos[$i]['cliente']);
        $sheet->setCellValueByColumnAndRow(4, $i + 2, $datos[$i]['cif']);
        $sheet->setCellValueByColumnAndRow(5, $i + 2, $datos[$i]['tarifa']);
        $sheet->setCellValueByColumnAndRow(6, $i + 2, $datos[$i]['familia']);
        $sheet->setCellValueByColumnAndRow(7, $i + 2, $datos[$i]['cantidad']);
        $sheet->setCellValueByColumnAndRow(8, $i + 2, number_format($datos[$i]['pvp'], 2));
        $sheet->setCellValueByColumnAndRow(9, $i + 2, number_format($datos[$i]['pvp_dto'], 2));
        $sheet->setCellValueByColumnAndRow(10, $i + 2, Utilidades::fecha2Spain($datos[$i]['fecha_activacion']));
        $sheet->setCellValueByColumnAndRow(11, $i + 2, number_format($datos[$i]['upfront'], 2));
    }

    $writer = new Xlsx($spread);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode("servicios_activados.xlsx") . '"');
    $writer->save("servicios_activados.xlsx");

    if (file_exists("servicios_activados.xlsx")) {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        try {

            $mail->IsSMTP();

            $mail->CharSet = "UTF-8";
            $mail->Host = "smtp.serviciodecorreo.es";
            $mail->SMTPDebug = 0;
            $mail->Port = 587; //465 or 587

            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->IsHTML(true);

            //Authentication
            $mail->Username = "info@maatcrm.es";
            $mail->Password = "Coral18262202";

            //Set Params
            $mail->setFrom("info@maatcrm.es", 'Comunicaciones MAAT Crm');
            $mail->AddAddress("pedrojose.gascon@gmail.com");
            $mail->addBCC('pedrojose.gascon@liberi.es');  
            $mail->Subject = "Informe servicios activados MAAT";
            $mail->Body = "Archivo de servicios activados MAAT";
            $mail->addAttachment("servicios_activados.xlsx");

            if (!$mail->Send()) {
                echo "\n\nError al enviar el correo";
            } else {
                echo "\n\nCorreo enviado";
                unlink("servicios_activados.xlsx");
            }
        } catch (Exception $e) {
            echo "Error en el envio de correo " . $e;
        }
    }
}
