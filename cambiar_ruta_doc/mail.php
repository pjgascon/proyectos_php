<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

require_once(getcwd() . '/vendor/conexion.php');

$procesarCorreo = false;
$c = new Conexion();
$c->conectar();

$r = $c->query("call ruta_documentos_nueva();");
if ($r->num_rows > 0) {
    $data = $r->fetch_all(MYSQLI_ASSOC);
    $rutaNueva = $data[0]["ruta_nueva"];
    $rutaAnterior = $data[0]["ruta_anterior"];

    exec("mv ".$rutaAnterior." ".$rutaNueva); 
    $c->close();
}else{
    $c->close();
    return;
}

