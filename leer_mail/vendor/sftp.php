<?php
class SFTP
{
    private $servidor = "maatcrm.es";
    private $puerto = 1826;
    private $usuario = "maatuser";
    private $pwd = "Coral18262202";
    private $ruta = "/var/www/html/maat/webroot/plantillas/contratos_firmados/";

    public function getServidor(): string
    {
        return $this->servidor;
    }
    public function getPuerto(): int
    {
        return $this->puerto;
    }
    public function getUsuario(): string
    {
        return $this->usuario;
    }
    public function getPwd(): string
    {
        return $this->pwd;
    }
    public function getRuta(): string
    {
        return $this->ruta;
    }
}
