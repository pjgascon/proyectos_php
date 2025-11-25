<?php

function procesarJson($data) {
    foreach ($data as $cif => $info) {
        echo "CIF: $cif\n";
        echo "Fecha de registro: " . ($info['timeStamp'] ?? 'N/A') . "\n";
        echo "Fuente: " . ($info['fuente'] ?? 'N/A') . "\n";

        if (isset($info['cliente'])) {
            echo "Cliente:\n";
            echo "- Nombre: " . ($info['cliente']['Nombre'] ?? 'N/A') . "\n";
            echo "- Dirección: " . ($info['cliente']['Dirección'] ?? 'N/A') . "\n";
        }

        echo "Permanencias y cuotas pendientes:\n";
        if (isset($info['permanencias_resumen']) && is_array($info['permanencias_resumen'])) {
            foreach ($info['permanencias_resumen'] as $fecha => $detalle) {
                echo "- Fecha/Cuota: $fecha\n";
                echo "  * Importe: " . ($detalle['importe'] ?? 0) . "\n";

                if (isset($detalle['telefonos']) && is_array($detalle['telefonos'])) {
                    echo "  * Teléfonos: " . implode(", ", $detalle['telefonos']) . "\n";
                } else {
                    echo "  * Teléfonos: No disponibles\n";
                }
            }
        } else {
            echo "No hay información de permanencias o cuotas pendientes.\n";
        }

        echo str_repeat("-", 50) . "\n"; // Separador visual
    }
}

$retorno = '{"resultados":{"B21176599":{"timeStamp":"2025-02-17T14:50:23.556Z","cif":"B21176599","fuente":"pangea","cliente":{"Nombre":". CEREZO ROMERO S.L","Dirección":"CALLE RUIZ DE ALDA, Nº 6, BA 21810, PALOS DE LA FRONTERA, HUELVA"},"permanencias_resumen":{"2025-12-13":{"importe":122.88,"telefonos":["615101267"]},"Cuotas pendientes 7":{"importe":0.07,"telefonos":["615101267"]},"2026-11-18":{"importe":550,"telefonos":["615101269","696669028","959048383"]},"Cuotas pendientes 21":{"importe":12.81,"telefonos":["696669028"]},"2025-11-18":{"importe":150,"telefonos":["690952043"]},"2025-03-17":{"importe":120,"telefonos":["695946312"]},"Cuotas pendientes 1":{"importe":2.83,"telefonos":["695946312"]}}}},"noEncontrados":[]}';
$datos = json_decode($retorno, true);

procesarJson($datos["resultados"]);
