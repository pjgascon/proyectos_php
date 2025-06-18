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
$capt_ep = 'E';
$capta_np = 'F';
$capta_vp = 'G';
$capta_pp = 'H';
$porta_ep = 'I';
$porta_np = 'J';
$porta_vp = 'K';
$porta_pp = 'L';

try {
    $c = new Conexion();
    $c->conectar();

    // Mantengo el nombre de las columnas y comienzo en la fila 1
    $filaInicio = 2;

    $spreadsheet = IOFactory::load(getcwd() . '/archivos_carga/subvencion_optima_iew.xlsx');
    $sheet = $spreadsheet->getActiveSheet();
    $nFilas = $sheet->getHighestRow();

    for ($i = $filaInicio; $i <= $nFilas; $i++) {
        $marcaValue = $sheet->getCell($marca . $i)->getValue();
        $modeloValue = $sheet->getCell($modelo . $i)->getValue();
        $gamaValue = $sheet->getCell($gama . $i)->getValue();
        $precioCesionValue = $sheet->getCell($precio_cesion . $i)->getValue();
        $captEpValue = $sheet->getCell($capt_ep . $i)->getValue();
        $captNpValue = $sheet->getCell($capta_np . $i)->getValue();
        $captVpValue = $sheet->getCell($capta_vp . $i)->getValue();
        $captPpValue = $sheet->getCell($capta_pp . $i)->getValue();
        $portaEpValue = $sheet->getCell($porta_ep . $i)->getValue();
        $portaNpValue = $sheet->getCell($porta_np . $i)->getValue();
        $portaVpValue = $sheet->getCell($porta_vp . $i)->getValue();
        $portaPpValue = $sheet->getCell($porta_pp . $i)->getValue();

        if (!is_numeric(strval($captEpValue))) $captEpValue = -1;
        if (!is_numeric(strval($captEpValue))) $captEpValue = -1;
        if (!is_numeric(strval($captNpValue))) $captNpValue = -1;
        if (!is_numeric(strval($captVpValue))) $captVpValue = -1;
        if (!is_numeric(strval($captPpValue))) $captPpValue = -1;
        if (!is_numeric(strval($portaEpValue))) $portaEpValue = -1;
        if (!is_numeric(strval($portaNpValue))) $portaNpValue = -1;
        if (!is_numeric(strval($portaVpValue))) $portaVpValue = -1;
        if (!is_numeric(strval($portaPpValue))) $portaPpValue = -1;

        $modeloValue = trim(addslashes(str_replace($marcaValue, "", $modeloValue)));
        $marcaValue = addslashes($marcaValue);
        $gamaValue = addslashes($gamaValue);

        if (strlen($marcaValue) > 0) {
            $c->next_result();
            $r = $c->query("call orange.or_terminales_iew_guardar({$version},'{$marcaValue}','{$modeloValue}','{$gamaValue}','{$precioCesionValue}',
                        '{$captEpValue}','{$captNpValue}','{$captVpValue}','{$captPpValue}',
                        '{$portaEpValue}','{$portaNpValue}','{$portaVpValue}','{$portaPpValue}');");
        } else {
            break;
        }
    }
} catch (Exception $e) {
    echo 'Error al cargar el archivo": ' . $e->getMessage() . PHP_EOL;
    exit;
}
echo "Carga finalizada version: {$version}" . PHP_EOL;
