<?php

$url = "http://desarrolloserver.liberi.es:3000/query"; // Asegúrate de que la URL sea absoluta si es necesario
$auth = "KmwLi4gJrWTRL1Yr1IWO7uyIPoXopM6CRFKHNyLzUEtE01HrNv"; // Sustituye con tu token de autenticación

$cifs = "B21176599";
$soloResumen = 0;
$noCache = 1;
$soloCache = false;

// Datos a enviar
$data = [
    "cifs" => $cifs,
    "soloResumen" => $soloResumen,
    "noCache" => $noCache,
    "soloCache" => $soloCache
];

// Inicializar cURL
$ch = curl_init();

// Configurar opciones de cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: $auth"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Ejecutar la petición
$response = curl_exec($ch);

// Manejar errores
if (curl_errno($ch)) {
    echo "Error en la solicitud cURL: " . curl_error($ch);
}

// Cerrar la conexión
curl_close($ch);
var_dump($response);exit;
// Decodificar la respuesta si es necesario
$resultado = json_decode($response, false);

// Imprimir la respuesta
var_dump($resultado);