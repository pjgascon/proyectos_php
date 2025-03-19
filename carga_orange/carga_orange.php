<?php

/***************************************************/
/* Carga de terminales pyme
/* 28/12/2024
/***************************************************/
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/util.php');

require_once(getcwd() . '/clases/vap_pyme.php');
require_once(getcwd() . '/clases/vap_iew_pyme.php');
require_once(getcwd() . '/clases/optima_pyme.php');
require_once(getcwd() . '/clases/optima_36_pyme.php');
require_once(getcwd() . '/clases/optima_aapp_pyme.php');
require_once(getcwd() . '/clases/sp_pyme.php');
require_once(getcwd() . '/clases/sp_aapp_pyme.php');
require_once(getcwd() . '/clases/iew_pyme.php');
require_once(getcwd() . '/clases/iew_appp_pyme.php');
require_once(getcwd() . '/clases/marco_retributivo.php');

$c = new Conexion();
$c->conectar();

$r = $c->query("call or_obtener_version();");
$version = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : null;

if (!is_null($version)) {
    // Carga de terminales venta a plazos pyme
    // $vapPyme = new vapPyme();
    // $b = $vapPyme->cargarVapPyme($version);

    // if ($b) {
    //     echo "Finalizado\n";
    // } else {
    //     echo "Se han producido errores al procesar el catálogo de venta a plazos pyme\n";
    // }

    // $vapIewPyme = new optimaPyme();
    // $b = $vapIewPyme->cargarOptimaPyme($version);

//    $cargaOptima36 = new terminalesOptima36Pyme();
//     $b = $cargaOptima36->cargarTerminalesOptima36Pyme($version);

    // $cargaOptimaAAPP = new terminalesOptimaAAPPPyme();
    // $b = $cargaOptimaAAPP->cargarTerminalesOptimaAAPPPyme($version);

//     $cargaSP = new terminalesSolucionPersonalizadayme();
//     $b = $cargaSP->cargarTerminaleSpPyme($version);

//     $cargaSPAAPP = new terminalesSolucionPersonalizadaAAPPPyme();
//     $b = $cargaSPAAPP->cargarTerminalesSPAAPPPyme($version);

    // $cargaIew = new terminalesIEWPyme();
    // $b = $cargaIew->cargarTerminalesIEWPyme($version);

//     $cargaIEWAAPP = new terminalesIEWAAPPPyme();
//     $b = $cargaIEWAAPP->cargarTerminalesIEWAAPPPyme($version);

    $cargarMarco = new marcoRetributivo();
    $cargarMarco->cargarMarco($vesion);
}
