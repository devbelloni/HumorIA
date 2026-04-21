from langchain_ollama import OllamaLLM as Ollama
import json
import os
import re
import logging

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

class ConsomeGemini:
    def __init__(self, transcricao: str):
        self.transcricao = transcricao
        self.llm = Ollama(
            model="llama3:8b",
            temperature=0.1,
            base_url=os.environ.get("OLLAMA_HOST", "http://localhost:11434"),
        )

    def GeminiAPI(self):
        prompt = f"""
        Analise o texto a seguir e retorne **apenas** um JSON com os índices "palavras_principais" (contendo no máximo 5 palavras principais) e "emoção" (a emoção transmitida pelo texto). Não inclua nenhum texto adicional, explicação ou frase fora do JSON. Texto: {self.transcricao}

        Exemplo de saída esperada:
        {{
            "palavras_principais": ["palavra1", "palavra2", "palavra3", "palavra4", "palavra5"],
            "emoção": "emoção"
        }}
        """
        # Invoke LLaMA
        answer = self.llm.invoke(prompt)
        logging.info(f"Raw LLaMA response: {answer}")

        # Try to extract JSON with a more flexible regex
        json_data = None
        try:
            # Match JSON, including cases with backticks or extra text
            json_match = re.search(r'(\{[\s\S]*?\})', answer, re.MULTILINE)
            if json_match:
                json_str = json_match.group(1)
                logging.info(f"Extracted JSON: {json_str}")
                json_data = json.loads(json_str)
            else:
                # Try to find JSON within code blocks (e.g., ```json
                json_match = re.search(r'```json\n([\s\S]*?)\n```', answer, re.MULTILINE)
                if json_match:
                    json_str = json_match.group(1)
                    logging.info(f"Extracted JSON from code block: {json_str}")
                    json_data = json.loads(json_str)
                else:
                    logging.error("No JSON found in response")
        except json.JSONDecodeError as e:
            logging.error(f"JSON decoding error: {e}")
            logging.debug(f"Attempted JSON string: {json_str}")

        # Fallback to default JSON if extraction fails
        if json_data is None:
            logging.warning("Using fallback JSON due to extraction failure")
            json_data = {
                "palavras_principais": [],
                "emoção": "Neutro"
            }

        return json_data