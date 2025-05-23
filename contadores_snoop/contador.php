<?php
require_once(getcwd() . "/vendor/conexion.php");

function contador($numero, $nIntentos)
{
    $conexion = new Conexion();
    $conexion->conectar();

    $r = $conexion->query("call captura.obtener_contadores();");
    $contadores = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC) : null;

    if (!is_null($contadores)) {
        $contadorSnoop = $contadores[0]["snoop"];
        $contadorEInforma = $contadores[0]["einforma"];
        $fecha = $contadores[0]["fecha"];
        $hora = $contadores[0]["hora"];

        $texto = "SNOOP: " . number_format($contadorSnoop, 0, ',', '.');
        $texto .= " EINFORMA: " . number_format($contadorEInforma, 0, ',', '.');
        $texto .= " FECHA: " . $fecha;
        $texto .= " HORA: " . $hora . PHP_EOL;
        echo $texto . PHP_EOL;

        if (!file_exists("semaforo")) {
            sleep(60);
            if ($numero == $contadorSnoop && $nIntentos == 2) {
                $nIntentos = 0;
                echo "Finalizando por servidor colgado" . PHP_EOL;
                exec("mv " . getcwd() . "/semaforo.stop " . getcwd() . "/semaforo");

                $fecha = date("Y-m-d H:i:s");
                $conexion->next_result();
                $conexion->query("insert into mails.emails values(NULL,0,'{$fecha}',NULL,'mailing@waspapp.es','pedrojose.gascon@waspapp.es','Caida SNOOP','Caida de SNOOP','0');");
                $conexion->close();
            } else {
                $nIntentos++;
                $conexion->close();
                contador($contadorSnoop, $nIntentos);
            }
        } else {
            echo "Finalizando por semáforo" . PHP_EOL;
        }
    } else {
        echo "Error al obtener los contadores" . PHP_EOL;
    }
}

exec("mv " . getcwd() . "/semaforo " . getcwd() . "/semaforo.stop");
contador(0, 0);
