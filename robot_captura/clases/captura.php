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
            $arr["url"] = "http://localhost:3001/query"; //$auth[0]["url"];
            // $arr["url"] = "http://212.227.145.7:3001/query"; //$auth[0]["url"];
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
        $fuentes = "agora,pangea";

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
            $this->setError("error", "Error al realizar la petición a la API");
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
            $this->setError("error", "Timeout");
            return "Timeout";
            exit;
        }

        if (array_key_exists("errores", $arrJson)) {
            if (count($arrJson["errores"]) > 0) {
                $this->setError("error", "Fuera de servicio");
                return "Servidor temporalmente fuera de servicio, por favor inténtalo dentro de unos minutos";
                exit;
            }
        }

        if (array_key_exists("bloqueado", $arrJson)) {
            if ($arrJson["bloqueado"] == true) {
                $this->setError("error", "Fuera de servicio");
                return "Demasiadas peticiones en este momento, por favor inténtalo dentro de unos minutos";
                exit;
            }
        }

        if (array_key_exists("noEncontrados", $arrJson)) {
            if (count($arrJson["noEncontrados"]) > 0) {
                $this->setError("error", "KO");
                return "No se han encontrado resultados";
                exit;
            }
        }

        $totalLineas = 0;
        $totalLineasConPermanencia = 0;
        $totalLineasSinPermanencia = 0;
        $totalImporte = 0;
        $texto = "";
        $arrFechas = [];

        $cif = array_key_first($arrJson['resultados']);
        $fechaConsultaAux = $arrJson['resultados'][$cif]['timeStamp'];
        $fuente = $arrJson['resultados'][$cif]['fuente'];
        $arrResumen =  $arrJson['resultados'][$cif]['permanencias_resumen'];

        // $totalImporte = $arrResumen['suma'];

        // Creo el array para ordenarlo por fecha
        if (strtoupper($fuente) == "PANGEA") {
            $arrLineas = array_keys($arrResumen["permanencias"]);
            for ($i = 0; $i < count($arrLineas); $i++) {
                if (Captura::esFecha($arrLineas[$i])) {
                    $arrFechas[$i]["fecha"] = $arrLineas[$i];
                    if (array_key_exists("telefonos", $arrResumen["permanencias"][$arrLineas[$i]])) {
                        $arrFechas[$i]["nLineas"] = count($arrResumen["permanencias"][$arrLineas[$i]]['telefonos']);
                    } else {
                        $arrFechas[$i]["nLineas"] = 0;
                    }
                    if (!is_null($arrResumen["permanencias"][$arrLineas[$i]]['importe'])) {
                        $arrFechas[$i]["importe"] = $arrResumen["permanencias"][$arrLineas[$i]]['importe'];
                    } else {
                        $arrFechas[$i]["importe"] = 0;
                    }
                    $totalImporte +=  $arrFechas[$i]["importe"];
                    $texto .= Captura::fecha2Spain($arrLineas[$i]) . ":" . $arrFechas[$i]["importe"] . "€" . PHP_EOL;
                }
            }
        } else {
            $arrLineas = array_keys($arrResumen);
            for ($i = 0; $i < count($arrLineas); $i++) {
                if (Captura::esFecha($arrLineas[$i])) {
                    $arrFechas[$i]["fecha"] = $arrLineas[$i];
                    if (array_key_exists("telefonos", $arrResumen[$arrLineas[$i]])) {
                        $arrFechas[$i]["nLineas"] = count($arrResumen[$arrLineas[$i]]['telefonos']);
                    } else {
                        $arrFechas[$i]["nLineas"] = 0;
                    }
                    if (!is_null($arrResumen[$arrLineas[$i]]['importe'])) {
                        $arrFechas[$i]["importe"] = $arrResumen[$arrLineas[$i]]['importe'];
                    } else {
                        $arrFechas[$i]["importe"] = 0;
                    }
                    $totalImporte +=  $arrFechas[$i]["importe"];

                    $texto .= Captura::fecha2Spain($arrLineas[$i]) . ":" . $arrFechas[$i]["importe"] . "€" . PHP_EOL;
                }
            }
        }

        $totalLineas = $arrJson['resultados'][$cif]['lineas']['numero_total'];
        $totalLineasSinPermanencia = $arrJson['resultados'][$cif]['lineas']['numero_sin_permanencia'];
        $totalLineasConPermanencia = $arrJson['resultados'][$cif]['lineas']['numero_con_permanencia'];

        $textoConsulta = "Fecha de consulta: " . Captura::fecha2Spain($fechaConsultaAux) . PHP_EOL . "{$cif}" . PHP_EOL . "Total Líneas: {$totalLineas}L" . PHP_EOL .
            "{$totalLineasSinPermanencia}L SP" . PHP_EOL . "{$totalLineasConPermanencia}L CP" . PHP_EOL . "Total importe: {$totalImporte}" . PHP_EOL . $texto;

        return $textoConsulta;
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
        $fuente = $arrJson['resultados'][$cif]['fuente'];
        $arrResumen =  $arrJson['resultados'][$cif]['permanencias_resumen'];

        // $totalImporte = $arrResumen['suma'];

        // Creo el array para ordenarlo por fecha
        if (strtoupper($fuente) == "PANGEA") {
            $arrLineas = array_keys($arrResumen["permanencias"]);
            for ($i = 0; $i < count($arrLineas); $i++) {
                if (Captura::esFecha($arrLineas[$i])) {
                    $arrFechas[$i]["fecha"] = $arrLineas[$i];
                    if (array_key_exists("telefonos", $arrResumen["permanencias"][$arrLineas[$i]])) {
                        $arrFechas[$i]["nLineas"] = count($arrResumen["permanencias"][$arrLineas[$i]]['telefonos']);
                    } else {
                        $arrFechas[$i]["nLineas"] = 0;
                    }
                    if (!is_null($arrResumen["permanencias"][$arrLineas[$i]]['importe'])) {
                        $arrFechas[$i]["importe"] = $arrResumen["permanencias"][$arrLineas[$i]]['importe'];
                    } else {
                        $arrFechas[$i]["importe"] = 0;
                    }
                    $totalImporte +=  $arrFechas[$i]["importe"];
                }
            }
        } else {
            $arrLineas = array_keys($arrResumen);
            for ($i = 0; $i < count($arrLineas); $i++) {
                if (Captura::esFecha($arrLineas[$i])) {
                    $arrFechas[$i]["fecha"] = $arrLineas[$i];
                    if (array_key_exists("telefonos", $arrResumen[$arrLineas[$i]])) {
                        $arrFechas[$i]["nLineas"] = count($arrResumen[$arrLineas[$i]]['telefonos']);
                    } else {
                        $arrFechas[$i]["nLineas"] = 0;
                    }
                    if (!is_null($arrResumen[$arrLineas[$i]]['importe'])) {
                        $arrFechas[$i]["importe"] = $arrResumen[$arrLineas[$i]]['importe'];
                    } else {
                        $arrFechas[$i]["importe"] = 0;
                    }
                    $totalImporte +=  $arrFechas[$i]["importe"];
                }
            }
        }


        $totalLineas = $arrJson['resultados'][$cif]['lineas']['numero_total'];
        $totalLineasSinPermanencia = $arrJson['resultados'][$cif]['lineas']['numero_sin_permanencia'];
        $totalLineasConPermanencia = $arrJson['resultados'][$cif]['lineas']['numero_con_permanencia'];

        if (strtoupper($fuente) == "AGORA") {
            // Obtengo las tarifas
            $tarifas = [];
            $arrTarifas = $arrJson['resultados'][$cif]['tarifas'];
            if (!is_null($arrTarifas)) {
                for ($t = 0; $t < count($arrTarifas); $t++) {
                    $telefono = $arrTarifas[$t]['telefono'];
                    $tarifa = $arrTarifas[$t]['tarifaNombre'];
                    array_push($tarifas, array("telefono" => $telefono, "tarifa" => $tarifa));
                }
            }
        } elseif (strtoupper($fuente) == "PANGEA") {
            $tarifas = [];
            $arrTarifas = $arrJson['resultados'][$cif]['permanencias'];
            if (!is_null($arrTarifas)) {
                for ($t = 0; $t < count($arrTarifas); $t++) {
                    $telefono = $arrTarifas[$t]['telefono'];
                    $tarifa = $arrJson['resultados'][$cif]['tarifa']["Tarifa"] ?? "";
                    array_push($tarifas, array("telefono" => $telefono, "tarifa" => $tarifa));
                }
            }
        }

        $arrRetorno = [];

        $arrRetorno["fecha"] = Captura::fecha2Spain($fechaConsultaAux);
        $arrRetorno["cif"] = $cif;
        $arrRetorno["importe"] = $totalImporte;
        $arrRetorno["totalLineas"] = $totalLineas;
        $arrRetorno["totalLSP"] = $totalLineasSinPermanencia;
        $arrRetorno["totalLCP"] = $totalLineasConPermanencia;
        $arrRetorno["lineas"] = $arrFechas;
        $arrRetorno["tarifas"] = $tarifas ?? null;

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
