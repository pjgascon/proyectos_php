<?php
error_reporting(1); // No reporta ningún error
ini_set('display_errors', 1); // No muestra errores en pantalla

require_once(getcwd() . "/vendor/util.php");
require_once(getcwd() . "/clases/captura.php");
require_once(getcwd() . "/clases/datos.php");

// $tiempoDeEspera = mt_rand(1, 3);
// sleep($tiempoDeEspera * 60);
if (file_exists(getcwd() . "/stop.flag")) {
    echo "Ejecución detenida por bandera." . PHP_EOL;
    exit;
}

if (file_exists(getcwd() . "/r")) {
    echo "Reinicio del servidor." . PHP_EOL;
    system("reiniciarCaptura_1");
    sleep(15);
    system("php " . __FILE__);
    exit;
}

echo "Iniciando peticion" . PHP_EOL;
$peticion = new DatosCaptura();
$r = $peticion->obtenerPeticion();
$estadoNoEncontrado = 1;

$dat = json_decode($r, true);

if (!array_key_exists("error", $dat)) {
    echo "Datos para la petición obtenidos correctamente" . PHP_EOL;

    $captura = new Captura();
    $cif = $dat["resultado"]["cif"];
    $usuario = $dat["resultado"]["usuario"];
    $id = $dat["resultado"]["id"];
    $tipoPeticion = $dat["resultado"]["origenManual"];

    $resultados = $captura->peticionIndividual($cif, $tipoPeticion);

    if (count($captura->getError()) > 0) {
        echo "Error en la petición: " . $captura->getError()["error"] . PHP_EOL;

        if ($captura->getError()["error"] == "Timeout" || $captura->getError()["error"] == "No se han encontrado resultados") {
            $peticion->guardarPeticion($id, $usuario, $cif, "No se han encontrado resultados (Timeout)");
            $peticion->actualizarEstadoPeticion($id, $estadoNoEncontrado);
        }

        sleep(5);
        system("clear");
        system("php " . __FILE__);
        exit;
    }

    echo "Petición realizada correctamente" . PHP_EOL;

    if ($tipoPeticion) {
        if (count($captura->getError()) == 0) {
            //Todo OK
            $peticion->guardarPeticion($id, $usuario, $cif, $resultados);
        } else {
            // Se han producido errores
            if ($captura->getError()[0] == "No se han encontrado resultados") {
                $peticion->guardarPeticion($id, $usuario, $cif, "No se han encontrado resultados");
            } elseif ($captura->getError()[0] == "Timeout") {
                $peticion->guardarPeticion($id, $usuario, $cif, "No se han encontrado resultados");
            } else {
                exit;
            }
        }
    } else {
        if (count($captura->getError()) == 0) {
            if ($peticion->guardarPeticionAutomatica(json_encode($captura->getPeticionAutomatica()), $id)) {
                $peticion->actualizarEstadoPeticion($id, 1);
                $peticion->guardarPeticion($id, -1, $cif, json_encode($captura->getPeticionAutomatica()));
                echo "Petición guardada correctamente" . PHP_EOL;
            }
        } else {
            // Se han producido errores
            if ($captura->getError()['error'] == "No se han encontrado resultados") {
                $peticion->actualizarEstadoPeticion($id, $estadoNoEncontrado);
            } elseif ($captura->getError()['error'] == "Timeout") {
                $peticion->actualizarEstadoPeticion($id, $estadoNoEncontrado);
                // exec("reiniciarCaptura_1");
                sleep(10);
            } else {
                //exec("reiniciarCaptura_1");
                sleep(15);
            }
            echo $captura->getError()['error'] . PHP_EOL;
        }

        sleep(5);
        system("clear");
        system("php " . __FILE__);
        exit;
    }
    echo "FIN" . PHP_EOL;
} else {
    echo $dat["error"] . PHP_EOL;
}
echo PHP_EOL;
