<?php
require_once(getcwd() . "/vendor/cloudflare/autoload.php");
require_once(getcwd() . '/vendor/conexion.php');

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class Cloudflare
{
    private $idAplicacion = "";
    private $idClave = "";
    private $clave = "";
    private $endPoint = "";
    private $s3Client = null;
    private $bucketName = "produccion";
    private $nombreDistribuidor = "enertel";

    public function __construct()
    {
        $arrCredenciales = $this->obtenerAccesoCloudflare();
        if (!is_null($arrCredenciales)) {
            $this->idAplicacion = $arrCredenciales[0]['id_cuenta'];
            $this->idClave = $arrCredenciales[0]['id_clave'];
            $this->clave = $arrCredenciales[0]['clave'];
            $this->endPoint = 'https://' . $this->idAplicacion . '.r2.cloudflarestorage.com';
            //Si la bd es maat (ha entrador como root) pongo el nombre de distribuidor enertel

            $this->s3Client = new S3Client([
                'region' => 'auto',
                'version' => 'latest',
                'endpoint' => $this->endPoint,
                'credentials' => [
                    'key'    => $this->idClave,
                    'secret' => $this->clave,
                ],
            ]);
        } else {
        }
    }

    // Función para subir un archivo a R2
    public function uploadFile($filePath): bool
    {
        $retorno = false;
        $key = "maat/" . $this->nombreDistribuidor . "/contratos/" . basename($filePath);

        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key'    => $key,
                'Body'   => fopen($filePath, 'r'),
            ]);
            // echo "Archivo subido con éxito: " . $result['ObjectURL'] . PHP_EOL;
            $retorno = true;
        } catch (AwsException $e) {
            // echo "Error al subir el archivo: " . $e->getMessage() . PHP_EOL;
            $retorno = false;
        }
        return $retorno;
    }

    //Obtiene un link de descarga de un archivo
    public function getLink($fileName): string
    {
        try {
            $key = "maat/" . $this->nombreDistribuidor . "/contratos/" . $fileName;
            $obj = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $this->bucketName,
                'Key'    => $key,
            ]);
            $request = $this->s3Client->createPresignedRequest($obj, '+2 minutes');
            return (string) $request->getUri();
        } catch (AwsException $e) {
            // echo "Error al subir el archivo: " . $e->getMessage() . PHP_EOL;
            return "";
        }
    }

    // Elimina un archivo de R2
    public function deleteFile($fileName): bool
    {
        $retorno = false;
        $key = "maat/" . $this->nombreDistribuidor . "/contratos/" . $fileName;

        try {
            $result = $this->s3Client->deleteObject([
                'Bucket' => $this->bucketName,
                'Key'    => $key,
            ]);
            // echo "Archivo eliminado con éxito: " . $result['ObjectURL'] . PHP_EOL;
            $retorno = true;
        } catch (AwsException $e) {
            // echo "Error al subir el archivo: " . $e->getMessage() . PHP_EOL;
            $retorno = false;
        }
        return $retorno;
    }

    private function obtenerAccesoCloudflare(): array
	{
        $con = new Conexion();
        $con->conectar();
		$data = $con->query("call maat.cloudflare_obtener_credenciales('produccion');");
		return ($data->num_rows > 0) ? $data->fetch_all(MYSQLI_ASSOC) : null;
	}
}
