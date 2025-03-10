<?php

/***************************************************/
/* Carga de terminales pyme
/* 28/12/2022
/***************************************************/
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/PHPExcel.php');
require_once(getcwd() . "/clases/util.php");

$c = new Conexion();
$c->conectar();

$version = 0;
$versionAtulado = 0;
$archivo = "terminales.xlsm";

if (!file_exists($archivo)) {
    echo "ERROR: Archivo de carga no encontrado";
    return;
}


$objReader = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($archivo));
$objReader->setReadDataOnly(true);
$objPHPExcel = $objReader->load($archivo);
$objWorksheet = $objPHPExcel->setActiveSheetIndexByName("PVP VaP Auton-McrPyme");

$lineaMarca = Utilidades::letraANumero(("B"));
$lineaModelo = Utilidades::letraANumero(("C"));
$lineaPrecioCesion = Utilidades::letraANumero(("D"));
$lineaGama = Utilidades::letraANumero(("F"));
$lineaPagoUnico = Utilidades::letraANumero(("H"));
$lineaPagoUnicoEP = Utilidades::letraANumero(("V")); // Entrada PRO
$lineaCuotaMesEP = Utilidades::letraANumero(("W"));
$lineaPagoUnicoNP = Utilidades::letraANumero(("X")); // Normal PRO
$lineaCuotaMesNP = Utilidades::letraANumero(("Y"));
$lineaPagoUnicoVP = Utilidades::letraANumero(("Z")); // Valor PRO
$lineaCuotaMesVP = Utilidades::letraANumero(("AA"));
$lineaPagoUnicoPP = Utilidades::letraANumero(("AB")); // Premium PRO
$lineaCuotaMesPP = Utilidades::letraANumero(("AC"));
$dxcEPSoloMovil = Utilidades::letraANumero(("AL"));
$dxcEPSoloMovilVAP = Utilidades::letraANumero(("AM"));
$dxcNPSoloMovil = Utilidades::letraANumero(("AN"));
$dxcNPSoloMovilVAP = Utilidades::letraANumero(("AO"));
$dxcVPSoloMovil = Utilidades::letraANumero(("AP"));
$dxcVPSoloMovilVAP = Utilidades::letraANumero(("AQ"));
$dxcPPSoloMovil = Utilidades::letraANumero(("AR"));
$dxcPPSoloMovilVAP = Utilidades::letraANumero(("AS"));
$dxcEPConvergencia = Utilidades::letraANumero(("AU"));
$dxcEPConvergenciaVAP = Utilidades::letraANumero(("AV"));
$dxcNPConvergencia = Utilidades::letraANumero(("AW"));
$dxcNPConvergenciaVAP = Utilidades::letraANumero(("AX"));
$dxcVPConvergencia = Utilidades::letraANumero(("AY"));
$dxcVPConvergenciaVAP = Utilidades::letraANumero(("AZ"));
$dxcPPConvergencia = Utilidades::letraANumero(("BC"));
$dxcPPConvergenciaVAP = Utilidades::letraANumero(("BD"));
$datos = [];

echo "Creando conjunto de datos JSON\n";
$contador = 29; // Fila de inicio
foreach ($objWorksheet->getRowIterator() as $row) {
    $marca = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaMarca, $contador)->getValue();
    $modelo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaModelo, $contador)->getValue();
    $precioCesion = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaPrecioCesion, $contador)->getValue();
    $gama = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaGama, $contador)->getValue();
    $pagoUnico = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaPagoUnico, $contador)->getValue();
    $pagoUnicoEP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaPagoUnicoEP, $contador)->getValue();
    $cuotaMesEP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaCuotaMesEP, $contador)->getValue();
    $pagoUnicoNP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaPagoUnicoNP, $contador)->getValue();
    $cuotaMesNP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaCuotaMesNP, $contador)->getValue();
    $pagoUnicoVP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaPagoUnicoVP, $contador)->getValue();
    $cuotaMesVP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaCuotaMesVP, $contador)->getValue();
    $pagoUnicoPP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaPagoUnicoPP, $contador)->getValue();
    $cuotaMesPP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaCuotaMesPP, $contador)->getValue();
    $cuotaMesPP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaCuotaMesPP, $contador)->getValue();
    $dxcPrecioSoloMovilEP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcEPSoloMovil, $contador)->getValue();
    $dxcVAPSoloMovilEP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcEPSoloMovilVAP, $contador)->getValue();
    $dxcPrecioSoloMovilNP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcNPSoloMovil, $contador)->getValue();
    $dxcVAPSoloMovilNP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcNPSoloMovilVAP, $contador)->getValue();
    $dxcPrecioSoloMovilVP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcVPSoloMovil, $contador)->getValue();
    $dxcVAPSoloMovilVP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcVPSoloMovilVAP, $contador)->getValue();
    $dxcPrecioSoloMovilPP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcPPSoloMovil, $contador)->getValue();
    $dxcVAPSoloMovilPP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcPPSoloMovilVAP, $contador)->getValue();
    $dxcPrecioConvergenciaEP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcEPConvergencia, $contador)->getValue();
    $dxcVAPConvergenciaEP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcEPConvergenciaVAP, $contador)->getValue();
    $dxcPrecioConvergenciaNP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcNPConvergencia, $contador)->getValue();
    $dxcVAPConvergenciaNP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcNPConvergenciaVAP, $contador)->getValue();
    $dxcPrecioConvergenciaVP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcVPConvergencia, $contador)->getValue();
    $dxcVAPConvergenciaVP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcVPConvergenciaVAP, $contador)->getValue();
    $dxcPrecioConvergenciaPP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcPPConvergencia, $contador)->getValue();
    $dxcVAPConvergenciaPP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dxcPPConvergenciaVAP, $contador)->getValue();

    if (!is_numeric($precioCesion)) $precioCesion = 0;
    if (!is_numeric($pagoUnico)) $pagoUnico = 0;
    if (!is_numeric($pagoUnicoEP)) $pagoUnicoEP = 0;
    if (!is_numeric($cuotaMesEP)) $cuotaMesEP = 0;
    if (!is_numeric($pagoUnicoNP)) $pagoUnicoNP = 0;
    if (!is_numeric($cuotaMesNP)) $cuotaMesNP = 0;
    if (!is_numeric($pagoUnicoVP)) $pagoUnicoVP = 0;
    if (!is_numeric($cuotaMesVP)) $cuotaMesVP = 0;
    if (!is_numeric($pagoUnicoPP)) $pagoUnicoPP = 0;
    if (!is_numeric($cuotaMesPP)) $cuotaMesPP = 0;
    if (!is_numeric($dxcPrecioSoloMovilEP)) $dxcPrecioSoloMovilEP = 0;
    if (!is_numeric($dxcVAPSoloMovilEP)) $dxcVAPSoloMovilEP = 0;
    if (!is_numeric($dxcPrecioSoloMovilNP)) $dxcPrecioSoloMovilNP = 0;
    if (!is_numeric($dxcVAPSoloMovilNP)) $dxcVAPSoloMovilNP = 0;
    if (!is_numeric($dxcPrecioSoloMovilVP)) $dxcPrecioSoloMovilVP = 0;
    if (!is_numeric($dxcVAPSoloMovilVP)) $dxcVAPSoloMovilVP = 0;
    if (!is_numeric($dxcPrecioSoloMovilPP)) $dxcPrecioSoloMovilPP = 0;
    if (!is_numeric($dxcVAPSoloMovilPP)) $dxcVAPSoloMovilPP = 0;
    if (!is_numeric($dxcPrecioConvergenciaEP)) $dxcPrecioConvergenciaEP = 0;
    if (!is_numeric($dxcVAPConvergenciaEP)) $dxcVAPConvergenciaEP = 0;
    if (!is_numeric($dxcPrecioConvergenciaNP)) $dxcPrecioConvergenciaNP = 0;
    if (!is_numeric($dxcVAPConvergenciaNP)) $dxcVAPConvergenciaNP = 0;
    if (!is_numeric($dxcPrecioConvergenciaVP)) $dxcPrecioConvergenciaVP = 0;
    if (!is_numeric($dxcVAPConvergenciaVP)) $dxcVAPConvergenciaVP = 0;
    if (!is_numeric($dxcPrecioConvergenciaPP)) $dxcPrecioConvergenciaPP = 0;
    if (!is_numeric($dxcVAPConvergenciaPP)) $dxcVAPConvergenciaPP = 0;

    $marca = addslashes($marca);
    $modelo = addslashes($modelo);

    if (strlen($marca) == 0 || is_null($marca)) break;

    array_push($datos, [
        "marca" => $marca,
        "modelo" => $modelo,
        "precio_cesion" => $precioCesion,
        "pago_unico" => $pagoUnico,
        "gama" => $gama,
        "pago_inicial" => $pagoUnicoEP,
        "pago_mensual" => $cuotaMesEP,
        "dxcPrecioSoloMovil" => $dxcPrecioSoloMovilEP,
        "dxcVAPSolomovil" => $dxcVAPSoloMovilEP,
        "dxcPrecioConvergencia" => $dxcPrecioConvergenciaEP,
        "dxcVAPConvergencia" => $dxcVAPConvergenciaEP,
        "categoria" => "ENTRADA PRO"
    ]);

    // array_push($datos, [
    //     "marca" => $marca,
    //     "modelo" => $modelo,
    //     "precio_cesion" => $precioCesion,
    //     "pago_unico" => $pagoUnico,
    //     "gama" => $gama,
    //     "pago_inicial" => $pagoUnicoNP,
    //     "pago_mensual" => $cuotaMesNP,
    //     "dxcPrecioSoloMovil" => $dxcPrecioSoloMovilNP,
    //     "dxcVAPSolomovil" => $dxcVAPSoloMovilNP,
    //     "dxcPrecioConvergencia" => $dxcPrecioConvergenciaNP,
    //     "dxcVAPConvergencia" => $dxcVAPConvergenciaNP,
    //     "categoria" => "NORMAL PRO"
    // ]);

    // array_push($datos, [
    //     "marca" => $marca,
    //     "modelo" => $modelo,
    //     "precio_cesion" => $precioCesion,
    //     "pago_unico" => $pagoUnico,
    //     "gama" => $gama,
    //     "pago_inicial" => $pagoUnicoVP,
    //     "pago_mensual" => $cuotaMesVP,
    //     "dxcPrecioSoloMovil" => $dxcPrecioSoloMovilVP,
    //     "dxcVAPSolomovil" => $dxcVAPSoloMovilVP,
    //     "dxcPrecioConvergencia" => $dxcPrecioConvergenciaVP,
    //     "dxcVAPConvergencia" => $dxcVAPConvergenciaVP,
    //     "categoria" => "VALOR PRO"
    // ]);

    // array_push($datos, [
    //     "marca" => $marca,
    //     "modelo" => $modelo,
    //     "precio_cesion" => $precioCesion,
    //     "pago_unico" => $pagoUnico,
    //     "gama" => $gama,
    //     "pago_inicial" => $pagoUnicoPP,
    //     "pago_mensual" => $cuotaMesPP,
    //     "dxcPrecioSoloMovil" => $dxcPrecioSoloMovilPP,
    //     "dxcVAPSolomovil" => $dxcVAPSoloMovilPP,
    //     "dxcPrecioConvergencia" => $dxcPrecioConvergenciaPP,
    //     "dxcVAPConvergencia" => $dxcVAPConvergenciaPP,
    //     "categoria" => "PREMIUM PRO"
    // ]);

    $contador++;
}

// echo "call orange.or_terminales_love_guardar('" . json_encode($datos) . "');";exit;
$contenido = "Este es el contenido del archivo.\n";
file_put_contents("archivo.txt", "call orange.or_terminales_love_guardar('" . json_encode($datos) . "');");

$r = $c->query("call orange.or_terminales_love_guardar('" . json_encode($datos) . "');");
$retorno = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : "0";
echo ($retorno == 1) ? "Carga correcta\n" : "Error en la carga\n";

$c->close();

echo "FIN\r\n";
