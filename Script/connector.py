import mysql.connector

class connector:
    def connect(self):
        try:
            conn = mysql.connector.connect(
                host="localhost",
                user="root",
                password="2aGHQPcW2#wxrC@e",
                database="humorai"
            )
            # password="2aGHQPcW2#wxrC@e"

            print("Conexão bem-sucedida!")
            return conn
        except mysql.connector.Error as e:
            print(f"Erro ao conectar ao banco de dados: {e}")
            return None
