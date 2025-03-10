<?php
// $i = 1;
//$archivo = "https://be6721.rcr72.waw04.cdn112.com/hls2/02/06222/wyvvshwe5qjk_n/seg-" . $i . "-v1-a1.ts?t=--zo1wbBdV81zzixyv23ss3z5djz7u_fhLTSjBfshhM&s=1717779309&e=43200&f=31543067&srv=25&asn=6739&sp=4000";
//while (!is_null($archivo)) {
for ($i = 1; $i < 1000; $i++) {
    try {
        $archivo = "https://be6721.rcr72.waw04.cdn112.com/hls2/02/06222/wyvvshwe5qjk_n/seg-" . $i . "-v1-a1.ts?t=tHIAbRmD8Esvvtf8QxnvoYXABRgqGePzJEcCynt9jPk&s=1734878992&e=129600&f=30647382&srv=FRdn5eAUPJ&i=0.4&sp=500&p1=FRdn5eAUPJ&p2=FRdn5eAUPJ&asn=6739";
        echo "\r";
        echo "\033[K";
        echo "Descargando parte " . $i;

        $mArchivo = fopen($i . ".ts", "w");

        $handle = fopen($archivo, "rb") or die("Finalizado");
        $contents = '';
        while (!feof($handle)) {
            $contents .= fread($handle, 8192);
        }
        fwrite($mArchivo, $contents);

        fclose($mArchivo);
        fclose($handle);

        //$i++;
        //$archivo = "https://be6721.rcr72.waw04.cdn112.com/hls2/02/06222/wyvvshwe5qjk_n/seg-" . $i . "-v1-a1.ts?t=--zo1wbBdV81zzixyv23ss3z5djz7u_fhLTSjBfshhM&s=1717779309&e=43200&f=31543067&srv=25&asn=6739&sp=4000";
    } catch (Exception $e) {
        break;
    }
}
echo "Finalizado";