<?php
class infoCif
{
    private $cif = "";

    public function __construct($cif)
    {
        $this->cif = strtoupper($cif);
    }

    public function obtenerDatosCif(): array
    {
        $datos = [];
        $url = "https://www.einforma.com/servlet/app/prod/ETIQUETA_EMPRESA/nif/{$this->cif}";
        $html = "";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);              // Establece la URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   // Devuelve el contenido como cadena
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);   // Sigue redirecciones
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Evita verificar el certificado SSL (opcional)
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_USERAGENT, "MiAplicacion/1.0 (Windows NT 10.0; Win64; x64)"); // User-Agent personalizado


        // Manejar errores
        if (curl_errno($ch) == 0) {
            // Ejecutar la solicitud
            $html = curl_exec($ch);
            $dom = new DOMDocument;

            // Evitar warnings debido a caracteres especiales en el HTML
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();

            // Crear el objeto XPath
            $xpath = new DOMXPath($dom);

            // Extraer la denominación
            $denominacion = $xpath->query("//td[strong[text()='Denominación:']]/following-sibling::td");
            $denominacion = $denominacion->length > 0 ? trim($denominacion[0]->textContent) : 'No encontrado';

            // Extraer el domicilio social actual
            $domicilio = $xpath->query("//td[strong[text()='Domicilio social actual:']]/following-sibling::td");
            $domicilio = $domicilio->length > 0 ? trim($domicilio[0]->textContent) : 'No encontrado';
            $domicilio = str_replace("Ver Mapa", "", $domicilio);

            // Extraer la localidad
            $localidad = $xpath->query("//td[strong[text()='Localidad:']]/following-sibling::td");
            $localidad = $localidad->length > 0 ? trim($localidad[0]->textContent) : 'No encontrado';

            // Extraigo el código postal de la población
            $cp = preg_match('/\b\d{5}\b/', $localidad, $matches);
            $datos = [
                'denominacion' => $denominacion,
                'domicilio' => $domicilio,
                'localidad' => $localidad,
                'cp' => $matches[0] ?? '0'
            ];
        }

        return $datos;
    }

    public function obtenerDatosCifPlanB(): array
    {
        // $options = [
        //     "http" => [
        //         "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36"
        //     ]
        // ];
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
            ]
        ];
        $datos = [];
        $url = "https://www.einforma.com/servlet/app/prod/ETIQUETA_EMPRESA/nif/{$this->cif}";

        $context = stream_context_create($options);
        $html = file_get_contents($url, false, $context);

        $dom = new DOMDocument;

        // Evitar warnings debido a caracteres especiales en el HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        // Crear el objeto XPath
        $xpath = new DOMXPath($dom);

        // Extraer la denominación
        $denominacion = $xpath->query("//td[strong[text()='Denominación:']]/following-sibling::td");
        $denominacion = $denominacion->length > 0 ? trim($denominacion[0]->textContent) : 'No encontrado';

        // Extraer el domicilio social actual
        $domicilio = $xpath->query("//td[strong[text()='Domicilio social actual:']]/following-sibling::td");
        $domicilio = $domicilio->length > 0 ? trim($domicilio[0]->textContent) : 'No encontrado';
        $domicilio = str_replace("Ver Mapa", "", $domicilio);

        // Extraer la localidad
        $localidad = $xpath->query("//td[strong[text()='Localidad:']]/following-sibling::td");
        $localidad = $localidad->length > 0 ? trim($localidad[0]->textContent) : 'No encontrado';

        // Extraigo el código postal de la población
        $cp = preg_match('/\b\d{5}\b/', $localidad, $matches);
        $datos = [
            'denominacion' => $denominacion,
            'domicilio' => $domicilio,
            'localidad' => $localidad,
            'cp' => $matches[0] ?? '0'
        ];

        return $datos;
    }
}
