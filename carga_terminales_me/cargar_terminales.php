<?php

/***************************************************/
/* Carga de terminales me
/* 28/12/2024
/***************************************************/
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/PHPExcel.php');
require_once(getcwd() . "/clases/util.php");

$logFile = 'log.txt';

function logMessage($message)
{
    global $logFile;
    file_put_contents($logFile, $message . PHP_EOL, FILE_APPEND);
}

$c = new Conexion();
$c->conectar();

$version = 0;
$versionAtulado = 0;
$archivo = "terminales.xlsx";

if (!file_exists($archivo)) {
    echo "ERROR: Archivo de carga no encontrado";
    return;
}


$objReader = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($archivo));
$objReader->setReadDataOnly(true);
$objPHPExcel = $objReader->load($archivo);
$objWorksheet = $objPHPExcel->setActiveSheetIndexByName("Fidelización");

$lTipo = Utilidades::letraANumero(("B"));
$lMarca = Utilidades::letraANumero(("G"));
$lModelo = Utilidades::letraANumero(("D"));
$lSap = Utilidades::letraANumero(("E"));
$lPagoInicial = Utilidades::letraANumero(("I"));
$lPagoMensual = Utilidades::letraANumero(("J"));
$lPagoUnico = Utilidades::letraANumero(("K"));

$datos = [];

echo "Creando conjunto de datos JSON\n";
$contador = 32; // Fila de inicio
foreach ($objWorksheet->getRowIterator() as $row) {
    $tipo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lTipo, $contador)->getValue();
    if ($tipo == "General") {
        $marca = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lMarca, $contador)->getValue();
        $modelo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lModelo, $contador)->getValue();
        $sap = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lSap, $contador)->getValue();
        $pagoInicial = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lPagoInicial, $contador)->getValue();
        $pagoMensual = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lPagoMensual, $contador)->getValue();
        $pagoUnico = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($lPagoUnico, $contador)->getValue();

        $marca = mb_decode_mimeheader(addslashes(trim($marca)));
        $modelo = mb_decode_mimeheader(addslashes(trim($modelo)));
        if (!is_numeric($pagoInicial)) $pagoInicial = 0;
        if (!is_numeric($pagoMensual)) $pagoMensual = 0;
        if (!is_numeric($pagoUnico)) $pagoUnico = 0;

        if (strlen($marca) == 0 || is_null($marca)) break;

        array_push($datos, [
            "marca" => $marca,
            "modelo" => $modelo,
            "sap" => $sap,
            "pago_unico" => $pagoUnico,
            "pago_inicial" => $pagoInicial,
            "pago_mensual" => $pagoMensual
        ]);
    }
    $contador++;
}

$r = $c->query("call distribuidores.terminales_me_guardar('" . json_encode($datos) . "');");
$retorno = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : "0";
echo ($retorno == 1) ? "Carga correcta\n" : "Error en la carga\n";
$c->close();

echo "FIN\r\n";
