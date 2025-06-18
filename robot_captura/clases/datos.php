<?php
require_once(getcwd() . "/vendor/conexion.php");
require_once(getcwd() . "/clases/peticion.php");

class DatosCaptura
{
    public function obtenerPeticion(): string
    {
        $con = new Conexion();
        $con->conectar();

        if ($con->getExisteError()) {
            return json_encode(["error" => "Error al conectar a la base de datos"]);
            exit;
        }

        $r = $con->query("call captura.peticiones_pool_seleccionar();");
        $peticiones = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC) : null;

        if (!is_null($peticiones)) {
            $peticion = new Peticion();
            $peticion->setId(intval($peticiones[0]["id"]));
            $peticion->setCif($peticiones[0]["cif"]);
            $peticion->setUsuario(intval($peticiones[0]["usuario_id"]));
            $peticion->setOrigenManual(boolval($peticiones[0]["origen_manual"]));
            return json_encode(["resultado" => $peticion]);
            exit;
        } else {
            // Envio el correo de aviso de finalización de datos disponibles
            $this->enviarAlerta('Finalización datos disponibles', 'Se han acabado los datos disponibles para procesar snoop');
            return json_encode(["error" => "No hay peticiones disponibles"]);
            exit;
        }
    }

    public function guardarPeticionAutomatica($datos, $id): bool
    {
        $con = new Conexion();
        $con->conectar();

        if ($con->getExisteError()) {
            return false;
            exit;
        }

        try {
            $r = $con->query("call captura.permanencias_guardar('" . $datos . "');");
            return ($r->num_rows > 0) ? (bool)$r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : false;
        } catch (Exception $e) {
            $con1 = new Conexion();
            $con1->conectar();
            $con1->query("update captura.peticiones_pool set estado = 1 where id = {$id};");
            $con1->close();
            return false;
        }
    }

    public function guardarPeticion($id, $usuario_id, $cif, $resultado): bool
    {
        try {
            $con = new Conexion();
            $con->conectar();
            if (!$con->getExisteError()) {
                $r = $con->query("call captura.peticiones_guardar({$usuario_id},'{$cif}','{$resultado}',{$id});");
                return ($r->num_rows > 0) ? (bool) $r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : false;
            } else {
                return false;
            }
        } catch (Exception $e) {
            $con1 = new Conexion();
            $con1->conectar();
            $con1->query("update captura.peticiones_pool set estado = 1 where id = {$id};");
            $con1->close();
            return false;
        }
    }

    public function actualizarEstadoPeticion($id, $estado): void
    {
        $con = new Conexion();
        $con->conectar();

        if ($con->getExisteError())
            exit;

        $r = $con->query("call captura.peticiones_actualizar_estado({$id},{$estado});");
    }

    public function enviarAlerta($asunto, $texto): void
    {
        $con = new Conexion();
        $con->conectar();

        $fecha = date("Y-m-d H:i:s");
        $query = "insert into mails.emails values(NULL,0,'{$fecha}',NULL,'notificaciones@liberi.es','pedrojose.gascon@waspapp.es','{$asunto}','{$texto}','0');";
        $con->query($query);

        $con->close();
    }
}
