<?php

session_start();

// Inclui arquivos de conexão
include_once("./../dbm/DbConnector.php");
include_once("./../dbm/DBConfig.php");

// Obtém os dados da conexão
$servername = DBConfig::getServer();
$username = DBConfig::getUsername();
$password = DBConfig::getPassword();
$dbname = DBConfig::getBdName();

// Conecta ao banco
$dbConnector = new DBConnector($servername, $username, $password, $dbname);
$conn = $dbConnector->connect();

// Verifica se o método da requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura o valor enviado no formulário
    $prazo = $_POST['prazo'];
    $user_id = $_SESSION['user_id'];

    // Atualiza o banco de dados com o novo prazo (prepared statement)
    $stmt = $conn->prepare("UPDATE users SET prazo = ? WHERE id = ?");
    $stmt->bind_param('si', $prazo, $user_id);
    if ($stmt->execute()) {
        echo "Prazo atualizado no banco.";
    } else {
        die("Erro ao atualizar o banco: " . $stmt->error);
    }
    $stmt->close();

    // Fecha a conexão
    $conn->close();

    // ---------------- CONFIGURAÇÃO DO CRON JOB -----------------
    
    // Define o fuso horário correto
    date_default_timezone_set('America/Sao_Paulo');

    // Define expressões cron para cada tipo de prazo
    $cronJobs = [
        "diario" => "0 8 * * *",
        "3dias" => "0 8 */3 * *",
        "segunda" => "0 8 * * 1",
        "quarta" => "0 8 * * 3",
        "sexta" => "0 8 * * 5",
        "sabado" => "0 8 * * 6",
        "15dias" => "0 8 */15 * *",
        "mensal" => "0 8 1 * *"
    ];

    // Define a expressão do cron com base no prazo
    $cronExpr = isset($cronJobs[$prazo]) ? $cronJobs[$prazo] : "0 8 1 * *";

    // Comando do cron job — usa o venv criado no container PHP
    $scriptPath = "/relatory-venv/bin/python3 /app/relatory.py --prazo " . escapeshellarg($prazo) . " --id " . intval($user_id);
    $cronCommand = "$cronExpr $scriptPath";

    // Captura os crons existentes
    $currentCron = shell_exec("crontab -l 2>/dev/null");

    // Se houver crons existentes, mantém todos menos o do usuário logado
    $filteredCron = array_filter(explode("\n", $currentCron), function($line) use ($user_id) {
        return strpos($line, "--id $user_id") === false;
    });

    // Adiciona o novo cron job para o usuário
    $filteredCron[] = $cronCommand;

    // Salva no arquivo temporário
    file_put_contents('/tmp/cron.tmp', implode("\n", $filteredCron) . "\n");

    // Aplica o novo crontab sem apagar os outros crons
    exec("crontab /tmp/cron.tmp", $output, $returnCode);
    unlink('/tmp/cron.tmp');

    // Registra o novo cron para depuração
    $verificaCron = shell_exec("crontab -l");
    file_put_contents('/tmp/cron_debug.log', "Novo agendamento:\n" . $verificaCron . "\n", FILE_APPEND);

    // Redireciona após sucesso
    echo "<script>
        alert('Sincronização concluída! Comando cron atualizado.');
        window.location.href = './../public/detail_user.php';
    </script>";
} else {
    die("Erro: Nenhum valor de prazo foi recebido.");
}

?>
