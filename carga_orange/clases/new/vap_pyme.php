<?php

require_once(getcwd()  . '/vendor/PhpSpreadsheet/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

function leerExcel($rutaArchivo): array
{
    // Cargar el archivo de Excel
    $spreadsheet = IOFactory::load($rutaArchivo);

    // Seleccionar la hoja "PVP VaP - Pyme"
    $hoja = $spreadsheet->getSheetByName('PVP VaP - Pyme');
    if ($hoja === null) {
        throw new Exception("La hoja 'PVP Vap - Pyme' no existe en el archivo.");
    }

    // Obtener la fila inicial (25) y el rango de columnas (B hasta V)
    $filaInicial = 25;
    $columnaInicio = 'B';
    $columnaFin = 'V';

    // Obtener el número de la última fila con datos
    $ultimaFila = $hoja->getHighestRow();

    // Recorrer las filas desde la 25 hasta la última fila con datos
    $datos = [];
    for ($fila = $filaInicial; $fila <= $ultimaFila; $fila++) {
        $filaDatos = [];
        // Recorrer las columnas de B a V
        foreach (range($columnaInicio, $columnaFin) as $columna) {
            $valor = $hoja->getCell($columna . $fila)->getValue();
            $filaDatos[$columna] = $valor;
        }
        if (strlen($filaDatos["B"]) > 0)
            $datos[] = $filaDatos;
    }

    return $datos;
}
