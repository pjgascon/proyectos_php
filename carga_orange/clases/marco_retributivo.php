<?php
require_once(getcwd()  . '/vendor/PhpSpreadsheet/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

class marcoRetributivo
{
    public function cargarMarco()
    {
        echo "Comienza la carga\n";
        echo "Marco retributivo\n";

        $c = new Conexion();
        $c->conectar();

        // Obtengo la última versión
        $r = $c->query("call or_obtener_version_marco_retributivo();");
        $version = ($r->num_rows > 0) ? $r->fetch_all(MYSQLI_ASSOC)[0]["retorno"] : null;

        if (!is_null($version)) {
            $plantilla = json_decode(file_get_contents(getcwd() . "/plantillas/marco_retributivo.json"));
            $hoja = $plantilla->campos->hoja;

            // Comienzo con la lectura de las tarifas OPTIMAS
            $inicioOptima = $plantilla->campos->tarifas[0]->optima[0]->fila;
            $modulo = $plantilla->campos->tarifas[0]->optima[0]->modulo;
            $paquete_voz_datos_20 = $plantilla->campos->tarifas[0]->optima[0]->paquete_voz_datos_20;
            $paquete_voz_datos_40 = $plantilla->campos->tarifas[0]->optima[0]->paquete_voz_datos_40;
            $paquete_voz_datos_mas_40 = $plantilla->campos->tarifas[0]->optima[0]->paquete_voz_datos_mas_40;
            $captacion = $plantilla->campos->tarifas[0]->optima[0]->captacion;
            $portabilidad = $plantilla->campos->tarifas[0]->optima[0]->portabilidad;
            $extracomision_terminal = $plantilla->campos->tarifas[0]->optima[0]->extracomision_terminal;
            $puntos = $plantilla->campos->tarifas[0]->optima[0]->puntos;
            $extracomision_estrategica = $plantilla->campos->tarifas[0]->optima[0]->extracomision_estrategica;
            $extracomision_terminal_36 = $plantilla->campos->tarifas[0]->optima[0]->extracomision_terminal_36;

            try {
                $spreadsheet = IOFactory::load(getcwd() . '/orange.xlsx');
                $spreadsheet->setActiveSheetIndexByName($hoja);

                $hojaActiva = $spreadsheet->getActiveSheet();
                $nFilas = $hojaActiva->getHighestRow();

                for ($i = $inicioOptima; $i < $nFilas; $i++) {
                    $vModulo = $hojaActiva->getCellByColumnAndRow($modulo, $i);

                    if (strlen(strval($vModulo)) > 0) {
                        $vPaquete_voz_datos_20 = $hojaActiva->getCellByColumnAndRow($paquete_voz_datos_20, $i);
                        $vPaquete_voz_datos_40 = $hojaActiva->getCellByColumnAndRow($paquete_voz_datos_40, $i);
                        $vPaquete_voz_datos_mas_40 = $hojaActiva->getCellByColumnAndRow($paquete_voz_datos_mas_40, $i);
                        $vCaptacion = $hojaActiva->getCellByColumnAndRow($captacion, $i);
                        $vPortabilidad = $hojaActiva->getCellByColumnAndRow($portabilidad, $i);
                        $vExtracomision_terminal = $hojaActiva->getCellByColumnAndRow($extracomision_terminal, $i);
                        $vPuntos = $hojaActiva->getCellByColumnAndRow($puntos, $i);
                        $vExtracomision_estrategica = $hojaActiva->getCellByColumnAndRow($extracomision_estrategica, $i);
                        $vExtracomision_terminal_36 = $hojaActiva->getCellByColumnAndRow($extracomision_terminal_36, $i);

                        $vPaquete_voz_datos_20 = (!is_numeric(strval($vPaquete_voz_datos_20))) ? 0 : strval($vPaquete_voz_datos_20);
                        $vPaquete_voz_datos_40 = (!is_numeric(strval($vPaquete_voz_datos_40))) ? 0 : strval($vPaquete_voz_datos_40);
                        $vPaquete_voz_datos_mas_40 = (!is_numeric(strval($vPaquete_voz_datos_mas_40))) ? 0 : strval($vPaquete_voz_datos_mas_40);
                        $vCaptacion = (!is_numeric(strval($vCaptacion))) ? 0 : strval($vCaptacion);
                        $vPortabilidad = (!is_numeric(strval($vPortabilidad))) ? 0 : strval($vPortabilidad);
                        $vExtracomision_terminal = (!is_numeric(strval($vExtracomision_terminal))) ? 0 : strval($vExtracomision_terminal);
                        $vPuntos = (!is_numeric(strval($vPuntos))) ? 0 : strval($vPuntos);
                        $vExtracomision_estrategica = (!is_numeric(strval($vExtracomision_estrategica))) ? 0 : strval($vExtracomision_estrategica);
                        $vExtracomision_terminal_36 = (!is_numeric(strval($vExtracomision_terminal_36))) ? 0 : strval($vExtracomision_terminal_36);
                        $vAlta = 0;

                        $c->next_result();
                        $r = $c->query("call or_marco_retributivo_cargar({$version},'OPTIMA','{$vModulo}','{$vPaquete_voz_datos_20}',
                                        '{$vPaquete_voz_datos_40}','{$vPaquete_voz_datos_mas_40}','{$vCaptacion}','{$vPortabilidad}',
                                        '{$vExtracomision_terminal}','{$vPuntos}','{$vExtracomision_estrategica}','{$vExtracomision_terminal_36}',
                                        '{$vAlta}');");
                    } else {
                        break;
                    }
                }
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                echo 'Error al leer el archivo: ', $e->getMessage();
            }

            // SOLUCION PERSONALIZADA
            $inicioSolucionPersonalizada = $plantilla->campos->tarifas[0]->solucion_personalizada[0]->fila;
            $modulo = $plantilla->campos->tarifas[0]->solucion_personalizada[0]->modulo;
            $paquete_voz_datos_20 = $plantilla->campos->tarifas[0]->solucion_personalizada[0]->paquete_voz_datos_20;
            $paquete_voz_datos_40 = $plantilla->campos->tarifas[0]->solucion_personalizada[0]->paquete_voz_datos_40;
            $paquete_voz_datos_mas_40 = $plantilla->campos->tarifas[0]->solucion_personalizada[0]->paquete_voz_datos_mas_40;
            $captacion = $plantilla->campos->tarifas[0]->solucion_personalizada[0]->captacion;
            $portabilidad = $plantilla->campos->tarifas[0]->solucion_personalizada[0]->portabilidad;
            $extracomision_terminal = $plantilla->campos->tarifas[0]->solucion_personalizada[0]->extracomision_terminal;
            $puntos = $plantilla->campos->tarifas[0]->solucion_personalizada[0]->puntos;
            $extracomision_estrategica = $plantilla->campos->tarifas[0]->solucion_personalizada[0]->extracomision_estrategica;
            $extracomision_terminal_36 = $plantilla->campos->tarifas[0]->solucion_personalizada[0]->extracomision_terminal_36;

            try {
                $spreadsheet = IOFactory::load(getcwd() . '/orange.xlsx');
                $spreadsheet->setActiveSheetIndexByName($hoja);

                $hojaActiva = $spreadsheet->getActiveSheet();
                $nFilas = $hojaActiva->getHighestRow();

                for ($i = $inicioSolucionPersonalizada; $i < $nFilas; $i++) {
                    $vModulo = $hojaActiva->getCellByColumnAndRow($modulo, $i);

                    if (strlen(strval($vModulo)) > 0) {
                        $vPaquete_voz_datos_20 = $hojaActiva->getCellByColumnAndRow($paquete_voz_datos_20, $i);
                        $vPaquete_voz_datos_40 = $hojaActiva->getCellByColumnAndRow($paquete_voz_datos_40, $i);
                        $vPaquete_voz_datos_mas_40 = $hojaActiva->getCellByColumnAndRow($paquete_voz_datos_mas_40, $i);
                        $vCaptacion = $hojaActiva->getCellByColumnAndRow($captacion, $i);
                        $vPortabilidad = $hojaActiva->getCellByColumnAndRow($portabilidad, $i);
                        $vExtracomision_terminal = $hojaActiva->getCellByColumnAndRow($extracomision_terminal, $i);
                        $vPuntos = $hojaActiva->getCellByColumnAndRow($puntos, $i);
                        $vExtracomision_estrategica = $hojaActiva->getCellByColumnAndRow($extracomision_estrategica, $i);
                        $vExtracomision_terminal_36 = $hojaActiva->getCellByColumnAndRow($extracomision_terminal_36, $i);

                        $vPaquete_voz_datos_20 = (!is_numeric(strval($vPaquete_voz_datos_20))) ? 0 : strval($vPaquete_voz_datos_20);
                        $vPaquete_voz_datos_40 = (!is_numeric(strval($vPaquete_voz_datos_40))) ? 0 : strval($vPaquete_voz_datos_40);
                        $vPaquete_voz_datos_mas_40 = (!is_numeric(strval($vPaquete_voz_datos_mas_40))) ? 0 : strval($vPaquete_voz_datos_mas_40);
                        $vCaptacion = (!is_numeric(strval($vCaptacion))) ? 0 : strval($vCaptacion);
                        $vPortabilidad = (!is_numeric(strval($vPortabilidad))) ? 0 : strval($vPortabilidad);
                        $vExtracomision_terminal = (!is_numeric(strval($vExtracomision_terminal))) ? 0 : strval($vExtracomision_terminal);
                        $vPuntos = (!is_numeric(strval($vPuntos))) ? 0 : strval($vPuntos);
                        $vExtracomision_estrategica = (!is_numeric(strval($vExtracomision_estrategica))) ? 0 : strval($vExtracomision_estrategica);
                        $vExtracomision_terminal_36 = (!is_numeric(strval($vExtracomision_terminal_36))) ? 0 : strval($vExtracomision_terminal_36);
                        $vAlta = 0;

                        $c->next_result();
                        $r = $c->query("call or_marco_retributivo_cargar({$version},'SOLUCION PERSONALIZADA','{$vModulo}','{$vPaquete_voz_datos_20}',
                                        '{$vPaquete_voz_datos_40}','{$vPaquete_voz_datos_mas_40}','{$vCaptacion}','{$vPortabilidad}',
                                        '{$vExtracomision_terminal}','{$vPuntos}','{$vExtracomision_estrategica}','{$vExtracomision_terminal_36}',
                                        '{$vAlta}');");
                    } else {
                        break;
                    }
                }
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                echo 'Error al leer el archivo: ', $e->getMessage();
            }


            // IEW
            $inicioIEW = $plantilla->campos->tarifas[0]->iew[0]->fila;
            $modulo = $plantilla->campos->tarifas[0]->iew[0]->modulo;
            $alta = $plantilla->campos->tarifas[0]->iew[0]->alta;
            $puntos = $plantilla->campos->tarifas[0]->iew[0]->puntos;
            $extracomision_estrategica = $plantilla->campos->tarifas[0]->iew[0]->extracomision_estrategica;

            try {
                $spreadsheet = IOFactory::load(getcwd() . '/orange.xlsx');
                $spreadsheet->setActiveSheetIndexByName($hoja);

                $hojaActiva = $spreadsheet->getActiveSheet();
                $nFilas = $hojaActiva->getHighestRow();

                for ($i = $inicioIEW; $i < $nFilas; $i++) {
                    $vModulo = $hojaActiva->getCellByColumnAndRow($modulo, $i);

                    if (strlen(strval($vModulo)) > 0) {
                        $vAlta = $hojaActiva->getCellByColumnAndRow($alta, $i);
                        $vPuntos = $hojaActiva->getCellByColumnAndRow($puntos, $i);
                        // $vExtracomision_estrategica = $hojaActiva->getCellByColumnAndRow($extracomision_estrategica, $i);
                        $vExtracomision_estrategica = $hojaActiva->getCellByColumnAndRow($extracomision_estrategica, $i);

                        if ($vExtracomision_estrategica->isFormula()) {
                            $vExtracomision_estrategica = $vExtracomision_estrategica->getCalculatedValue();
                        } else {
                            $vExtracomision_estrategica = $vExtracomision_estrategica->getValue();
                        }
                        
                        $vExtracomision_estrategica = str_replace("€", "", $vExtracomision_estrategica);

                        $vPaquete_voz_datos_20 = 0;
                        $vPaquete_voz_datos_40 = 0;
                        $vPaquete_voz_datos_mas_40 = 0;
                        $vCaptacion = 0;
                        $vPortabilidad = 0;
                        $vExtracomision_terminal = 0;
                        $vPuntos = (!is_numeric(strval($vPuntos))) ? 0 : strval($vPuntos);
                        $vExtracomision_estrategica = (!is_numeric(strval($vExtracomision_estrategica))) ? 0 : strval($vExtracomision_estrategica);
                        $vExtracomision_terminal_36 = 0;
                        $vAlta = (!is_numeric(strval($vAlta))) ? 0 : strval($vAlta);

                        $c->next_result();
                        $r = $c->query("call or_marco_retributivo_cargar({$version},'','{$vModulo}','{$vPaquete_voz_datos_20}',
                                        '{$vPaquete_voz_datos_40}','{$vPaquete_voz_datos_mas_40}','{$vCaptacion}','{$vPortabilidad}',
                                        '{$vExtracomision_terminal}','{$vPuntos}','{$vExtracomision_estrategica}','{$vExtracomision_terminal_36}',
                                        '{$vAlta}');");
                    } else {
                        break;
                    }
                }
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                echo 'Error al leer el archivo: ', $e->getMessage();
            }

            // CONECTA PYMES
            $inicioCPymes = $plantilla->campos->tarifas[0]->conecta_pymes[0]->fila;
            $modulo = $plantilla->campos->tarifas[0]->conecta_pymes[0]->modulo;
            $alta = $plantilla->campos->tarifas[0]->conecta_pymes[0]->alta;
            $puntos = $plantilla->campos->tarifas[0]->conecta_pymes[0]->puntos;
            $extracomision_estrategica = $plantilla->campos->tarifas[0]->conecta_pymes[0]->extracomision_estrategica;

            try {
                $spreadsheet = IOFactory::load(getcwd() . '/orange.xlsx');
                $spreadsheet->setActiveSheetIndexByName($hoja);

                $hojaActiva = $spreadsheet->getActiveSheet();
                $nFilas = $hojaActiva->getHighestRow();

                for ($i = $inicioCPymes; $i < $nFilas; $i++) {
                    $vModulo = $hojaActiva->getCellByColumnAndRow($modulo, $i);

                    if (strlen(strval($vModulo)) > 0) {
                        $vAlta = $hojaActiva->getCellByColumnAndRow($alta, $i);
                        $vPuntos = $hojaActiva->getCellByColumnAndRow($puntos, $i);
                        $vExtracomision_estrategica = $hojaActiva->getCellByColumnAndRow($extracomision_estrategica, $i);
                        if ($vExtracomision_estrategica->isFormula()) {
                            $vExtracomision_estrategica = $vExtracomision_estrategica->getCalculatedValue();
                        } else {
                            $vExtracomision_estrategica = $vExtracomision_estrategica->getValue();
                        }
                        
                        $vExtracomision_estrategica = str_replace("€", "", $vExtracomision_estrategica);

                        $vPaquete_voz_datos_20 = 0;
                        $vPaquete_voz_datos_40 = 0;
                        $vPaquete_voz_datos_mas_40 = 0;
                        $vCaptacion = 0;
                        $vPortabilidad = 0;
                        $vExtracomision_terminal = 0;
                        $vPuntos = (!is_numeric(strval($vPuntos))) ? 0 : strval($vPuntos);
                        $vExtracomision_estrategica = (!is_numeric(strval($vExtracomision_estrategica))) ? 0 : strval($vExtracomision_estrategica);
                        $vExtracomision_terminal_36 = 0;
                        $vAlta = (!is_numeric(strval($vAlta))) ? 0 : strval($vAlta);

                        $c->next_result();
                        $r = $c->query("call or_marco_retributivo_cargar({$version},'','{$vModulo}','{$vPaquete_voz_datos_20}',
                                        '{$vPaquete_voz_datos_40}','{$vPaquete_voz_datos_mas_40}','{$vCaptacion}','{$vPortabilidad}',
                                        '{$vExtracomision_terminal}','{$vPuntos}','{$vExtracomision_estrategica}','{$vExtracomision_terminal_36}',
                                        '{$vAlta}');");
                    } else {
                        break;
                    }
                }
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                echo 'Error al leer el archivo: ', $e->getMessage();
            }

            // CONECTA PYMES CENTRALITA
            $inicioCPymesCentralita = $plantilla->campos->tarifas[0]->conecta_pymes_centralita[0]->fila;
            $modulo = $plantilla->campos->tarifas[0]->conecta_pymes_centralita[0]->modulo;
            $alta = $plantilla->campos->tarifas[0]->conecta_pymes_centralita[0]->alta;
            $puntos = $plantilla->campos->tarifas[0]->conecta_pymes_centralita[0]->puntos;
            $extracomision_estrategica = $plantilla->campos->tarifas[0]->conecta_pymes_centralita[0]->extracomision_estrategica;

            try {
                $spreadsheet = IOFactory::load(getcwd() . '/orange.xlsx');
                $spreadsheet->setActiveSheetIndexByName($hoja);

                $hojaActiva = $spreadsheet->getActiveSheet();
                $nFilas = $hojaActiva->getHighestRow();

                for ($i = $inicioCPymesCentralita; $i < $nFilas; $i++) {
                    $vModulo = $hojaActiva->getCellByColumnAndRow($modulo, $i);

                    if (strlen(strval($vModulo)) > 0) {
                        $vAlta = $hojaActiva->getCellByColumnAndRow($alta, $i);
                        $vPuntos = $hojaActiva->getCellByColumnAndRow($puntos, $i);
                        $vExtracomision_estrategica = $hojaActiva->getCellByColumnAndRow($extracomision_estrategica, $i);
                        if ($vExtracomision_estrategica->isFormula()) {
                            $vExtracomision_estrategica = $vExtracomision_estrategica->getCalculatedValue();
                        } else {
                            $vExtracomision_estrategica = $vExtracomision_estrategica->getValue();
                        }
                        
                        $vExtracomision_estrategica = str_replace("€", "", $vExtracomision_estrategica);

                        $vPaquete_voz_datos_20 = 0;
                        $vPaquete_voz_datos_40 = 0;
                        $vPaquete_voz_datos_mas_40 = 0;
                        $vCaptacion = 0;
                        $vPortabilidad = 0;
                        $vExtracomision_terminal = 0;
                        $vPuntos = (!is_numeric(strval($vPuntos))) ? 0 : strval($vPuntos);
                        $vExtracomision_estrategica = (!is_numeric(strval($vExtracomision_estrategica))) ? 0 : strval($vExtracomision_estrategica);
                        $vExtracomision_terminal_36 = 0;
                        $vAlta = (!is_numeric(strval($vAlta))) ? 0 : strval($vAlta);

                        $c->next_result();
                        $r = $c->query("call or_marco_retributivo_cargar({$version},'','{$vModulo}','{$vPaquete_voz_datos_20}',
                                        '{$vPaquete_voz_datos_40}','{$vPaquete_voz_datos_mas_40}','{$vCaptacion}','{$vPortabilidad}',
                                        '{$vExtracomision_terminal}','{$vPuntos}','{$vExtracomision_estrategica}','{$vExtracomision_terminal_36}',
                                        '{$vAlta}');");
                    } else {
                        break;
                    }
                }
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                echo 'Error al leer el archivo: ', $e->getMessage();
            }

            // NETWORK PLUS
            $inicioNetworkPlus = $plantilla->campos->tarifas[0]->network_plus[0]->fila;
            $modulo = $plantilla->campos->tarifas[0]->network_plus[0]->modulo;
            $alta = $plantilla->campos->tarifas[0]->network_plus[0]->alta;
            $puntos = $plantilla->campos->tarifas[0]->network_plus[0]->puntos;
            $extracomision_estrategica = $plantilla->campos->tarifas[0]->network_plus[0]->extracomision_estrategica;

            try {
                $spreadsheet = IOFactory::load(getcwd() . '/orange.xlsx');
                $spreadsheet->setActiveSheetIndexByName($hoja);

                $hojaActiva = $spreadsheet->getActiveSheet();
                $nFilas = $hojaActiva->getHighestRow();

                for ($i = $inicioNetworkPlus; $i < $nFilas; $i++) {
                    $vModulo = $hojaActiva->getCellByColumnAndRow($modulo, $i);

                    if (strlen(strval($vModulo)) > 0) {
                        $vAlta = $hojaActiva->getCellByColumnAndRow($alta, $i);
                        $vPuntos = $hojaActiva->getCellByColumnAndRow($puntos, $i);
                        $vExtracomision_estrategica = $hojaActiva->getCellByColumnAndRow($extracomision_estrategica, $i);
                        if ($vExtracomision_estrategica->isFormula()) {
                            $vExtracomision_estrategica = $vExtracomision_estrategica->getCalculatedValue();
                        } else {
                            $vExtracomision_estrategica = $vExtracomision_estrategica->getValue();
                        }
                        
                        $vExtracomision_estrategica = str_replace("€", "", $vExtracomision_estrategica);

                        $vPaquete_voz_datos_20 = 0;
                        $vPaquete_voz_datos_40 = 0;
                        $vPaquete_voz_datos_mas_40 = 0;
                        $vCaptacion = 0;
                        $vPortabilidad = 0;
                        $vExtracomision_terminal = 0;
                        $vPuntos = (!is_numeric(strval($vPuntos))) ? 0 : strval($vPuntos);
                        $vExtracomision_estrategica = (!is_numeric(strval($vExtracomision_estrategica))) ? 0 : strval($vExtracomision_estrategica);
                        $vExtracomision_terminal_36 = 0;
                        $vAlta = (!is_numeric(strval($vAlta))) ? 0 : strval($vAlta);

                        $c->next_result();
                        $r = $c->query("call or_marco_retributivo_cargar({$version},'','{$vModulo}','{$vPaquete_voz_datos_20}',
                                        '{$vPaquete_voz_datos_40}','{$vPaquete_voz_datos_mas_40}','{$vCaptacion}','{$vPortabilidad}',
                                        '{$vExtracomision_terminal}','{$vPuntos}','{$vExtracomision_estrategica}','{$vExtracomision_terminal_36}',
                                        '{$vAlta}');");
                    } else {
                        break;
                    }
                }
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                echo 'Error al leer el archivo: ', $e->getMessage();
            }

            // CONECTA EMPRESAS
            $inicioCEmpresas = $plantilla->campos->tarifas[0]->conecta_empresas[0]->fila;
            $modulo = $plantilla->campos->tarifas[0]->conecta_empresas[0]->modulo;
            $alta = $plantilla->campos->tarifas[0]->conecta_empresas[0]->alta;
            $puntos = $plantilla->campos->tarifas[0]->conecta_empresas[0]->puntos;
            $extracomision_estrategica = $plantilla->campos->tarifas[0]->conecta_empresas[0]->extracomision_estrategica;

            try {
                $spreadsheet = IOFactory::load(getcwd() . '/orange.xlsx');
                $spreadsheet->setActiveSheetIndexByName($hoja);

                $hojaActiva = $spreadsheet->getActiveSheet();
                $nFilas = $hojaActiva->getHighestRow();

                for ($i = $inicioCEmpresas; $i < $nFilas; $i++) {
                    $vModulo = $hojaActiva->getCellByColumnAndRow($modulo, $i);

                    if (strlen(strval($vModulo)) > 0) {
                        $vAlta = $hojaActiva->getCellByColumnAndRow($alta, $i);
                        $vPuntos = $hojaActiva->getCellByColumnAndRow($puntos, $i);
                        $vExtracomision_estrategica = $hojaActiva->getCellByColumnAndRow($extracomision_estrategica, $i);
                        if ($vExtracomision_estrategica->isFormula()) {
                            $vExtracomision_estrategica = $vExtracomision_estrategica->getCalculatedValue();
                        } else {
                            $vExtracomision_estrategica = $vExtracomision_estrategica->getValue();
                        }
                        
                        $vExtracomision_estrategica = str_replace("€", "", $vExtracomision_estrategica);

                        $vPaquete_voz_datos_20 = 0;
                        $vPaquete_voz_datos_40 = 0;
                        $vPaquete_voz_datos_mas_40 = 0;
                        $vCaptacion = 0;
                        $vPortabilidad = 0;
                        $vExtracomision_terminal = 0;
                        $vPuntos = (!is_numeric(strval($vPuntos))) ? 0 : strval($vPuntos);
                        $vExtracomision_estrategica = (!is_numeric(strval($vExtracomision_estrategica))) ? 0 : strval($vExtracomision_estrategica);
                        $vExtracomision_terminal_36 = 0;
                        $vAlta = (!is_numeric(strval($vAlta))) ? 0 : strval($vAlta);

                        $c->next_result();
                        $r = $c->query("call or_marco_retributivo_cargar({$version},'','{$vModulo}','{$vPaquete_voz_datos_20}',
                                        '{$vPaquete_voz_datos_40}','{$vPaquete_voz_datos_mas_40}','{$vCaptacion}','{$vPortabilidad}',
                                        '{$vExtracomision_terminal}','{$vPuntos}','{$vExtracomision_estrategica}','{$vExtracomision_terminal_36}',
                                        '{$vAlta}');");
                    } else {
                        break;
                    }
                }
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                echo 'Error al leer el archivo: ', $e->getMessage();
            }

            echo "fina carga marco";
        }
    }
}
