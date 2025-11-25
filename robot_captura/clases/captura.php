<?php
require_once(getcwd() . "/vendor/conexion.php");
class Captura
{
    private $error = [];
    private $peticionAutomatica = [];

    public function getError(): array
    {
        return $this->error;
    }

    private function setError($clave, $valor)
    {
        $this->error[$clave] = $valor;
    }

    public function getPeticionAutomatica(): array
    {
        return $this->peticionAutomatica;
    }

    private function obtenerAuth(): array
    {
        $con = new Conexion();
        $con->conectar();

        if ($con->getExisteError()) {
            return ["error" => "Error al conectar a la base de datos"];
            exit;
        }

        $arr = [];

        $r = $con->query("call captura.auth_get();");
        $auth = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC) : null;

        if (!is_null($auth)) {
            $arr["auth"] = $auth[0]["clave"];
            $arr["url"] = $auth[0]["url"];
        }
        return $arr;
    }

    public function peticionIndividual($document, $tipoPeticion): string
    {
        $error = [];
        $peticionAutomatica = [];

        $datosAcceso = $this->obtenerAuth();
        if (array_key_exists("error", $datosAcceso)) {
            $error["error"] = $datosAcceso["error"];
            return "";
            exit;
        }

        $url = $datosAcceso["url"];
        $auth = $datosAcceso["auth"];

        $cifs = $document;
        $soloResumen = false;
        $noCache = true;
        $soloCache = false;
        $reconectar = false;
        $fuentes = "agora";

        $data = [
            "cifs" => $cifs,
            "soloResumen" => $soloResumen,
            "noCache" => $noCache,
            "soloCache" => $soloCache,
            "reconectar" => $reconectar,
            "fuentes" => $fuentes
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: $auth"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error["error"] = "Error al realizar la petición a la API";
            return "";
            exit;
        } else {
            curl_close($ch);
            if ($tipoPeticion == 1) {
                // Petición manual
                return $this->procesarJSON($response);
            } else {
                // Petición desde la automática
                $this->peticionAutomatica = $this->procesarJSONPeticionAutomatica($response);
                return "";
            }
        }
    }

    private function procesarJSON($json): string
    {
        $arrJson = json_decode($json, true);

        if (array_key_exists("error", $arrJson)) {
            $error["error"] = "Timeout";
            return "";
            exit;
        }

        if (array_key_exists("errores", $arrJson)) {
            if (count($arrJson["errores"]) > 0) {
                $error["error"] = "Servidor temporalmente fuera de servicio, por favor inténtalo dentro de unos minutos";
                return "";
                exit;
            }
        }

        if (array_key_exists("bloqueado", $arrJson)) {
            if ($arrJson["bloqueado"] == true) {
                $error["error"] = "Demasiadas peticiones en este momento, por favor inténtalo dentro de unos minutos";
                return "";
                exit;
            }
        }

        if (array_key_exists("noEncontrados", $arrJson)) {
            if (count($arrJson["noEncontrados"]) > 0) {
                $error["error"] = "No se han encontrado resultados";
                return "";
                exit;
            }
        }

        $texto = "";
        $totalLineas = 0;
        $totalLineasConYSinPermancia = 0;
        $totalLineasSinPermanencia = 0;
        $totalImporte = 0;
        $arrFechas = [];

        $cif = array_key_first($arrJson['resultados']);
        $fechaConsultaAux = $arrJson['resultados'][$cif]['timeStamp'];
        $fuente = $arrJson['resultados'][$cif]['fuente'];
        $arrLineasAux = $arrJson['resultados'][$cif]['permanencias_resumen'];
        try {
            $totalLineasConYSinPermancia = count($arrJson['resultados'][$cif]['permanencias']);
        } catch (Exception $e) {
            $totalLineasConYSinPermancia = 0;
        }

        $date = new DateTime($fechaConsultaAux);
        $fechaConsulta = $date->format('d/m/Y H:i:s');

        // Creo el array para ordenarlo por fecha
        $arrLineas = array_keys($arrLineasAux);
        for ($i = 0; $i < count($arrLineas); $i++) {
            if (Captura::esFecha($arrLineas[$i])) {
                $fecha = Captura::fecha2Spain($arrLineas[$i]);
                $arrFechas[$fecha]["nLineas"] = count($arrLineasAux[$arrLineas[$i]]['telefonos']);
                $arrFechas[$fecha]["importe"] = $arrLineasAux[$arrLineas[$i]]['importe'];
            }
        }

        // Ordeno el array por fecha de menor a mayor
        uksort($arrFechas, function ($a, $b) {
            $fechaA = DateTime::createFromFormat('d/m/Y', $a);
            $fechaB = DateTime::createFromFormat('d/m/Y', $b);
            return $fechaA <=> $fechaB;
        });

        // Recojo los resultados
        foreach ($arrFechas as $fecha => $datos) {
            $texto .= "{$fecha}: {$datos['nLineas']}L {$datos['importe']}€ \n";
            $totalLineas += $datos['nLineas'];
            $totalImporte += $datos['importe'];
        }

        $totalLineasSinPermanencia = $totalLineasConYSinPermancia - $totalLineas;

        return "Fecha de consulta: {$fechaConsulta} \n{$cif} \n{$totalLineasSinPermanencia}L SP\n" . $totalLineas . "L CP " . number_format($totalImporte, 2, ",", ".") . "€\n" . $texto;
    }

    private function procesarJSONPeticionAutomatica($json): array
    {
        $arrJson = json_decode($json, true);
        // $arrJson = json_decode(file_get_contents("pangea.json"), true);

        if (array_key_exists("error", $arrJson)) {
            $this->setError("error", "Timeout");
            $error["error"] = "Timeout";
            return $error;
            exit;
        }

        if (array_key_exists("errores", $arrJson)) {
            if (count($arrJson["errores"]) > 0) {
                $this->setError("error", "Servidor temporalmente fuera de servicio, por favor inténtalo dentro de unos minutos");
                $error["error"] = "Servidor temporalmente fuera de servicio, por favor inténtalo dentro de unos minutos";
                return $error;
                exit;
            }
        }

        if (array_key_exists("bloqueado", $arrJson)) {
            if ($arrJson["bloqueado"] == true) {
                $this->setError("error", "Demasiadas peticiones en este momento, por favor inténtalo dentro de unos minutos");
                $error["error"] = "Demasiadas peticiones en este momento, por favor inténtalo dentro de unos minutos";
                return $error;
                exit;
            }
        }

        if (array_key_exists("noEncontrados", $arrJson)) {
            if (count($arrJson["noEncontrados"]) > 0) {
                $this->setError("error", "No se han encontrado resultados");
                $error["error"] = "No se han encontrado resultados";
                return $error;
                exit;
            }
        }

        $totalLineas = 0;
        $totalLineasConPermanencia = 0;
        $totalLineasSinPermanencia = 0;
        $totalImporte = 0;
        $arrFechas = [];

        $cif = array_key_first($arrJson['resultados']);
        $fechaConsultaAux = $arrJson['resultados'][$cif]['timeStamp'];
        $arrResumen =  $arrJson['resultados'][$cif]['permanencias_resumen'];

        // Creo el array para ordenarlo por fecha
        $arrLineas = array_keys($arrResumen);
        for ($i = 0; $i < count($arrLineas); $i++) {
            if (Captura::esFecha($arrLineas[$i])) {
                $arrFechas[$i]["fecha"] = $arrLineas[$i];
                $arrFechas[$i]["nLineas"] = count($arrResumen[$arrLineas[$i]]['telefonos']);
                $arrFechas[$i]["importe"] = $arrResumen[$arrLineas[$i]]['importe'];
                $totalImporte +=  $arrResumen[$arrLineas[$i]]['importe'];
            }
        }

        $totalLineas = $arrJson['resultados'][$cif]['lineas']['numero_total'];
        $totalLineasSinPermanencia = $arrJson['resultados'][$cif]['lineas']['numero_sin_permanencia'];
        $totalLineasConPermanencia = $arrJson['resultados'][$cif]['lineas']['numero_con_permanencia'];

        $arrRetorno = [];

        $arrRetorno["fecha"] = $fechaConsultaAux;
        $arrRetorno["cif"] = $cif;
        $arrRetorno["importe"] = $totalImporte;
        $arrRetorno["totalLineas"] = $totalLineas;
        $arrRetorno["totalLSP"] = $totalLineasSinPermanencia;
        $arrRetorno["totalLCP"] = $totalLineasConPermanencia;
        $arrRetorno["lineas"] = $arrFechas;

        return $arrRetorno;
    }

    private static function esFecha($fecha): bool
    {
        $retorno = false;
        $formato = "Y-m-d";

        $d = DateTime::createFromFormat($formato, $fecha);
        $errores = DateTime::getLastErrors();

        if ($errores['warning_count'] == 0 && $errores['error_count'] == 0)
            $retorno = true;

        return $retorno;
    }

    private static function fecha2Spain($f): string
    {
        return date("d/m/Y", strtotime($f));
    }
}
