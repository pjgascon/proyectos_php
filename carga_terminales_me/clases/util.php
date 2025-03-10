<?php
class Utilidades
{
    public static function numeroALetra($numeroColumna)
    {
        $letra = '';
        $numeroColumna += 1;
        while ($numeroColumna > 0) {
            $modulo = ($numeroColumna - 1) % 26;
            $letra = chr(65 + $modulo) . $letra;
            $numeroColumna = intval(($numeroColumna - $modulo) / 26);
        }
        return $letra;
    }

    public static function letraANumero($letraColumna)
    {
        $letraColumna = strtoupper($letraColumna); // Asegurarse de que las letras sean mayúsculas
        $numeroColumna = 0;
        $longitud = strlen($letraColumna);
        for ($i = 0; $i < $longitud; $i++) {
            $numeroColumna *= 26;
            $numeroColumna += ord($letraColumna[$i]) - 64; // 'A' corresponde a 1
        }
        return $numeroColumna - 1;
    }
}
