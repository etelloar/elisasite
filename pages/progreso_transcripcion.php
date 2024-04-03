<?php
// progreso_transcripcion.php

// Asume que job_id se pasa como un parámetro GET o POST.
$job_id = isset($_GET['job_id']) ? $_GET['job_id'] : (isset($_POST['job_id']) ? $_POST['job_id'] : '');
error_log("progreso_transcripcion");
error_log("job_id=".$job_id );
// Si no se proporciona job_id, devuelve error.
if (empty($job_id)) {
    error_log("Error: job_id no proporcionado.");
    exit;
}

$progress_file = "./progress_{$job_id}.txt"; // Archivo de progreso basado en job_id

 error_log("Ruta del archivo de progreso: " . $progress_file);
 error_log("file_exists($progress_file): " .file_exists($progress_file));
if (file_exists($progress_file)) {
	 error_log("reportando progreso");
    $progress = file_get_contents($progress_file);  // Leerá el contenido del archivo de progreso
   error_log("progreso: " .$progress);
    echo $progress;  // Devuelve el progreso al frontend
    // Si el progreso es 100, entonces borra el archivo de progreso
    if (intval($progress) >= 100) {
        unlink($progress_file);  // Elimina el archivo
    }
    
} else {
	  error_log("sin progreso");
    echo "0";  // Si el archivo no existe, devuelve 0
}
?>
