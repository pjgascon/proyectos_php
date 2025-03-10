<?php
require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/clases/new/vap_pyme.php');

// Uso de la función
try {
    $rutaArchivo = getcwd() . "/orange.xlsx";
    $datosExtraidos = leerExcel($rutaArchivo);

    if (count($datosExtraidos) > 0) {
        $c = new Conexion();
        $c->conectar();

        $r = $c->query("call orange.or_terminales_vap_pyme_guardar('" . addslashes(json_encode($datosExtraidos)) . "');");
        $b = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : 0;

        if ($b) {
            echo "Finalizado\n";
        } else {
            echo "Se han producido errores al procesar el catálogo de venta a plazos pyme\n";
        }
    }

    // Mostrar los datos obtenidos
    // foreach ($datosExtraidos as $fila) {
    //     print_r($fila);
    // }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
