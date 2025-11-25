<?php

try {
    // Conexión a la base de datos
    // $conexion = new mysqli('localhost', 'root', 'root', 'cronos_g', 3306);
    // $conexion = new mysqli('desarrolloserver.liberi.es', 'root', 'JTkt5FWySb', 'cronos_g', 3306);
    $conexion = new mysqli('distribuidoresserver.liberi.es', 'root', 'Coral18262202', 'cronos_g', 3306);
    $conexion->set_charset("utf8");

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Array que mapea días en inglés a español
    $DIAS = [
        'Monday' => 'lunes',
        'Tuesday' => 'martes',
        'Wednesday' => 'miercoles',
        'Thursday' => 'jueves',
        'Friday' => 'viernes',
        'Saturday' => 'sabado',
        'Sunday' => 'domingo'
    ];

    // Obtiene el día actual en español y la hora en zona horaria de Madrid
    $hoy = $DIAS[date('l')];
    $horaActual = new DateTime("now", new DateTimeZone('Europe/Madrid'));

    // Llama a un procedimiento almacenado para obtener IDs de CRMs
    $resultado_crms_id = $conexion->query("CALL supercronos_g.obtener_crm_id_distribuidores();");
    $crms_id = $resultado_crms_id->num_rows > 0 ? $resultado_crms_id->fetch_all(MYSQLI_ASSOC) : [];

    // Recorre cada CRM
    foreach ($crms_id as $crm) {
        $crm_id = (int) $crm['id'];

        // Limpia el buffer de resultados y obtiene la tipología del CRM
        $conexion->next_result();
        $resultado_tipologia = $conexion->query("CALL supercronos_g.obtener_tipologia_por_crm($crm_id);");
        $tipologia = $resultado_tipologia->num_rows > 0 ? (int) $resultado_tipologia->fetch_assoc()['tipologia'] : "";

        // Asigna correo emisor según la tipología
        $correo_emisor = match ($tipologia) {
            1 => 'info@cronoscrm.es',
            2 => 'correo@fichabit.es',
            3 => 'correo@fichacloud.es',
            default => 'info@cronoscrm.es',
        };

        // Obtiene fichajes activos sin finalizar para el CRM
        $conexion->next_result();
        $resultado = $conexion->query("CALL rrhh.obtener_fichajes_sin_finalizar_activos($crm_id)");
        $fichajes = $resultado->num_rows > 0 ? $resultado->fetch_all(MYSQLI_ASSOC) : [];

        // Si no hay fichajes, registra error y continúa
        if (empty($fichajes)) {
            error_log("CRM $crm_id no tiene fichajes activos para ser finalizados.");
        }

        // Recorre cada fichaje
        foreach ($fichajes as $fichaje) {
            $tipo_horario = $fichaje['tipo_horario'];
            $tipo_jornada = $fichaje['tipo_jornada'];
            $horarios = json_decode($fichaje['horarios'], true); // Decodifica horarios en JSON
            $horario_hoy = $horarios[$hoy] ?? null; // Horario del día actual
            $salida = null;
            $salidaEstimada = null;

            // Si no hay horario para hoy, registra error y continúa
            if (!$horario_hoy) {
                error_log("No hay horario definido para hoy ($hoy) para el fichaje ID: {$fichaje['fichaje_id']}");
                continue;
            }

            // Si el horario es fijo
            if ($tipo_horario === "fijo") {
                // Jornada continua: toma la hora de salida directamente
                if ($tipo_jornada === "continua") {
                    $salida = DateTime::createFromFormat("H:i", $horario_hoy[0]['salida'], new DateTimeZone('Europe/Madrid'));
                    $salidaEstimada = $salida;
                }
                // Jornada partida: busca la hora de salida del tramo correspondiente
                elseif ($tipo_jornada === "partida") {
                    foreach ($horario_hoy as $tramo) {
                        $horaSalidaTramo = DateTime::createFromFormat("H:i", $tramo['salida'], new DateTimeZone('Europe/Madrid'));

                        // Si el formato es inválido, registra error y continúa
                        if (!$horaSalidaTramo) {
                            error_log("Formato inválido para hora de salida: '{$tramo['salida']}' en fichaje ID: {$fichaje['fichaje_id']}");
                            continue;
                        }

                        $salidaEstimada = $horaSalidaTramo;
                        $horaSalidaTramo->setDate(date('Y'), date('m'), date('d'));

                        // Si la hora actual es menor a la salida del tramo, usa esa salida
                        if ($horaActual < $horaSalidaTramo) {
                            $salida = $horaSalidaTramo;
                            break;
                        }
                    }
                }

                // Ajusta la fecha de salida estimada al día actual
                if ($salidaEstimada) {
                    $salidaEstimada->setDate(date('Y'), date('m'), date('d'));
                }

                // Si no se determinó salida y la hora actual pasó la estimada, registra error
                if (!$salida && $salidaEstimada && $horaActual > $salidaEstimada) {
                    error_log("No se pudo determinar la hora de salida y/o no marcó su salida para el fichaje ID: {$fichaje['fichaje_id']}");
                    continue;
                }

                // Si no se determinó salida, registra error
                if (!$salida) {
                    error_log("No se pudo determinar la hora de salida para el fichaje ID: {$fichaje['fichaje_id']}");
                    continue;
                }

                // Ajusta la fecha de salida al día actual
                $salida->setDate(date('Y'), date('m'), date('d'));

                // Calcula minutos hasta la salida
                $minutos_diferencia = ($salida->getTimestamp() - $horaActual->getTimestamp()) / 60;

                // Muestra info de salida programada
                echo "----------------------------------------------------------------------------------------\n";
                echo "Salida programada para el fichaje con id: {$fichaje['fichaje_id']} en el crm $crm_id: \n" . "Salida programada: " . $salida->format('H:i') . " | Hora actual: " . $horaActual->format('H:i') . " | Minutos para salida: " . floor($minutos_diferencia) . " minutos\n";
                echo "----------------------------------------------------------------------------------------\n";

                if ($minutos_diferencia <= 15 && $minutos_diferencia > 0 && $horaActual < $salida) {
                    $receptor = $fichaje["email_usuario"];
                    $usuario = $fichaje["nombre_usuario"];
                    $asunto = "Aviso de salida próxima";
                    $salidaStr = $salida->format('H:i');
                    $cuerpo = "Hola $usuario, te quedan 15 minutos para tu salida programada de las $salidaStr.";
                    $conexion->next_result();

                    $resultado = $conexion->query("CALL supercronos_g.existe_email_para_enviar_fichaje('$receptor')");
                    $resultado = $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;

                    if ($resultado && $resultado['retorno'] == 0) {

                        $conexion->next_result();
                        $conexion->query("CALL supercronos_g.insertar_email_fichajes('$correo_emisor', '$receptor', '$asunto', '$cuerpo', NULL)");
                        error_log("Se ha insertado un email para $receptor con el fichaje ID: {$fichaje['fichaje_id']}");
                    }
                }
            }
        }
    }

    $conexion->close();
} catch (\Throwable $th) {
    error_log("Error en el script: " . $th->getMessage());
}
