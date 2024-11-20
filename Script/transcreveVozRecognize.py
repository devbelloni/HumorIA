import speech_recognition as sr

class Transcrever():

    def __init__(self, arquivo_wav:str):
        self.arquivo_wav = arquivo_wav

    def transcrever_wav(self):
        

        recognizer = sr.Recognizer()
        try:

            # print(self.arquivo_wav)
            with sr.AudioFile(self.arquivo_wav) as source:
                audio_data = recognizer.record(source)
                texto_transcrito = recognizer.recognize_google(audio_data, language='pt-BR') 

            return texto_transcrito
 
        except:
            return ""
