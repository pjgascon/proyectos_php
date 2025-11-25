<?php
// error_reporting(E_ALL);
ini_set('display_errors', '0');
// ini_set('error_reporting', E_ALL);

require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/util.php');
require_once(getcwd() . '/vendor/PhpSpreadsheet/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

// Indico la versión
$version = 6;

// Defino las columnas
$marca = 'A';
$modelo = 'B';
$precio_cesion = 'C';
$pago_unico = 'D';
$pagoInicial_ep = 'E';
$cuota_ep = 'F';
$pagoInicial_np = 'G';
$cuota_np = 'H';
$pagoInicial_vp = 'I';
$cuota_vp = 'J';
$pagoInicial_pp = 'K';
$cuota_pp = 'L';

try {
    $c = new Conexion();
    $c->conectar();
    $arrDatos = array();

    // Mantengo el nombre de las columnas y comienzo en la fila 1
    $filaInicio = 2;

    $spreadsheet = IOFactory::load(getcwd() . '/archivos_carga/subvencion_love_iew.xlsx');
    $sheet = $spreadsheet->getActiveSheet();
    $nFilas = $sheet->getHighestRow();

    for ($i = $filaInicio; $i <= $nFilas; $i++) {
        $marcaValue = $sheet->getCell($marca . $i)->getValue();
        $modeloValue = $sheet->getCell($modelo . $i)->getValue();
        $precioCesionValue = $sheet->getCell($precio_cesion . $i)->getValue();
        $pagoUnicoValue = $sheet->getCell($pago_unico . $i)->getValue();
        $pagoInicial_epValue = $sheet->getCell($pagoInicial_ep . $i)->getValue();
        $cuota_epValue = $sheet->getCell($cuota_ep . $i)->getValue();
        $pagoInicial_npValue = $sheet->getCell($pagoInicial_np . $i)->getValue();
        $cuotaNpValue = $sheet->getCell($cuota_np . $i)->getValue();
        $pagoInicial_vpValue = $sheet->getCell($pagoInicial_vp . $i)->getValue();
        $cuotaVpValue = $sheet->getCell($cuota_vp . $i)->getValue();
        $pagoInicialPpValue = $sheet->getCell($pagoInicial_pp . $i)->getValue();
        $cuotaPpValue = $sheet->getCell($cuota_pp . $i)->getValue();

        if (!is_numeric(strval($precioCesionValue))) $precioCesionValue = 0;
        if (!is_numeric(strval($pagoUnicoValue))) $pagoUnicoValue = 0;
        if (!is_numeric(strval($pagoInicial_epValue))) $pagoInicial_epValue = 0;
        if (!is_numeric(strval($cuota_epValue))) $cuota_epValue = 0;
        if (!is_numeric(strval($pagoInicial_npValue))) $pagoInicial_npValue = 0;
        if (!is_numeric(strval($cuotaNpValue))) $cuotaNpValue = 0;
        if (!is_numeric(strval($pagoInicial_vpValue))) $pagoInicial_vpValue = 0;
        if (!is_numeric(strval($cuotaVpValue))) $cuotaVpValue = 0;
        if (!is_numeric(strval($cuotaVpValue))) $cuotaVpValue = 0;
        if (!is_numeric(strval($pagoInicialPpValue))) $pagoInicialPpValue = 0;
        if (!is_numeric(strval($cuotaPpValue))) $cuotaPpValue = 0;

        $importeEP = $pagoInicial_epValue + $cuota_epValue;
        $importeNP = $pagoInicial_npValue + $cuotaNpValue;
        $importeVP = $pagoInicial_vpValue + $cuotaVpValue;
        $importePP = $pagoInicialPpValue + $cuotaPpValue;

        $modeloValue = trim(addslashes(str_replace($marcaValue, "", $modeloValue)));
        $marcaValue = addslashes($marcaValue);

        if (strlen($marcaValue) > 0) {
            array_push($arrDatos, array(
                'version' => $version,
                'marca' => $marcaValue,
                'modelo' => $modeloValue,
                'precio_cesion' => $precioCesionValue,
                'pago_unico' => $pagoUnicoValue,
                'importe' => $importeEP,
                'categoria' => "ENTRADA PRO"
            ));

            array_push($arrDatos, array(
                'version' => $version,
                'marca' => $marcaValue,
                'modelo' => $modeloValue,
                'precio_cesion' => $precioCesionValue,
                'pago_unico' => $pagoUnicoValue,
                'importe' => $importeNP,
                'categoria' => "NORMAL PRO"
            ));

            array_push($arrDatos, array(
                'version' => $version,
                'marca' => $marcaValue,
                'modelo' => $modeloValue,
                'precio_cesion' => $precioCesionValue,
                'pago_unico' => $pagoUnicoValue,
                'importe' => $importeVP,
                'categoria' => "VALOR PRO"
            ));

            array_push($arrDatos, array(
                'version' => $version,
                'marca' => $marcaValue,
                'modelo' => $modeloValue,
                'precio_cesion' => $precioCesionValue,
                'pago_unico' => $pagoUnicoValue,
                'importe' => $importePP,
                'categoria' => "PREMIUM PRO"
            ));
        } else {
            break;
        }
    }
} catch (Exception $e) {
    echo 'Error al cargar el archivo": ' . $e->getMessage() . PHP_EOL;
    exit;
}

if (count($arrDatos) > 0) {
    $r = $c->query("call orange.or_terminales_love_iew_guardar('" . json_encode($arrDatos, JSON_UNESCAPED_UNICODE) . "');");
} else {
    echo 'No se encontraron datos para cargar' . PHP_EOL;
    exit;
}

echo "Carga finalizada version: {$version}" . PHP_EOL;
