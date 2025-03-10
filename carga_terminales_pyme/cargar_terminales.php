<?php
/***************************************************/
/* Carga de terminales pyme
/* 28/12/2022
/***************************************************/
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/conexionAtulado.php');
require_once(getcwd() . '/vendor/PHPExcel.php');

$c = new Conexion();
$c->conectar();

$cAtulado = new ConexionAtulado();
$cAtulado->conectar();

$version = 0;
$versionAtulado = 0;
$archivo = "terminales.xlsx";

$r = $c->query("call php_obtener_version_terminales_pyme();");
if ($r->num_rows > 0) {
    $r = $r->fetch_all(MYSQLI_ASSOC);
    $version = $r[0]['max_version'];
    $c->next_result();
}

$rAtulado = $cAtulado->query("call terminales_obtener_version();");
if ($rAtulado->num_rows > 0) {
    $rAtulado = $rAtulado->fetch_all(MYSQLI_ASSOC);
    $versionAtulado = $rAtulado[0]['terminales_version'];
    $cAtulado->next_result();
}

if(is_numeric($version) && $version > 0){
    if(!file_exists(getcwd() . "/plantillas/config.json")){
        "ERROR: Plantilla de configuración no encontrada\n\r";
        return;
    }

    if(!file_exists($archivo)){
        echo "ERROR: Archivo de carga no encontrado";
        return;
    }

    $plantilla = json_decode(file_get_contents(getcwd() . "/plantillas/config.json"));

    $objReader = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($archivo));
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($archivo);
    $objWorksheet = $objPHPExcel->setActiveSheetIndexByName($plantilla->tipoVoz->hoja);

    $sap = $plantilla->tipoVoz->comunes[0]->SAP;
    $marca = $plantilla->tipoVoz->comunes[0]->Marca;
    $modelo = $plantilla->tipoVoz->comunes[0]->Modelo;
    $vacio = $plantilla->tipoVoz->tipo[0]->Blanco;
    $alta_intermedia = $plantilla->tipoVoz->tipo[0]->Alta_Intermedia;
    $solo_voz = $plantilla->tipoVoz->tipo[0]->Solo_Voz;
    $hotel = $plantilla->tipoVoz->tipo[0]->Hotel;
    $high = $plantilla->tipoVoz->tipo[0]->High;
    $plus = $plantilla->tipoVoz->tipo[0]->Plus;
    $exclusivo = $plantilla->tipoVoz->tipo[0]->Exclusivo;
    $deals = $plantilla->tipoVoz->tipo[0]->Deals;
    $renovacion = $plantilla->tipoVoz->tipo[0]->Renovacion;
    $inicio = $plantilla->tipoVoz->fila_inicio;

    echo "Cargando gama Alta Nueva\r\n";
    $contador = $inicio;
    foreach ($objWorksheet->getRowIterator() as $row) {
        if(is_numeric($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($vacio, $contador)->getValue())){
            $sapCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($sap, $contador)->getValue();
            $marcaCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($marca, $contador)->getValue();
            $modeloCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($modelo, $contador)->getValue();
            $pvpCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($vacio, $contador)->getValue();

            $marcaCargar = str_replace(",",".",$marcaCargar);
            $marcaCargar = str_replace("'",".",$marcaCargar);
            $marcaCargar = str_replace('"','',$marcaCargar);

            $modeloCargar = str_replace(",",".",$modeloCargar);
            $modeloCargar = str_replace("'"," ",$modeloCargar);
            $modeloCargar = str_replace('"','',$modeloCargar);

            $pvpCargar = str_replace(",",".",$pvpCargar);

            $c->query("call php_nuevo_terminal_pyme('','".$sapCargar."','".addslashes($marcaCargar)."','".addslashes($modeloCargar)."','".$pvpCargar."',".$version.");");
        }
        $contador++;
    }

    echo "Cargando gama Renovacion\r\n";
    $contador = $inicio;
    foreach ($objWorksheet->getRowIterator() as $row) {
        if(is_numeric($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($renovacion, $contador)->getValue())){
            $sapCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($sap, $contador)->getValue();
            $marcaCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($marca, $contador)->getValue();
            $modeloCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($modelo, $contador)->getValue();
            $pvpCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($renovacion, $contador)->getValue();

            $marcaCargar = str_replace(",",".",$marcaCargar);
            $marcaCargar = str_replace("'",".",$marcaCargar);
            $marcaCargar = str_replace('"','',$marcaCargar);

            $modeloCargar = str_replace(",",".",$modeloCargar);
            $modeloCargar = str_replace("'"," ",$modeloCargar);
            $modeloCargar = str_replace('"','',$modeloCargar);

            $pvpCargar = str_replace(",",".",$pvpCargar);

            $cAtulado->query("call terminales_guardar('".$sapCargar."','".addslashes($marcaCargar)."','".addslashes($modeloCargar)."','".$pvpCargar."',".$versionAtulado.");");
        }
        $contador++;
    }

    echo "Cargando gama Alta Intermedia\r\n";
    $contador = $inicio;
    foreach ($objWorksheet->getRowIterator() as $row) {
        if(is_numeric($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($alta_intermedia, $contador)->getValue())){
            $sapCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($sap, $contador)->getValue();
            $marcaCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($marca, $contador)->getValue();
            $modeloCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($modelo, $contador)->getValue();
            $pvpCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($alta_intermedia, $contador)->getValue();

            $marcaCargar = str_replace(",",".",$marcaCargar);
            $marcaCargar = str_replace("'",".",$marcaCargar);
            $marcaCargar = str_replace('"','',$marcaCargar);

            $modeloCargar = str_replace(",",".",$modeloCargar);
            $modeloCargar = str_replace("'"," ",$modeloCargar);
            $modeloCargar = str_replace('"','',$modeloCargar);

            $pvpCargar = str_replace(",",".",$pvpCargar);

            $c->query("call php_nuevo_terminal_pyme('Alta Intermedia','".$sapCargar."','".addslashes($marcaCargar)."','".addslashes($modeloCargar)."','".$pvpCargar."',".$version.");");
        }
        $contador++;
    }
    
    echo "Cargando gama Solo Voz\r\n";
    $contador = $inicio;
    foreach ($objWorksheet->getRowIterator() as $row) {
        if(is_numeric($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($solo_voz, $contador)->getValue())){
            $sapCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($sap, $contador)->getValue();
            $marcaCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($marca, $contador)->getValue();
            $modeloCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($modelo, $contador)->getValue();
            $pvpCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($solo_voz, $contador)->getValue();

            $marcaCargar = str_replace(",",".",$marcaCargar);
            $marcaCargar = str_replace("'",".",$marcaCargar);
            $marcaCargar = str_replace('"','',$marcaCargar);

            $modeloCargar = str_replace(",",".",$modeloCargar);
            $modeloCargar = str_replace("'"," ",$modeloCargar);
            $modeloCargar = str_replace('"','',$modeloCargar);

            $pvpCargar = str_replace(",",".",$pvpCargar);

            $c->query("call php_nuevo_terminal_pyme('Solo Voz','".$sapCargar."','".addslashes($marcaCargar)."','".addslashes($modeloCargar)."','".$pvpCargar."',".$version.");");
        }
        $contador++;
    }

    echo "Cargando gama Hotel\r\n";
    $contador = $inicio;
    foreach ($objWorksheet->getRowIterator() as $row) {
        if(is_numeric($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($hotel, $contador)->getValue())){
            $sapCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($sap, $contador)->getValue();
            $marcaCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($marca, $contador)->getValue();
            $modeloCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($modelo, $contador)->getValue();
            $pvpCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($hotel, $contador)->getValue();

            $marcaCargar = str_replace(",",".",$marcaCargar);
            $marcaCargar = str_replace("'",".",$marcaCargar);
            $marcaCargar = str_replace('"','',$marcaCargar);

            $modeloCargar = str_replace(",",".",$modeloCargar);
            $modeloCargar = str_replace("'"," ",$modeloCargar);
            $modeloCargar = str_replace('"','',$modeloCargar);

            $pvpCargar = str_replace(",",".",$pvpCargar);

            $c->query("call php_nuevo_terminal_pyme('Hotel','".$sapCargar."','".addslashes($marcaCargar)."','".addslashes($modeloCargar)."','".$pvpCargar."',".$version.");");
        }
        $contador++;
    }

    echo "Cargando gama High\r\n";
    $contador = $inicio;
    foreach ($objWorksheet->getRowIterator() as $row) {
        if(is_numeric($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($high, $contador)->getValue())){
            $sapCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($sap, $contador)->getValue();
            $marcaCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($marca, $contador)->getValue();
            $modeloCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($modelo, $contador)->getValue();
            $pvpCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($high, $contador)->getValue();

            $marcaCargar = str_replace(",",".",$marcaCargar);
            $marcaCargar = str_replace("'",".",$marcaCargar);
            $marcaCargar = str_replace('"','',$marcaCargar);

            $modeloCargar = str_replace(",",".",$modeloCargar);
            $modeloCargar = str_replace("'"," ",$modeloCargar);
            $modeloCargar = str_replace('"','',$modeloCargar);

            $pvpCargar = str_replace(",",".",$pvpCargar);

            $c->query("call php_nuevo_terminal_pyme('High','".$sapCargar."','".addslashes($marcaCargar)."','".addslashes($modeloCargar)."','".$pvpCargar."',".$version.");");
        }
        $contador++;
    }

    echo "Cargando gama Plus\r\n";
    $contador = $inicio;
    foreach ($objWorksheet->getRowIterator() as $row) {
        if(is_numeric($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($plus, $contador)->getValue())){
            $sapCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($sap, $contador)->getValue();
            $marcaCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($marca, $contador)->getValue();
            $modeloCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($modelo, $contador)->getValue();
            $pvpCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($plus, $contador)->getValue();

            $marcaCargar = str_replace(",",".",$marcaCargar);
            $marcaCargar = str_replace("'",".",$marcaCargar);
            $marcaCargar = str_replace('"','',$marcaCargar);

            $modeloCargar = str_replace(",",".",$modeloCargar);
            $modeloCargar = str_replace("'"," ",$modeloCargar);
            $modeloCargar = str_replace('"','',$modeloCargar);

            $pvpCargar = str_replace(",",".",$pvpCargar);

            $c->query("call php_nuevo_terminal_pyme('Plus','".$sapCargar."','".addslashes($marcaCargar)."','".addslashes($modeloCargar)."','".$pvpCargar."',".$version.");");
        }
        $contador++;
    }

    echo "Cargando gama Exclusivo\r\n";
    $contador = $inicio;
    foreach ($objWorksheet->getRowIterator() as $row) {
        if(is_numeric($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($exclusivo, $contador)->getValue())){
            $sapCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($sap, $contador)->getValue();
            $marcaCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($marca, $contador)->getValue();
            $modeloCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($modelo, $contador)->getValue();
            $pvpCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($exclusivo, $contador)->getValue();

            $marcaCargar = str_replace(",",".",$marcaCargar);
            $marcaCargar = str_replace("'",".",$marcaCargar);
            $marcaCargar = str_replace('"','',$marcaCargar);

            $modeloCargar = str_replace(",",".",$modeloCargar);
            $modeloCargar = str_replace("'"," ",$modeloCargar);
            $modeloCargar = str_replace('"','',$modeloCargar);

            $pvpCargar = str_replace(",",".",$pvpCargar);

            $c->query("call php_nuevo_terminal_pyme('Exclusivo','".$sapCargar."','".addslashes($marcaCargar)."','".addslashes($modeloCargar)."','".$pvpCargar."',".$version.");");
        }
        $contador++;
    }

    echo "Cargando gama Deals\r\n";
    $contador = $inicio;
    foreach ($objWorksheet->getRowIterator() as $row) {
        if(is_numeric($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($deals, $contador)->getValue())){
            $sapCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($sap, $contador)->getValue();
            $marcaCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($marca, $contador)->getValue();
            $modeloCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($modelo, $contador)->getValue();
            $pvpCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($deals, $contador)->getValue();

            $marcaCargar = str_replace(",",".",$marcaCargar);
            $marcaCargar = str_replace("'",".",$marcaCargar);
            $marcaCargar = str_replace('"','',$marcaCargar);

            $modeloCargar = str_replace(",",".",$modeloCargar);
            $modeloCargar = str_replace("'"," ",$modeloCargar);
            $modeloCargar = str_replace('"','',$modeloCargar);

            $pvpCargar = str_replace(",",".",$pvpCargar);

            $c->query("call php_nuevo_terminal_pyme('Deals','".$sapCargar."','".$marcaCargar."','".addslashes($modeloCargar)."','".addslashes($pvpCargar)."',".$version.");");
        }
        $contador++;
    }

    echo "Cambiando a terminales de Datos\r\n";
    $objWorksheet = $objPHPExcel->setActiveSheetIndexByName($plantilla->tipoDatos->hoja);

    $sap = $plantilla->tipoDatos->comunes[0]->SAP;
    $marca = $plantilla->tipoDatos->comunes[0]->Marca;
    $modelo = $plantilla->tipoDatos->comunes[0]->Modelo;
    $moden = $plantilla->tipoDatos->tipo[0]->modem_router;
    $inicio = $plantilla->tipoDatos->fila_inicio;

    echo "Cargando gama Datos\r\n";
    $contador = $inicio;
    foreach ($objWorksheet->getRowIterator() as $row) {
        if(is_numeric($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($moden, $contador)->getValue())){
            $sapCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($sap, $contador)->getValue();
            $marcaCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($marca, $contador)->getValue();
            $modeloCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($modelo, $contador)->getValue();
            $pvpCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($moden, $contador)->getValue();

            $marcaCargar = str_replace(",",".",$marcaCargar);
            $marcaCargar = str_replace("'",".",$marcaCargar);
            $marcaCargar = str_replace('"','',$marcaCargar);

            $modeloCargar = str_replace(",",".",$modeloCargar);
            $modeloCargar = str_replace("'"," ",$modeloCargar);
            $modeloCargar = str_replace('"','',$modeloCargar);

            $pvpCargar = str_replace(",",".",$pvpCargar);

            $c->query("call php_nuevo_terminal_pyme('modem/router','".$sapCargar."','".addslashes($marcaCargar)."','".addslashes($modeloCargar)."','".$pvpCargar."',".$version.");");
        }
        $contador++;
    }

    echo "Recalculando cuotas\r\n";
    $c->query("call terminals_pyme_calcular_cuota_mes(".$version.");");

    $c->close();
    
    echo "FIN\r\n";
}
?>
