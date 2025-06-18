<?php

/***************************************************/
/* Carga de terminales pyme
/* 06/03/2025
/***************************************************/
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

require_once(getcwd()  . '/vendor/PhpSpreadsheet/autoload.php');
require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/util.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

echo "Comienza la carga\n";
echo "Catálogo de terminales Love\n";

$c = new Conexion();
$c->conectar();

$hoja = "PVP VaP Auton-McrPyme";

try {
    $spreadsheet = IOFactory::load(getcwd() . '/love.xlsx');
    $spreadsheet->setActiveSheetIndexByName($hoja);

    $lineaMarca = "B"; //Utilidades::letraANumero(("B"));
    $lineaModelo = "C";
    $lineaPrecioCesion = "D";
    $lineaGama = "F";
    $lineaPagoUnico = "H";
    $lineaPagoUnicoEP = "V"; // Entrada PRO
    $lineaCuotaMesEP = "W";
    $lineaPagoUnicoNP = "X"; // Normal PRO
    $lineaCuotaMesNP = "Y";
    $lineaPagoUnicoVP = "Z"; // Valor PRO
    $lineaCuotaMesVP = "AA";
    $lineaPagoUnicoPP = "AB"; // Premium PRO
    $lineaCuotaMesPP = "AC";
    $dxcEPSoloMovil = "AL";
    $dxcEPSoloMovilVAP = "AM";
    $dxcNPSoloMovil = "AN";
    $dxcNPSoloMovilVAP = "AO";
    $dxcVPSoloMovil = "AP";
    $dxcVPSoloMovilVAP = "AQ";
    $dxcPPSoloMovil = "AR";
    $dxcPPSoloMovilVAP = "AS";
    $dxcEPConvergencia = "AU";
    $dxcEPConvergenciaVAP = "AV";
    $dxcNPConvergencia = "AW";
    $dxcNPConvergenciaVAP = "AX";
    $dxcVPConvergencia = "AY";
    $dxcVPConvergenciaVAP = "AZ";
    $dxcPPConvergencia = "BC";
    $dxcPPConvergenciaVAP = "BD";

    $hojaActiva = $spreadsheet->getActiveSheet();
    $nFilas = $hojaActiva->getHighestRow();
    $datos = [];

    echo "Creando conjunto de datos JSON\n";
    $contador = 29; // Fila de inicio
    for ($i = 0; $i < $nFilas; $i++) {
        $marca = $hojaActiva->getCell($lineaMarca . $contador);
        $modelo = $hojaActiva->getCell($lineaModelo . $contador);
        $precioCesion = $hojaActiva->getCell($lineaPrecioCesion . $contador);
        $gama = $hojaActiva->getCell($lineaGama . $contador);
        $pagoUnico = $hojaActiva->getCell($lineaPagoUnico . $contador)->getValue();
        $pagoUnicoEP = $hojaActiva->getCell($lineaPagoUnicoEP . $contador)->getValue();
        $cuotaMesEP = $hojaActiva->getCell($lineaCuotaMesEP . $contador)->getValue();
        $pagoUnicoNP = $hojaActiva->getCell($lineaPagoUnicoNP . $contador)->getValue();
        $cuotaMesNP = $hojaActiva->getCell($lineaCuotaMesNP . $contador)->getValue();
        $pagoUnicoVP = $hojaActiva->getCell($lineaPagoUnicoVP . $contador)->getValue();
        $cuotaMesVP = $hojaActiva->getCell($lineaCuotaMesVP . $contador)->getValue();
        $pagoUnicoPP = $hojaActiva->getCell($lineaPagoUnicoPP . $contador)->getValue();
        $cuotaMesPP = $hojaActiva->getCell($lineaCuotaMesPP . $contador)->getValue();
        $dxcPrecioSoloMovilEP = $hojaActiva->getCell($dxcEPSoloMovil . $contador)->getValue();
        $dxcVAPSoloMovilEP = $hojaActiva->getCell($dxcEPSoloMovilVAP . $contador)->getValue();
        $dxcPrecioSoloMovilNP = $hojaActiva->getCell($dxcNPSoloMovil . $contador)->getValue();
        $dxcVAPSoloMovilNP = $hojaActiva->getCell($dxcNPSoloMovilVAP . $contador)->getValue();
        $dxcPrecioSoloMovilVP = $hojaActiva->getCell($dxcVPSoloMovil . $contador)->getValue();
        $dxcVAPSoloMovilVP = $hojaActiva->getCell($dxcVPSoloMovilVAP . $contador)->getValue();
        $dxcPrecioSoloMovilPP = $hojaActiva->getCell($dxcPPSoloMovil . $contador)->getValue();
        $dxcVAPSoloMovilPP = $hojaActiva->getCell($dxcPPSoloMovilVAP . $contador)->getValue();
        $dxcPrecioConvergenciaEP = $hojaActiva->getCell($dxcEPConvergencia . $contador)->getValue();
        $dxcVAPConvergenciaEP = $hojaActiva->getCell($dxcEPConvergenciaVAP . $contador)->getValue();
        $dxcPrecioConvergenciaNP = $hojaActiva->getCell($dxcNPConvergencia . $contador)->getValue();
        $dxcVAPConvergenciaNP = $hojaActiva->getCell($dxcNPConvergenciaVAP . $contador)->getValue();
        $dxcPrecioConvergenciaVP = $hojaActiva->getCell($dxcVPConvergencia . $contador)->getValue();
        $dxcVAPConvergenciaVP = $hojaActiva->getCell($dxcVPConvergenciaVAP . $contador)->getValue();
        $dxcPrecioConvergenciaPP = $hojaActiva->getCell($dxcPPConvergencia . $contador)->getValue();
        $dxcVAPConvergenciaPP = $hojaActiva->getCell($dxcPPConvergenciaVAP . $contador)->getValue();

        if (!is_numeric(strval($precioCesion))) $precioCesion = 0;
        if (!is_numeric(strval($pagoUnico))) $pagoUnico = 0;
        if (!is_numeric(strval($pagoUnicoEP))) $pagoUnicoEP = 0;
        if (!is_numeric(strval($cuotaMesEP))) $cuotaMesEP = 0;
        if (!is_numeric(strval($pagoUnicoNP))) $pagoUnicoNP = 0;
        if (!is_numeric(strval($cuotaMesNP))) $cuotaMesNP = 0;
        if (!is_numeric(strval($pagoUnicoVP))) $pagoUnicoVP = 0;
        if (!is_numeric(strval($cuotaMesVP))) $cuotaMesVP = 0;
        if (!is_numeric(strval($pagoUnicoPP))) $pagoUnicoPP = 0;
        if (!is_numeric(strval($cuotaMesPP))) $cuotaMesPP = 0;
        if (!is_numeric(strval($dxcPrecioSoloMovilEP))) $dxcPrecioSoloMovilEP = 0;
        if (!is_numeric(strval($dxcVAPSoloMovilEP))) $dxcVAPSoloMovilEP = 0;
        if (!is_numeric(strval($dxcPrecioSoloMovilNP))) $dxcPrecioSoloMovilNP = 0;
        if (!is_numeric(strval($dxcVAPSoloMovilNP))) $dxcVAPSoloMovilNP = 0;
        if (!is_numeric(strval($dxcPrecioSoloMovilVP))) $dxcPrecioSoloMovilVP = 0;
        if (!is_numeric(strval($dxcVAPSoloMovilVP))) $dxcVAPSoloMovilVP = 0;
        if (!is_numeric(strval($dxcPrecioSoloMovilPP))) $dxcPrecioSoloMovilPP = 0;
        if (!is_numeric(strval($dxcVAPSoloMovilPP))) $dxcVAPSoloMovilPP = 0;
        if (!is_numeric(strval($dxcPrecioConvergenciaEP))) $dxcPrecioConvergenciaEP = 0;
        if (!is_numeric(strval($dxcVAPConvergenciaEP))) $dxcVAPConvergenciaEP = 0;
        if (!is_numeric(strval($dxcPrecioConvergenciaNP))) $dxcPrecioConvergenciaNP = 0;
        if (!is_numeric(strval($dxcVAPConvergenciaNP))) $dxcVAPConvergenciaNP = 0;
        if (!is_numeric(strval($dxcPrecioConvergenciaVP))) $dxcPrecioConvergenciaVP = 0;
        if (!is_numeric(strval($dxcVAPConvergenciaVP))) $dxcVAPConvergenciaVP = 0;
        if (!is_numeric(strval($dxcPrecioConvergenciaPP))) $dxcPrecioConvergenciaPP = 0;
        if (!is_numeric(strval($dxcVAPConvergenciaPP))) $dxcVAPConvergenciaPP = 0;

        $marca = addslashes($marca);
        $modelo = addslashes($modelo);
        $gama = addslashes($gama);

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

        array_push($datos, [
            "marca" => $marca,
            "modelo" => $modelo,
            "precio_cesion" => $precioCesion,
            "pago_unico" => $pagoUnico,
            "gama" => $gama,
            "pago_inicial" => $pagoUnicoNP,
            "pago_mensual" => $cuotaMesNP,
            "dxcPrecioSoloMovil" => $dxcPrecioSoloMovilNP,
            "dxcVAPSolomovil" => $dxcVAPSoloMovilNP,
            "dxcPrecioConvergencia" => $dxcPrecioConvergenciaNP,
            "dxcVAPConvergencia" => $dxcVAPConvergenciaNP,
            "categoria" => "NORMAL PRO"
        ]);

        array_push($datos, [
            "marca" => $marca,
            "modelo" => $modelo,
            "precio_cesion" => $precioCesion,
            "pago_unico" => $pagoUnico,
            "gama" => $gama,
            "pago_inicial" => $pagoUnicoVP,
            "pago_mensual" => $cuotaMesVP,
            "dxcPrecioSoloMovil" => $dxcPrecioSoloMovilVP,
            "dxcVAPSolomovil" => $dxcVAPSoloMovilVP,
            "dxcPrecioConvergencia" => $dxcPrecioConvergenciaVP,
            "dxcVAPConvergencia" => $dxcVAPConvergenciaVP,
            "categoria" => "VALOR PRO"
        ]);

        array_push($datos, [
            "marca" => $marca,
            "modelo" => $modelo,
            "precio_cesion" => $precioCesion,
            "pago_unico" => $pagoUnico,
            "gama" => $gama,
            "pago_inicial" => $pagoUnicoPP,
            "pago_mensual" => $cuotaMesPP,
            "dxcPrecioSoloMovil" => $dxcPrecioSoloMovilPP,
            "dxcVAPSolomovil" => $dxcVAPSoloMovilPP,
            "dxcPrecioConvergencia" => $dxcPrecioConvergenciaPP,
            "dxcVAPConvergencia" => $dxcVAPConvergenciaPP,
            "categoria" => "PREMIUM PRO"
        ]);

        $contador++;
    }

    $r = $c->query("call orange.or_terminales_love_guardar('" . json_encode($datos) . "');");
    $retorno = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : "0";
    echo ($retorno == 1) ? "Carga correcta\n" : "Error en la carga\n";

    $c->close();

    echo "FIN\r\n";
} catch (Exception $e) {
    echo "Se ha producido un error" . $e->getMessage();
}
