<?php
// Mato los procesos del robot
system("mv stop.flag1 stop.flag");
sleep(15);

// Reinicio el servicio de captura
system("/usr/bin/reiniciarCaptura");

sleep(10);

// Vuelvo a lanzar el robot
system("mv stop.flag stop.flag_1");
system("/usr/bin/php8.3 robot_captura.php");

exit;
