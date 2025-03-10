<?php
require_once(getcwd() . "/vendor/conexion.php");

$con = new Conexion();

$con->conectar();

$r = $con->query("select * from test.orange");
$datos = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC) : null;

if (!is_null($datos)) {
    for ($i = 0; $i < count($datos); $i++) {
        $id = $datos[$i]["id"];
        $modelo = $datos[$i]["modelo"];

        $sql = "call test.actualizar({$id},'{$modelo}');";
echo $sql;
        $con->next_result();
        $con->query($sql);

        "Actualizando " . $modelo . PHP_EOL;
    }
    echo "FIN" . PHP_EOL;
}
