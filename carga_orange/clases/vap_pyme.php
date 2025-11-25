<?php
require_once(getcwd()  . '/vendor/PhpSpreadsheet/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

class vapPyme
{
    public function cargarVapPyme($version): bool
    {
        $retorno = false;

        echo "Comienza la carga\n";
        echo "Catálogo de terminales VAP Pyme\n";

        $c = new Conexion();
        $c->conectar();

        $plantilla = json_decode(file_get_contents(getcwd() . "/plantillas/terminales_vap_pyme.json"));
        $hoja = $plantilla->campos->hoja;

        try {
            $spreadsheet = IOFactory::load(getcwd() . '/orange.xlsx');
            $spreadsheet->setActiveSheetIndexByName($hoja);

            $columnaMarca = $plantilla->campos->columnas[0]->marca;
            $columnaModelo = $plantilla->campos->columnas[0]->modelo;
            $columnaPrecioCesion = $plantilla->campos->columnas[0]->precio_cesion;
            $columnaGama = $plantilla->campos->columnas[0]->gama;
            $columnaMeses = $plantilla->campos->columnas[0]->meses;
            $columnaDxcPvpCapta = $plantilla->campos->columnas[0]->dxcpvp;
            $columnaDxcVapCapta = $plantilla->campos->columnas[0]->dxcvap;
            $columnaDxcPvpEpPorta = $plantilla->campos->columnas[0]->dxcpvp_porta_ep;
            $columnaDxcVapEpPorta = $plantilla->campos->columnas[0]->dxcvap_porta_ep;
            $columnaDxcPvpNpPorta = $plantilla->campos->columnas[0]->dxcpvp_porta_np;
            $columnaDxcVapNpPorta = $plantilla->campos->columnas[0]->dxcvap_porta_np;
            $columnaDxcPvpVpPorta = $plantilla->campos->columnas[0]->dxcpvp_porta_vp;
            $columnaDxcVapVpPorta = $plantilla->campos->columnas[0]->dxcvap_porta_vp;
            $columnaDxcPvpPpPorta = $plantilla->campos->columnas[0]->dxcpvp_porta_pp;
            $columnaDxcVapPpPorta = $plantilla->campos->columnas[0]->dxcvap_porta_pp;
            $columnaDxcPvpTpPorta = $plantilla->campos->columnas[0]->dxcpvp_porta_tp;
            $columnaDxcVapTpPorta = $plantilla->campos->columnas[0]->dxcvap_porta_tp;

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
                $meses = $hojaActiva->getCellByColumnAndRow($columnaMeses, $i);
                $dxcPvpCapta = $hojaActiva->getCellByColumnAndRow($columnaDxcPvpCapta, $i);
                $dxcVapCapta = $hojaActiva->getCellByColumnAndRow($columnaDxcVapCapta, $i);
                $dxcPvpEpPorta = $hojaActiva->getCellByColumnAndRow($columnaDxcPvpEpPorta, $i);
                $dxcVapEpPorta = $hojaActiva->getCellByColumnAndRow($columnaDxcVapEpPorta, $i);
                $dxcPvpNpPorta = $hojaActiva->getCellByColumnAndRow($columnaDxcPvpNpPorta, $i);
                $dxcVapNpPorta = $hojaActiva->getCellByColumnAndRow($columnaDxcVapNpPorta, $i);
                $dxcPvpVpPorta = $hojaActiva->getCellByColumnAndRow($columnaDxcPvpVpPorta, $i);
                $dxcVapVpPorta = $hojaActiva->getCellByColumnAndRow($columnaDxcVapVpPorta, $i);
                $dxcPvpPpPorta = $hojaActiva->getCellByColumnAndRow($columnaDxcPvpPpPorta, $i);
                $dxcVapPpPorta = $hojaActiva->getCellByColumnAndRow($columnaDxcVapPpPorta, $i);
                $dxcPvpTpPorta = $hojaActiva->getCellByColumnAndRow($columnaDxcPvpTpPorta, $i);
                $dxcVapTpPorta = $hojaActiva->getCellByColumnAndRow($columnaDxcVapTpPorta, $i);

                if (!is_numeric($meses)) $meses = -1;

                $captacion = (is_numeric($dxcPvpCapta) && is_numeric($dxcVapCapta)) ? $dxcPvpCapta + $dxcVapCapta : -1;
                $ep = (is_numeric(strval($dxcPvpEpPorta)) && is_numeric(strval($dxcVapEpPorta))) ? strval($dxcPvpEpPorta) + strval($dxcVapEpPorta) : -1;
                $np = (is_numeric(strval($dxcPvpNpPorta)) && is_numeric(strval($dxcVapNpPorta))) ? strval($dxcPvpNpPorta) + strval($dxcVapNpPorta) : -1;
                $vp = (is_numeric(strval($dxcPvpVpPorta)) && is_numeric(strval($dxcVapVpPorta))) ? strval($dxcPvpVpPorta) + strval($dxcVapVpPorta) : -1;
                $pp = (is_numeric(strval($dxcPvpPpPorta)) && is_numeric(strval($dxcVapPpPorta))) ? strval($dxcPvpPpPorta) + strval($dxcVapPpPorta) : -1;
                $tp = (is_numeric(strval($dxcPvpTpPorta)) && is_numeric(strval($dxcVapTpPorta))) ? strval($dxcPvpTpPorta) + strval($dxcVapTpPorta) : -1;

                if (strlen($marca) > 0) {
                    $c->next_result();
                    $r = $c->query("call or_terminales_vap_pyme_guardar({$version},'{$marca}','{$modelo}','{$gama}','{$precioCesion}',{$meses},
                                '{$captacion}','{$ep}','{$np}','{$vp}','{$pp}','{$tp}');");
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
