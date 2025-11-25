<?php
require_once(getcwd()  . '/vendor/PhpSpreadsheet/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

class optimaPyme
{
    public function cargarOptimaPyme($version): bool
    {
        $retorno = false;

        echo "Comienza la carga\n";
        echo "Catálogo de terminales Optima Pyme\n";

        $c = new Conexion();
        $c->conectar();

        $r = $c->query("select max(version) as version from orange.or_terminales_optima");
        $version = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]['version'] : null;

        if (!is_null($version))
            $version++;
        
        $plantilla = json_decode(file_get_contents(getcwd() . "/plantillas/terminales_optima_pyme.json"));
        $hoja = $plantilla->campos->hoja;

        try {
            $spreadsheet = IOFactory::load(getcwd() . '/orange.xlsx');
            $spreadsheet->setActiveSheetIndexByName($hoja);

            $columnaMarca = $plantilla->campos->columnas[0]->marca;
            $columnaModelo = $plantilla->campos->columnas[0]->modelo;
            $columnaPrecioCesion = $plantilla->campos->columnas[0]->precio_cesion;
            $columnaGama = $plantilla->campos->columnas[0]->gama;
            $columnaDxcPvpCaptaEp = $plantilla->campos->columnas[0]->dxcpvp_capta_ep;
            $columnaDxcCaptaEn = $plantilla->campos->columnas[0]->dxcvap_capta_en;
            $columnaDxcCaptaVp = $plantilla->campos->columnas[0]->dxcpvp_capta_vp;
            $columnaDxcCaptaPp = $plantilla->campos->columnas[0]->dxcvap_capta_pp;
            $columnaDxcCaptaTp = $plantilla->campos->columnas[0]->dxcpvp_capta_tp;
            $columnaDxcPortaEp = $plantilla->campos->columnas[0]->dxcpvp_porta_ep;
            $columnaDxcPortaEn = $plantilla->campos->columnas[0]->dxcvap_porta_en;
            $columnaDxcPortaVp = $plantilla->campos->columnas[0]->dxcpvp_porta_vp;
            $columnaDxcPortaPp = $plantilla->campos->columnas[0]->dxcvap_porta_pp;
            $columnaDxcPortaTp = $plantilla->campos->columnas[0]->dxcpvp_porta_tp;

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
                $dxcPvpCaptaEp = $hojaActiva->getCellByColumnAndRow($columnaDxcPvpCaptaEp, $i);
                $dxcCaptaEn = $hojaActiva->getCellByColumnAndRow($columnaDxcCaptaEn, $i);
                $dxcCaptaVp = $hojaActiva->getCellByColumnAndRow($columnaDxcCaptaVp, $i);
                $dxcCaptaPp = $hojaActiva->getCellByColumnAndRow($columnaDxcCaptaPp, $i);
                $dxcCaptaTp = $hojaActiva->getCellByColumnAndRow($columnaDxcCaptaTp, $i);
                $dxcPortaEp = $hojaActiva->getCellByColumnAndRow($columnaDxcPortaEp, $i);
                $dxcPortaEn = $hojaActiva->getCellByColumnAndRow($columnaDxcPortaEn, $i);
                $dxcPortaVp = $hojaActiva->getCellByColumnAndRow($columnaDxcPortaVp, $i);
                $dxcPortaPp = $hojaActiva->getCellByColumnAndRow($columnaDxcPortaPp, $i);
                $dxcPortaTp = $hojaActiva->getCellByColumnAndRow($columnaDxcPortaTp, $i);

                $captaEp = (is_numeric(strval($dxcPvpCaptaEp))) ? $dxcPvpCaptaEp : -1;
                $captaNp = (is_numeric(strval($dxcCaptaEn))) ? strval($dxcCaptaEn) : -1;
                $captaVp = (is_numeric(strval($dxcCaptaVp))) ? strval($dxcCaptaVp)  : -1;
                $captaPp = (is_numeric(strval($dxcCaptaPp))) ? strval($dxcCaptaPp) : -1;
                $captaTp = (is_numeric(strval($dxcCaptaTp))) ? strval($dxcCaptaTp) : -1;

                $portaEp = (is_numeric(strval($dxcPortaEp))) ? strval($dxcPortaEp) : -1;
                $portaNp = (is_numeric(strval($dxcPortaEn))) ? strval($dxcPortaEn) : -1;
                $portaVp = (is_numeric(strval($dxcPortaVp))) ? strval($dxcPortaVp)  : -1;
                $portaPp = (is_numeric(strval($dxcPortaPp))) ? strval($dxcPortaPp) : -1;
                $portaTp = (is_numeric(strval($dxcPortaTp))) ? strval($dxcPortaTp) : -1;

                if (strlen($marca) > 0) {
                    $c->next_result();
                    $r = $c->query("call orange.or_terminales_optima_guardar({$version},'{$marca}','{$modelo}','{$gama}','{$precioCesion}',
                                '{$captaEp}','{$captaNp}','{$captaVp}','{$captaPp}','{$captaTp}',
                                '{$portaEp}','{$portaNp}','{$portaVp}','{$portaPp}','{$portaTp}');");
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
