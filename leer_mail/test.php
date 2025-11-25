<?php
require_once(getcwd() . '/cloudflare.php');
$archivo =  "test.php";

$cloudflare = new Cloudflare();
$b = $cloudflare->uploadFile(getcwd() . "/" . $archivo);

echo "b";
