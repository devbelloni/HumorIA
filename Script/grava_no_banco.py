from connector import connector

class grava_no_banco:

    def __init__(self, tabela, colunas, valores):
        self.tabela = tabela
        self.colunas = colunas
        self.valores = valores

    def gravar(self):
        try:
            # Conecta ao banco
            conecta = connector()
            conn = conecta.connect()

            if conn:
                # Cria um cursor
                cursor = conn.cursor()

                # Query
                query = f"INSERT INTO {self.tabela} ({', '.join(self.colunas)}) VALUES {self.valores}"
                print("Query:", query)

                # Executa a query
                cursor.execute(query)

                # Commit da transação
                conn.commit()

                print("Dados inseridos com sucesso no banco de dados!")
            else:
                print("Erro ao conectar ao banco de dados")

        except Exception as e:
            print("Erro ao gravar no banco de dados:", e)
        finally:
            # Fecha a conexão e o cursor
            if 'cursor' in locals() and cursor:
                cursor.close()
            if 'conn' in locals() and conn:
                conn.close()