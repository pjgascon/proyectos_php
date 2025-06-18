<?php
require_once(getcwd()  . '/vendor/PhpSpreadsheet/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

class terminalesOptima36Pyme
{
    public function cargarTerminalesOptima36Pyme($version): bool
    {
        $retorno = false;

        echo "Comienza la carga\n";
        echo "Catálogo de terminales Optima Pyme 36 meses\n";

        $c = new Conexion();
        $c->conectar();

        $r = $c->query("select max(version) as version from orange.or_terminales_optima_36");
        $version = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]['version'] : null;

        if (!is_null($version))
            $version++;

        $plantilla = json_decode(file_get_contents(getcwd() . "/plantillas/terminales_optima_36_pyme.json"));
        $hoja = $plantilla->campos->hoja;

        try {
            $spreadsheet = IOFactory::load(getcwd() . '/orange.xlsx');
            $spreadsheet->setActiveSheetIndexByName($hoja);

            $columnaMarca = $plantilla->campos->columnas[0]->marca;
            $columnaModelo = $plantilla->campos->columnas[0]->modelo;
            $columnaPrecioCesion = $plantilla->campos->columnas[0]->precio_cesion;
            $columnaGama = $plantilla->campos->columnas[0]->gama;

            $columnaCaptaEntradaPro = $plantilla->campos->columnas[0]->captaEntradaPro;
            $columnaCaptaNormalPro = $plantilla->campos->columnas[0]->captaNormalPro;
            $columnaCaptaValorPro = $plantilla->campos->columnas[0]->captaValorPro;
            $columnaCaptaPremiumPro = $plantilla->campos->columnas[0]->captaPremiumPro;
            $columnaCaptaTopPro = $plantilla->campos->columnas[0]->captaTopPro;
            $columnaPortaEntradaPro = $plantilla->campos->columnas[0]->portaEntradaPro;
            $columnaPortaNormalPro = $plantilla->campos->columnas[0]->portaNormalPro;
            $columnaPortaValorPro = $plantilla->campos->columnas[0]->portaValorPro;
            $columnaPortaPremiumPro = $plantilla->campos->columnas[0]->portaPremiumPro;
            $columnaPortaTopPro = $plantilla->campos->columnas[0]->portaTopPro;

            $filaInicio = 0;

            $hojaActiva = $spreadsheet->getActiveSheet();
            $nFilas = $hojaActiva->getHighestRow();

            for ($i = 0; $i < $nFilas; $i++) {
                $valor = $hojaActiva->getCell($columnaMarca. $i);
                if (strtoupper($valor) == "MARCA") {
                    $filaInicio = $i + 1;
                    break;
                }
            }

            for ($i = $filaInicio; $i < $nFilas; $i++) {
                $marca = $hojaActiva->getCell($columnaMarca. $i);
                $modelo = $hojaActiva->getCell($columnaModelo. $i);
                $precioCesion = $hojaActiva->getCell($columnaPrecioCesion. $i);
                $gama = $hojaActiva->getCell($columnaGama. $i);
                $captaEntradaPro = $hojaActiva->getCell($columnaCaptaEntradaPro. $i);
                $captaNormalPro = $hojaActiva->getCell($columnaCaptaNormalPro. $i);
                $captaValorPro = $hojaActiva->getCell($columnaCaptaValorPro. $i);
                $captaPremiumPro = $hojaActiva->getCell($columnaCaptaPremiumPro. $i);
                $captaTopPro = $hojaActiva->getCell($columnaCaptaTopPro. $i);
                $portaEntradaPro = $hojaActiva->getCell($columnaPortaEntradaPro. $i);
                $portaNormalPro = $hojaActiva->getCell($columnaPortaNormalPro. $i);
                $portaValorPro = $hojaActiva->getCell($columnaPortaValorPro. $i);
                $portaPremiumPro = $hojaActiva->getCell($columnaPortaPremiumPro. $i);
                $portaTopPro = $hojaActiva->getCell($columnaPortaTopPro. $i);

                $cEntadaPro = (is_numeric(strval($captaEntradaPro))) ? strval($captaEntradaPro) : -1;
                $cNormalPro = (is_numeric(strval($captaNormalPro))) ? strval($captaNormalPro)  : -1;
                $cValorPro = (is_numeric(strval($captaValorPro))) ? strval($captaValorPro) : -1;
                $cPremiumPro = (is_numeric(strval($captaPremiumPro))) ? strval($captaPremiumPro) : -1;
                $cTopPro = (is_numeric(strval($captaTopPro))) ? strval($captaTopPro)  : -1;
                $pEntadaPro = (is_numeric(strval($portaEntradaPro))) ? strval($portaEntradaPro) : -1;
                $pNormalPro = (is_numeric(strval($portaNormalPro))) ? strval($portaNormalPro)  : -1;
                $pValorPro = (is_numeric(strval($portaValorPro))) ? strval($portaValorPro) : -1;
                $pPremiumPro = (is_numeric(strval($portaPremiumPro))) ? strval($portaPremiumPro) : -1;
                $pTopPro = (is_numeric(strval($portaTopPro))) ? strval($portaTopPro)  : -1;

                if (strlen($marca) > 0) {
                    $c->next_result();
                    $r = $c->query("call or_terminales_optima_36_guardar({$version},'{$marca}','{$modelo}','{$gama}','{$precioCesion}',
                                '{$cEntadaPro}','{$cNormalPro}','{$cValorPro}','{$cPremiumPro}','{$cTopPro}',
                                '{$pEntadaPro}','{$pNormalPro}','{$pValorPro}','{$pPremiumPro}','{$pTopPro}');");
                } else {
                    break;
                }
                $retorno = true;
            }
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            echo 'Error al leer el archivo: ', $e->getMessage();
            $retorno = false;
        }

        return $retorno;
    }
}
