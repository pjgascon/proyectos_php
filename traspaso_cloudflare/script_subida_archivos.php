<?php

require 'vendor/cloudflare/autoload.php'; // Para usar las dependencias instaladas via Composer

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/**
 * Sube los archivos de las tablas especificadas en $tablesWithFiles en los
 * databases especificados en $dbsNames a un bucket de S3.
 *
 * @param S3Client $s3 Un objeto S3Client para interactuar con S3.
 * @param string $dirFilePath La ruta local donde se encuentran los archivos.
 * @param mysqli $mysqli Un objeto mysqli para interactuar con la base de datos.
 * @param array $dbsNames Un array de nombres de bases de datos que se van a procesar.
 * @param string $environment El entorno de la base de datos (desarrollo o produccion), que sería el bucket de S3.
 */
function uploadFile(S3Client $s3, string $dirFilePath, mysqli $mysqli, array $dbsNames, string $environment): void
{
    $tablesWithFiles = [
        ['usuarios', 'usuarios_documentacion'],
        ['incidencias', 'incidencia_documentacion'],
        ['clientes', 'cliente_documentacion'],
        ['charly', 'charly_documentacion'],
        ['oportunidades', 'oportunidad_documentacion']
    ];

    foreach ($dbsNames as $dbName) {
        foreach ($tablesWithFiles as $tableInfo) {
            $folder = $tableInfo[0];
            $tableName = $tableInfo[1];

            $query = "SELECT * FROM `$dbName`.`$tableName`";
            $result = $mysqli->query($query);

            if ($result && $result->num_rows > 0) {
                echo "Processing $dbName.$tableName...\n";

                while ($row = $result->fetch_assoc()) {
                    $fileName = $row['archivo'];
                    $filePath = "$dirFilePath/$folder/$fileName";
                    $key = "$dbName/$fileName";

                    try {
                        if (file_exists($filePath)) {
                            $s3->putObject([
                                'Bucket' => $environment,
                                'Key' => $key,
                                'SourceFile' => $filePath
                            ]);
                            echo "Uploaded existing file $fileName to $key\n";
                        }
                        // Descomentar si se desea subir archivos vacíos
                        // else {
                        //     $s3->putObject([
                        //         'Bucket' => $environment,
                        //         'Key' => $key
                        //     ]);
                        //     echo "Uploaded empty file $fileName to $key\n";
                        // }
                    } catch (AwsException $e) {
                        echo "Error uploading $fileName to $folder: " . $e->getMessage() . "\n";
                    }
                }
                $result->free();
            } else {
                echo "No files found in $dbName.$tableName.\n";
            }
        }
    }
}


/**
 * Elimina archivos de un bucket de S3 que estan en las bases de datos especificadas en $dbsNames.
 *
 * @param S3Client $s3 Un objeto S3Client para interactuar con S3.
 * @param array $dbsNames Un array de nombres de bases de datos que se van a procesar.
 * @param mysqli $mysqli Un objeto mysqli para interactuar con la base de datos.
 * @param array $tablesWithFiles Un array de nombres de tablas que se van a procesar.
 * @param string $environment El entorno de la base de datos (desarrollo o produccion), que sería el bucket de S3.
 */
function deleteFile(S3Client $s3, array $dbsNames, mysqli $mysqli, array $tablesWithFiles, string $environment): void
{
    foreach ($dbsNames as $dbName) {
        foreach ($tablesWithFiles as $tableInfo) {
            $folder = $tableInfo[0];
            $tableName = $tableInfo[1];

            $query = "SELECT * FROM `$dbName`.`$tableName`";
            $result = $mysqli->query($query);

            if ($result && $result->num_rows > 0) {
                echo "Processing $dbName.$tableName...\n";

                while ($row = $result->fetch_assoc()) {
                    $fileName = $row['archivo'];
                    $key = "$dbName/$fileName";

                    try {
                        $s3->deleteObject([
                            'Bucket' => $environment,
                            'Key' => $key
                        ]);
                        echo "Deleted $fileName from $key\n";
                    } catch (AwsException $e) {
                        echo "Error deleting $fileName from $folder: " . $e->getMessage() . "\n";
                    }
                }
                $result->free();
            } else {
                echo "No files found in $dbName.$tableName.\n";
            }
        }
    }
}

// Directorios
$root = getcwd();
$dirFiles = "$root/webroot/files";

// Conexión a la base de datos con MySQLi
$mysqli = new mysqli(
    'desarrolloserver.liberi.es',
    'root',
    'JTkt5FWySb',
    '',
    3306
);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Configurar charset
$mysqli->set_charset('utf8mb4');

// Obtener credenciales
$query = "CALL maat.cloudflare_obtener_credenciales('desarrollo')";
$result = $mysqli->query($query);

if ($result && $result->num_rows > 0) {
    $credentials = $result->fetch_assoc();
    echo "Credenciales: " . print_r($credentials, true) . "\n";
    $result->free();
} else {
    die("Error: No se pudieron obtener las credenciales");
}

// Manejar múltiples conjuntos de resultados si los hay
while ($mysqli->more_results()) {
    $mysqli->next_result();
}

// Configurar cliente S3
$s3 = new S3Client([
    'version' => 'latest',
    'region' => 'auto',
    'endpoint' => "https://{$credentials['id_cuenta']}.r2.cloudflarestorage.com",
    'credentials' => [
        'key' => $credentials['id_clave'],
        'secret' => $credentials['clave']
    ]
]);

// Lista de bases de datos, añade las que necesites
$dbsNames = ["enertel"];

// Ejemplo de uso
uploadFile($s3, $dirFiles, $mysqli, $dbsNames, 'desarrollo');
//deleteFile($s3, $dbsNames, $mysqli, [['oportunidades', 'oportunidad_documentacion']], 'desarrollo');

// Cerrar conexión
$mysqli->close();
