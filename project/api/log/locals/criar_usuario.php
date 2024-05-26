<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definindo a variável de mensagem
$error_message = "";

// Verifica se os dados foram submetidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inclui o arquivo de conexão com o banco de dados
    include 'conexao.php';

    try {
        // Verifica se o CPF já existe na tabela pclog
        $check_stmt = $conn->prepare("SELECT cpf FROM pclog WHERE cpf = :cpf");
        $check_stmt->bindParam(':cpf', $_POST['cpf']);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            // Prepara a instrução SQL para inserção dos dados
            $stmt = $conn->prepare("INSERT INTO pcusuario (nome, email, cpf, senha) VALUES (:nome, :email, :cpf, :senha)");

            // Bind dos parâmetros
            $stmt->bindParam(':nome', $_POST['nome']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':cpf', $_POST['cpf']);
            $stmt->bindParam(':senha', $_POST['senha']);

            // Executa a instrução SQL
            $stmt->execute();

            // Verifica se a inserção foi bem-sucedida
            if ($stmt->rowCount() > 0) {
                $success_message = "Cadastro realizado com sucesso!";
            } else {
                $error_message = "Erro ao cadastrar usuário.";
            }
        } else {
            $error_message = "Cadastro Não Autorizado.";
        }
    } catch (PDOException $e) {
        // Exibe mensagem de erro
        echo "Erro ao cadastrar usuário: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Cadastro</title>
    <style>
        .message-container {
            text-align: center;
            margin-top: 50px;
        }

        .message-image {
            width: 250px;
        }

        .message-text {
            font-size: 35px;
            font-family: Arial, sans-serif;
            font-weight: bold;
            color: black;
        }
    </style>
</head>

<body>
    <?php if (!empty($error_message)): ?>
        <div class="message-container">
            <img src="http://192.168.0.13/log/images/Error.png" alt="Erro" class="message-image">
            <p class="message-text"><?php echo $error_message; ?></p>
        </div>
    <?php elseif (!empty($success_message)): ?>
        <div class="message-container">
            <img src="http://192.168.0.13/log/images/Confirmation.png" alt="Confirmação" class="message-image">
            <p class="message-text"><?php echo $success_message; ?></p>
        </div>
    <?php endif; ?>
    
    <script>
        // Redireciona a página após 2 segundos
        setTimeout(function () {
            window.location.href = "http://192.168.0.13/log/index.php";
        }, 2000); // 2000 milissegundos = 2 segundos
    </script>
</body>

</html>
