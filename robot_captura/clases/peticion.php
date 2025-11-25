<?php

class Peticion{
    public int $id;
    public string $cif;
    public int $usuario;
    public bool $origenManual;

    public function setId($value):void{
        $this->id = $value;
    }
    public function getId():int{
        return $this->id;
    }
    public function setCif($value):void{
        $this->cif = $value;
    }       
    public function getCif():string{
        return $this->cif;
    }
    public function setUsuario($value):void{
        $this->usuario = $value;
    }
    public function getUsuario():int{
        return $this->usuario;
    }  
    
    public function setOrigenManual($value):void{
        $this->origenManual = $value;
    }
    public function getOrigenManual():bool{
        return $this->origenManual;
    }
}
