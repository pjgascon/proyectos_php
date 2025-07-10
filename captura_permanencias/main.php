
<?php
require_once("vendor/conexion.php");
require_once("clases/permanencias.php");

$objPermanencias = new Permanencias();

$arr = $objPermanencias->obtenerPeticion();

if (!is_null($arr)) {
}
