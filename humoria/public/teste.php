<?php
echo "Iniciando sincronização... " . date('Y-m-d H:i:s') . "<br>";

// Configuração do comando
$senha = "Humor2024";
$usuario = "humoria";
$servidor = "omniramalvirtual.my3cx.com.br";
$origem = "/var/lib/3cxpbx/Instance1/Data/Recordings/";
$destino = "/var/www/humoria/'recordings (3)'/70";

// Adicionando a opção para ignorar a verificação da chave do host
$sshpass = "/usr/bin/sshpass -p $senha";
$rsync = "/usr/bin/rsync -avz --progress --chown=www-data:www-data --chmod=ugo+rwx --debug=all";
$ssh = "-e 'ssh -o StrictHostKeyChecking=no'";
$comando = "$sshpass $rsync $ssh $usuario@$servidor:$origem $destino";

echo($comando);
// Executa o comando e captura a saída e o código de retorno
$output = [];
$returnCode = 0;
exec("nohup $comando 2>&1", $output, $returnCode);

// Exibe a saída do comando
echo "<pre>" . implode("\n", $output) . "</pre>";

// Exibe o código de retorno
if ($returnCode === 0) {
    echo "Sincronização concluída com sucesso!<br>";
} else {
    echo "Erro na sincronização. Código de retorno: $returnCode<br>";
    error_log("Erro ao executar o rsync. Comando: $comando. Código: $returnCode. Saída: " . implode("\n", $output));
}
?>
