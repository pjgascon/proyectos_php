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
            return json_encode(["error" => "No hay peticiones disponibles"]);
            exit;
        }
    }

    public function guardarPeticionAutomatica($datos): bool
    {
        $con = new Conexion();
        $con->conectar();

        if ($con->getExisteError()) {
            return false;
            exit;
        }

        $r = $con->query("call captura.permanencias_guardar('" . $datos . "');");
        return ($r->num_rows > 0) ? (bool)$r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : false;
    }

    public function guardarPeticion($id, $usuario_id, $cif, $resultado): bool
    {
        $con = new Conexion();
        $con->conectar();
        $r = $con->query("call captura.peticiones_guardar({$usuario_id},'{$cif}','{$resultado}',{$id});");
        return ($r->num_rows > 0) ? (bool) $r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : false;
    }

    public function actualizarEstadoPeticion($id): void
    {
        $con = new Conexion();
        $con->conectar();

        if ($con->getExisteError())
            exit;

        $r = $con->query("call captura.peticiones_actualizar_estado({$id});");
    }
}
