<?php

function timer($intervalo, $contadorReinicio): void
{
    $Object = new DateTime();
    $hora = $Object->format("H");

    // if ($contadorReinicio == 50) {
    //     exec("/usr/bin/reiniciarCaptura");
    //     $contadorReinicio = 0;
    //     sleep(60);
    // }

    exec(getcwd() . "/lanzarPeticion.sh");
    sleep($intervalo);

    if (strval($hora) >= 7 && strval($hora) <= 21)
        timer($intervalo, 0);
}

timer(15, 0);
