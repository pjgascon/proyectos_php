<?php
require_once(getcwd()  . '/vendor/PhpSpreadsheet/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

class vapIewPyme
{
    public function cargarVapIewPyme($version): bool
    {
        $retorno = false;

        echo "Comienza la carga\n";
        echo "Catálogo de terminales VAP IEW Pyme\n";

        $c = new Conexion();
        $c->conectar();

        $plantilla = json_decode(file_get_contents(getcwd() . "/plantillas/terminales_iew_vap_pyme.json"));
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
                $meses = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaMeses . $i)));
                $dxcPvpCapta = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaDxcPvpCapta . $i)));
                $dxcVapCapta = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaDxcVapCapta . $i)));
                $dxcPvpEpPorta = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaDxcPvpEpPorta . $i)));
                $dxcVapEpPorta = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaDxcVapEpPorta . $i)));
                $dxcPvpNpPorta = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaDxcPvpNpPorta . $i)));
                $dxcVapNpPorta = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaDxcVapNpPorta . $i)));
                $dxcPvpVpPorta = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaDxcPvpVpPorta . $i)));
                $dxcVapVpPorta = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaDxcVapVpPorta . $i)));
                $dxcPvpPpPorta = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaDxcPvpPpPorta . $i)));
                $dxcVapPpPorta = Utilidades::codificarUTF(addslashes($hojaActiva->getCell($columnaDxcVapPpPorta . $i)));

                if (!is_numeric($meses)) $meses = -1;

                $captacion = (is_numeric($dxcPvpCapta) && is_numeric($dxcVapCapta)) ? $dxcPvpCapta + $dxcVapCapta : -1;
                $ep = (is_numeric(strval($dxcPvpEpPorta)) && is_numeric(strval($dxcVapEpPorta))) ? strval($dxcPvpEpPorta) + strval($dxcVapEpPorta) : -1;
                $np = (is_numeric(strval($dxcPvpNpPorta)) && is_numeric(strval($dxcVapNpPorta))) ? strval($dxcPvpNpPorta) + strval($dxcVapNpPorta) : -1;
                $vp = (is_numeric(strval($dxcPvpVpPorta)) && is_numeric(strval($dxcVapVpPorta))) ? strval($dxcPvpVpPorta) + strval($dxcVapVpPorta) : -1;
                $pp = (is_numeric(strval($dxcPvpPpPorta)) && is_numeric(strval($dxcVapPpPorta))) ? strval($dxcPvpPpPorta) + strval($dxcVapPpPorta) : -1;

                if (strlen($marca) > 0) {
                    $c->next_result();
                    $r = $c->query("call or_terminales_vap_iew_pyme_guardar({$version},'{$marca}','{$modelo}','{$gama}','{$precioCesion}',{$meses},
                                '{$precioCesion}','{$captacion}','{$ep}','{$np}','{$vp}');");
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
