<?php

class Permanencias
{
    public function obtenerPeticion(): array
    {
        $arr = [];
        $con = new Conexion();
        $con->conectar();

        $r = $con->query("call permanencias_seleccionar();");
        $arr = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC) : null;
        $con->close();

        return $arr;
    }
}
