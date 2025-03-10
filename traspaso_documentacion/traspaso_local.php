<?php
require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/subir_archivo.php');

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

$rutaLocal = "/opt/documentos18262202/";
$rutaRemoto = "/media/pedro/Distribuidores/connect/";

$con = new Conexion();
$con->conectar();

$r = $con->query("call documentacion_traspaso_obtener_directorios();");

if ($r->num_rows > 0) {
    $data = $r->fetch_all(MYSQLI_ASSOC);
    for ($i = 0; $i < count($data); $i++) {
        if (is_null($cliente))
            $cliente = "Desconocido";
        $cliente = $data[$i]["name"];
        $cliente = str_replace(",", "", $cliente);
        $cliente = str_replace(".", "", $cliente);
        $cliente = str_replace(":", "", $cliente);
        $cliente = str_replace(";", "", $cliente);
        $cliente = str_replace(";", "", $cliente);
        $cliente = str_replace("'", "", $cliente);
        $cliente = ltrim(rtrim($cliente));

        if (strlen($cliente) == 0)
            $cliente = "Desconocido";

        if (!file_exists($rutaRemoto . $cliente)) {
            echo "Creando " . $rutaRemoto . $cliente . "\n";
            mkdir($rutaRemoto . $cliente);
        }
    }
}

$con->next_result();
$r = $con->query("call documentacion_traspaso();");

if ($r->num_rows > 0) {
    $data = $r->fetch_all(MYSQLI_ASSOC);
    for ($i = 0; $i < count($data); $i++) {
        $nombreArchivo = $data[$i]["nombre"];
        $archivo = $data[$i]["archivo"];
        $cliente = $data[$i]["name"];

        $cliente = str_replace(",", "", $cliente);
        $cliente = str_replace(".", "", $cliente);
        $cliente = str_replace(":", "", $cliente);
        $cliente = str_replace(";", "", $cliente);
        $cliente = str_replace(";", "", $cliente);
        $cliente = str_replace("'", "", $cliente);
        $cliente = ltrim(rtrim($cliente));

        if (strlen($cliente) == 0) {
            $cliente = "Desconocido/";
        } else {
            $cliente .= "/";
        }


        if (copy($rutaLocal . $archivo, $rutaRemoto . $cliente . $archivo)) {
            echo "Copiado " . $rutaLocal . $archivo, $rutaRemoto . $cliente . $archivo . "\n";
        } else {
            echo "Error al copiar el archivo " . $archivo;
        }
    }
}
