<?php

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InformeActivaciones
{
    public function obtenerCabecera(&$sheet, int &$linea): void
    {
        $estiloCabecera = InformeActivaciones::obtenerEstiloCabecera();
        $sheet->getStyle('A' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        // $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->setCellValue('A' . $linea, 'CLIENTE');

        $sheet->getStyle('B' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        // $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->setCellValue('B' . $linea, 'DNI');

        $sheet->getStyle('C' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        // $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->setCellValue('C' . $linea, 'TELEFONO');

        $sheet->getStyle('D' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        // $sheet->getRowDimension('1')->setRowHeight(30);

        $sheet->getStyle('E' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        // $sheet->getRowDimension('1')->setRowHeight(30);

        $sheet->getStyle('F' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        // $sheet->getRowDimension('1')->setRowHeight(30);

        $sheet->getStyle('G' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        // $sheet->getRowDimension('1')->setRowHeight(30);
        $linea++;
    }

    public function obtenerLinea(&$sheet, &$linea): void
    {
        $estiloCabecera = InformeActivaciones::obtenerEstiloLinea();
        $sheet->getStyle('A' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->setCellValue('A' . $linea, 'SERVICIO');

        $sheet->getStyle('B' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->setCellValue('B' . $linea, 'OPERADOR DONANTE');

        $sheet->getStyle('C' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->setCellValue('C' . $linea, 'ICCID antigua (movil prepago)');

        $sheet->getStyle('D' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->setCellValue('D' . $linea, 'DIRECCIÓN INSTALACION');

        $sheet->getStyle('E' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->setCellValue('E' . $linea, 'DIRECCION ENTREGA SIM');

        $sheet->getStyle('F' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->setCellValue('F' . $linea, 'TARIFA');

        $sheet->getStyle('G' . $linea)->applyFromArray($estiloCabecera);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->setCellValue('G' . $linea, 'OBSERVACIONES');
        $linea++;
    }

    public function obtenerDesplegableServicios(&$sheet, &$linea): bool
    {
        try {
            $tarifas = ['ALTA NUEVA MOVIL', 'ALTA NUEVA FIJA', 'PORTABILIDAD MOVIL', 'PORTABILIDAD FIJA', 'FIBRA', 'TERMINAL'];
            // Convierte las opciones a un formato de cadena separado por comas
            $optionsString = '"' . implode(',', $tarifas) . '"';

            // Crear una validación de datos
            $dataValidation = $sheet->getCell('A' . $linea)->getDataValidation();

            $dataValidation->setType(DataValidation::TYPE_LIST);
            $dataValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $dataValidation->setAllowBlank(false);
            $dataValidation->setShowInputMessage(true);
            $dataValidation->setShowErrorMessage(true);
            $dataValidation->setShowDropDown(true);
            $dataValidation->setFormula1($optionsString);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function obtenerDesplegableOperadorDonante(&$sheet, &$linea): bool
    {
        try {
            $operadores = ['N/A', 'MOVISTAR', 'VODAFONE', 'ORANGE', 'YOIGO', 'EUSKALTEL', 'AIRENETWORK', 'ALTECOM', 'LEMONVIL', 'TELECABLE', 'LLAMAYA_GMM', 'SIMYO', 'PEPEPHONE', 'R CABLE', 'DIGI SPAIN TELECOM', 'LYCAMOBILE', 'YOUMOBILE', 'QUATRE'];
            // Convierte las opciones a un formato de cadena separado por comas
            $optionsString = '"' . implode(',', $operadores) . '"';

            // Crear una validación de datos
            $dataValidation = $sheet->getCell('C' . $linea)->getDataValidation();

            $dataValidation->setType(DataValidation::TYPE_LIST);
            $dataValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $dataValidation->setAllowBlank(false);
            $dataValidation->setShowInputMessage(true);
            $dataValidation->setShowErrorMessage(true);
            $dataValidation->setShowDropDown(true);
            $dataValidation->setFormula1($optionsString);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private static function obtenerEstiloCabecera(): array
    {
        $styleArray = [
            'font' => [
                'bold' => true,
                'italic' => false,
                'color' => ['rgb' => '000000'],  // Color negro
                'size' => 10,
                'name' => 'Arial',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8E8E8'],  // Color gris de fondo
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => '000000'],  // Borde negro
                ],
            ]
        ];
        return $styleArray;
    }

    private static function obtenerEstiloLinea(): array
    {
        $styleArray = [
            'font' => [
                'bold' => true,
                'italic' => false,
                'color' => ['rgb' => '000000'],  // Color negro
                'size' => 11,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],  // Color amarillo de fondo
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['rgb' => '000000'],  // Borde negro
                ],
            ]
        ];
        return $styleArray;
    }

    public function clonarCabecera(&$sheet, $sourceRow, $destinationRow): void
    {
        for ($i = 0; $i < 4; $i++) {
            foreach ($sheet->getColumnIterator() as $column) {
                $cell = $column->getColumnIndex() . $sourceRow + $i;
                $destinationCell = $column->getColumnIndex() . $destinationRow + $i;

                // Clonar el valor de la celda
                $sheet->setCellValue($destinationCell, $sheet->getCell($cell)->getValue());

                // Clonar el estilo de la celda
                $sheet->duplicateStyle($sheet->getStyle($cell), $destinationCell);

                // Clonar la validación de datos, si existe
                $sourceValidation = $sheet->getCell($cell)->getDataValidation();
                if ($sourceValidation->getType() != DataValidation::TYPE_NONE) {
                    $destinationValidation = clone $sourceValidation;
                    $sheet->getCell($destinationCell)->setDataValidation($destinationValidation);
                }
            }
        }
    }

    public function listadoDeClientes($datos): array
    {
        $arrayClientes = [];
        $clienteActual = $datos[0]["cif"];
        array_push($arrayClientes, $datos[0]["cif"]);

        for ($i = 0; $i < count($datos); $i++) {
            if ($clienteActual != $datos[$i]["cif"]) {
                array_push($arrayClientes, $datos[$i]["cif"]);
                $clienteActual = $datos[$i]["cif"];
            }
        }

        return $arrayClientes;
    }
}
