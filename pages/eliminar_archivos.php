<?php
// Este script debe ser protegido para asegurarse de que sólo usuarios autorizados puedan borrar archivos.

// Verifica si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Suponiendo que se envía un array con los nombres de los archivos
    $archivosParaEliminar = $_POST['archivos'] ?? [];

    // Ruta a la carpeta donde se guardan los archivos
    $directorioArchivos = '../uploads/';
    $directorioTranscriptions = '../transcriptions/';

    // Recorre la lista de archivos y los elimina
    foreach ($archivosParaEliminar as $nombreArchivo) {
        $rutaArchivo = $directorioArchivos . basename($nombreArchivo);
        $rutaTranscription = $directorioTranscriptions . pathinfo($nombreArchivo, PATHINFO_FILENAME) . ".txt";
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo); // Elimina el archivo de audio.
        }
        if (file_exists($rutaTranscription)) {
            unlink($rutaTranscription); // Elimina la transcription si existe.
        }
    }

    echo "Archivos eliminados correctamente.";
} else {
    // No es una solicitud POST
    header("HTTP/1.1 403 Forbidden");
    exit('Método no permitido.');
}
?>
