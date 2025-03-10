<?php
error_reporting(1); // No reporta ningún error
ini_set('display_errors', 1); // No muestra errores en pantalla

$retorno = [];
$mJson = json_decode(file_get_contents("agora.json"), true);

echo procesarJSON(json_encode($mJson));

function procesarJSON($json): string
{
    $arrJson = json_decode($json, true);

    if (array_key_exists("error", $arrJson)) {
        $error["error"] = "Timeout";
        return "";
        exit;
    }

    if (array_key_exists("errores", $arrJson)) {
        if (count($arrJson["errores"]) > 0) {
            $error["error"] = "Servidor temporalmente fuera de servicio, por favor inténtalo dentro de unos minutos";
            return "";
            exit;
        }
    }

    if (array_key_exists("bloqueado", $arrJson)) {
        if ($arrJson["bloqueado"] == true) {
            $error["error"] = "Demasiadas peticiones en este momento, por favor inténtalo dentro de unos minutos";
            return "";
            exit;
        }
    }

    if (array_key_exists("noEncontrados", $arrJson)) {
        if (count($arrJson["noEncontrados"]) > 0) {
            $error["error"] = "No se han encontrado resultados";
            return "";
            exit;
        }
    }

    $texto = "";
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
            // $fecha = fecha2Spain($arrLineas[$i]);
            // $arrFechas[$fecha]["nLineas"] = count($arrResumen[$arrLineas[$i]]['telefonos']);
            // $arrFechas[$fecha]["importe"] = $arrResumen[$arrLineas[$i]]['importe'];
            // $totalImporte +=  $arrResumen[$arrLineas[$i]]['importe'];

            // $texto .= "{$fecha}: {$arrFechas[$fecha]["nLineas"]}L {$arrResumen[$arrLineas[$i]]['importe']}€" . PHP_EOL;
            $arrFechas[$i]["fecha"] = $arrLineas[$i];
            $arrFechas[$i]["nLineas"] = count($arrResumen[$arrLineas[$i]]['telefonos']);
            $arrFechas[$i]["importe"] = $arrResumen[$arrLineas[$i]]['importe'];
            $totalImporte +=  $arrResumen[$arrLineas[$i]]['importe'];
        }
    }

    // Ordeno el array por fecha de menor a mayor
    // uksort($arrFechas, function ($a, $b) {
    //     $fechaA = DateTime::createFromFormat('d/m/Y', $a);
    //     $fechaB = DateTime::createFromFormat('d/m/Y', $b);
    //     return $fechaA <=> $fechaB;
    // });

    $totalLineas = $arrJson['resultados'][$cif]['lineas']['numero_total'];
    $totalLineasSinPermanencia = $arrJson['resultados'][$cif]['lineas']['numero_sin_permanencia'];
    $totalLineasConPermanencia = $arrJson['resultados'][$cif]['lineas']['numero_con_permanencia'];

    $arrRetorno = [];

    $arrRetorno["fecha"] = $fechaConsultaAux;
    $arrRetorno["cif"] = $cif;
    $arrRetorno["importe"] = $totalImporte;
    $arrRetorno["totalLineas"] = $totalLineas;
    $arrRetorno["totalLSP"] = $totalLineasSinPermanencia;
    $arrRetorno["totalLCP"] = $totalLineasConPermanencia;
    $arrRetorno["lineas"] = $arrFechas;

    echo json_encode($arrRetorno);

    return "";

    // return "Fecha de consulta: {$fechaConsultaAux}" . PHP_EOL . "{$cif}" . PHP_EOL . "Total Líneas: {$totalLineas}L" . PHP_EOL . "{$totalLineasSinPermanencia}L SP" . PHP_EOL . "{$totalLineasConPermanencia}L CP" . PHP_EOL . $texto;
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
