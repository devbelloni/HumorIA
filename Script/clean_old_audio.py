#!/usr/bin/env python3
import os
import time
import logging
from pathlib import Path
import shutil
import shlex

# Configurações
BASE_DIR = "/var/www/humoria/recordings (3)"
LOG_FILE = "/var/log/clean_old_audio.log"
MIN_DISK_SPACE = 100 * 1024 * 1024  # 100 MB em bytes
ONE_YEAR_SECONDS = 365 * 24 * 60 * 60  # 1 ano em segundos
AUDIO_EXTENSIONS = {'.wav', '.mp3', '.ogg', '.flac', '.aac'}  # Extensões de áudio

# Configurar logging
logging.basicConfig(
    filename=LOG_FILE,
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

def check_disk_space():
    """Verifica se há espaço suficiente no disco."""
    disk_usage = shutil.disk_usage(BASE_DIR)
    free_space = disk_usage.free
    if free_space < MIN_DISK_SPACE:
        raise RuntimeError(f"Espaço em disco insuficiente: apenas {free_space / 1024 / 1024:.2f} MB disponíveis")
    logging.info(f"Espaço disponível: {free_space / 1024 / 1024:.2f} MB")

def is_audio_file(file_path):
    """Verifica se o arquivo tem uma extensão de áudio."""
    return Path(file_path).suffix.lower() in AUDIO_EXTENSIONS

def delete_old_audio_files():
    """Deleta arquivos de áudio com mais de 1 ano e registra no log."""
    cutoff_time = time.time() - ONE_YEAR_SECONDS
    total_size_deleted = 0
    files_deleted = 0

    try:
        # Verificar espaço em disco
        check_disk_space()

        logging.info(f"Iniciando limpeza de arquivos de áudio antigos em {BASE_DIR}")

        # Percorrer todas as pastas de empresas
        for empresa_dir in Path(BASE_DIR).iterdir():
            if not empresa_dir.is_dir():
                continue  # Ignorar arquivos no nível de BASE_DIR

            # Percorrer pastas de ramais
            for ramal_dir in empresa_dir.iterdir():
                if not ramal_dir.is_dir():
                    continue  # Ignorar arquivos no nível de empresa

                # Percorrer arquivos na pasta do ramal
                for audio_file in ramal_dir.iterdir():
                    if audio_file.is_file() and is_audio_file(audio_file):
                        file_mtime = audio_file.stat().st_mtime
                        if file_mtime < cutoff_time:
                            file_size = audio_file.stat().st_size
                            file_path = str(audio_file)
                            try:
                                audio_file.unlink()  # Deletar arquivo
                                total_size_deleted += file_size
                                files_deleted += 1
                                logging.info(f"Deletado: {file_path} ({file_size / 1024 / 1024:.2f} MB)")
                            except Exception as e:
                                logging.error(f"Falha ao deletar {file_path}: {str(e)}")

        logging.info(f"Limpeza concluída. Arquivos deletados: {files_deleted}, Espaço liberado: {total_size_deleted / 1024 / 1024:.2f} MB")

        # Verificar espaço após limpeza
        disk_usage = shutil.disk_usage(BASE_DIR)
        logging.info(f"Espaço disponível após limpeza: {disk_usage.free / 1024 / 1024:.2f} MB")

        # Alerta se o espaço ainda for crítico
        if disk_usage.free < 500 * 1024 * 1024:  # Menos de 500 MB
            logging.warning("Espaço em disco ainda crítico!")

    except Exception as e:
        logging.error(f"Erro durante a limpeza: {str(e)}")
        raise

def adjust_permissions():
    """Ajusta permissões do diretório base para www-data."""
    try:
        os.system(f"chown -R www-data:www-data {shlex.quote(BASE_DIR)}")
        os.system(f"chmod -R 775 {shlex.quote(BASE_DIR)}")
        logging.info(f"Permissões ajustadas para {BASE_DIR}")
    except Exception as e:
        logging.error(f"Falha ao ajustar permissões: {str(e)}")
        raise

if __name__ == "__main__":
    try:
        # Ajustar permissões antes de iniciar
        adjust_permissions()
        # Executar a limpeza
        delete_old_audio_files()
    except Exception as e:
        logging.error(f"Execução interrompida: {str(e)}")
        exit(1)
    exit(0)