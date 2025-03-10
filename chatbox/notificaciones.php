<?php

/***************************************************/
/* Envio de notificaciones push chatbox
/* 28/11/2023
/***************************************************/
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/class/pushy.php');

$c = new Conexion();
$c->conectar();

$r = $c->query("call app_chatbox_obtener_mensajes_pendientes();");
($r->num_rows > 0) ? $notificaciones = $r->fetch_all(MYSQLI_ASSOC) : $notificaciones = null;

if (!is_null($notificaciones)) {
    for ($i = 0; $i < count($notificaciones); $i++) {
        // Compruebo la disponibilidad del token
        $pushy = new pushy();
        if ($pushy->comprobarDisponibilidadToken($notificaciones[$i]["token"])) {
            // Si el terminal está online realizo el envío del mensaje
            if ($pushy->enviarNotificacion($notificaciones[$i]["token"], $notificaciones[$i]["texto"], $notificaciones[$i]["quotes_id"])) {
                // Se ha enviado el mensaje, lo marco como enviado
                //$c->next_result();
                $c->query("call app_chatbox_marcar_como_enviado(" . $notificaciones[$i]["id"] . ");");
            } else {
                "no enviado";
            }
        } else {
            echo "No disponible";
        }
    }
}
