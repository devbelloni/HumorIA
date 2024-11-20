from process_diretory import process_diretory
import os

# os.chdir("/media/belloni/Arquivos/DEVELOPMENT/HUMOR/SERVER/frontend/recordings (3)")
os.chdir("/var/www/humoria/recordings (3)")


diretorios = os.listdir()

for dir in diretorios:
    print(f"Observando o diretório: {os.getcwd()}/{dir}")
    processa = process_diretory(dir)
    file_path=processa.process()


