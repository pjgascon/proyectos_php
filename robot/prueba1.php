<?php
$processLoginURL = 'https://herramientapuntoventa.cliente.orange.es/PortalCliente/appmanager/PortalCliente/portalCliente';
$userName = 'DIsolsho';
$password = 'Invierno_2023';
$ch = curl_init($processLoginURL);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS,
    "j_username=$userName&jpassword=$password");
$res = curl_exec( $ch );
echo curl_getinfo( $ch, CURLINFO_HTTP_CODE);
if ( $error = curl_error( $ch ) ) {
    die ($error);
}
echo $res;
curl_close( $ch );
?>