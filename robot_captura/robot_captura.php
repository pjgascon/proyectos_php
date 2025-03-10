<?php
error_reporting(1); // No reporta ningún error
ini_set('display_errors', 1); // No muestra errores en pantalla

require_once(getcwd() . "/vendor/util.php");
require_once(getcwd() . "/clases/captura.php");
require_once(getcwd() . "/clases/datos.php");

// $tiempoDeEspera = mt_rand(1, 3);
// sleep($tiempoDeEspera * 60);

$peticion = new DatosCaptura();
$r = $peticion->obtenerPeticion();

$dat = json_decode($r, true);

if (!array_key_exists("error", $dat)) {
    $captura = new Captura();
    $cif = $dat["resultado"]["cif"];
    $usuario = $dat["resultado"]["usuario"];
    $id = $dat["resultado"]["id"];
    $tipoPeticion = $dat["resultado"]["origenManual"];

    $resultados = $captura->peticionIndividual($cif, $tipoPeticion);

    if ($tipoPeticion) {
        if (count($captura->getError()) == 0) {
            //Todo OK
            $peticion->guardarPeticion($id, $usuario, $cif, $resultados);
        } else {
            // Se han producido errores
            if ($captura->getError()[0] == "No se han encontrado resultados")
                $peticion->guardarPeticion($id, $usuario, $cif, "No se han encontrado resultados");

            if ($captura->getError()[0] == "Timeout")
                $peticion->guardarPeticion($id, $usuario, $cif, "No se han encontrado resultados");
        }
    } else {
        if (count($captura->getError()) == 0) {
            if ($peticion->guardarPeticionAutomatica(json_encode($captura->getPeticionAutomatica()))) {
                $peticion->actualizarEstadoPeticion($id);
                $peticion->guardarPeticion($id, -1, $cif, json_encode($captura->getPeticionAutomatica()));
            }
        } else {
            // Se han producido errores
            if ($captura->getError()['error'] == "No se han encontrado resultados" || $captura->getError()['error'] == "Timeout") {
                $peticion->actualizarEstadoPeticion($id);
            }
            echo $captura->getError()['error'] . PHP_EOL;
        }
    }
    echo "FIN" . PHP_EOL;
} else {
    echo $dat["error"] . PHP_EOL;
}
echo PHP_EOL;
