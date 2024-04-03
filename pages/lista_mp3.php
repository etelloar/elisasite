<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
 <link rel="stylesheet" type="text/css" href="/elisa/assets/css/styles_audios.css">
 <script src="/elisa/assets/js/DataTables/DataTables-1.13.6/js/jquery.datatables.min.js"> </script>
<link rel="stylesheet" type="text/css" href="/elisa/assets/js/DataTables/DataTables-1.13.6/css/jquery.datatables.css">
<script type="text/javascript" charset="utf8" src="/elisa/assets/js/DataTables/DataTables-1.13.6/js/jquery.datatables.js"></script>
<!-- Agrega un input para búsqueda -->





  <?php
// Mostramos los archivos MP3 disponibles para reproducir

function mostrarListaArchivos() {
$mp3_files = glob("../uploads/*.mp3");


    echo '<h3>Audios Disponibles:</h3>';
    echo '<table id="mp3-table" >';
     echo '<thead><tr><th>Nombre de archivo</th><th>Ejecución</th><th>Transcripción</th><th>Resumen</th><th>Traducción</th></tr></thead>';
    echo '<tbody>';
 if (!empty($mp3_files)) {   
    foreach ($mp3_files as $file) {
        $filename = basename($file);
        
        $mp3url ="http://localhost/elisa/uploads/" . htmlentities($filename);
        echo '<tr>';
        echo '<td>' . $filename . '</td>';
        echo '<td><audio controls><source src="' . $mp3url . '" type="audio/mpeg">Tu navegador no soporta la reproducción de audio.</audio></td>';
        echo '<td><button class="transcribe" data-url="' . $mp3url . '">Transcribir</button></td>'; // Agrega aquí la transcripción del archivo si la tienes
        echo '<td><button class="summarize" data-url="' . $mp3url . '">Resumen</button></td>'; // Agrega aquí la interpretación del archivo si la tienes
        echo '<td><button class="translate" data-url="' . $mp3url . '">Traducir</button></td>';
        echo '</tr>';
    }
} 


   echo '</tbody>';
    echo '</table>';


}
?>
<?php mostrarListaArchivos();     ?>
<script>
	
var dataTableInitialized = false; // Variable de bandera
	
$(document).ready(function() {
	
	if (!dataTableInitialized) { // Verifica si DataTables ya se ha inicializado
    $('#mp3-table').DataTable({
        "paging": true,  // Habilita paginación
        "ordering": true,  // Habilita ordenar columnas
        "searching": true,  // Habilita la barra de búsqueda
        "lengthMenu": [5, 10, 20],  // Control de registros por página
        "order": [],  // Columna por defecto para ordenar (ninguna)
        "language": {
            "url": "/elisa/assets/js/Spanish.json"  // Cambia "Spanish" por el idioma deseado
        }
    });
      dataTableInitialized = true;
     } 
    // Agrega la funcionalidad de búsqueda en el input "search"
    $('#search').on('keyup', function() {
        $('#mp3-table').DataTable().search(this.value).draw();
    });
  


});
</script>

