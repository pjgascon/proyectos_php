<?php
$host = 'distribuidoresserver.liberi.es';
$usuario = 'root';
$clave = 'Coral18262202'; // Tu contraseña
$bd = 'mysql';
$dbObjetivo = 'supercronos_g';

$carpetaBase = __DIR__ . '/procedimientos_sql';
$carpetaProcedures = "$carpetaBase/procedures";
$carpetaFunctions  = "$carpetaBase/functions";

foreach ([$carpetaBase, $carpetaProcedures, $carpetaFunctions] as $carpeta) {
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }
}

$mysqli = new mysqli($host, $usuario, $clave, $bd);
$mysqli->set_charset("utf8mb4");

if ($mysqli->connect_errno) {
    die("❌ Error de conexión: " . $mysqli->connect_error);
}

$tipos = ['PROCEDURE' => $carpetaProcedures, 'FUNCTION' => $carpetaFunctions];
$contador = 0;

foreach ($tipos as $tipo => $carpetaDestino) {
    echo "🔎 Exportando $tipo...\n";

    $consulta = "
        SELECT name, body_utf8, param_list, returns
        FROM mysql.proc
        WHERE db = '$dbObjetivo' AND type = '$tipo'
    ";

    $res = $mysqli->query($consulta);
    if (!$res) {
        echo "⚠️ Error en la consulta de $tipo: " . $mysqli->error . "\n";
        continue;
    }

    while ($row = $res->fetch_assoc()) {
        $nombre = $row['name'];
        $parametros = trim($row['param_list']);
        $cuerpo = trim($row['body_utf8']);
        $retorno = $tipo === 'FUNCTION' ? " RETURNS {$row['returns']}" : '';

        // Verificar si el cuerpo ya contiene BEGIN
        $tieneBegin = preg_match('/^\s*BEGIN/i', $cuerpo);

        $sql = "DELIMITER $$\n";
        $sql .= "CREATE $tipo `$nombre`($parametros)$retorno\n";
        $sql .= ($tieneBegin ? "$cuerpo" : "BEGIN\n$cuerpo\nEND");
        $sql .= "\n$$\nDELIMITER ;\n";

        file_put_contents("$carpetaDestino/{$nombre}.sql", $sql);
        echo "✅ $tipo exportado: $nombre\n";
        $contador++;
    }
}

echo "✔️ Exportación completada: $contador rutinas\n";
echo "📁 Procedimientos en: $carpetaProcedures\n";
echo "📁 Funciones en:     $carpetaFunctions\n";
