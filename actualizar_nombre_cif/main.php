<?php
require_once(getcwd() . "/vendor/conexion.php");
require_once(getcwd() . "/clases/infocif.php");

function obtenerDatos()
{
    $con = new Conexion();
    $con->conectar();
    if (!$con->getExisteError()) {
        // Obtengo el cif a consultar
        $r = $con->query("call captura.datos_cif_seleccionar();");
        $cif = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]["cif"] : null;

        if (!is_null($cif)) {
            $infoCif = new infoCif($cif);
            $arrDatos = $infoCif->obtenerDatosCifPlanB();
            if (count($arrDatos) > 0) {
                $nombre = addslashes($arrDatos["denominacion"]);
                $domicilio = addslashes($arrDatos["domicilio"]);
                $localidad = addslashes($arrDatos["localidad"]);
                $cp = $arrDatos["cp"];

                if (strlen($nombre) > 0) {
                    $con->next_result();
                    $r = $con->query("call captura.datos_cif_guardar('{$cif}','{$nombre}','{$domicilio}','{$localidad}','{$cp}');");
                    $retorno = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : 0;
                    if ($retorno == 1) {
                        echo "Guardado cif {$cif} - {$nombre}" . PHP_EOL;
                        sleep(mt_rand(3, 20));
                        obtenerDatos();
                    } else {
                        echo "Error al guardar el cif: {$cif}" . PHP_EOL;
                    }
                }
            }
        }
    } else {
        echo "Error en la conexion" . PHP_EOL;
    }
}

obtenerDatos();
