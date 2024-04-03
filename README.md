# ELISA Transcription Platform

## Descripción
ELISA es una plataforma que apoya en la transcripción de audios mp3 o wav, diseñada para asistir a gestores de proyectos, escritores, periodistas o cualquier persona que necesite convertir audio en texto por motivos laborales o educativos. Además, ELISA está preparada para facilitar el resumen de textos transcritos mediante herramientas de IA.

## Instalación
Para instalar ELISA, necesitarás:

- Apache con PHP
- Python 3.8 o superior

### Windows
Realiza las siguientes configuraciones en `generador.php` para la ejecución del script Python:

(Inserta las instrucciones específicas aquí)
Asegurar que el comando a continuacion contiene las rutas correctas :
$command = escapeshellcmd("start /B $pythonExe $pythonScript $mp3File $job_id $transcriptionFilePY > $ErrorFile 2>&1 ");

para esto debe definir : 
$upload_path = "../uploads/"; // Ruta de uploads de los archivos de audio respecto a la web ELISA
$upload_python = "C:\\Apache\\htdocs\\elisa\\uploads\\"; // Ruta absoluta en el servidor de la carpeta de uploads.
$pythonExe = "C:\\Python310\\python.exe"; //Ruta absoluta del python utilizado
$pythonScript = "C:\\Apache\\htdocs\\elisa\\assets\\py\\transcribir.py"; // ruta del script python utilizado
$outputFilehtml = "/transcriptions/"; // Ruta donde quieres que se guarde el archivo de salida respecto a la web ELISA
$outputFile = "C:\\Apache\\htdocs\\elisa\\transcriptions\\"; // Ruta absoluta donde quieres que se guarde el archivo de salida.


### Linux (Ubuntu 22)
Asegúrate de que Python 3.8 esté instalado y configurado para permitir la ejecución de Whisper:
Verficar version de Python utilizando el comando : python3 --version

## Uso
Para utilizar ELISA:

1. Asegúrate de que el archivo mp3 que deseas transcribir no supere la hora de duración y no esté corrupto para garantizar una transcripción exitosa.
2. (Inserta instrucciones específicas de uso, como comandos o pasos para ejecutar la transcripción)

## Contribuciones
Las contribuciones son bienvenidas, siempre y cuando se sigan las pautas de uso ético y respeto a los derechos humanos. Cualquier cuenta que haga un uso inadecuado de la plataforma será bloqueada.

## Licencia
Este proyecto está bajo la Licencia Pública General de GNU (GPL). Al utilizar o contribuir a ELISA, debes hacer referencia al autor y a la página del proyecto en GitHub.

## Contacto
Si tienes preguntas o necesitas contactar por motivos de colaboración o soporte, por favor utiliza los medios indicados en GitHub.

