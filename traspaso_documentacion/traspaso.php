<?php
require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/subir_archivo.php');

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

$rutaLocal = "/media/pedro/Distribuidores/yulmacat/";
$rutaRemoto = "/var/www/html/wasp/app/webroot/documentos18262202/";

$con = new Conexion();
$con->conectar();

$r = $con->query("call documentacion_traspaso_obtener_directorios();");

if ($r->num_rows > 0) {
    $data = $r->fetch_all(MYSQLI_ASSOC);
    for ($i = 0; $i < count($data); $i++) {
        if (is_null($cliente))
            $cliente = "Desconocido";
        $cliente = addslashes($data[$i]["name"]);

        $cliente = str_replace(",", "", $cliente);
        $cliente = str_replace(".", "", $cliente);
        $cliente = str_replace(":", "", $cliente);
        $cliente = str_replace(";", "", $cliente);
        $cliente = str_replace("/", "_", $cliente);
        $cliente = ltrim(rtrim($cliente));

        if (!file_exists($rutaLocal . $cliente)) {
            echo "Creando " . $rutaLocal . $cliente . "\n";
            mkdir($rutaLocal . $cliente);
        }
    }
}

$con->next_result();
$r = $con->query("call documentacion_traspaso();");

if ($r->num_rows > 0) {
    $data = $r->fetch_all(MYSQLI_ASSOC);
    $sftpConnection = ssh2_connect("waspserver.liberi.es", 1826);
    if (ssh2_auth_password($sftpConnection, "root", "Coral18262202")) {
        for ($i = 0; $i < count($data); $i++) {
            $cliente = addslashes($data[$i]["name"]);
            $nombreArchivo = $data[$i]["nombre"];
            $archivo = $data[$i]["archivo"];

            $cliente = str_replace(",", "", $cliente);
            $cliente = str_replace(".", "", $cliente);
            $cliente = str_replace(":", "", $cliente);
            $cliente = str_replace(";", "", $cliente);
            $cliente = str_replace("/", "_", $cliente);
            $cliente = ltrim(rtrim($cliente));

            if(!file_exists($rutaLocal . $cliente . "/" . $archivo)){
                ssh2_scp_recv($sftpConnection, $rutaRemoto . $archivo, $rutaLocal . $cliente . "/" . $archivo);
                echo "Copiado " . $rutaLocal . $cliente . "/" . $archivo . "\n";
            }            
        }
    }
}
