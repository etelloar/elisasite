<?php
session_start();
$job_id = '';
$mensaje = '';
$upload_path = "../uploads/";
$upload_python = "C:\\Apache\\htdocs\\elisa\\uploads\\";
$pythonExe = "C:\\Python310\\python.exe";
$pythonScript = "C:\\Apache\\htdocs\\elisa\\assets\\py\\transcribir.py"; // Cambia a la ruta donde está tu script.
$outputFilehtml = "/transcriptions/"; // Ruta donde quieres que se guarde el archivo de salida.
$outputFile = "C:\\Apache\\htdocs\\elisa\\transcriptions\\"; // Ruta donde quieres que se guarde el archivo de salida.
// Verifica si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['mp3file'])) {
    $file_tmp = $_FILES['mp3file']['tmp_name'];
    $file_name = basename($_FILES['mp3file']['name']);
 

    // Asegúrate de que la carpeta de destino existe y es escribible
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0777, true);
    }
       
    
    // Intenta mover el archivo cargado al directorio de destino
    if (move_uploaded_file($file_tmp, $upload_path . $file_name)) {
         $_SESSION['mensaje'] = "Archivo cargado con éxito.";
          $job_id = uniqid('job_', true);
          $transcriptionFilehtml = $outputFilehtml . pathinfo($file_name, PATHINFO_FILENAME) . ".txt";
          $transcriptionFilePY = $outputFile . pathinfo($file_name, PATHINFO_FILENAME) . ".txt";
          $ErrorFile = $outputFile . pathinfo($file_name, PATHINFO_FILENAME) . "_log.txt";
     
          $mp3File =  $upload_python . $file_name;
         //Específica los descriptores de tubería que el proceso hijo heredará
         $descriptorspec = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w']   // stderr
        ];
          
        $command = escapeshellcmd("start /B $pythonExe $pythonScript $mp3File $job_id $transcriptionFilePY > $ErrorFile 2>&1 ");
     
        $process = proc_open($command,$descriptorspec,  $pipes); 
        if (is_resource($process)) {
        	  $status = proc_get_status($process);
            $pid = $status['pid'];
            error_log("pid 1=".$pid);
            error_log("job_id 1=".$job_id);
            // Cerrar los recursos de tubería que ya no se necesitan
            fclose($pipes[0]); // Cerrar stdin
            fclose($pipes[1]); // Cerrar stdout
            fclose($pipes[2]); // Cerrar stderr
            // Almacenar el job_id y el PID en la sesión o base de datos
            $_SESSION['current_job'] = ['job_id' => $job_id,'pid' => $pid];
            // Agrega la ruta del archivo de transcripción a la sesión para poder mostrar un enlace de descarga
            $_SESSION['transcription_path'] = $transcriptionFilePY;
            $_SESSION['file_name'] = $transcriptionFilehtml;
            $response = ['job_id' => $job_id, 'message' => 'Archivo cargado con éxito.'];                 
            proc_close($process);
       
        }
        else {
            $response = ['error' => 'No se pudo iniciar el proceso de transcripción.'];
         }
          
         echo json_encode($response);
         exit;
        
    } else {
       $response = ['error' => 'Hubo un error al cargar el archivo.'];
       echo json_encode($response);
       exit; // Detiene la ejecución después de enviar la respuesta
    }
     

}
?>
<html>
<body>


    <form id="upload-form" action="/elisa/pages/generador.php" method="post" enctype="multipart/form-data">
    	  <label for="file-input">Selecciona un archivo MP3:</label>
        <input type="file" id="file-input" name="mp3file" accept=".mp3">
        <button type="submit">Subir Archivo</button>
       
       </form>
   
  
       <!-- Si hay un mensaje para mostrar, inserción condicional del HTML -->

    
<div class="mp3-list-container">
	 
	<button id="btn-eliminar" disabled>Eliminar</button>
    <button id="btn-transcribir" disabled>Transcribir</button>
    <button id="btn-resumen" disabled>Resumen</button>    
<?php
function mostrarListaArchivos() {
$mp3_files = glob("../uploads/*.mp3");
global $outputFile;

    echo '<h3>Audios Disponibles:</h3>';
    echo '<table id="mp3-table" >';
    
     echo '<thead><tr><th><input type="checkbox" id="select-all"></th><th>Nombre de archivo</th><th>Audio</th><th>Transcripcion</th></thead>';
    echo '<tbody>';
 if (!empty($mp3_files)) {   
    foreach ($mp3_files as $file) {
        $filename = basename($file);
        
        $mp3url ="http://localhost/elisa/uploads/" . htmlentities($filename);
        
        $thereistranscription= $_SERVER['DOCUMENT_ROOT'] . '/elisa/transcriptions/' . pathinfo($filename, PATHINFO_FILENAME) . ".txt";
        
       
        echo '<tr>';
        
        echo '<td><input type="checkbox" class="file-select" data-file-name="'. htmlentities($filename).'"></td>';
        echo '<td>' . $filename . '</td>';
        echo '<td><audio controls><source src="' . $mp3url . '" type="audio/mpeg">Tu navegador no soporta la reproducción de audio.</audio></td>';

 	      // Comprueba si existe el archivo de transcripción
        if (file_exists($thereistranscription)) {
                // Aquí tienes que poner la ruta correcta para el href, asumiendo que tienes un enlace directo accesible vía HTTP
               
  
                //echo '<td><a href=/elisa/pages/download.php?file='. urlencode($transcriptionPath).'" class="descargar-btn" download>Descargar Transcripción</a></td>';
                echo '<td><a href=/elisa/pages/download.php?file='.pathinfo($filename, PATHINFO_FILENAME).'.txt class="descargar-btn" download>Descargar Transcripción</a></td>';
 
                
        } else {
                echo '<td>No disponible</td>';
        }
        echo '</tr>';
    }
} 


   echo '</tbody>';
    echo '</table>';


}

?>
<?php mostrarListaArchivos();?>

</div>

<!-- Modal para el progreso de la transcripción -->
<div class="modal fade" id="transcriptionModal" tabindex="-1" aria-labelledby="transcriptionModalLabel" aria-hidden="false">

  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="transcriptionModalLabel">Progreso de la Transcripción</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="progress-container">
            <div class="progress">
                <div class="progress-bar" id="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
        </div>
        
      </div>
      <div class="modal-footer">
        

        <button id="cancelar-transcription" type="button" class="btn btn-secondary" data-dismiss="modal" data-job-id="">Cancelar</button>

      </div>
    </div>
  </div>
</div>


    <!-- Aquí iría el resto de tu HTML y scripts si es necesario -->


<script src="/elisa/assets/js/scriptgenerador.js"></script>

  </body>  
</html>
