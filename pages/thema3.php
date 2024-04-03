
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transcriptor - Elisa Site</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
       <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="/elisa/assets/js/DataTables/DataTables-1.13.6/css/jquery.datatables.css">
    <link rel="stylesheet" type="text/css" href="/elisa/assets/css/styles_principal.css">
    <link rel="stylesheet" type="text/css" href="/elisa/assets/css/styles_audios.css">


    <script type="text/javascript" src="/elisa/assets/js/DataTables/DataTables-1.13.6/js/jquery.datatables.js"></script>

</head>
<body>
    <header>
        <img src="/elisa/assets/img/Elisa2.png" alt="Elisa LOGO" style="width:150px;">
    </header>
          

    <!-- Cuerpo principal -->
    <div class="container">
 

        
        <!-- Lista de archivos MP3 disponibles -->
        <?php include_once 'generador.php'; // Incluye el contenido de generador.php aquí 
  
        ?>

        <!-- Modal para el progreso de la transcripción -->
        <div class="modal fade" id="transcriptionModal" tabindex="-1" aria-labelledby="transcriptionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Contenido del modal -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <!-- Información del pie de página -->
    </footer>

</body>
</html>
