from nltk.sentiment.vader import SentimentIntensityAnalyzer
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
from collections import Counter
import nltk
nltk.download('stopwords')
nltk.download('vader_lexicon')
nltk.download('punkt') 
 
class humor_analize():

    def __init__(self, transcricao:str):

        self.transcricao = transcricao
        self.palavras_list=[]

    def humor_core(self):



        sid = SentimentIntensityAnalyzer()  # Inicializa o analisador de sentimento


        print(f"Frase: {self.transcricao}")  # Exibe a frase antes da análise

        try:
            ss = sid.polarity_scores(self.transcricao)


            # Classifica o sentimento com base na pontuação e exibe o resultado
            if ss['compound'] >= 0.05:
                Sentimento= "Positivo"
            elif ss['compound'] <= -0.05:
                Sentimento= "Negativo"
            else:
                Sentimento= "Neutro"

        # # Exibe os scores de polaridade, neutralidade e subjetividade
        # for k, v in sorted(ss   .items()):
        #     print(f"{k.upper()}: {v:.3f}", end=' ')
        # print()

        # Tokenização
            tokens = word_tokenize((self.transcricao).lower())

            # Remover stopwords
            stop_words = set(stopwords.words('portuguese'))
            tokens = [word for word in tokens if word.isalnum() and word not in stop_words]

            # Calcular frequência de palavras
            frequencia_palavras = Counter(tokens)

            # Número de palavras-chave que deseja extrair
            num_palavras_chave = 5

            # Extrair as palavras-chave mais frequentes
            palavras_chave = frequencia_palavras.most_common(num_palavras_chave)

            # print(f"Palavras-chave da conversa:{palavras_chave}")
            return ({'palavras_principais': palavras_chave,'emoção': Sentimento})
        
        except:
            print("Transcrição está vazia.")

        # for palavra, frequencia in palavras_chave:
        #     print(palavra) 