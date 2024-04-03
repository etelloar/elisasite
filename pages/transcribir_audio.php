<?php
// transcribir_audio.php
session_start();
$job_id = '';
$mensaje = '';
$upload_path = "../uploads/";
$upload_python = "C:\\Apache\\htdocs\\elisa\\uploads\\";
$pythonExe = "C:\\Python310\\python.exe";
$pythonScript = "C:\\Apache\\htdocs\\elisa\\assets\\py\\transcribir.py"; // Cambia a la ruta donde está tu script.
$outputFilehtml = "/transcriptions/"; // Ruta donde quieres que se guarde el archivo de salida.
$outputFile = "C:\\Apache\\htdocs\\elisa\\transcriptions\\"; // Ruta donde quieres que se guarde el archivo de salida.


// Verificar si se recibió un nombre de archivo
if (isset($_POST['filename'])) {
        $filename = $_POST['filename'];
        error_log("filename=".$filename);
        $job_id = uniqid('job_', true);  // Crear un job_id único para esta transcripción
        $descriptorspec = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w']   // stderr
         ];
         $mp3File =  $upload_python . $filename;
         $transcriptionFilePY = $outputFile . pathinfo($filename, PATHINFO_FILENAME) . ".txt"; 
         $ErrorFile = $outputFile . pathinfo($filename, PATHINFO_FILENAME) . "_log.txt";
         $command = escapeshellcmd("start /B $pythonExe $pythonScript $mp3File $job_id $transcriptionFilePY > $ErrorFile 2>&1 ");
         $process = proc_open($command,$descriptorspec,  $pipes); 
         if (is_resource($process)) {
        	    $status = proc_get_status($process);
              $pid = $status['pid'];
              // Cerrar los recursos de tubería que ya no se necesitan
              fclose($pipes[0]); // Cerrar stdin
              fclose($pipes[1]); // Cerrar stdout
              fclose($pipes[2]); // Cerrar stderr
              // Almacenar el job_id y el PID en la sesión o base de datos
              $_SESSION['current_job'] = ['job_id' => $job_id,'pid' => $pid];
              // Agrega la ruta del archivo de transcripción a la sesión para poder mostrar un enlace de descarga
              $response = ['job_id' => $job_id, 'message' => 'Transcripcion realizada con exito.'];                 
              proc_close($process);
       
         }
         else {
             $response = ['error' => 'No se pudo iniciar el proceso de transcripción.'];
         }

         // Devolver el job_id al cliente
         echo json_encode($response);
         exit;
} else {
    echo json_encode(['error' => 'No se proporcionó ningún archivo para transcribir.']);
    exit;
}
?>
