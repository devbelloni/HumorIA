from vaderSentiment.vaderSentiment import SentimentIntensityAnalyzer
import os
import google.generativeai as genai


class EmotionFind():

    def __init__(self, transcricao:str):
        self.transcricao = transcricao

    def sentimentos(self):
        analisador = SentimentIntensityAnalyzer()


        # Verifica se tem algo transcrito
        if self.transcricao == "":
            return 0
        
        else:
            # Analisar o texto e obter a pontuação de sentimento
            pontuacao = analisador.polarity_scores(self.transcricao)

            # Verificar a pontuação de sentimento
            return pontuacao['compound']


