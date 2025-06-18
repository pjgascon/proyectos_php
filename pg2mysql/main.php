<?php

require_once("vendor/conexion.php");
require_once("clases/pg.php");
require_once("clases/mysql.php");

$pg = new pg();
$tablas = $pg->obtenerTablasBD("db_dupol");

if (count($tablas) > 0) {
    for ($i = 0; $i < count($tablas); $i++) {
        $mysql = new mysql_db();

        echo "Creando tabla: {$tablas[$i]}" . PHP_EOL;
        $s = $mysql->crearTabla("dupo_1", $tablas[$i], $pg->obtenerColumnasTabla($tablas[$i]));
    }
}
