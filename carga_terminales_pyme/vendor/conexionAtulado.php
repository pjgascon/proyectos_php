<?php
class ConexionAtulado extends mysqli
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
		parent::__construct("82.223.17.193",
							"root",
							"Coral2202",
							"atulado",
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
