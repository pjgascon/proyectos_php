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
		parent::__construct("maat.local",
							"root",
							"Coral18262202",
							"maat",
							3307);

		if($this->connect_errno){
			$this->exiteError = true;
			$this->errorMensaje = $this->connect_error;
		}else{
			$this->exiteError = false;
			$this->errorMensaje = '';
		}
	}
}
