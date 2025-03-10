<?php

/*************************************************************************************************************/
/* Recorre todos los subdirectorios y copia la documentacion donde le digamos en el 2 parametro
/* 13/06/2024
/* Pedro José Gascón Moya
/*************************************************************************************************************/
$debug = false;

if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('error_reporting', E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

try {
    $origen = $argv[1];
    $destino = $argv[2];
} catch (Exception $e) {
    $origen = "";
    $destino = "";
}

if (strlen($origen > 0) && strlen($destino) > 0) {
    if (is_dir($origen) && is_dir($destino)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($origen, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isFile()) {
                // echo basename($file) . "\n";
                copy($file->getPathname(), $destino . "/" . basename($file));
                echo "Copianado archivo " . basename($file) . "\n";
            }
        }
    } else {
        echo "Tienes que introducir un origen y destino válidos\n";
        exit;
    }
} else {

    echo "Tienes que introducir un origen y destino válidos\n";
    exit;
}
echo "Finalizado\n";
