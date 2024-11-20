<?php
/*

20/11/24


Explicação:
Conexão SSH:

O script utiliza ssh2_connect para estabelecer a conexão com o servidor SSH.
Autenticação:

Realiza a autenticação usando ssh2_auth_password (você pode ajustar para usar uma chave privada com ssh2_auth_pubkey_file se necessário).
Verificação se o arquivo já existe:

Usa ssh2_sftp para acessar o sistema de arquivos remoto e file_exists para verificar se o arquivo de destino já está presente.
Cópia do Arquivo:

O comando ssh2_scp_recv copia o arquivo do servidor remoto para o local especificado.
Evitar Sobrescrita:

Se o arquivo já existir no diretório de destino, ele não será copiado novamente.

Certifique-se de que a extensão ssh2 está habilitada no PHP:

sudo apt install php-ssh2

Ou no Windows, habilite php_ssh2.dll no php.ini.

*/




function copiarArquivoSSH($host, $usuario, $senha, $caminhoOrigem, $caminhoDestino) {
    // Conectando ao servidor SSH
    $conexao = ssh2_connect($host, 22);
    if (!$conexao) {
        die("Não foi possível conectar ao servidor SSH: $host");
    }

    // Autenticação
    if (!ssh2_auth_password($conexao, $usuario, $senha)) {
        die("Falha na autenticação com o servidor SSH.");
    }

    // Verifica se o arquivo já existe no destino
    $sftp = ssh2_sftp($conexao);
    $destinoCompleto = "ssh2.sftp://$sftp$caminhoDestino";

    if (file_exists($destinoCompleto)) {
        echo "O arquivo já existe no destino: $caminhoDestino\n";
        return false;
    }

    // Copiar o arquivo
    if (ssh2_scp_recv($conexao, $caminhoOrigem, $caminhoDestino)) {
        echo "Arquivo copiado com sucesso para: $caminhoDestino\n";
        return true;
    } else {
        echo "Erro ao copiar o arquivo.\n";
        return false;
    }
}

// Exemplo de uso
$host = '192.168.1.100';
$usuario = 'usuario';
$senha = 'senha';
$caminhoOrigem = '/home/usuario/arquivo.txt'; // Caminho no servidor remoto
$caminhoDestino = '/local/destino/arquivo.txt'; // Caminho no servidor local

copiarArquivoSSH($host, $usuario, $senha, $caminhoOrigem, $caminhoDestino);
?>
