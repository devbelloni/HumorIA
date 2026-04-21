<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../dbm/DbConnector.php");

if (!isset($_GET['id'])) {
    die("Parâmetro 'id' não encontrado na URL.");
}

include_once("./../dbm/DBConfig.php");

$dsn = DBConfig::getDSN();
$username = DBConfig::getUsername();
$password = DBConfig::getPassword();
$empresaId = intval($_GET['id']);

// Diretório base
$diretorio = "/var/www/humoria/recordings (3)/{$empresaId}";

try {
    echo "Conectando ao banco de dados...<br>";
    $dbConnector = new PDO($dsn, $username, $password);
    $dbConnector->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Iniciar transação apenas para operações no banco
    echo "Iniciando transação...<br>";
    $dbConnector->beginTransaction();

    // Deletar empresa do banco
    echo "Deletando empresa com ID: {$empresaId}<br>";
    $query = "DELETE FROM empresas WHERE id = :id";
    $stmt = $dbConnector->prepare($query);
    $stmt->bindParam(':id', $empresaId, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        throw new Exception("Falha ao deletar a empresa do banco de dados.");
    }

    // Commit da transação do banco
    $dbConnector->commit();
    echo "Empresa deletada do banco com sucesso.<br>";

    // Função para deletar diretório e subdiretórios
    function deleteDirectory($dir) {
        if (!is_dir($dir)) {
            echo "O diretório {$dir} não existe.<br>";
            return false;
        }

        if (!is_readable($dir) || !is_writable($dir)) {
            echo "O diretório {$dir} não tem permissões de leitura/escrita adequadas.<br>";
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $filePath = "{$dir}/{$file}";
            if (is_dir($filePath)) {
                deleteDirectory($filePath); // Recursivamente deletar subdiretórios
            } else {
                if (!unlink($filePath)) {
                    echo "Falha ao deletar o arquivo: {$filePath}<br>";
                    return false;
                }
            }
        }

        if (!rmdir($dir)) {
            echo "Falha ao deletar o diretório: {$dir}<br>";
            return false;
        }
        return true;
    }

    // Ajustar permissões do diretório
    if (is_dir($diretorio)) {
        echo "Ajustando proprietário e permissões para: {$diretorio}<br>";
        $safeDir = escapeshellarg($diretorio);
        system("chown -R www-data:www-data {$safeDir} 2>&1");
        system("chmod -R 775 {$safeDir} 2>&1");

        echo "Permissões ajustadas. Tentando deletar o diretório...<br>";
        if (deleteDirectory($diretorio)) {
            echo "Diretório e subdiretórios deletados com sucesso.<br>";
        } else {
            echo "Falha ao deletar o diretório ou subdiretórios.<br>";
        }
    } else {
        echo "O diretório {$diretorio} não existe, nada a deletar.<br>";
    }

    // Redirecionar com PHP
    header("Location: painel_controle.php?status=success");
    exit;

} catch (Exception $e) {
    if (isset($dbConnector) && $dbConnector->inTransaction()) {
        $dbConnector->rollBack();
    }
    echo "Erro: " . $e->getMessage() . "<br>";
    // Opcional: registrar o erro em um log
    error_log("Erro ao deletar empresa ID {$empresaId}: " . $e->getMessage());
    header("Location: painel_controle.php?status=error");
    exit;
}





// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// require_once("../dbm/DbConnector.php");

// if (isset($_GET['id'])) {
//     include_once("./../dbm/DBConfig.php");

//     $dsn = DBConfig::getDSN();
//     $username = DBConfig::getUsername();
//     $password = DBConfig::getPassword();

//     $empresaId = intval($_GET['id']);

//     try {
//         echo "Conectando ao banco de dados...<br>";
//         $dbConnector = new PDO($dsn, $username, $password);
//         $dbConnector->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//         echo "Iniciando transação...<br>";
//         $dbConnector->beginTransaction();

//         echo "Deletando empresa com ID: {$empresaId}<br>";
//         $query = "DELETE FROM empresas WHERE id = :id";
//         $stmt = $dbConnector->prepare($query);
//         $stmt->bindParam(':id', $empresaId, PDO::PARAM_INT);

//         if ($stmt->execute()) {
//             echo "Empresa deletada do banco com sucesso.<br>";

//             $diretorio = "/var/www/humoria/recordings (3)/{$empresaId}";

//             // Ajustar proprietário e permissões
//             echo "Ajustando proprietário e permissões para: {$diretorio}<br>";
//             system("chown -R www-data:www-data {$diretorio}");
//             system("chmod -R 775 {$diretorio}");

//             echo "Permissões ajustadas. Tentando deletar o diretório...<br>";

//             function deleteDirectory($dir) {
//                 if (!is_dir($dir)) {
//                     echo "O diretório {$dir} não existe.<br>";
//                     return false;
//                 }

//                 $files = scandir($dir);
//                 if ($files === false) {
//                     echo "Não foi possível acessar o conteúdo do diretório: {$dir}.<br>";
//                     return false;
//                 }

//                 $files = array_diff($files, array('.', '..'));
//                 foreach ($files as $file) {
//                     $filePath = "{$dir}/{$file}";
//                     if (is_dir($filePath)) {
//                         deleteDirectory($filePath);
//                     } else {
//                         unlink($filePath);
//                     }
//                 }

//                 return rmdir($dir);
//             }

//             if (deleteDirectory($diretorio)) {
//                 echo "Empresa e diretório deletados com sucesso.<br>";
//             } else {
//                 echo "Falha ao deletar o diretório.<br>";
//             }
//         } else {
//             echo "Falha ao deletar a empresa do banco de dados.<br>";
//         }

//         $dbConnector->commit();
//         echo "<script>
//         alert('Transação concluída.');
//         window.location.href = 'painel_controle.php';
//     </script>";
//         echo "<br>";
//     } catch (Exception $e) {
//         $dbConnector->rollBack();
//         echo "Erro: " . $e->getMessage() . "<br>";
//     }
// } else {
//     echo "Parâmetro 'id' não encontrado na URL.<br>";
// }
?>
