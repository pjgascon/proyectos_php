<?php

$debug = true;

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
require_once('informe_activaciones_aux.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$objInforme = new InformeActivaciones();
$spreadsheet = IOFactory::load('plantilla.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$operadores = ['N/A', 'MOVISTAR', 'VODAFONE', 'ORANGE', 'YOIGO', 'EUSKALTEL', 'AIRENETWORK', 'ALTECOM', 'LEMONVIL', 'TELECABLE', 'LLAMAYA_GMM', 'SIMYO', 'PEPEPHONE', 'R CABLE', 'DIGI SPAIN TELECOM', 'LYCAMOBILE', 'YOUMOBILE', 'QUATRE'];

$c = new Conexion();
$iLinea = 2;

$c->conectar();
$r = $c->query("call maat.informe_activaciones_envio_fi();");
$datos = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC) : null;

if (!is_null($datos)) {
    $arrClientes = $objInforme->listadoDeClientes($datos);
    if (count($arrClientes) > 0) {
        for ($n = 0; $n < count($arrClientes); $n++) {
            $bPintarCabecera = true;
            for ($i = 0; $i < count($datos); $i++) {
                if ($datos[$i]["cif"] == $arrClientes[$n]) {
                    if ($bPintarCabecera) {
                        if ($n > 0) {
                            $objInforme->clonarCabecera($sheet, 1, $iLinea);
                            $iLinea++;
                        }
                        $cliente = (!Utilidades::esUTF($datos[$i]['cliente'])) ? Utilidades::codificarUTF($datos[$i]['cliente']) : $datos[$i]['cliente'];

                        $sheet->setCellValue('A' . $iLinea, $cliente);
                        $sheet->setCellValue('B' . $iLinea, $datos[$i]["cif"]);
                        $sheet->setCellValue('C' . $iLinea, $datos[$i]["telefono_cliente"]);
                        $iLinea += 3;
                        $bPintarCabecera = false;
                    }

                    $objInforme->obtenerDesplegableServicios($sheet, $iLinea);
                    $objInforme->obtenerDesplegableOperadorDonante($sheet, $iLinea);

                    $dirInstalacion = ($datos[$i]['direccion_instalacion'] == " ()") ? "" : $datos[$i]['direccion_instalacion'];
                    $dirInstalacion = str_replace("()", " ", $dirInstalacion);

                    $direccion = (!Utilidades::esUTF($datos[$i]['direccion_cliente'])) ? Utilidades::codificarUTF($datos[$i]['direccion_cliente']) : $datos[$i]['direccion_cliente'];
                    $dirInstalacion = (!Utilidades::esUTF($dirInstalacion)) ? Utilidades::codificarUTF($dirInstalacion) : $dirInstalacion;

                    // TIPO
                    if ($datos[$i]["tipo"] == "HOME MOBILE") {
                        if ($datos[$i]["portabilidad"] == 1) {
                            $sheet->setCellValue('A' . $iLinea, 'PORTABILIDAD MOVIL');
                        } else {
                            $sheet->setCellValue('A' . $iLinea, 'ALTA NUEVA MOVIL');
                        }
                    } elseif ($datos[$i]["tipo"] == "HOME CONNECT") {
                        $sheet->setCellValue('A' . $iLinea, 'FIBRA');
                        if (strlen($dirInstalacion) == 0) $dirInstalacion = $direccion;
                    } elseif ($datos[$i]["tipo"] == "EQUIPOS Y TERMINALES") {
                        $sheet->setCellValue('A' . $iLinea, 'TERMINAL');
                    } else {
                        $sheet->setCellValue('A' . $iLinea, '');
                    }

                    // NUMERACION
                    $sheet->setCellValue('B' . $iLinea, $datos[$i]["numeracion"]);

                    // OPERADOR DONANTE
                    if (in_array(strtoupper($datos[$i]["op_donante"]), $operadores)) {
                        $sheet->setCellValue('C' . $iLinea, strtoupper($datos[$i]["op_donante"]));
                    } else {
                        $sheet->setCellValue('C' . $iLinea, "N/A");
                    }

                    // DIRECCIÓN DE INSTALACIÓN
                    $sheet->setCellValue('E' . $iLinea, $dirInstalacion);

                    // DIRECCIÓN DEL CLIENTE
                    $sheet->setCellValue('F' . $iLinea, $direccion);

                    // TARIFA
                    $sheet->setCellValue('G' . $iLinea, $datos[$i]["tarifa"]);

                    $iLinea++;
                }
            }
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);

        // Guardar el archivo
        $writer = new Xlsx($spreadsheet);
        $writer->save('informe_activaciones.xlsx');

        echo "Archivo creado con éxito";

        if (file_exists("informe_activaciones.xlsx")) {
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            try {

                $dia = date("d/m/Y");
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
                //$mail->AddAddress("tramitaciones@lynks-tic.com");
                $mail->AddAddress("pedrojose.gascon@gmail.com");
                $mail->addBCC('pedrojose.gascon@liberi.es');
                $mail->Subject = "Ofertas para activar de Vertigoo";
                $mail->Body = "Adjuntamos excel con las ofertas firmadas el día {$dia}, para activar en plataforma.<br />Un saludo <b>Departamento de Tramitación de Vertigoo.</b>";
                $mail->addAttachment("informe_activaciones.xlsx");

                if (!$mail->Send()) {
                    echo "\n\nError al enviar el correo";
                } else {
                    echo "\n\nCorreo enviado\n";
                    unlink("informe_activaciones.xlsx");
                }
            } catch (Exception $e) {
                echo "Error en el envio de correo " . $e;
            }
        }
    }
}
