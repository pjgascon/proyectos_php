<?php

class Conexion extends mysqli
{
	private $servidor;
	private $puerto;
	private $usuario;
	private $bd;
	private $pwd;
	private $conn;
	private $exiteError;
	private $errorMensaje;	
	
	public function conectar()
	{	
		parent::__construct("distribuidoresserver.liberi.es",
							"root",
							"Coral18262202",
							"temporal",
							3306);

		if($this->connect_errno){
			$this->exiteError = true;
			$this->errorMensaje = $this->connect_error;
		}else{
			$this->exiteError = false;
			$this->errorMensaje = '';
		}
	}
}
