<?php
require_once(getcwd()  . '/vendor/PhpSpreadsheet/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

class terminalesOptimaAAPPPyme
{
    public function cargarTerminalesOptimaAAPPPyme($version): bool
    {
        $retorno = false;

        echo "Comienza la carga\n";
        echo "Catálogo de terminales Optima Pyme AAPP\n";

        $c = new Conexion();
        $c->conectar();

        // $r = $c->query("select max(version) as version from orange.or_terminales_iew_pyme");
        // $version = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]['version'] : null;

        if (!is_null($version))
            $version++;

        $plantilla = json_decode(file_get_contents(getcwd() . "/plantillas/terminales_aapp_optima.json"));
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
                $valor = $hojaActiva->getCell($columnaMarca . $i);
                if (strtoupper($valor) == "MARCA") {
                    $filaInicio = $i + 1;
                    break;
                }
            }

            for ($i = $filaInicio; $i < $nFilas; $i++) {
                $marca = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaMarca . $i)));
                $modelo = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaModelo . $i)));
                $precioCesion = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaPrecioCesion . $i)));
                $gama = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaGama . $i)));
                $captaEntradaPro = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaCaptaEntradaPro . $i)));
                $captaNormalPro = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaCaptaNormalPro . $i)));
                $captaValorPro = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaCaptaValorPro . $i)));
                $captaPremiumPro = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaCaptaPremiumPro . $i)));
                $captaTopPro = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaCaptaTopPro . $i)));
                $portaEntradaPro = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaPortaEntradaPro . $i)));
                $portaNormalPro = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaPortaNormalPro . $i)));
                $portaValorPro = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaPortaValorPro . $i)));
                $portaPremiumPro = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaPortaPremiumPro . $i)));
                $portaTopPro = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaPortaTopPro . $i)));

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
                    $r = $c->query("call or_terminales_optima_aapp_guardar({$version},'{$marca}','{$modelo}','{$gama}','{$precioCesion}',
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
