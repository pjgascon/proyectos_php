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

$version = 4;
$versionAtulado = 4;
$archivo = "Mayo_2025_Pyme_Plus.xls";

if (!file_exists($archivo)) {
    echo "ERROR: Archivo de carga no encontrado";
    return;
}


$objReader = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($archivo));
$objReader->setReadDataOnly(true);
$objPHPExcel = $objReader->load($archivo);
$objWorksheet = $objPHPExcel->setActiveSheetIndexByName("PVP VaP - Pyme");

$lineaMarca = Utilidades::letraANumero(("B"));
$lineaModelo = Utilidades::letraANumero(("C"));
$lineaPrecioCesion = Utilidades::letraANumero(("D"));
$lineaGama = Utilidades::letraANumero(("F"));
$lineaPagoUnico = Utilidades::letraANumero(("H"));
$lineaPagoUnicoEP = Utilidades::letraANumero(("AC")); // Entrada PRO
$lineaCuotaMesEP = Utilidades::letraANumero(("AD"));
$lineaPagoUnicoNP = Utilidades::letraANumero(("AE")); // Normal PRO
$lineaCuotaMesNP = Utilidades::letraANumero(("AF"));
$lineaPagoUnicoVP = Utilidades::letraANumero(("AG")); // Valor PRO
$lineaCuotaMesVP = Utilidades::letraANumero(("AH"));
$lineaPagoUnicoPP = Utilidades::letraANumero(("AB")); // Premium PRO
$lineaCuotaMesPP = Utilidades::letraANumero(("AI"));
$lineaCuotaMesTP = Utilidades::letraANumero(("AK"));
$lineaPagoUnicoTP = Utilidades::letraANumero(("AL")); // TOP PRO
$datos = [];

echo "Creando conjunto de datos JSON\n";
$contador = 25; // Fila de inicio

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
    $pagoUnicoTP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaCuotaMesTP, $contador)->getValue();
    $cuotaMesTP = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lineaPagoUnicoTP, $contador)->getValue();

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
    if (!is_numeric($pagoUnicoTP)) $pagoUnicoTP = 0;
    if (!is_numeric($cuotaMesTP)) $cuotaMesTP = 0;

    $marca = addslashes($marca);
    $modelo = addslashes($modelo);

    if (strlen($marca) == 0 || is_null($marca)) break;


    array_push($datos, [
        "version" => $version,
        "marca" => $marca,
        "modelo" => $modelo,
        "precio_cesion" => $precioCesion,
        "pago_unico" => $pagoUnico,
        "gama" => $gama,
        "pago_inicial_ep" => $pagoUnicoEP,
        "cuota_ep" => $cuotaMesEP,
        "pago_inicial_np" => $pagoUnicoNP,
        "cuota_np" => $cuotaMesNP,
        "pago_inicial_vp" => $pagoUnicoVP,
        "cuota_vp" => $cuotaMesVP,
        "pago_inicial_pp" => $pagoUnicoPP,
        "cuota_pp" => $cuotaMesPP,
        "pago_inicial_tp" => $pagoUnicoTP,
        "cuota_tp" => $cuotaMesTP
    ]);
    $contador++;
}

$r = $c->query("call orange.or_terminales_vap_pyme_cargar('" . json_encode($datos, JSON_UNESCAPED_UNICODE) . "');");
$retorno = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : "0";
echo ($retorno == 1) ? "Carga correcta\n" : "Error en la carga\n";

$c->close();

echo "FIN\r\n";
