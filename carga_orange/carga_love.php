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
        $pagoUnico = $hojaActiva->getCell($lineaPagoUnico . $contador);
        $pagoUnicoEP = $hojaActiva->getCell($lineaPagoUnicoEP . $contador);
        $cuotaMesEP = $hojaActiva->getCell($lineaCuotaMesEP . $contador);
        $pagoUnicoNP = $hojaActiva->getCell($lineaPagoUnicoNP . $contador);
        $cuotaMesNP = $hojaActiva->getCell($lineaCuotaMesNP . $contador);
        $pagoUnicoVP = $hojaActiva->getCell($lineaPagoUnicoVP . $contador);
        $cuotaMesVP = $hojaActiva->getCell($lineaCuotaMesVP . $contador);
        $pagoUnicoPP = $hojaActiva->getCell($lineaPagoUnicoPP . $contador);
        $cuotaMesPP = $hojaActiva->getCell($lineaCuotaMesPP . $contador);
        $dxcPrecioSoloMovilEP = $hojaActiva->getCell($dxcEPSoloMovil . $contador);
        $dxcVAPSoloMovilEP = $hojaActiva->getCell($dxcEPSoloMovilVAP . $contador);
        $dxcPrecioSoloMovilNP = $hojaActiva->getCell($dxcNPSoloMovil . $contador);
        $dxcVAPSoloMovilNP = $hojaActiva->getCell($dxcNPSoloMovilVAP . $contador);
        $dxcPrecioSoloMovilVP = $hojaActiva->getCell($dxcVPSoloMovil . $contador);
        $dxcVAPSoloMovilVP = $hojaActiva->getCell($dxcVPSoloMovilVAP . $contador);
        $dxcPrecioSoloMovilPP = $hojaActiva->getCell($dxcPPSoloMovil . $contador);
        $dxcVAPSoloMovilPP = $hojaActiva->getCell($dxcPPSoloMovilVAP . $contador);
        $dxcPrecioConvergenciaEP = $hojaActiva->getCell($dxcEPConvergencia . $contador);
        $dxcVAPConvergenciaEP = $hojaActiva->getCell($dxcEPConvergenciaVAP . $contador);
        $dxcPrecioConvergenciaNP = $hojaActiva->getCell($dxcNPConvergencia . $contador);
        $dxcVAPConvergenciaNP = $hojaActiva->getCell($dxcNPConvergenciaVAP . $contador);
        $dxcPrecioConvergenciaVP = $hojaActiva->getCell($dxcVPConvergencia . $contador);
        $dxcVAPConvergenciaVP = $hojaActiva->getCell($dxcVPConvergenciaVAP . $contador);
        $dxcPrecioConvergenciaPP = $hojaActiva->getCell($dxcPPConvergencia . $contador);
        $dxcVAPConvergenciaPP = $hojaActiva->getCell($dxcPPConvergenciaVAP . $contador);

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
