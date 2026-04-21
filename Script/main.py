# from process_diretory import process_diretory
# import os

# # os.chdir("/media/belloni/Arquivos/DEVELOPMENT/HUMOR/SERVER/frontend/recordings (3)")
# os.chdir("/var/www/humoria/recordings (3)")


# diretorios = os.listdir()

# for dir in diretorios:
#     # print(f"Observando o diretório: {os.getcwd()}/{dir}")
#     processa = process_diretory(dir)
#     file_path=processa.process()



from process_diretory import process_diretory
import os

# Definir o diretório base
base_path = "/var/www/humoria/recordings (3)"
os.chdir(base_path)

# Obter lista de diretórios
try:
    diretorios = [d for d in os.listdir() if os.path.isdir(os.path.join(base_path, d))]
except OSError as e:
    print(f"Erro ao listar diretórios em {base_path}: {e}")
    exit(1)

# Processar cada diretório
for dir in diretorios:
    try:
        print(f"Processando diretório: {os.path.join(base_path, dir)}")
        processa = process_diretory(dir)
        file_path = processa.process()
        print(f"Processamento concluído para {dir}. Resultado: {file_path}")
    except Exception as e:
        print(f"Erro ao processar o diretório {dir}: {e}")
        continue

print("Todos os diretórios foram processados.")
