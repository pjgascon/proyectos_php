<?php
require_once(getcwd()  . '/vendor/PhpSpreadsheet/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

class terminalesIEWAAPPPyme
{
    public function cargarTerminalesIEWAAPPPyme($version): bool
    {
        $retorno = false;

        echo "Comienza la carga\n";
        echo "Catálogo de terminales IEW Pyme\n";

        $c = new Conexion();
        $c->conectar();

        $r = $c->query("select max(version) as version from orange.or_terminales_vap_iew_pyme");
        $version = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]['version'] : null;

        if (!is_null($version))
            $version++;

        $plantilla = json_decode(file_get_contents(getcwd() . "/plantillas/terminales_iew_aapp_pyme.json"));
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
            $columnaPortaEntradaPro = $plantilla->campos->columnas[0]->portaEntradaPro;
            $columnaPortaNormalPro = $plantilla->campos->columnas[0]->portaNormalPro;
            $columnaPortaValorPro = $plantilla->campos->columnas[0]->portaValorPro;
            $columnaPortaPremiumPro = $plantilla->campos->columnas[0]->portaPremiumPro;

            $filaInicio = 0;

            $hojaActiva = $spreadsheet->getActiveSheet();
            $nFilas = $hojaActiva->getHighestRow();

            for ($i = 0; $i < $nFilas; $i++) {
                $valor = $hojaActiva->getCellByColumnAndRow($columnaMarca, $i);
                if (strtoupper($valor) == "MARCA") {
                    $filaInicio = $i + 1;
                    break;
                }
            }

            for ($i = $filaInicio; $i < $nFilas; $i++) {
                $marca = Utilidades::codificarUTF(addslashes($hojaActiva->getCellByColumnAndRow($columnaMarca, $i)));
                $modelo = Utilidades::codificarUTF(addslashes($hojaActiva->getCellByColumnAndRow($columnaModelo, $i)));
                $precioCesion = $hojaActiva->getCellByColumnAndRow($columnaPrecioCesion, $i);
                $gama = Utilidades::codificarUTF(addslashes($hojaActiva->getCellByColumnAndRow($columnaGama, $i)));
                $captaEntradaPro = $hojaActiva->getCellByColumnAndRow($columnaCaptaEntradaPro, $i);
                $captaNormalPro = $hojaActiva->getCellByColumnAndRow($columnaCaptaNormalPro, $i);
                $captaValorPro = $hojaActiva->getCellByColumnAndRow($columnaCaptaValorPro, $i);
                $captaPremiumPro = $hojaActiva->getCellByColumnAndRow($columnaCaptaPremiumPro, $i);
                $portaEntradaPro = $hojaActiva->getCellByColumnAndRow($columnaPortaEntradaPro, $i);
                $portaNormalPro = $hojaActiva->getCellByColumnAndRow($columnaPortaNormalPro, $i);
                $portaValorPro = $hojaActiva->getCellByColumnAndRow($columnaPortaValorPro, $i);
                $portaPremiumPro = $hojaActiva->getCellByColumnAndRow($columnaPortaPremiumPro, $i);

                $cEntadaPro = (is_numeric(strval($captaEntradaPro))) ? strval($captaEntradaPro) : -1;
                $cNormalPro = (is_numeric(strval($captaNormalPro))) ? strval($captaNormalPro)  : -1;
                $cValorPro = (is_numeric(strval($captaValorPro))) ? strval($captaValorPro) : -1;
                $cPremiumPro = (is_numeric(strval($captaPremiumPro))) ? strval($captaPremiumPro) : -1;
                $pEntadaPro = (is_numeric(strval($portaEntradaPro))) ? strval($portaEntradaPro) : -1;
                $pNormalPro = (is_numeric(strval($portaNormalPro))) ? strval($portaNormalPro)  : -1;
                $pValorPro = (is_numeric(strval($portaValorPro))) ? strval($portaValorPro) : -1;
                $pPremiumPro = (is_numeric(strval($portaPremiumPro))) ? strval($portaPremiumPro) : -1;

                if (strlen($marca) > 0) {
                    $c->next_result();
                    $r = $c->query("call or_terminales_iew_aapp_guardar({$version},'{$marca}','{$modelo}','{$gama}','{$precioCesion}',
                                '{$cEntadaPro}','{$cNormalPro}','{$cValorPro}','{$cPremiumPro}',
                                '{$pEntadaPro}','{$pNormalPro}','{$pValorPro}','{$pPremiumPro}');");
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
