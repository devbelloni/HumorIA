<?php

session_start();

require_once("../dbm/DbConnector.php");
require_once("./../controllers/CreateController.php");

// Caminho do arquivo de log
$logFile = "/var/www/humoria/system.log";

function logMessage($msg, $level = 'INFO') {
    global $logFile;
    error_log("[{$level}] {$msg}\n", 3, $logFile);
}

// Auth check — deve ser o primeiro passo
$horaAtual = time();
if (!isset($_SESSION['user_email'])) {
    header('location:./logout.php');
    exit;
}
$tempoDecorrido = $horaAtual - $_SESSION['ultima_atividade'];
if ($tempoDecorrido > 6000) {
    header('location:./logout.php');
    exit;
}

if (isset($_POST['email']) && isset($_POST['cnpj'])) {
    include_once("./../dbm/DBConfig.php");

    $dsn = DBConfig::getDSN();
    $username = DBConfig::getUsername();
    $password = DBConfig::getPassword();
    $options = DBConfig::getOptions();
    $servername = DBConfig::getServer();
    $dbname = DBConfig::getBdName();

    $empresa = array(
        'razao' => $_POST['razao'],
        'cnpj' => $_POST['cnpj'],
        'telefone' => $_POST['telefone'],
        'email' => $_POST['email'],
        'endereco' => $_POST['endereco'],
        'cidade' => $_POST['cidade'],
        'estado' => $_POST['estado'],
        'pais' => $_POST['pais'],
        'cep' => $_POST['cep'],
    );

    // Parâmetros
    $usuario = $_POST['pabx_user'];
    $servidor = $_POST['pabx'];
    $senha = $_POST['pabx_senha'];
    $origem = $_POST['pabx_dir'];

    // Criar conexão com o banco
    $dbConnector = new DBConnector($servername, $username, $password, $dbname);
    $userRegister = new CreateController($dbConnector, 'empresas');
    $empresaId = $userRegister->registerEmpresa($empresa);

    try {
        error_log("\n=== Nova Execução - " . date('Y-m-d H:i:s') . " ===\n", 3, $logFile);

        $diretorio = "/var/www/humoria/recordings (3)/{$empresaId}";
        $diretorio_aspas = "/var/www/humoria/'recordings (3)'/{$empresaId}";

        // Garantir que o diretório existe
        if (!is_dir($diretorio)) {
            if (!mkdir($diretorio, 0777, true)) {
                throw new Exception("Falha ao criar o diretório: {$diretorio}");
            }
            error_log("Diretório criado: {$diretorio}\n", 3, $logFile);
        }

                // Configuração do comando — escapeshellarg em todos os dados do usuário
        $sshpass = "/usr/bin/sshpass -p " . escapeshellarg($senha);
        $rsync = "/usr/bin/rsync -avz --progress --chown=www-data:www-data --chmod=ugo+rwx --debug=all";
        $ssh_opt = "-e " . escapeshellarg("ssh -o StrictHostKeyChecking=no");
        $remote_src = escapeshellarg("{$usuario}@{$servidor}:{$origem}");
        $local_dst = escapeshellarg($diretorio_aspas);
        $comando = "nohup $sshpass $rsync $ssh_opt $remote_src $local_dst";


        // Captura o horário atual para agendamento
        $currentTime = date('H:i');
        list($hour, $minute) = explode(':', $currentTime);

        // Configuração do comando cron
        $cronCommand = "$minute $hour * * * " . escapeshellarg($comando);

        // Lê as entradas de cron atuais
        $currentCron = shell_exec('crontab -l 2>/dev/null');
        
        // Verifica se o comando já existe
        if (strpos($currentCron, $comando) === false) {
            // Adiciona o comando ao cron
            $newCron = $currentCron . "\n" . $cronCommand . "\n";
            file_put_contents('/tmp/cron.tmp', $newCron);
            shell_exec('crontab /tmp/cron.tmp');
            unlink('/tmp/cron.tmp');
        } else {
            echo "O comando cron já está configurado.\n";
        }

        // Montar o comando
        $comando_now = "nohup $sshpass $rsync $ssh_opt $remote_src $local_dst 2>&1";

        // Executar o comando
        logMessage("Executando comando: $comando_now");
        $output = [];
        $return_var = 0;
        exec($comando_now, $output, $return_var);

        if ($return_var === 0) {
            logMessage("Comando executado com sucesso. Saída: " . implode("\n", $output));
        } else {
            logMessage("Erro ao executar comando. Saída: " . implode("\n", $output), 'ERROR');
        }

        echo "<script>
        alert('Sincronização concluída com sucesso.\nComando cron adicionado com sucesso!\n');
        window.location.href = 'painel_controle.php';
    </script>";
        error_log("Sincronização concluída com sucesso.\nComando cron adicionado com sucesso!\n", 3, $logFile);
    } catch (Exception $e) {
        error_log("Erro: " . $e->getMessage(), 3, $logFile);
    }

}
?>
