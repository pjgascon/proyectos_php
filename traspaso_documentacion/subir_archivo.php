<?php

class subirArchivo
{
    private $servidor;
    private $usuario;
    private $pwd;
    private $puerto;

    public function setServidor($value)
    {
        $this->servidor = $value;
    }

    public function setUsuario($value)
    {
        $this->usuario = $value;
    }

    public function setPwd($value)
    {
        $this->pwd = $value;
    }

    public function setPuerto($value)
    {
        $this->puerto = $value;
    }

    public function conectar(): bool
    {
        $sftpConnection = ssh2_connect($this->servidor, $this->puerto);
        return (ssh2_auth_password($sftpConnection, $this->usuario, $this->pwd)) ? true : false;
    }

    public function desconectar()
    {
        if (isset($sftpConnection))
            ssh2_disconnect($sftpConnection);
    }


    public function existeDirectorio($directorio): bool
    {
        $sftpConnection = ssh2_connect($this->servidor, $this->puerto);
        if(ssh2_auth_password($sftpConnection, $this->usuario, $this->pwd)){
            return (ssh2_exec($sftpConnection, '[ -d "' . $directorio . '" ]')) ? true : false;
        }else{
            return false;
        }        
    }
}
