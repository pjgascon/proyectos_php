<?php

require_once('crypto.php');
$c = new crypto();

echo "Selecciona una opcion:\n\n 1 Cifrar un archivo\n 2 Cifrar un directorio\n 3 Descifrar un archivo\n 4 Descifrar un directorio\n";
$opcion = fgets(STDIN);

if (!is_numeric($opcion) || $opcion > 4) {
    echo "\nOpción no válida\n";
    exit;
}

switch ($opcion) {
    case "1":
        $entrada = readline("\nIntroduce la ruta del archivo: ");
        $pwd = readline("\nIntroduce la contraseña:");

        $entrada = str_replace("'", "", $entrada);

        if (!is_file($entrada)) {
            echo "\nArchivo de entrada no válido";
            exit;
        }

        if (strlen($pwd) == 0) {
            echo "\nContraseña no válida";
            exit;
        }

        if ($c->encryptFile($entrada, $entrada . ".encrypt", $pwd)) {
            unlink($entrada);
            echo "\nArchivo encriptado\n";
        } else {
            echo "\nError al encriptar el archivo\n";
        }

        break;
    case "2":
        $entrada = readline("\nIntroduce la ruta del directorio a encriptar: ");
        $pwd = readline("\nIntroduce la contraseña: ");

        if (!is_dir($entrada)) {
            echo "Error al abrir el directorio";
            exit;
        }

        if (strlen($pwd) == 0) {
            echo "\nContraseña no válida";
            exit;
        }

        echo "\nComprimiendo directorio\n";
        exec("tar czvf " . $entrada . ".tar.gz " . $entrada);

        echo "\nEncriptando directorio\n";
        if ($c->encryptFile($entrada . ".tar.gz", $entrada . ".encrypt", $pwd)) {
            unlink($entrada . ".tar.gz");
            echo "\nArchivo encriptado\n";
        } else {
            echo "\nError al encriptar el archivo\n";
        }
        break;
    case "3":
        $entrada = readline("\nIntroduce la ruta del archivo: ");
        $salida = str_replace(".encrypt", "", $entrada);
        $pwd = readline("\nIntroduce la contraseña:");

        $entrada = str_replace("'", "", $entrada);

        if (!is_file($entrada)) {
            echo "\nArchivo de entrada no válido";
            exit;
        }

        if (strlen($pwd) == 0) {
            echo "\nContraseña no válida";
            exit;
        }

        if ($c->decryptFile($entrada, $salida, $pwd)) {
            if (filesize($salida) > 0) {
                unlink($entrada);
                echo "\nArchivo desencriptado\n";
            } else {
                unlink($salida);
                echo "\nError al desencriptar el archivo\n";
            }
        } else {
            echo "\nError al desencriptar el archivo\n";
        }

        break;
    case "4":
        $entrada = readline("\nIntroduce la ruta del archivo: ");
        $salida = readline("Introducel el directorio de salida: ")."/".basename(str_replace(".encrypt", ".tar.gz", $entrada));
        $pwd = readline("\nIntroduce la contraseña:");

        $entrada = str_replace("'", "", $entrada);
       
        if (!is_file($entrada)) {
            echo "\nArchivo de entrada no válido";
            exit;
        }

        if (strlen($pwd) == 0) {
            echo "\nContraseña no válida";
            exit;
        }

        if ($c->decryptFile($entrada, $salida, $pwd)) {
            if (filesize($salida) > 0) {
                unlink($entrada);
                exec('tar xzvf '.$salida." -C ".dirname($salida));
                unlink($salida);
                echo "\nArchivo desencriptado\n";
            } else {
                unlink($salida);
                echo "\nError al desencriptar el archivo\n";
            }
        } else {
            echo "\nError al desencriptar el archivo\n";
        }
        break;
    default:
        exit;
        break;
}

function recorrerDir($entrada)
{
    $dir = opendir($entrada);

    while ($elemento = readdir($dir)) {
        if ($elemento != "." && $elemento != "..") {
            if (is_dir($entrada . "/" . $elemento)) {
                echo $entrada . "/" . $elemento . "\n";
                recorrerDir($entrada . "/" . $elemento);
            } else {
                echo "      " . $elemento . "\n";
            }
        }
    }
}
