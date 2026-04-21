from connector import connector

class le_no_banco:

    def __init__(self, tabela, parametro, valor):
        self.tabela = tabela
        self.parametro = parametro
        self.valor = valor

    def getter(self):
        try:
            # Conecta ao banco
            conecta = connector()
            conn = conecta.connect()

            if conn:
                # Cria um cursor
                cursor = conn.cursor()

                # Query com parâmetro para evitar SQL Injection
                query = f"SELECT * FROM {self.tabela} WHERE {self.parametro} = %s"

                cursor.execute(query, (self.valor,))

                # Obtém os resultados da consulta
                results = cursor.fetchall()

                return results

            else:
                print("Erro ao conectar ao banco de dados")

        except Exception as e:
            print("Erro ao ler no banco de dados:", e)
        finally:
            # Fecha o cursor e a conexão
            try:
                cursor.close()
            except NameError:
                pass

            try:
                conn.close()
            except NameError:
                pass

