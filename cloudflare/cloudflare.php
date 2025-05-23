<?php
require_once(getcwd() . '/vendor/autoload.php');

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$bucketName = 'desarrollo'; // Cambia por el nombre de tu bucket
$accessKey = '8205088a2da2490160de6356a7f30afd'; // Tu Access Key ID
$secretKey = '4ecf78f76fd289e9adb7d3aac1ccf63c6f991b8f9b90e7fbd073c40e9ae8e9f4'; // Tu Secret Access Key
$endpoint = 'https://a3f3121abe2a5fb47df420632c6a166b.r2.cloudflarestorage.com'; // Cambia <account_id> con tu ID de cuenta

// Crear el cliente S3 compatible con R2
$s3Client = new S3Client([
    'region' => 'auto',
    'version' => 'latest',
    'endpoint' => $endpoint,
    'credentials' => [
        'key'    => $accessKey,
        'secret' => $secretKey,
    ],
]);

// Función para subir un archivo a R2
function uploadFile($s3Client, $bucketName, $filePath, $key)
{
    try {
        $result = $s3Client->putObject([
            'Bucket' => $bucketName,
            'Key'    => $key,
            'Body'   => fopen($filePath, 'r'),
        ]);
        echo "Archivo subido con éxito: " . $result['ObjectURL'] . PHP_EOL;
    } catch (AwsException $e) {
        echo "Error al subir el archivo: " . $e->getMessage() . PHP_EOL;
    }
}

// Función para descargar un archivo desde R2
function downloadFile($s3Client, $bucketName, $key, $saveToPath)
{
    try {
        $result = $s3Client->getObject([
            'Bucket' => $bucketName,
            'Key'    => $key,
        ]);
        file_put_contents($saveToPath, $result['Body']);
        echo "Archivo descargado con éxito: " . $saveToPath . PHP_EOL;
    } catch (AwsException $e) {
        echo "Error al descargar el archivo: " . $e->getMessage() . PHP_EOL;
    }
}

function getLink($s3Client, $bucketName, $fileName): string
{
    $obj = $s3Client->getCommand('GetObject', [
        'Bucket' => $bucketName,
        'Key'    => $fileName,
    ]);

    $request = $s3Client->createPresignedRequest($obj, '+2 minutes');

    return (string) $request->getUri();
}

// Ejemplo de uso
$filePath = '2.png'; // Ruta del archivo a subir
$key = 'desarrollo/foto_ari.png'; // Nombre del archivo en el bucket
$saveToPath = "/home/pedro/www/php/cloudflare/descargas/1.png"; // Ruta donde guardar el archivo descargado

// Subir archivo
//uploadFile($s3Client, $bucketName, $filePath, $key);

// Descargar archivo
//downloadFile($s3Client, $bucketName, $key, $saveToPath);

echo getLink($s3Client, $bucketName, $key);
