<?php
require_once(getcwd() . '/vendor/conexion.php');

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

$rutaRemoto = "/var/www/html/wasp/app/webroot/documentos18262202/";

$con = new Conexion();
$con->conectar();

$r = $con->query("call documentacion_traspaso();");

if ($r->num_rows > 0) {
    $data = $r->fetch_all(MYSQLI_ASSOC);
    for ($i = 0; $i < count($data); $i++) {
        $archivo = $data[$i]["archivo"];

        if (file_exists($rutaRemoto . $archivo)) {
            if (unlink($rutaRemoto . $archivo)) {
                echo "Eliminando " . $archivo . "\n";
            } else {
                echo "Error al eliminar el archivo";
            }
        } 
    }
}
