<?php
require 'vendor/autoload.php'; // Cargar autoload de Composer

use thiagoalessio\TesseractOCR\TesseractOCR;

// Ruta a la imagen que deseas analizar
$imagePath = '/home/pedro/prueba.png';

try {
    // Crear una instancia de TesseractOCR
    $ocr = new TesseractOCR($imagePath);
    
    // Configurar opciones (idioma, etc.)
    $ocr->lang('spa'); // Cambia a 'spa' para español, 'fra' para francés, etc.
    
    // Ejecutar el reconocimiento OCR
    $text = $ocr->run();

    // Mostrar el texto extraído
    echo "Texto extraído:\n";
    echo $text;
} catch (Exception $e) {
    echo "Error al procesar la imagen: " . $e->getMessage();
}
