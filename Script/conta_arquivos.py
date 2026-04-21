import os
import sys


if __name__ == "__main__":
    if len(sys.argv) > 1:  # Verifica se há argumentos após o nome do script
        id_empresa = sys.argv[1]  # O primeiro argumento é o nome a ser usado
    else:
        print("Uso: python conta_arquivos.py <id_empresa>")
        sys.exit(1)

    os.chdir(f"/var/www/humoria/recordings (3)/{id_empresa}")

    diretorios = os.listdir()
    count = 0

    for subdiretorio in diretorios:
        os.chdir(f"/var/www/humoria/recordings (3)/{id_empresa}/{subdiretorio}")
        arquivos = os.listdir()
        print(arquivos)
        count += len(arquivos)

    print(count)

