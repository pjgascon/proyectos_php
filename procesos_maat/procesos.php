<?php

/***************************************************/
/* Gestión de procesos en segundo plano MAAT
/* 06/06/2024
/* Pedro José Gascón Moya
/***************************************************/
$debug = true;

if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('error_reporting', E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// require_once(getcwd() . '/vendor/conexion.php');

try {
    $proceso = $argv[1];
} catch (Exception $e) {
    $proceso = null;
}

if (!is_null($proceso)) {
    if ($proceso == "-l" || $proceso == "--list") {
        // Muestro el listado de procesos
        echo "1 __________ Generación y envio del informe de servicios activados\n\n";
        exit;
    }

    if (is_numeric($proceso)) {
        switch ($proceso) {
            case 1:
                $output = shell_exec('php servicios_activados/servicios_activados.php');
                echo $output;
                break;
            case 2:
                $output = shell_exec('php informe_activaciones_fi/informe_activaciones_fi.php');
                echo $output;
                break;
        }
    } else {
        echo "Nada que hacer\n\n";
        exit;
    }
} else {
    echo "No se puede ejecuar el script sin parámetros\n\n";
}
