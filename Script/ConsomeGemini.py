import os
import google.generativeai as genai
import json



class ConsomeGemini():

    def __init__(self, transcricao:str):

        self.transcricao = transcricao
        self.api_key = "AIzaSyDQO4IDL0AfZ_6Xn4D8Sv2_C0vaK0x_tCg"

        

        # Set up the model
        self.generation_config = {
        "temperature": 0.2, # Valores mais baixos para temperature são bons para comandos que exigem uma resposta mais determinista e menos aberta ou criativa, enquanto valores mais altos para temperature podem gerar resultados mais diversos ou criativos.
        "top_p": 0.5,# um valor FLOAT64 no intervalo [0.0,1.0] ajuda a determinar a probabilidade dos tokens selecionados. Especifique um valor mais baixo para respostas menos aleatórias e um valor mais alto para respostas mais aleatórias. O padrão é 0.95.
        "top_k": 0,
        "max_output_tokens": 2048,
        }

        self.safety_settings = [
        {
            "category": "HARM_CATEGORY_HARASSMENT",
            "threshold": "BLOCK_MEDIUM_AND_ABOVE"
        },
        {
            "category": "HARM_CATEGORY_HATE_SPEECH",
            "threshold": "BLOCK_MEDIUM_AND_ABOVE"
        },
        {
            "category": "HARM_CATEGORY_SEXUALLY_EXPLICIT",
            "threshold": "BLOCK_MEDIUM_AND_ABOVE"
        },
        {
            "category": "HARM_CATEGORY_DANGEROUS_CONTENT",
            "threshold": "BLOCK_MEDIUM_AND_ABOVE"
        },
        ]




    def GeminiAPI(self):


        # genai.configure(api_key=os.environ[self.apy_key])

        genai.configure(api_key= self.api_key)

        

        # model = genai.GenerativeModel(model_name="gemini-1.5-pro-latest",
        # model = genai.GenerativeModel(model_name="gemini-1.0-pro-vision",

        model = genai.GenerativeModel(model_name="gemini-pro",
                                    generation_config=self.generation_config,
                                    safety_settings=self.safety_settings)

        answer = model.generate_content(self.transcricao)


        return answer
