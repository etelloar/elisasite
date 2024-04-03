<?php
session_start();

function buscarPIDPorJobId($job_id) {
    // Aquí necesitas implementar cómo obtener el PID basado en el job_id.
    // Si estás guardando el PID en la sesión, sería algo así:
    error_log("buscarPIDPorJobId");
    error_log("job_id 3=".$job_id);
 
    if (isset($_SESSION['current_job']) && $_SESSION['current_job']['job_id'] == $job_id) {
    	  
    	 
        return $_SESSION['current_job']['pid'];
    }
    // Si no lo encuentras, regresa false o null
    return null;
}

function marcarTrabajoComoCancelado($job_id) {
    // Aquí debes implementar cómo marcar el trabajo como cancelado.
    // Por ejemplo, si usas un archivo o base de datos para rastrear esto, actualiza ese estado aquí.
      sleep(5); // Espera 5 segundos
      $cancel_file = "cancel_{$job_id}.txt";
      $progress_file = "progress_{$job_id}.txt";
      
    // Crea el archivo de cancelación
    file_put_contents($cancel_file, "cancel");
    
    error_log ("La transcripción para el job_id {$job_id} ha sido cancelada.");
      // Elimina el archivo de cancelación
    if (file_exists($cancel_file)) {
    	  
        unlink($cancel_file);
        error_log("Archivo de cancelación eliminado: {$cancel_file}");
    }
    
    if (file_exists($progress_file)) {
        unlink($progress_file); // Elimina el archivo de progreso
        error_log("Archivo de progreso eliminado: {$progress_file}");
    }
    
}
error_log("_POST['job_id']=".$_POST['job_id']);
error_log("isset(_POST['job_id'])=".isset($_POST['job_id']));
if (isset($_POST['job_id'])) {
    $job_id = $_POST['job_id'];
      
    // Intenta buscar el PID usando el job_id proporcionado
    $pid = buscarPIDPorJobId($job_id);
    error_log("pid=".$pid);
    // Si se encuentra el PID, mata el proceso y marca el trabajo como cancelado
    //if ($pid) {
    //    exec("taskkill /F /PID $pid");
        
        marcarTrabajoComoCancelado($job_id);
        error_log ("La transcripción para el job_id {$job_id} ha sido cancelada y el proceso con PID {$pid} ha sido terminado.");
        echo "La transcripcion ha sido cancelada";
    //} else {
    //    echo "No se pudo encontrar el proceso con el job_id proporcionado.";
    //}
} else {
    echo "No se proporcionó un job_id.";
}
?>