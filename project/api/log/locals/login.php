<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configura o manipulador de exceções
set_exception_handler('exception_handler');

function exception_handler($exception) {
    // Mostra uma página de erro personalizada
    include 'error_no_access.php';
    exit();
}

// Define uma variável para armazenar a mensagem de erro
$error_message = "";

// Verifica se os dados foram submetidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inclui o arquivo de conexão com o banco de dados
    include 'conexao.php';

    try {
        // Consulta SQL para verificar as credenciais do usuário e CPF na pclog
        $stmt = $conn->prepare("SELECT * FROM pcusuario WHERE email = :email AND cpf IN (SELECT cpf FROM pclog)");
        
        // Bind do parâmetro
        $stmt->bindParam(':email', $_POST['email']);
        
        // Executa a consulta SQL
        $stmt->execute();
        
        // Verifica se encontrou um usuário com o email fornecido e CPF na pclog
        if ($stmt->rowCount() > 0) {
            // Obtém os dados do usuário
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verifica se a senha fornecida corresponde à senha armazenada
            if ($_POST['senha'] === $usuario['senha']) {
                // Usuário autenticado com sucesso
                // Inicia a sessão
                session_start();
                // Armazena os dados do usuário na sessão
                $_SESSION['user_data'] = $usuario;
                // Redireciona para o caminho local
                header("Location: http://192.168.0.13/mix/index.php");
                exit; // Certifica-se de que o script não continua a ser executado após o redirecionamento
            } else {
                // Senha incorreta
                include 'error_wrong_password.php';
                exit();
            }
        } else {
            // Usuário com o email fornecido não encontrado ou CPF não está na pclog
            include 'error_email_not_found.php';
            exit();
        }
    } catch (PDOException $e) {
        $error_message = "Erro ao realizar login: " . $e->getMessage();
    }
}

// Verifica se o usuário está logado
session_start();
if (isset($_SESSION['user_data'])) {
    // Inclui o arquivo de conexão com o banco de dados
    include 'conexao.php';

    // Consulta SQL para recuperar os dados do usuário
    $stmt = $conn->prepare("SELECT * FROM pcusuario WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_data']['id']);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .error-container {
            text-align: center;
        }
        .error-message {
            font-size: 35px;
            font-weight: bold;
            color: red;
            font-family: Arial, sans-serif;
        }
        .error-image {
            width: 300px; /* Ajuste o tamanho conforme necessário */
            height: auto;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php if (!empty($error_message)): ?>
        <div class="error-container">
            <p class="error-message"><?php echo $error_message; ?></p>
            <img src="log/images/Error.png" alt="Error Image" class="error-image">
        </div>
    <?php endif; ?>
</body>
</html>
