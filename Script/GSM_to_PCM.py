import soundfile as sf
from pydub import AudioSegment
import os
import urllib.parse
import sys


def converter_gsm_para_pcm(input_path, output_path):
    # Carregar o arquivo GSM610
    audio = AudioSegment.from_file(input_path, format="wav")
    # Converter para PCM_16
    audio = audio.set_sample_width(2)  # 2 bytes (16 bits)
    # Salvar o novo arquivo como PCM_16
    audio.export(output_path, format="wav", codec="pcm_s16le")

def obter_informacoes_arquivo(arquivo):
    info = sf.info(arquivo)
    return info




if __name__ == "__main__":
    if len(sys.argv) > 1:  # Verifica se há argumentos após o nome do script
        id_empresa = sys.argv[1]  # O primeiro argumento é o nome a ser usado
        print(f"Corrigindo os arquivos da empresa de id: {id_empresa}")



        # diretorio_gsm = f"/media/belloni/Arquivos/DEVELOPMENT/HUMOR/SERVER/frontend/recording_GSM/{id_empresa}_gsm"
        # diretorio_pcm = f"/media/belloni/Arquivos/DEVELOPMENT/HUMOR/SERVER/frontend/recordings (3)/{id_empresa}"

        diretorio_gsm = f"/var/www/humoria/recordings(GSM)/{id_empresa}_gsm"
        diretorio_pcm = f"/var/www/humoria/recordings (3)/{id_empresa}"




        for dirs in os.listdir(diretorio_gsm):

            if os.path.exists(f"{diretorio_pcm}/{dirs}"):
                print (f"Diretório {dirs} já existe")
                
            else:
                os.mkdir(f"{diretorio_pcm}/{dirs}")

            
            for arquivo_gsm in os.listdir(f'{diretorio_gsm}/{dirs}'):

                arquivo_pcm = f"{arquivo_gsm[:-4]}_PCM_{id_empresa}{arquivo_gsm[-4:]}"
                file_path_gsm = os.path.join(diretorio_gsm, dirs, arquivo_gsm)
                file_path_pcm = os.path.join(diretorio_pcm, dirs, arquivo_pcm)


                if os.path.exists(file_path_pcm):
                    print (f"Arquivo {arquivo_pcm} já existe")
                    continue

                else:
            



                    print (f"Convertendo: {arquivo_gsm} para {arquivo_pcm}")
                    
                    print(file_path_gsm)
                    print(file_path_pcm)


                    converter_gsm_para_pcm(file_path_gsm, file_path_pcm)

                    informacoes = obter_informacoes_arquivo(file_path_pcm)
                    # Exibindo informações disponíveis
                    print(f"Informações do arquivo '{arquivo_pcm}':")
                    print(f"  Formato: {informacoes.format}")
                    print(f"  Número de canais: {informacoes.channels}")
                    print(f"  Taxa de amostragem: {informacoes.samplerate} Hz")
                    print(f"  Número de frames: {informacoes.frames}")
                    print(f"  Duração: {informacoes.duration} segundos")
                    # print(f"  Comprimido: {informacoes.compression}")
                    print(f"  Tipo de codificação: {informacoes.subtype}")

    else:
        print("Erro: Nenhum nome fornecido.")