
from process_diretory import process_diretory
import os

# Definir o diretório base
base_path = "/var/www/humoria/recordings (3)"

processa = process_diretory('1')
file_path = processa.process()
print(f"Processamento concluído para {base_path}. Resultado: {file_path}")

