<?php

/***************************************************/
/* Carga de terminales pyme
/* 28/12/2022
/* Version php 7.3
/***************************************************/
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('error_reporting', E_ALL);

require_once(getcwd() . '/vendor/conexion.php');
require_once(getcwd() . '/vendor/PHPExcel.php');

$c = new Conexion();
$c->conectar();

$version = 0;
$archivo = "terminales.xls";

$r = $c->query("call php_obtener_version_terminales_residencial();");
if ($r->num_rows > 0) {
    $r = $r->fetch_all(MYSQLI_ASSOC);
    $version = $r[0]['retorno'];
    $c->next_result();
}


if (is_numeric($version) && $version > 0) {
    $version++;
    if (!file_exists($archivo)) {
        echo "ERROR: Archivo de carga no encontrado";
        return;
    }



    $objReader = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($archivo));
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($archivo);
    $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

    $sap = 2;
    $marca = 1;
    $pvp = 3;
    $inicio = 3;

    echo "Cargando Catálogo de terminales\r\n";
    $contador = $inicio;
    foreach ($objWorksheet->getRowIterator() as $row) {

        $sapCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($sap, $contador)->getValue();
        $marcarCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($marca, $contador)->getValue();
        $pvpCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($pvp, $contador)->getValue();

        $marcarCargar = addslashes(str_replace(",", ".", $marcarCargar));
        $pvpCargar = str_replace(",", ".", $pvpCargar);

        if (strlen($sapCargar) > 0) {
            $c->query("call php_nuevo_terminal_residencial({$version},'{$marcarCargar}','{$pvpCargar}','{$sapCargar}');");
            $c->next_result();
        }

        $contador++;
    }

    $objReader = null;
    $objWorksheet = null;
    $objPHPExcel = null;
    $archivo = "internet_en_tu_casa.xlsx";

    $sap = 2;
    $marca = 0;
    $modelo = 1;
    $pvp = 5;
    $inicio = 5;

    $objReader = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($archivo));
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($archivo);
    $objWorksheet = $objPHPExcel->setActiveSheetIndexByName("Internet en tu Casa");

    echo "Cargando Catálogo de Internet en casa\r\n";
    $contador = $inicio;
    foreach ($objWorksheet->getRowIterator() as $row) {
        $sapCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($sap, $contador)->getValue();
        $marcarCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($marca, $contador)->getValue();
        $modeloCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($modelo, $contador)->getValue();
        $pvpCargar = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($pvp, $contador)->getValue();

        $marcarCargar = addslashes(str_replace(",", ".", $marcarCargar) . " " . str_replace(",", ".", $modeloCargar));
        $pvpCargar = str_replace(",", ".", $pvpCargar);

        if (strlen($sapCargar) > 0) {
            $c->query("call php_nuevo_terminal_residencial({$version},'{$marcarCargar}','{$pvpCargar}','{$sapCargar}');");
            $c->next_result();
        }
        $contador++;
    }

    $c->close();

    echo "FIN\r\n";
}
