from connector import connector

class le_no_banco:

    def __init__(self, data_inicio, data_final, empresa_id):
        self.data_inicio = data_inicio
        self.data_final = data_final
        self.empresa_id = empresa_id

    def getter(self):
        try:
            # Conecta ao banco
            conecta = connector()
            conn = conecta.connect()

            if conn:
                # Cria um cursor
                cursor = conn.cursor()

                # Query com parâmetros para evitar SQL Injection
                query = "SELECT * FROM palavras WHERE data BETWEEN %s AND %s AND id_empresa = %s"

                cursor.execute(query, (self.data_inicio, self.data_final, self.empresa_id))

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
