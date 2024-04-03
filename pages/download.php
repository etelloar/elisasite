<?php
// download.php

if(isset($_GET['file'])) {
	   error_log("dentro");
  
     $filePath = $_SERVER['DOCUMENT_ROOT'] . '/elisa/transcriptions/' . basename($_GET['file']); 
     $fileName = basename($_GET['file']); 
     if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . filesize($filePath));
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Transfer-Encoding: binary');
        ob_clean();
        flush();
        readfile($filePath);
        exit;
    } else {
        echo 'El archivo no existe.';
    }
}
?>