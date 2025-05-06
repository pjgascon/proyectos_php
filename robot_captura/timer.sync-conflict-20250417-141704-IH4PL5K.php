<?php

function timer($intervalo, $contadorReinicio): void
{
    $Object = new DateTime();
    $hora = $Object->format("H");

    if ($contadorReinicio == 50) {
        exec("/usr/bin/reiniciarCaptura");
        $contadorReinicio = 0;
    }

    exec(getcwd() . "/lanzarPeticion.sh");
    sleep($intervalo);

    if (strval($hora) >= 8 && strval($hora) <= 21)
        timer($intervalo, $contadorReinicio++);
}

timer(60, 0);
