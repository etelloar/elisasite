#!/usr/bin/env python
# coding: utf-8

# In[3]:


#pip install librosa


# In[3]:


#pip install --upgrade librosa soundfile audioread


# In[ ]:


import librosa
#import whisper
import time
import numpy as np
import sys
import os
import signal
import logging

# Configura el logging
logging.basicConfig(filename='./debug.log', level=logging.DEBUG, 
                    format='%(asctime)s:%(levelname)s:%(message)s')

# Usa logging para imprimir un mensaje de seguimiento
logging.info("Inicio del proceso de transcripción")






def check_cancellation(job_id):
    # Comprueba si existe un archivo de cancelación específico para este job_id
    return os.path.exists(f'cancel_{job_id}.txt')
    
def divide_audio(filename, sr=22050, segment_length=60):  # segment_length en segundos
    y, sr = librosa.load(filename, sr=sr)
    total_length = len(y)
    segment_samples = sr * segment_length  # Cantidad de muestras por segmento
    segments = []

    for start in range(0, total_length, segment_samples):
        end = min(start + segment_samples, total_length)
        segments.append(y[start:end])

    return segments

def transcribe_audio(segment, model=None):
   try:
      # Asegúrate de que los datos estén en el formato correcto
      if segment.dtype != np.float32:
          segment = segment.astype(np.float32)

      # Normaliza el volumen
      segment = librosa.util.normalize(segment)

      # Transcribe
      #result = model.transcribe(segment)

      #return result.get("text", "")    
      time.sleep(5)  # Simula un delay para cada segmento
      logging.info("Transcripción de segmento completada")
      return "Texto simulado de transcripción."
   except Exception as e:
      logging.error("Error durante la transcripción del segmento: %s", e)
      raise  
    

def main_transcription(filename,job_id):
    # Carga el modelo una sola vez
    #model = whisper.load_model("large")
    time.sleep(2)  # Simula la carga del modelo
    #print(f"main_transcription")  
    # Divide el audio
    segments = divide_audio(filename)
    transcriptions = []
    progress_file = "./progress_"+job_id+".txt"

    for i, segment in enumerate(segments):

        # continua el script     
        #print(f"Transcribing segment {i+1}/{len(segments)}")
        start_time = time.time()
        #transcription = transcribe_audio(segment, model)
        transcription = transcribe_audio(segment)
        end_time = time.time()
        #print(f"Segment {i+1} transcribed in {end_time - start_time:.2f} seconds")       
        with open(progress_file,"w") as f:
           f.write(f"{(i + 1) / len(segments) * 100}")
           #print(transcription)  # Imprime la transcripción del segmento actual
        transcriptions.append(transcription)
        if check_cancellation(job_id):
        	      os.remove(f'cancel_{job_id}.txt')  # Asegúrate de eliminar el archivo de cancelación.
                sys.exit('Transcripción cancelada.')   
                
    
   
    return " ".join(transcriptions)


if __name__ == "__main__":
   try:
        if len(sys.argv) > 1:
           # Al inicio de tu script, antes de llamar a main_transcription()
           filename = sys.argv[1]  # Toma el nombre del archivo desde el argumento de la línea de comandos
           job_id = sys.argv[2]  #  el job_id único
           output_transcription_file = sys.argv[3]
           logging.info("output_transcription_file:%s",output_transcription_file)
           # Establece un manejador para limpieza si se recibe una señal de terminación
           def handle_sigterm(*args):
              sys.exit()
           signal.signal(signal.SIGTERM, handle_sigterm)
           start_time_global = time.time()  
           transcription = main_transcription(filename,job_id)
           end_time_global = time.time()
           elapsed_time_global = end_time_global - start_time_global
           # Guarda la transcripción y el tiempo transcurrido en un archivo
           with open(output_transcription_file, "w") as file:
              file.write(transcription)
              file.write(f"\n\nTranscripción completada en: {elapsed_time_global:.2f} segundos.")
           logging.info(transcription)   
           logging.info(f"Transcripción completada en: {elapsed_time_global:.2f} segundos.")
           
   except Exception as e: 
      logging.error("Error en el proceso de transcripción: %s", e)
