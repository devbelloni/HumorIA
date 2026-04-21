#!/bin/bash

# Recebe os parâmetros do PHP
SENHA=$1
USUARIO=$2
SERVIDOR=$3
ORIGEM=$4
DIRETORIO=$5

# Exibe os parâmetros (para depuração, opcional)
echo "Senha: $SENHA"
echo "Usuário: $USUARIO"
echo "Servidor: $SERVIDOR"
echo "Origem: $ORIGEM"
echo "Diretório: $DIRETORIO"

# Executa o comando rsync com sshpass
sshpass -p "$SENHA" rsync -avz --progress --debug=all -e ssh "$USUARIO@$SERVIDOR:$ORIGEM" "$DIRETORIO"

# Verifica o resultado do comando rsync
if [ $? -eq 0 ]; then
    echo "Rsync executado com sucesso."
else
    echo "Erro ao executar rsync."
    exit 1
fi
