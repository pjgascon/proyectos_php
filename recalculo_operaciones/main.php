<?php require_once(getcwd() . '/vendor/conexion.php');
$con = new Conexion();
$con->conectar();

if(strlen($argv[1]) == 0){
    echo "Falta el argumento de la base de datos";
    exit;   
}
else{
    $db = $argv[1];
}
// $db = "cusar";
$fecha = "2025-04-01 00:00:00";
$query = "SELECT quotes_id FROM {$db}.quotes_offers_pyme_addons WHERE plan_precios_id = 1719 AND quotes_id IN (SELECT id FROM {$db}.quotes WHERE date_last >= '{$fecha}')";
$r = $con->query($query);
$registros = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC) : null;

if (!is_null($registros)) {
    $total = count($registros);
    for ($i = 0; $i < count($registros); $i++) {
        $contador = $i + 1;
        // $con->next_result();
        echo "\rRecalculando id: {$registros[$i]['quotes_id']}: {$contador} de {$total}              ";
        flush();

        $con->query("call recalcular_operaciones('{$db}',{$registros[$i]['quotes_id']})");

        // Limpiar resultados pendientes del procedimiento
        do {
            if ($res = $con->store_result()) {
                $res->free();
            }
        } while ($con->more_results() && $con->next_result());
    }
}
$con->close();
echo "Finalizado";
