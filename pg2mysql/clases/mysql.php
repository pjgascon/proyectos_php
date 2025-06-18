<?php

class mysql_db
{
    public function crearTabla($db, $nombre, $columnas): string
    {
        if ($nombre != "users_rolepermission" && $nombre != "users_rolepermission") {
            $con = new Conexion();
            $query = "create table {$db}.{$nombre} (";

            foreach ($columnas as $item) {
                $columna = explode("|", $item);
                $tipo = $columna[1];
                $pk = ($columna[3] == "YES" ? "PRIMARY KEY" : "");
                $autoincrement = (strtolower($columna[0]) == "id" ? "AUTO_INCREMENT" : "");

                if (strtolower($tipo) == "character varying") {
                    $tipo = str_replace($tipo, "character varying", "varchar");
                } elseif (strtolower($tipo) == "jsonb") {
                    $tipo = "json";
                } elseif (strtolower($tipo) == "timestamp with time zone") {
                    $tipo = "datetime";
                } elseif (strtolower($tipo) == "timestamp with time zone") {
                    $tipo = "datetime";
                }

                if ($tipo == "varchar")
                    $tipo = "text";

                $query .= "{$columna[0]} {$tipo} {$columna[2]} {$pk} {$autoincrement},";
            }

            $query .= ")";
            $query = str_replace(',)', ')', $query);

            $con->conectar();
            $con->query($query);
            $con->close();

            echo "Copiando datos {$nombre}" . PHP_EOL;
            $this->copiarDatos($nombre, $db);
        }
        return "OK";
    }

    private function copiarDatos($nombre, $db): void
    {
        $conexion = new Conexion();
        $conexion->conectar();

        $pg = new pg();
        $datos = $pg->obtenerDatos($nombre);

        foreach ($datos as $item) {
            $query = "insert into {$db}.{$nombre} values(";
            $arrClaves = array_keys($item);

            for ($i = 0; $i < count($arrClaves); $i++) {
                $query .= "'" . addslashes($item[$arrClaves[$i]]) . "',";
            }
            $query .= ")";
            $query = str_replace(",)", ")", $query);

            $conexion->query("{$query}");
        }

        $conexion->close();
    }
}
