import mysql.connector
import os

class connector:
    def connect(self):
        try:
            conn = mysql.connector.connect(
                host=os.environ.get("DB_HOST", "localhost"),
                user=os.environ.get("DB_USER", "root"),
                password=os.environ["DB_PASSWORD"],
                database=os.environ.get("DB_NAME", "humorai")
            )
            return conn
        except mysql.connector.Error as e:
            print(f"Erro ao conectar ao banco de dados: {e}")
            return None
