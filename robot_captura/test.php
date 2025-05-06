<?php
error_reporting(1); // No reporta ningún error
ini_set('display_errors', 1); // No muestra errores en pantalla

$retorno = [];
$mJson = json_decode(file_get_contents("agora.json"), true);

echo procesarJSON(json_encode($mJson));

function procesarJSON($json): array
{
    $arrJson = json_decode($json, true);
    // $arrJson = json_decode(file_get_contents("pangea.json"), true);

    $totalLineas = 0;
    $totalLineasConPermanencia = 0;
    $totalLineasSinPermanencia = 0;
    $totalImporte = 0;
    $arrFechas = [];

    $cif = array_key_first($arrJson['resultados']);
    $fechaConsultaAux = $arrJson['resultados'][$cif]['timeStamp'];
    $fuente = $arrJson['resultados'][$cif]['fuente'];
    $arrResumen =  $arrJson['resultados'][$cif]['permanencias_resumen'];

    // Creo el array para ordenarlo por fecha
    $arrLineas = array_keys($arrResumen);
    for ($i = 0; $i < count($arrLineas); $i++) {
        if (esFecha($arrLineas[$i])) {
            $arrFechas[$i]["fecha"] = $arrLineas[$i];
            $arrFechas[$i]["nLineas"] = count($arrResumen[$arrLineas[$i]]['telefonos']);
            $arrFechas[$i]["importe"] = $arrResumen[$arrLineas[$i]]['importe'];
            $totalImporte +=  $arrResumen[$arrLineas[$i]]['importe'];
        }
    }

    $totalLineas = $arrJson['resultados'][$cif]['lineas']['numero_total'];
    $totalLineasSinPermanencia = $arrJson['resultados'][$cif]['lineas']['numero_sin_permanencia'];
    $totalLineasConPermanencia = $arrJson['resultados'][$cif]['lineas']['numero_con_permanencia'];

    if (strtoupper($fuente) == "AGORA") {
        // Obtengo las tarifas
        $tarifas = [];
        $arrTarifas = $arrJson['resultados'][$cif]['tarifas'];
        for ($t = 0; $t < count($arrTarifas); $t++) {
            $telefono = $arrTarifas[$t]['telefono'];
            $tarifa = $arrTarifas[$t]['tarifaNombre'];
            array_push($tarifas, array("telefono" => $telefono, "tarifa" => $tarifa));
        }
    } elseif (strtoupper($fuente) == "PANGEA") {
        $tarifas = [];
        $arrTarifas = $arrJson['resultados'][$cif]['permanencias'];
        for ($t = 0; $t < count($arrTarifas); $t++) {
            $telefono = $arrTarifas[$t]['telefono'];
            $tarifa = $arrJson['resultados'][$cif]['tarifa']["Tarifa"] ?? "";
            array_push($tarifas, array("telefono" => $telefono, "tarifa" => $tarifa));
        }
    }
    $arrRetorno = [];

    $arrRetorno["fecha"] = $fechaConsultaAux;
    $arrRetorno["cif"] = $cif;
    $arrRetorno["importe"] = $totalImporte;
    $arrRetorno["totalLineas"] = $totalLineas;
    $arrRetorno["totalLSP"] = $totalLineasSinPermanencia;
    $arrRetorno["totalLCP"] = $totalLineasConPermanencia;
    $arrRetorno["lineas"] = $arrFechas;
    $arrRetorno["tarifas"] = $tarifas ?? null;
    return $arrRetorno;
}

function esFecha($fecha): bool
{
    $retorno = false;
    $formato = "Y-m-d";

    $d = DateTime::createFromFormat($formato, $fecha);
    $errores = DateTime::getLastErrors();

    if ($errores['warning_count'] == 0 && $errores['error_count'] == 0)
        $retorno = true;

    return $retorno;
}

function fecha2Spain($f): string
{
    return date("d/m/Y", strtotime($f));
}
