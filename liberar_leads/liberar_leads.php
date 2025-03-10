<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/conexion_distri.php');

$c = new Conexion();
$cd = new ConexionDistri();
$c->conectar();
$cd->conectar();

$r = $c->query("call get_distribuidores();");

$distri = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC) : null;
$c->next_result();

if (!is_null($distri)) {
    for ($i = 0; $i < count($distri); $i++) {
        $bd = $distri[$i]["bd"];
        $servidor = $distri[$i]["servidor"];

        $query = "call " . $bd . ".tmk_liberar_leads_asignados();";

        echo "Ejecutando en " . $servidor . " " . $bd . "\n";

        if ($servidor == "wasp") {
            if ($c->more_results())
                $c->next_result();
            $c->query($query);
        } else {
            if ($cd->more_results())
                $cd->next_result();
            $cd->query($query);
        }
    }
}
echo "Finalizado\n";
