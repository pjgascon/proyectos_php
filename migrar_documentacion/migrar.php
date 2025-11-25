<?php
require_once(getcwd() . "/vendor/conexion.php");
require_once(getcwd() . "/cloudflare.php");

$con = new Conexion();
$con->conectar();

$data = $con->query("call maat.temporal_migracion();");
if ($data->num_rows > 0) {
    $contador = 1;
    $cloudflare = new Cloudflare();
    $rows = $data->fetch_all(MYSQLI_ASSOC);
    for ($i = 0; $i < count($rows); $i++) {
        if (file_exists(getcwd() . "/contratos/" . $rows[$i]['archivo'])) {
            if ($cloudflare->uploadFile(getcwd() . "/contratos/" . $rows[$i]['archivo'])) {
                echo "Archivo subido: " . $contador . " de " . count($rows) . PHP_EOL;
            } else {
                echo "Error al subir el archivo" . PHP_EOL;
            }
        } else {
            echo "El archivo no existe" . PHP_EOL;
        }

        $contador++;
    }
}
