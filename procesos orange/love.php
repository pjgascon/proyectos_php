<?php
// error_reporting(E_ALL);
ini_set('display_errors', '0');
// ini_set('error_reporting', E_ALL);

require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/util.php');
require_once(getcwd() . '/vendor/PhpSpreadsheet/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PSpell\Config;

// Indico la versión
$version = 5;

// Defino las columnas
// $Pi = Pago inicial
// $Sl = 'Solo movil'
// $Cm = Cuota mes
// $Co = Convergente
$marca = 'A';
$modelo = 'B';
$precio_cesion = 'C';
$pago_unico = 'D';
$gama = 'E';
$Pi_Sl_ep = 'F';
$Cm_Sl_ep = 'G';
$Pi_Sl_vp = 'H';
$Cm_Sl_vp = 'I';
$Pi_Sl_pp = 'J';
$Cm_Sl_pp = 'K';
$Pi_Co_ep = 'L';
$Cm_Co_ep = 'M';
$Pi_Co_np = 'N';
$Cm_Co_np = 'O';
$Pi_Co_vp = 'P';
$Cm_Co_vp = 'Q';
$Pi_Co_pp = 'R';
$Cm_Co_pp = 'S';
$Pi_Co_tp = 'T';
$Cm_Co_tp = 'U';
$dxcPrecioSoloMovilEp = 'V';
$dxcVapSoloMovilEp = 'W';
$dxcPrecioSoloMovilVp = 'X';
$dxcVapSoloMovilVp = 'Y';
$dxcPrecioSoloMovilPp = 'Z';
$dxcVapSoloMovilPp = 'AA';
$dxcPrecioConvergenteEp = 'AB';
$dxcVapConvergenteEp = 'AC';
$dxcPrecioConvergenteNp = 'AD';
$dxcVapConvergenteNp = 'AE';
$dxcPrecioConvergenteVp = 'AF';
$dxcVapConvergenteVp = 'AG';
$dxcPrecioConvergentePp = 'AH';
$dxcVapConvergentePp = 'AI';
$dxcPrecioConvergenteTp = 'AJ';
$dxcVapConvergenteTp = 'AK';

try {
    $arrDatos = array();
    $c = new Conexion();
    $c->conectar();

    // Mantengo el nombre de las columnas y comienzo en la fila 1
    $filaInicio = 3;

    $spreadsheet = IOFactory::load(getcwd() . '/archivos_carga/subvencion_love_vap.xlsx');
    $sheet = $spreadsheet->getActiveSheet();
    $nFilas = $sheet->getHighestRow();

    for ($i = $filaInicio; $i <= $nFilas; $i++) {
        $marcaValue = $sheet->getCell($marca . $i)->getValue();
        $modeloValue = $sheet->getCell($modelo . $i)->getValue();
        $precioCesionValue = $sheet->getCell($pago_unico . $i)->getValue();
        $pagoUnicoValue = $sheet->getCell($precio_cesion . $i)->getValue();
        $gamaValue = $sheet->getCell($gama . $i)->getValue();
        $Pi_Sl_epValue = $sheet->getCell($Pi_Sl_ep . $i)->getValue();
        $Cm_Sl_epValue = $sheet->getCell($Cm_Sl_ep . $i)->getValue();
        $Pi_Sl_vpValue = $sheet->getCell($Pi_Sl_vp . $i)->getValue();
        $Cm_Sl_vpValue = $sheet->getCell($Cm_Sl_vp . $i)->getValue();
        $Pi_Sl_ppValue = $sheet->getCell($Pi_Sl_pp . $i)->getValue();
        $Cm_Sl_ppValue = $sheet->getCell($Cm_Sl_pp . $i)->getValue();
        $Pi_Co_epValue = $sheet->getCell($Pi_Co_ep . $i)->getValue();
        $Cm_Co_epValue = $sheet->getCell($Cm_Co_ep . $i)->getValue();
        $Pi_Co_npValue = $sheet->getCell($Pi_Co_np . $i)->getValue();
        $Cm_Co_npValue = $sheet->getCell($Cm_Co_np . $i)->getValue();
        $Pi_Co_vpValue = $sheet->getCell($Pi_Co_vp . $i)->getValue();
        $Cm_Co_vpValue = $sheet->getCell($Cm_Co_vp . $i)->getValue();
        $Pi_Co_ppValue = $sheet->getCell($Pi_Co_pp . $i)->getValue();
        $Cm_Co_ppValue = $sheet->getCell($Cm_Co_pp . $i)->getValue();
        $Pi_Co_tpValue = $sheet->getCell($Pi_Co_tp . $i)->getValue();
        $Cm_Co_tpValue = $sheet->getCell($Cm_Co_tp . $i)->getValue();
        $dxcPrecioSoloMovilEpValue = $sheet->getCell($dxcPrecioSoloMovilEp . $i)->getValue();
        $dxcVapSoloMovilEpValue = $sheet->getCell($dxcVapSoloMovilEp . $i)->getValue();
        $dxcPrecioSoloMovilVpValue = $sheet->getCell($dxcPrecioSoloMovilVp . $i)->getValue();
        $dxcVapSoloMovilVpValue = $sheet->getCell($dxcVapSoloMovilVp . $i)->getValue();
        $dxcPrecioSoloMovilPpValue = $sheet->getCell($dxcPrecioSoloMovilPp . $i)->getValue();
        $dxcVapSoloMovilPpValue = $sheet->getCell($dxcVapSoloMovilPp . $i)->getValue();
        $dxcPrecioConvergenteEpValue = $sheet->getCell($dxcPrecioConvergenteEp . $i)->getValue();
        $dxcVapConvergenteEpValue = $sheet->getCell($dxcVapConvergenteEp . $i)->getValue();
        $dxcPrecioConvergenteNpValue = $sheet->getCell($dxcPrecioConvergenteNp . $i)->getValue();
        $dxcVapConvergenteNpValue = $sheet->getCell($dxcVapConvergenteNp . $i)->getValue();
        $dxcPrecioConvergenteVpValue = $sheet->getCell($dxcPrecioConvergenteVp . $i)->getValue();
        $dxcVapConvergenteVpValue = $sheet->getCell($dxcVapConvergenteVp . $i)->getValue();
        $dxcPrecioConvergentePpValue = $sheet->getCell($dxcPrecioConvergentePp . $i)->getValue();
        $dxcVapConvergentePpValue = $sheet->getCell($dxcVapConvergentePp . $i)->getValue();
        $dxcPrecioConvergenteTpValue = $sheet->getCell($dxcPrecioConvergenteTp . $i)->getValue();
        $dxcVapConvergenteTpValue = $sheet->getCell($dxcVapConvergenteTp . $i)->getValue();


        if (!is_numeric(strval($precioCesionValue))) $precioCesionValue = -1;
        if (!is_numeric(strval($pagoUnicoValue))) $pagoUnicoValue = -1;
        if (!is_numeric(strval($Pi_Sl_epValue))) $Pi_Sl_epValue = -1;
        if (!is_numeric(strval($Cm_Sl_epValue))) $Cm_Sl_epValue = -1;
        if (!is_numeric(strval($Pi_Sl_vpValue))) $Pi_Sl_vpValue = -1;
        if (!is_numeric(strval($Cm_Sl_vpValue))) $Cm_Sl_vpValue = -1;
        if (!is_numeric(strval($Pi_Sl_ppValue))) $Pi_Sl_ppValue = -1;
        if (!is_numeric(strval($Cm_Sl_ppValue))) $Cm_Sl_ppValue = -1;
        if (!is_numeric(strval($Pi_Co_epValue))) $Pi_Co_epValue = -1;
        if (!is_numeric(strval($Cm_Co_epValue))) $Cm_Co_epValue = -1;
        if (!is_numeric(strval($Pi_Co_npValue))) $Pi_Co_npValue = -1;
        if (!is_numeric(strval($Cm_Co_npValue))) $Cm_Co_npValue = -1;
        if (!is_numeric(strval($Pi_Co_vpValue))) $Pi_Co_vpValue = -1;
        if (!is_numeric(strval($Cm_Co_vpValue))) $Cm_Co_vpValue = -1;
        if (!is_numeric(strval($Pi_Co_ppValue))) $Pi_Co_ppValue = -1;
        if (!is_numeric(strval($Cm_Co_ppValue))) $Cm_Co_ppValue = -1;
        if (!is_numeric(strval($Pi_Co_tpValue))) $Pi_Co_tpValue = -1;
        if (!is_numeric(strval($Cm_Co_tpValue))) $Cm_Co_tpValue = -1;
        if (!is_numeric(strval($dxcPrecioSoloMovilEpValue))) $dxcPrecioSoloMovilEpValue = -1;
        if (!is_numeric(strval($dxcVapSoloMovilEpValue))) $dxcVapSoloMovilEpValue = -1;
        if (!is_numeric(strval($dxcPrecioSoloMovilVpValue))) $dxcPrecioSoloMovilVpValue = -1;
        if (!is_numeric(strval($dxcVapSoloMovilVpValue))) $dxcVapSoloMovilVpValue = -1;
        if (!is_numeric(strval($dxcPrecioSoloMovilPpValue))) $dxcPrecioSoloMovilPpValue = -1;
        if (!is_numeric(strval($dxcVapSoloMovilPpValue))) $dxcVapSoloMovilPpValue = -1;
        if (!is_numeric(strval($dxcPrecioConvergenteEpValue))) $dxcPrecioConvergenteEpValue = -1;
        if (!is_numeric(strval($dxcVapConvergenteEpValue))) $dxcVapConvergenteEpValue = -1;
        if (!is_numeric(strval($dxcPrecioConvergenteNpValue))) $dxcPrecioConvergenteNpValue = -1;
        if (!is_numeric(strval($dxcVapConvergenteNpValue))) $dxcVapConvergenteNpValue = -1;
        if (!is_numeric(strval($dxcPrecioConvergenteVpValue))) $dxcPrecioConvergenteVpValue = -1;
        if (!is_numeric(strval($dxcVapConvergenteVpValue))) $dxcVapConvergenteVpValue = -1;
        if (!is_numeric(strval($dxcPrecioConvergentePpValue))) $dxcPrecioConvergentePpValue = -1;
        if (!is_numeric(strval($dxcVapConvergentePpValue))) $dxcVapConvergentePpValue = -1;
        if (!is_numeric(strval($dxcPrecioConvergenteTpValue))) $dxcPrecioConvergenteTpValue = -1;
        if (!is_numeric(strval($dxcVapConvergenteTpValue))) $dxcVapConvergenteTpValue = -1;


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
                'pago_inicial' => $Pi_Sl_epValue,
                'pago_mensual_solo_movil' => $Cm_Sl_epValue,
                'pago_mensual' => $Cm_Co_epValue,
                'dxcPrecioSoloMovil' => $dxcPrecioSoloMovilEpValue,
                'dxcVAPSolomovil' => $dxcVapSoloMovilEpValue,
                'dxcPrecioConvergencia' => $dxcPrecioConvergenteEpValue,
                'dxcVAPConvergencia' => $dxcVapConvergenteEpValue,
                'categoria' => 'ENTRADA PRO'
            ));

            array_push($arrDatos, array(
                'version' => $version,
                'marca' => $marcaValue,
                'modelo' => $modeloValue,
                'gama' => $gamaValue,
                'precio_cesion' => $precioCesionValue,
                'pago_unico' => $pagoUnicoValue,
                'pago_inicial' => 0,
                'pago_mensual_solo_movil' => 0,
                'pago_mensual' => $Cm_Co_npValue,
                'dxcPrecioSoloMovil' => -1,
                'dxcVAPSolomovil' => -1,
                'dxcPrecioConvergencia' => $dxcPrecioConvergenteNpValue,
                'dxcVAPConvergencia' => $dxcVapConvergenteNpValue,
                'categoria' => 'NORMAL PRO'
            ));

            array_push($arrDatos, array(
                'version' => $version,
                'marca' => $marcaValue,
                'modelo' => $modeloValue,
                'gama' => $gamaValue,
                'precio_cesion' => $precioCesionValue,
                'pago_unico' => $pagoUnicoValue,
                'pago_inicial' => $Pi_Sl_vpValue,
                'pago_mensual_solo_movil' => $Cm_Sl_vpValue,
                'pago_mensual' => $Cm_Co_vpValue,
                'dxcPrecioSoloMovil' => $dxcPrecioSoloMovilVpValue,
                'dxcVAPSolomovil' => $dxcVapSoloMovilVpValue,
                'dxcPrecioConvergencia' => $dxcPrecioConvergenteVpValue,
                'dxcVAPConvergencia' => $dxcVapConvergenteVpValue,
                'categoria' => 'VALOR PRO'
            ));

            array_push($arrDatos, array(
                'version' => $version,
                'marca' => $marcaValue,
                'modelo' => $modeloValue,
                'gama' => $gamaValue,
                'precio_cesion' => $precioCesionValue,
                'pago_unico' => $pagoUnicoValue,
                'pago_inicial' => $Pi_Sl_ppValue,
                'pago_mensual_solo_movil' => $Cm_Sl_ppValue,
                'pago_mensual' => $Cm_Co_ppValue,
                'dxcPrecioSoloMovil' => $dxcPrecioSoloMovilPpValue,
                'dxcVAPSolomovil' => $dxcVapSoloMovilPpValue,
                'dxcPrecioConvergencia' => $dxcPrecioConvergentePpValue,
                'dxcVAPConvergencia' => $dxcVapConvergentePpValue,
                'categoria' => 'PREMIUM PRO'
            ));

            array_push($arrDatos, array(
                'version' => $version,
                'marca' => $marcaValue,
                'modelo' => $modeloValue,
                'gama' => $gamaValue,
                'precio_cesion' => $precioCesionValue,
                'pago_unico' => $pagoUnicoValue,
                'pago_inicial' => 0,
                'pago_mensual_solo_movil' => 0,
                'pago_mensual' => $Cm_Co_tpValue,
                'dxcPrecioSoloMovil' => -1,
                'dxcVAPSolomovil' => -1,
                'dxcPrecioConvergencia' => $dxcPrecioConvergenteTpValue,
                'dxcVAPConvergencia' => $dxcVapConvergenteTpValue,
                'categoria' => 'TOP PRO'
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
    $r = $c->query("call orange.or_terminales_love_guardar('" . json_encode($arrDatos, JSON_UNESCAPED_UNICODE) . "');");
} else {
    echo 'No se encontraron datos para cargar' . PHP_EOL;
    exit;
}
echo "Carga finalizada version: {$version}" . PHP_EOL;
