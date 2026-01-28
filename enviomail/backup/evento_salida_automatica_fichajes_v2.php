<?php
try {
    // $conexion = new mysqli('localhost', 'root', 'root', 'cronos_g', 3306);
    // $conexion = new mysqli('desarrolloserver.liberi.es', 'root', 'JTkt5FWySb', 'cronos_g', 3306);
    $conexion = new mysqli('distribuidoresserver.liberi.es', 'root', 'Coral18262202', 'cronos_g', 3306);
    $conexion->set_charset("utf8");

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }


    $DIAS = [
        'Monday' => 'lunes',
        'Tuesday' => 'martes',
        'Wednesday' => 'miercoles',
        'Thursday' => 'jueves',
        'Friday' => 'viernes',
        'Saturday' => 'sabado',
        'Sunday' => 'domingo'
    ];

    $hoy = $DIAS[date('l')];
    $horaActual = new DateTime("now", new DateTimeZone('Europe/Madrid'));

    $resultado_crms_id = $conexion->query("CALL supercronos_g.obtener_crm_id_distribuidores();");
    $crms_id = $resultado_crms_id->num_rows > 0 ? $resultado_crms_id->fetch_all(MYSQLI_ASSOC) : [];

    foreach ($crms_id as $crm) {
        $crm_id = (int) $crm['id'];

        $conexion->next_result();
        $resultado_tipologia = $conexion->query("CALL supercronos_g.obtener_tipologia_por_crm($crm_id);");
        $tipologia = $resultado_tipologia->num_rows > 0 ? (int) $resultado_tipologia->fetch_assoc()['tipologia'] : "";

        $correo_emisor = match ($tipologia) {
            1 => 'info@cronoscrm.es',
            2 => 'correo@fichabit.es',
            3 => 'correo@fichacloud.es',
            default => 'info@cronoscrm.es',
        };

        $conexion->next_result();
        $resultado = $conexion->query("CALL rrhh.obtener_fichajes_sin_finalizar_activos($crm_id)");
        $fichajes = $resultado->num_rows > 0 ? $resultado->fetch_all(MYSQLI_ASSOC) : [];

        if (empty($fichajes)) {
            error_log("CRM $crm_id no tiene fichajes activos para ser finalizados.");
        }


        foreach ($fichajes as $fichaje) {

            $tipo_horario = $fichaje['tipo_horario'];
            $tipo_jornada = $fichaje['tipo_jornada'];
            $horarios = json_decode($fichaje['horarios'], true);
            $horario_hoy = $horarios[$hoy] ?? null;
            $salida = null;
            $salidaEstimada = null;

            if (!$horario_hoy) {
                error_log("No hay horario definido para hoy ($hoy) para el fichaje ID: {$fichaje['fichaje_id']}");
                continue;
            }

            if ($tipo_horario === "fijo") {
                if ($tipo_jornada == "continua") {
                    $salida = DateTime::createFromFormat("H:i", $horario_hoy[0]['salida'], new DateTimeZone('Europe/Madrid'));
                    $salidaEstimada = $salida;
                } elseif ($tipo_jornada === "partida") {
                    foreach ($horario_hoy as $tramo) {
                        $horaSalidaTramo = DateTime::createFromFormat("H:i", $tramo['salida'], new DateTimeZone('Europe/Madrid'));

                        if (!$horaSalidaTramo) {
                            error_log("Formato inválido para hora de salida: '{$tramo['salida']}' en fichaje ID: {$fichaje['fichaje_id']}");
                            continue;
                        }

                        $salidaEstimada = $horaSalidaTramo;
                        $horaSalidaTramo->setDate(date('Y'), date('m'), date('d'));

                        if ($horaActual < $horaSalidaTramo) {
                            $salida = $horaSalidaTramo;
                            break;
                        }
                    }
                }

                if ($salidaEstimada) {
                    $salidaEstimada->setDate(date('Y'), date('m'), date('d'));
                }


                if (!$salida && $salidaEstimada && $horaActual > $salidaEstimada) {
                    error_log("No se pudo determinar la hora de salida y/o no marcó su salida para el fichaje ID: {$fichaje['fichaje_id']}");
                    continue;
                }

                if (!$salida) {
                    error_log("No se pudo determinar la hora de salida para el fichaje ID: {$fichaje['fichaje_id']}");
                    continue;
                }

                $salida->setDate(date('Y'), date('m'), date('d'));
                $segundos_diferencia = $salida->getTimestamp() - $horaActual->getTimestamp();

                if ($segundos_diferencia <= 0) { // Si ya es la hora de la salida o más

                    // obtener el estado del empleado
                    $conexion->next_result();
                    $resultado_estado = $conexion->query("CALL rrhh.obtener_estado_fichaje_usuario({$fichaje['usuario_id']});");
                    $estado = $resultado_estado->num_rows > 0 ? $resultado_estado->fetch_assoc() : null;

                    if ($estado) {

                        $estadoJson = json_decode($estado['estado'], true);
                        $desde_timestamp = $estadoJson["desde_timestamp"] ?? 0;
                        $segundos_acumulados = $estadoJson["segundos_acumulados"] ?? 0;
                        $tipo_tramo = $estadoJson["tipo_tramo"];
                        $tramo_id = $estadoJson["tramo_id"];
                        $horario_id = $fichaje['horario_id'];
                        $centro_id = $fichaje['centro_id'] ?? "null";
                        $descando_id = $estadoJson["descanso_id"] ?? "null";
                        $crm_id = $fichaje['crm_id'];

                        // Calcular cuantos minutos han pasado desde que empezó el estado actual
                        $ahora_timestamp = $horaActual->getTimestamp() * 1000; // Convertir a milisegundos
                        $diferencia_milisegundos = $ahora_timestamp - $desde_timestamp;
                        $minutos_pasados = floor($diferencia_milisegundos / (1000 * 60)); // Convertir a minutos
                        $fecha_y_hora_salida = $horaActual->format('Y-m-d H:i:s');

                        $metadatos = [
                            "accion" => $tipo_tramo,
                            $tipo_tramo === "trabajo" ? "minutosTrabajados"  : "minutosDescansados" => $minutos_pasados,
                        ];
                        $metadatos_json = json_encode($metadatos, JSON_UNESCAPED_UNICODE);

                        // Finalizar el fichaje
                        $conexion->next_result();
                        $resultado = $conexion->query("call rrhh.finalizar_tramo($tramo_id, $descando_id, $centro_id, '$fecha_y_hora_salida', '$metadatos_json', null, null);");

                        $retorno = $resultado->num_rows > 0 ? $resultado->fetch_assoc()["retorno"] : 0;

                        if ($retorno > 0) {
                            $receptor = $fichaje["email_usuario"];
                            $usuario = $fichaje["nombre_usuario"];
                            $asunto = "Aviso de salida automática";
                            $salidaStr = $salida->format('H:i');
                            $cuerpo = "Hola $usuario, se ha registrado automáticamente tu salida a las $salidaStr.";
                            // Envio de email
                            $conexion->next_result();
                            $conexion->query("CALL supercronos_g.insertar_email_fichajes('$correo_emisor', '$receptor', '$asunto', '$cuerpo', NULL)");
                            error_log("Se ha insertado un email para $receptor con el fichaje ID: {$fichaje['fichaje_id']}, asunto: $asunto");

                            $conexion->next_result();
                            $mensaje = "Salida automática registrada a las {$horaActual->format('H:i')} (programada a las {$salida->format('H:i')}).";
                            $conexion->query("call supercronos_g.guardar_accion_log($crm_id, {$fichaje['usuario_id']}, '$mensaje');");

                            $conexion->next_result();

                            $resultado = $conexion->query("call rrhh.eliminar_estado_fichaje_usuario({$fichaje['usuario_id']});");
                            $retorno_eliminar_estado = $resultado->num_rows > 0 ? $resultado->fetch_assoc()["retorno"] : 0;

                            if ($retorno_eliminar_estado > 0) {
                                $conexion->next_result();
                                $conexion->query("call supercronos_g.guardar_accion_log($crm_id, {$fichaje['usuario_id']}, 'Se ha eliminado el estado de fichaje del usuario.');");
                            }
                            error_log("Se ha finalizado el tramo para el fichaje ID: {$fichaje['fichaje_id']}");
                        }
                    }
                }
            }
        }
    }

    $conexion->close();
} catch (\Throwable $th) {
    error_log("Error en el script: " . $th->getMessage());
}
