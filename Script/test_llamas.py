import os
from langchain_ollama import OllamaLLM as Ollama
import json
import re

class ConsomeGemini:
    def __init__(self, transcricao: str):
        self.transcricao = transcricao
        # Configurar o modelo com temperatura baixa para respostas mais determinísticas
        self.llm = Ollama(model="llama3:8b", temperature=0.0)

    def GeminiAPI(self):
        # Instrução mais rígida e clara
        prompt = f"""
Analise o texto a seguir e retorne **apenas** um JSON com os índices "palavras_principais" (contendo no máximo 5 palavras principais) e "emoção" (a emoção transmitida pelo texto). Não inclua nenhum texto adicional, explicação ou frase fora do JSON. Texto: {self.transcricao}

Exemplo de saída esperada:
{{
    "palavras_principais": ["palavra1", "palavra2", "palavra3", "palavra4", "palavra5"],
    "emoção": "emoção"
}}
"""
        # Invocar o modelo
        answer = self.llm.invoke(prompt)

        # Pós-processamento para extrair apenas o JSON
        try:
            # Usar regex para encontrar o primeiro bloco JSON na resposta
            json_match = re.search(r'\{.*?\}', answer, re.DOTALL)
            if json_match:
                json_str = json_match.group(0)
                # Validar e parsear o JSON
                json_data = json.loads(json_str)
                return json.dumps(json_data)  # Retorna o JSON como string
            else:
                raise ValueError("Nenhum JSON encontrado na resposta")
        except json.JSONDecodeError as e:
            print(f"Erro ao decodificar JSON: {e}")
            return None
        except ValueError as e:
            print(f"Erro: {e}")
            return None

if __name__ == "__main__":
    transcricao = (
        'cinco Comerciários próximo dia de casa Bom dia isso é meu nome é Robson eu falo aqui da ramal virtual '
        'Telecom O que posso falar sobre telefonia aí do sindicato sobre internet por favor sobre o quê telefonia '
        'internet isso é lá na nossa sede você quer anotar o telefone tem um e-mail que posso passar lá tem o e-mail '
        'do financeiro qual que é por favor é financeiro@gmail.com org.br Ah tá bom eu posso falar então mas se você '
        'puder me passar o telefone também de lá passa o 4574 4574 288 2888 em Guarulhos'
    )
    consome = ConsomeGemini(transcricao=transcricao)
    resposta = consome.GeminiAPI()
    if resposta:
        try:
            json_data = json.loads(resposta)
            print(json.dumps(json_data, indent=4, ensure_ascii=False))
        except json.JSONDecodeError as e:
            print(f"Erro ao decodificar JSON: {e}")