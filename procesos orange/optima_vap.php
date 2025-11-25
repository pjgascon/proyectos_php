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
$gama = 'C';
$precio_cesion = 'D';
$pago_unico = 'E';
$pago_inicial_ep = 'F';
$cuota_ep = 'G';
$pago_inicial_np = 'H';
$cuota_np = 'I';
$pago_inicial_vp = 'J';
$cuota_vp = 'K';
$pago_inicial_pp = 'L';
$cuota_pp = 'M';
$pago_inicial_tp = 'N';
$cuota_tp = 'O';

try {
    $c = new Conexion();
    $c->conectar();
    $arrDatos = array();

    // Mantengo el nombre de las columnas y comienzo en la fila 1
    $filaInicio = 2;

    $spreadsheet = IOFactory::load(getcwd() . '/archivos_carga/subvencion_optima_vap.xlsx');
    $sheet = $spreadsheet->getActiveSheet();
    $nFilas = $sheet->getHighestRow();

    for ($i = $filaInicio; $i <= $nFilas; $i++) {
        $marcaValue = $sheet->getCell($marca . $i)->getValue();
        $modeloValue = $sheet->getCell($modelo . $i)->getValue();
        $gamaValue = $sheet->getCell($gama . $i)->getValue();
        $precioCesionValue = $sheet->getCell($precio_cesion . $i)->getValue();
        $pagoUnicoValue = $sheet->getCell($pago_unico . $i)->getValue();
        $pagoInicialEpValue = $sheet->getCell($pago_inicial_ep . $i)->getValue();
        $cuotaEpValue = $sheet->getCell($cuota_ep . $i)->getValue();
        $pagoInicialNpValue = $sheet->getCell($pago_inicial_np . $i)->getValue();
        $cuotaNpValue = $sheet->getCell($cuota_np . $i)->getValue();
        $pagoInicialVpValue = $sheet->getCell($pago_inicial_vp . $i)->getValue();
        $cuotaVpValue = $sheet->getCell($cuota_vp . $i)->getValue();
        $pagoInicialPpValue = $sheet->getCell($pago_inicial_pp . $i)->getValue();
        $cuotaPpValue = $sheet->getCell($cuota_pp . $i)->getValue();
        $pagoInicialTpValue = $sheet->getCell($pago_inicial_tp . $i)->getValue();
        $cuotaTpValue = $sheet->getCell($cuota_tp . $i)->getValue();

        if (!is_numeric(strval($precioCesionValue))) $precioCesionValue = -1;
        if (!is_numeric(strval($pagoUnicoValue))) $pagoUnicoValue = -1;
        if (!is_numeric(strval($pagoInicialEpValue))) $pagoInicialEpValue = -1;
        if (!is_numeric(strval($cuotaEpValue))) $cuotaEpValue = -1;
        if (!is_numeric(strval($pagoInicialNpValue))) $pagoInicialNpValue = -1;
        if (!is_numeric(strval($cuotaNpValue))) $cuotaNpValue = -1;
        if (!is_numeric(strval($pagoInicialVpValue))) $pagoInicialVpValue = -1;
        if (!is_numeric(strval($cuotaVpValue))) $cuotaVpValue = -1;
        if (!is_numeric(strval($pagoInicialPpValue))) $pagoInicialPpValue = -1;
        if (!is_numeric(strval($cuotaPpValue))) $cuotaPpValue = -1;
        if (!is_numeric(strval($pagoInicialTpValue))) $pagoInicialTpValue = -1;
        if (!is_numeric(strval($cuotaTpValue))) $cuotaTpValue = -1;

        $modeloValue = trim(addslashes(str_replace($marcaValue, "", $modeloValue)));
        $marcaValue = addslashes($marcaValue);
        $gamaValue = addslashes($gamaValue);

        if (strlen($marcaValue) > 0) {
            array_push($arrDatos, array(
                'version' => $version,
                'marca' => $marcaValue,
                'modelo' => $modeloValue,
                'gama' => $gamaValue,
                'precio_cesion' => $precioCesionValue,
                'pago_unico' => $pagoUnicoValue,
                'pago_inicial_ep' => $pagoInicialEpValue,
                'cuota_ep' => $cuotaEpValue,
                'pago_inicial_np' => $pagoInicialNpValue,
                'cuota_np' => $cuotaNpValue,
                'pago_inicial_vp' => $pagoInicialVpValue,
                'cuota_vp' => $cuotaVpValue,
                'pago_inicial_pp' => $pagoInicialPpValue,
                'cuota_pp' => $cuotaPpValue,
                'pago_inicial_tp' => $pagoInicialTpValue,
                'cuota_tp' => $cuotaTpValue
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
    $r = $c->query("call orange.or_terminales_vap_pyme_cargar('" . json_encode($arrDatos, JSON_UNESCAPED_UNICODE) . "');");
} else {
    echo 'No se encontraron datos para cargar' . PHP_EOL;
    exit;
}

echo "Carga finalizada version: {$version}" . PHP_EOL;
