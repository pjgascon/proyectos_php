<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);


require_once(getcwd() . '/vendor/Exception.php');
require_once(getcwd() . '/vendor/PHPMailer.php');
require_once(getcwd() . '/vendor/SMTP.php');
require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/PHPExcel.php');

$procesarCorreo = false;
$c = new Conexion();
$c->conectar();

$r = $c->query("call robot_permanencias();");
if ($r->num_rows > 0) {
    $r = $r->fetch_all(MYSQLI_ASSOC);

    $templatePath = getcwd() . '/plantillas/plantilla_campania_comercial.xlsx';

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Waspapp Solutions")
        ->setLastModifiedBy("Waspapp Solutions")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("RAG")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("");

    $objPHPExcel = PHPExcel_IOFactory::load($templatePath);

    $row = 2;
    for ($i = 0; $i < count($r); $i++) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $r[$i]["cif"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $r[$i]["nombre"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $r[$i]["direccion"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $r[$i]["poblacion"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $r[$i]["provincia"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $r[$i]["contacto"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $r[$i]["mail"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $r[$i]["telefono"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(30, $row, $r[$i]["nif_comercial"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(32, $row, $r[$i]["n_lineas_sin_permanencia"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(33, $row, $r[$i]["n_lineas_con_permanencia"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(34, $row, $r[$i]["n_lineas"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(35, $row, $r[$i]["inicio"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(38, $row, $r[$i]["fin"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(41, $row, $r[$i]["importe_penalizacion"]);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(42, $row, $r[$i]["observaciones"]);
        $row++;
    }

    $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("M")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("O")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("P")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("Q")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("R")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("S")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("T")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("U")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("V")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("W")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("X")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("Y")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("Z")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AB")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AD")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AE")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AF")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AG")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AH")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AI")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AJ")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AK")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AL")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AM")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AN")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AO")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AP")->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension("AQ")->setAutoSize(true);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="permanencias.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('permanencias.xlsx');

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
        $mail->AddAddress("manuel.beltran@waspapp.es");
        // $mail->AddAddress("pedrojose.gascon@liberi.es");
        $mail->addCC("pedrojose.gascon@waspapp.es");
        $mail->addCC("david.ruiz@waspapp.es");
        $mail->Subject = "Permanencias";
        $mail->Body = "Envio de permanencias";
        $mail->addAttachment(getcwd() . "/permanencias.xlsx");

        if (!$mail->Send()) {
            echo "Error al enviar el correo\n";
        } else {
            echo "Correo enviado\n";
            unlink("permanencias.xlsx");
        }
    } catch (Exception $e) {
        echo "Error al enviar el correo";
    }
} else {
    $c->close();
    echo "Nada que hacer";
    return;
}
