<?php

class pg
{
    private $hots = "localhost";
    private $port = 5432;
    private $dbname = "db_dupol";
    private $user = "pedro";
    private $password = "87654321";

    public function obtenerTablasBD($db): array
    {
        $arrTablas = [];
        try {
            $pdo = new PDO("pgsql:host=" . $this->hots . ";port=" . $this->port . ";dbname=" . $this->dbname, $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "SELECT tablename 
                        FROM pg_catalog.pg_tables 
                        WHERE schemaname NOT IN ('pg_catalog', 'information_schema');";
            $stmt = $pdo->query($query);
            $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tablas as $tabla) {
                array_push($arrTablas, $tabla);
            }
        } catch (Exception $e) {
        }
        return $arrTablas;
    }

    public function obtenerColumnasTabla($tabla): array
    {
        $arrColumnas = [];
        try {
            $pdo = new PDO("pgsql:host=" . $this->hots . ";port=" . $this->port . ";dbname=" . $this->dbname, $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "SELECT *
                            FROM information_schema.columns
                            WHERE table_catalog  ='db_dupol' and
                            table_name = '{$tabla}'
                            ORDER BY table_name, ordinal_position;";
            $stmt = $pdo->query($query);
            $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($columnas) {
                foreach ($columnas as $columna) {
                    $t = $columna['column_name'] . "|" . $columna['data_type'];
                    if ($columna['character_maximum_length']) {
                        $t .= "(" . $columna['character_maximum_length'] . ")";
                    }
                    $t .= "|" . ($columna['is_nullable'] === 'YES' ? 'NULL' : 'NOT NULL');
                    $t .= "|" . ($columna['is_identity'] === 'YES' ? 'YES' : 'NO');

                    array_push($arrColumnas, $t);
                }
            }
        } catch (Exception $e) {
            $arrColumnas = [];
        }
        return $arrColumnas;
    }

    public function obtenerDatos($tabla): array
    {
        $arrColumnas = [];
        try {
            $pdo = new PDO("pgsql:host=" . $this->hots . ";port=" . $this->port . ";dbname=" . $this->dbname, $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "SELECT *
                            FROM {$tabla}";
            $stmt = $pdo->query($query);
            $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($columnas)
                $arrColumnas = $columnas;
        } catch (Exception $e) {
            $arrColumnas = [];
        }
        return $arrColumnas;
    }
}
