<?php
class Utilidades
{
    public static function fecha2Spain($f)
	{
		return date("d/m/Y", strtotime($f));
	}

	public static function hora2Spain($f)
	{
		return date("H:i", strtotime($f));
	}

	public static function fecha2Mysql($fecha)
	{
		preg_match('/(19[5-9][0-9]|20[0-9][0-9])[\/-](0[1-9]|1[0-2])[\/-](0[1-9]|1[0-9]|2[0-9]|3[01])/', $fecha, $mifecha);
		$lafecha = $mifecha[1] . "-" . $mifecha[2] . "-" . $mifecha[3];
		return $lafecha;
	}

    public static function encrypt($string, $key)
	{
		$result = '';
		for ($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result .= $char;
		}
		return base64_encode($result);
	}

	public static function decrypt($string, $key)
	{
		$result = '';
		// Reemplazo los espacios por + cuando se pasa por GET
		$string = str_replace(" ", "+", $string);
		$string = base64_decode($string);
		for ($i = 0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			$result .= $char;
		}
		return $result;
	}
	public static function guid()
	{
		mt_srand((float) microtime() * 10000); //optional for php 4.2.0 and up.
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45); // "-"
		$uuid   = substr($charid, 0, 8) . $hyphen
			. substr($charid, 8, 4) . $hyphen
			. substr($charid, 12, 4) . $hyphen
			. substr($charid, 16, 4) . $hyphen
			. substr($charid, 20, 12);
		return $uuid;
	}

	public static function codificarUTF($cadena): string
	{
		return mb_convert_encoding($cadena, "ISO-8859-1", "UTF-8");
	}

	public static function esUTF($cadena): bool
	{
		return (mb_detect_encoding($cadena, mb_list_encodings(), true) != "UTF-8") ? false : true;
	}
}
