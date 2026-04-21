import argparse
import os
from datetime import datetime, timedelta
import smtplib
import re
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from connector import connector

# Configuração do e-mail — definir EMAIL_SENDER e EMAIL_PASSWORD como variáveis de ambiente
SMTP_SERVER = "smtp.gmail.com"
SMTP_PORT = 587
EMAIL_SENDER = os.environ["EMAIL_SENDER"]
EMAIL_PASSWORD = os.environ["EMAIL_PASSWORD"]

parser = argparse.ArgumentParser(description="Gerar e enviar relatório do Humor IA")
parser.add_argument("--prazo", required=True, help="Frequência escolhida (ex: 'diario', 'mensal', '3dias')")
parser.add_argument("--id", required=True, type=int, help="ID do usuário")
args = parser.parse_args()

# Se --currentTime não for fornecido, usar a data e hora atuais

currentTime = datetime.now()


# Mapeamento de prazos para número de dias
PRAZO_MAP = {
    "diario": 1,
    "3dias": 3,
    "semanal": 7,
    "15dias": 15,
    "mensal": 30,
    "segunda": 7,
    "quarta": 7,
    "sexta": 7,
    "sábado": 7
}

# Função para extrair ou mapear o prazo
def get_prazo_days(prazo_str):
    if prazo_str.lower() in PRAZO_MAP:
        return PRAZO_MAP[prazo_str.lower()]
    match = re.search(r'\d+', prazo_str)
    if match:
        return int(match.group())
    raise ValueError(f"Formato de prazo inválido: {prazo_str}. Use formatos como 'diario', 'mensal', etc.")

# Calcular as datas
prazo_numerico = get_prazo_days(args.prazo)
startTime = currentTime - timedelta(days=prazo_numerico)
endTime = currentTime - timedelta(days=1)  # Exclui o dia atual
startTime_str = startTime.strftime("%Y-%m-%d")
endTime_str = endTime.strftime("%Y-%m-%d")

# Conectar ao banco de dados via `connector.py`
conecta = connector()
conn = conecta.connect()

if not conn:
    print("Erro ao conectar ao banco de dados.")
    exit(1)

cursor = conn.cursor()

# Buscar nome e e-mail do usuário
query_usuario = "SELECT nome, email FROM users WHERE id = %s"
cursor.execute(query_usuario, (args.id,))
user = cursor.fetchone()

if not user:
    print(f"Usuário ID {args.id} não encontrado.")
    exit(1)

nome, email_destino = user

# Validar e-mail antes de enviar
def is_valid_email(email):
    pattern = r'^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$'
    return re.match(pattern, email) is not None

if not is_valid_email(email_destino):
    print(f"Erro: E-mail inválido detectado: {email_destino}")
    exit(1)

# Buscar os arquivos do usuário
query_files = "SELECT id FROM files WHERE id_empresa = %s"
cursor.execute(query_files, (args.id,))
files = cursor.fetchall()
file_ids = [str(file[0]) for file in files]

if not file_ids:
    print("Nenhum arquivo associado ao usuário.")
    exit(1)

file_ids_str = ",".join(file_ids)

# Buscar contagem de emoções `VADER`
query_vader = """
    SELECT emotion_vader, COUNT(*) 
    FROM emotion_vader 
    WHERE id_file IN ({}) 
    AND CONVERT(data_criacao, DATE) BETWEEN %s AND %s 
    GROUP BY emotion_vader
""".format(file_ids_str)

cursor.execute(query_vader, (startTime_str, endTime_str))
result_vader = cursor.fetchall()

positivos, neutros, negativos = 0, 0, 0
for emotion, count in result_vader:
    if emotion == "Positivo":
        positivos = count
    elif emotion == "Neutro":
        neutros = count
    elif emotion == "Negativo":
        negativos = count

# Buscar conversas com emoções negativas (VADER)
query_negativas = """
    SELECT e.id_file, f.path, f.data
    FROM emotion_vader e
    JOIN files f ON e.id_file = f.id
    WHERE e.emotion_vader = 'Negativo' 
    AND e.id_file IN ({}) 
    AND CONVERT(f.data_criacao, DATE) BETWEEN %s AND %s 
""".format(file_ids_str)

cursor.execute(query_negativas, (startTime_str, endTime_str))
conversas_negativas = cursor.fetchall()

# Buscar emoção mais frequente segundo `Gemini`
query_gemini = """
    SELECT emotion_gemini, COUNT(*) 
    FROM emotion_gemini 
    WHERE id_file IN ({}) 
    AND CONVERT(data_criacao, DATE) BETWEEN %s AND %s 
    GROUP BY emotion_gemini 
    ORDER BY COUNT(*) DESC 
    LIMIT 1
""".format(file_ids_str)

cursor.execute(query_gemini, (startTime_str, endTime_str))
emo_gemini_result = cursor.fetchone()
emoção_mais_comum = emo_gemini_result[0] if emo_gemini_result else "Indefinida"

# Buscar conversas relacionadas à emoção mais frequente (Gemini)
query_gemini_conversas = """
    SELECT e.id_file, f.path, f.data
    FROM emotion_gemini e
    JOIN files f ON e.id_file = f.id
    WHERE e.emotion_gemini = %s 
    AND e.id_file IN ({}) 
    AND CONVERT(f.data_criacao, DATE) BETWEEN %s AND %s 
""".format(file_ids_str)

cursor.execute(query_gemini_conversas, (emoção_mais_comum, startTime_str, endTime_str))
conversas_gemini = cursor.fetchall()

# Fechar a conexão com o banco
cursor.close()
conn.close()

# Formatar e-mail
html_body = f"""
<html>
<head></head>
<body>
    <p>Olá <b>{nome}</b>,</p>
    <p>O sistema Humor IA identificou as seguintes ocorrências de <b>{startTime_str}</b> a <b>{endTime_str}</b>, segundo configurado no painel administrador.</p>
    
    <p><b>Pelo sistema VADER de Análise de Emoções, houveram:</b></p>
    <ul>
        <li><b>{positivos}</b> conversas com emoções positivas</li>
        <li><b>{neutros}</b> conversas com emoções neutras</li>
        <li><b>{negativos}</b> conversas com emoções negativas</li>
    </ul>

    <p><b>Pelo mesmo sistema, seguem as conversas com aspectos negativos:</b></p>
    <table border='1' cellpadding='5'>
        <tr>
            <th>Data</th>
            <th>Ramal</th>
        </tr>
        {''.join(f'<tr><td>{conv[2]}</td><td>{conv[1]}</td></tr>' for conv in conversas_negativas)}
    </table>

    <p><b>Pelo sistema de Análise de Emoções Gemini (Google), foi identificado que a maior parte das conversas tiveram emoção <b>{emoção_mais_comum}</b>:</b></p>
    <table border='1' cellpadding='5'>
        <tr>
            <th>Data</th>
            <th>Ramal</th>
        </tr>
        {''.join(f'<tr><td>{conv[2]}</td><td>{conv[1]}</td></tr>' for conv in conversas_gemini)}
    </table>

    <p>Acesse o sistema para saber mais: <a href='https://www.humoria.com.br'>www.humoria.com.br</a></p>

    <img src='https://www.humoria.com.br/imagem_logo.png' alt='HUMORIA' width='200'>
</body>
</html>
"""

# Enviar e-mail
msg = MIMEMultipart()
msg["From"] = EMAIL_SENDER
msg["To"] = email_destino
msg["Subject"] = "Relatório Humor IA"
msg.attach(MIMEText(html_body, "html"))

print(f"Usando data e hora: {currentTime}")
print(f"Prazo: {args.prazo}, ID: {args.id}")

try:
    server = smtplib.SMTP(SMTP_SERVER, SMTP_PORT)
    server.starttls()
    server.login(EMAIL_SENDER, EMAIL_PASSWORD)
    server.sendmail(EMAIL_SENDER, email_destino, msg.as_string())
    server.quit()
    print("E-mail enviado com sucesso!")
except Exception as e:
    print(f"Erro ao enviar e-mail: {e}")