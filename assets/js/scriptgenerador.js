// scriptgenerador.js

// Definición de la variable global al inicio del archivo
var globalJobId;
var transcripcionActiva = false;
// Este es el bloque de código que se ejecutará cuando la página esté lista.
var table;
$(document).ready(function() {
	  console.log("document");
    initDataTable();
    attachEventHandlers();
});

// Esta función inicializa el DataTable y puede ser llamada nuevamente si es necesario.
function initDataTable() {
	    if ($.fn.DataTable.isDataTable('#mp3-table')) {
        // Si ya está inicializada, destrúyela primero
        $('#mp3-table').DataTable().destroy();
    }
	
	
 
         table = $('#mp3-table').DataTable({
            "paging": true,
            "ordering": true,
            "searching": true,
            "language": {
                "url": "/elisa/assets/js/Spanish.json"
            }
        });
    
}

// Esta función adjunta los manejadores de eventos a los botones y selecciones de archivo. Se llama después de cada actualización de contenido.
function attachEventHandlers() {
    console.log("attachEventHandlers");
    $('#select-all').on('click', function() {
        
        if (table) {
        var rows = table.rows({ 'search': 'applied' }).nodes();	
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
        handleSelection();
      }
    });

    // Maneja el estado de las casillas de verificación
    $('#mp3-table tbody').on('change', 'input[type="checkbox"]', handleSelection);
     attachButtonHandlers();
 }   
 
 function attachButtonHandlers() {
        // Eliminar previos manejadores de eventos para evitar la duplicación
     	   console.log("attachButtonHandlers");   
 
    $('#btn-eliminar').on('click', deleteSelectedFiles);
    $('#btn-transcribir').on('click', transcribeSelectedFile);
    $('#cancelar-transcription').off('click').on('click',cancelarTranscripcion); 
     
    
    
}

function handleSelection() {
    var selectedFiles = $('.file-select:checked').length;
    $('#btn-eliminar').prop('disabled', selectedFiles === 0);
    $('#btn-transcribir').prop('disabled', selectedFiles !== 1);
    // Actualiza el estado de otros botones si es necesario
}
// Función que se llamará tras el envío exitoso del archivo
function handleFileUploadSuccess(response) {

    globalJobId = response.job_id; // Asigna el job_id que recibiste del servidor
    console.log("handleFileUploadSuccess(response) globalJobId =", globalJobId);
    $('#cancelar-transcription').data('job-id', globalJobId);
    $('#transcriptionModal').modal('show'); // Muestra el modal de progreso
    actualizarProgreso(); // Inicia la función que actualiza el progreso
}

function deleteSelectedFiles() {    
    
    // Botón de eliminar
 $('#btn-eliminar').on('click', function() {
        // Recoge los nombres de los archivos seleccionados
        var archivosSeleccionados = $('.file-select:checked').map(function() {
            return $(this).data('file-name');
        }).get();

        // Confirma con el usuario antes de eliminar
        if (archivosSeleccionados.length && confirm('¿Estás seguro de que quieres eliminar los archivos seleccionados?')) {
            $.ajax({
                url: '/elisa/pages/eliminar_archivos.php', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: { 'archivos': archivosSeleccionados },
                success: function(data) {
                    // Actualiza la lista de archivos o recarga la página
                    location.reload(); // Por simplicidad, recargamos la página
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert('Error al eliminar los archivos: ' + textStatus);
                }
            });
        }
    });
}

function transcribeSelectedFile() {
// Botón de transcribir
$('#btn-transcribir').on('click', function() {
    var selectedFile = $('.file-select:checked').first().data('file-name');
    console.log("selectedFile="+selectedFile);
    transcripcionActiva = true;
    if (selectedFile) {
        // Muestra la modal de Bootstrap


        // Iniciar la transcripción y mostrar el progreso
        $.ajax({
            url: '/elisa/pages/transcribir_audio.php',
            type: 'POST',
            data: { filename: selectedFile },
            dataType: 'json', // Esperamos una respuesta en formato JSON
            success: function(response) {
               if (response.job_id) {
                        globalJobId = response.job_id; // Asignar el job_id a la variable global
                        $('#transcriptionModal').modal('show'); // Muestra el modal de progreso
                        actualizarProgreso(); // Comienza a actualizar el progreso
                        actualizarListaArchivos(); 
                  } else if (response.error) {
                      alert(response.error);
                  }

            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Si hubo un error en la carga, esconde la barra de progreso y muestra un mensaje
                
                console.error('Error al cargar el archivo:', textStatus, errorThrown);
            }
                      
        });
    } else {
        alert("Por favor, selecciona un archivo para transcribir.");
    }
});

}

function resumenSelectedFile() {
    // Botón de resumen
    $('#btn-resumen').on('click', function() {
        // Código para resumir el archivo seleccionado
    });



}




 $('#upload-form').on('submit', function(e) {
 	       console.log("upload-form");  
        e.preventDefault(); // Previene el envío por defecto del formulario

        var formData = new FormData(this);
        console.log("upload_form");
        console.log("globalJobId:", globalJobId);
        transcripcionActiva = true;
        // Utiliza AJAX para enviar el archivo
        $.ajax({
            url: '/elisa/pages/generador.php', // Ruta al script PHP que manejará la carga.
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json', // Asegúrate de que esperas una respuesta JSON
            success: function(response) {
            	  
                 console.log("Respuesta recibida", response); // Log de depuración
                 if (response.job_id) {
                        globalJobId = response.job_id; // Asignar el job_id a la variable global
                        console.log("Archivo cargado con éxito, job_id:", globalJobId);
                        $('#transcriptionModal').modal('show'); // Muestra el modal de progreso
                        actualizarProgreso(); // Comienza a actualizar el progreso
                  } else if (response.error) {
                  	  console.log("Error recibido:", response.error); // Log de depuración
                      alert(response.error);
                   }
                alert(response.message); // Muestra el mensaje recibido del servidor
                actualizarListaArchivos(); // Actualizar la lista de archivos
                
                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Si hubo un error en la carga, esconde la barra de progreso y muestra un mensaje
                
                console.error('Error al cargar el archivo:', textStatus, errorThrown);
            }
        });
    });




    // Función para actualizar la lista de archivos
   // Función para actualizar la lista de archivos
function actualizarListaArchivos() {
	console.log("actualizarListaArchivos");
    $.ajax({
        url: '/elisa/pages/generador.php',
        type: 'GET',
         success: function(data) {
            // Crear un documento HTML virtual a partir de la respuesta
            var virtualDocument = new DOMParser().parseFromString(data, 'text/html');
            // Buscar el contenedor .mp3-list-container dentro del documento virtual
            var newHtml = virtualDocument.querySelector('.mp3-list-container').innerHTML;
            // Actualizar el contenedor real en la página
            $('.mp3-list-container').html(newHtml);
            // Re-inicializar DataTable y adjuntar manejadores de eventos
            initDataTable();
            attachEventHandlers();
            
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Error al actualizar la lista de archivos: ' + textStatus);
        }
    });
}




function actualizarProgreso() {
	
	console.log("actualizarProgreso");
  console.log("Job ID enviado:", globalJobId);
  console.log("antes del ajax:");

    $.ajax({

        url: '/elisa/pages/progreso_transcripcion.php',  // Asegúrate de que esta sea la ruta correcta
        data: { job_id: globalJobId  },

      
        success: function(data) {
            var progress = parseInt(data);
            $('#progress-bar').css('width', progress + '%').attr('aria-valuenow', progress).text(progress + '%');
            console.log("progress= "+progress);
            if (progress >= 100) {
                // Cierra el modal cuando el progreso es 100%
                $('#transcriptionModal').modal('hide');
                transcripcionActiva=false;
            }else {
            	// Si no se ha completado, sigue actualizando
            	 console.log("transcripcionActiva="+transcripcionActiva);
            	 if (transcripcionActiva) {
            	        setTimeout(actualizarProgreso, 1000);  // Vuelve a llamar la función después de 1 segundo
            	 }
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al obtener el progreso: " + error);
             if (transcripcionActiva) {
            setTimeout(actualizarProgreso, 1000);  // Intenta nuevamente después de 1 segundo
            }
        }
    });
    console.log("fin actualizarProgreso");
    
}

 // Se ejecuta cuando el modal se cierra completamente
$('#transcriptionModal').on('hidden.bs.modal', function () {
    console.log("Modal hidden. Updating file list.");
    actualizarListaArchivos();
}); 

function cancelarTranscripcion() {
        console.log("cancelarTranscripcion globalJobId= " + globalJobId);
        transcripcionActiva = false;
    // Llama al script PHP para cancelar la transcripción
    if (globalJobId) {
        console.log("Cancelando transcripción para job_id: " + globalJobId);

        // Llama al script PHP para cancelar la transcripción
        $.ajax({
            url: '/elisa/pages/cancelar_transcripcion.php',
            type: 'POST',
            data: { job_id: globalJobId },
            success: function(data) {
                alert("Transcripción cancelada: " + data);
                $('#transcriptionModal').modal('hide');
                actualizarListaArchivos();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error al cancelar la transcripción: ' + textStatus);
            }
        });
    } else {
        console.error("No se pudo obtener job_id para cancelar la transcripción.");
    }
    
}

