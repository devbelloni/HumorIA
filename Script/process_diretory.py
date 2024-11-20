import traceback
from humor_analize import humor_analize
from ConsomeGemini import ConsomeGemini
from transcreveVozRecognize import Transcrever
from grava_no_banco import grava_no_banco
from get_no_banco import le_no_banco
from connector import connector
import traceback
import json
import os
import re
from datetime import datetime



class process_diretory:  

    def __init__(self, id_empresa):
        self.id_empresa=id_empresa


    def process(self):
        # define o diretório 
        directory_path = f"/var/www/humoria/recordings (3)/{self.id_empresa}"
        # directory_path = f"/media/belloni/Arquivos/DEVELOPMENT/HUMOR/SERVER/frontend/recordings (3)/{self.id_empresa}"


        print(directory_path)
        try:
            # verifica se existe o diretório
            if not os.path.exists(directory_path):
                print ("ERRO")
                return {'error': f"Directory '{directory_path}' does not exist."}

            # identifica o diretório do ramal
            for dirs in os.listdir(directory_path):
                # print(dirs)
                for file in os.listdir(f'{directory_path}/{dirs}'):
                    file_path = os.path.join(directory_path, dirs, file)

                    # conecta no banco
                    conecta = connector()
                    conn=conecta.connect()

                    try:

                        # Verifica se o arquivo está no banco de dados    
                        cursor=conn.cursor()
                        query = f"SELECT * FROM files WHERE filename = '{file}'"

                        # print(f'Query: {query}')

                        cursor.execute(query)
                        arquivo = cursor.fetchall()
                        # print(f'Arquivo: {arquivo}')

                        # condicional se o arquivo existe
                        if arquivo:
                            print('Arquivo já está no banco de dados')
                            continue
                        else:

                            # CONVOCA AS IAS!!!!!

                            # tenta convocar o transcritor
                            try:
                                print(f"file_path: {file_path}")
                                transcritor = Transcrever(file_path)
                                transcricao = transcritor.transcrever_wav()

                                print (f'Transcricao: {transcricao}')


                                print(f"id_empresa: {self.id_empresa}")

                                # SEPARANDO A DATA

                                if self.id_empresa=="1":
                                    print(f"id_empresa: {self.id_empresa}")

                                    # Usando expressão regular para encontrar a parte desejada após o segundo underline
                                    match = re.search(r'^(?:[^_]*_){2}([^_]*)', file)
                                    # match=match[0:7]

                                    if match:
                                        data_str = match.group(1)  # Captura o texto após o segundo underline
                                        # Formatando a data no formato desejado (DD/MM/AAAA)
                                        data_formatada = datetime.strptime(data_str[0:8], '%Y%m%d').strftime('%Y-%m-%d')


                                if self.id_empresa>"1":
                                    print(f"id_empresa: {self.id_empresa}")


                                    print(f"file: {file}")
                                    
                                    # Usando expressão regular para encontrar a parte desejada no início do filename
                                    # year=file[0:4]
                                    # month=file[6:7]
                                    # day=file[9:10]
                                    # data_formatada=f'{year}-{month}-{day}'

                                    data_part = file.split('_')[0]
                                    # Se precisar apenas da data no formato "YYYY-MM-DD"
                                    data_formatada = data_part[:10]
                                    
                                    print(f'Data: {data_formatada}')

                                # GRAVANDO OS RESULTADOS - FILES
                                # Gravando na tabela Files
                                tabela='files'
                                colunas=('id_empresa','filename','path', 'data', 'resumo')
                                valores=(self.id_empresa, file, dirs, data_formatada, transcricao)
                                grava=grava_no_banco(tabela, colunas, valores)
                                grava.gravar()


                                if not transcricao:
                                    print('Transcrição está vazia')
                                    with open('saida.txt','w') as f:
                                        f.write(f'Transcrição está vazia\n')

                                else:


                                    # HUMOR BASICS
                                    humor = humor_analize(transcricao)
                                    humor_basics=humor.humor_core()

                                    print(f'Humor Basic: {humor_basics}')




                                    # HUMOR COMPLEX
                                    humor_gemini = ConsomeGemini(f"Você deve analisar o texto e criar um json com o índice palavras principais e emoção, somente com as 5 palavras principais no máximo e somente qual a emoção está transmitindo o texto todo sem muita explicação: {transcricao}. Use palavras_principais e emoção como índices do json!")

                                    humor_complex = humor_gemini.GeminiAPI()
                                    print(humor_complex)

                                    try:

                                        # Acessa o texto dentro do objeto Part/
                                        part = humor_complex._result.candidates[0].content.parts[0]
                                        json_data = part.text

                                        # Remove o prefixo "```json" e o sufixo "```"
                                        json_data = json_data.replace("```json", "").replace("```", "").strip()

                                        # Analisa o JSON
                                        data = json.loads(json_data)

                                        # Acessa as palavras principais
                                        palavras_principais = data["palavras_principais"]
                                        emos = data["emoção"]
                                        
                                        words = []
                                        # Exibe as palavras principais
                                        for palavra in palavras_principais:
                                            words.append(palavra)
                                        
                                        print(f'Palavras Principais: {words}')

                                        # Exibe as emoções
                                        print(f'"Emoções: {emos}')

                                    except Exception as e:
                                        error_message = f"Gemini respodeu erro: {str(e)}"
                                        print(error_message)
                                        print(traceback.format_exc())
                                        words=[]
                                        emos=''



                                    # WORDS
                                    # Extrai apenas as palavras principais
                                    palavras_principais = [palavra[0] for palavra in humor_basics['palavras_principais']]
                                    tabela='files'
                                    parameter=('filename')
                                    valores=(file)
                                    # Le o file
                                    leitor = le_no_banco(tabela, parameter, valores)
                                    answer_files=leitor.getter()
                                    print(answer_files)
                                    # Gravando na tabela Words
                                    tabela_words='words'
                                    colunas_Words=('id_file','word')
                                    for word in palavras_principais:
                                        valores=(answer_files[0][0], word)
                                        grava=grava_no_banco(tabela_words, colunas_Words, valores)
                                        grava.gravar()

                                    # EMOTION_GEMINI
                                    # Gravando na tabela emotion_gemini
                                    tabela_gemini='emotion_gemini'
                                    colunas_gemini=('id_file','emotion_gemini')
                                    valores_gemini=(answer_files[0][0], emos)
                                    grava=grava_no_banco(tabela_gemini, colunas_gemini, valores_gemini)
                                    grava.gravar()

                                    # EMOTION_VADER
                                    # Gravando na tabela emotion_vader
                                    tabela_vader='emotion_vader'
                                    colunas_vader=('id_file','emotion_vader')
                                    valores_vader=(answer_files[0][0], humor_basics['emoção'])
                                    grava=grava_no_banco(tabela_vader, colunas_vader, valores_vader)
                                    grava.gravar()


                                # Quando não encontrar mais arquivos no diretório, pula o for
                                if not os.path.isfile(file_path):
                                    continue  



                                print(transcricao)

                            
                            except Exception as e:
                                error_message = f"Error: {str(e)}"
                                print(error_message)
                                print(traceback.format_exc())

                    # Se não converte, avisa que o arquivos está vazio.
                    except Exception as e:
                        error_message = f"General error in process_directory: {str(e)}"
                        print(error_message)
                        print(traceback.format_exc())
                        

        except Exception as e:
            error_message = f"General error in process_directory: {str(e)}"
            print(error_message)
            print(traceback.format_exc())
