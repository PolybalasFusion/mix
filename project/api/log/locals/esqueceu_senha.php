<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Mensagens e imagens correspondentes
$mensagem = "";
$imagem = "";

// Verifica se os dados foram submetidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inclui o arquivo de conexão com o banco de dados
    include 'conexao.php';

    try {
        // Prepara a instrução SQL para verificar o email do usuário
        $stmt = $conn->prepare("SELECT * FROM pcusuario WHERE email = :email AND nome = :nome AND cpf = :cpf");

        // Bind dos parâmetros
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':nome', $_POST['nome']);
        $stmt->bindParam(':cpf', $_POST['cpf']);

        // Executa a consulta SQL
        $stmt->execute();

        // Verifica se encontrou um usuário com os dados fornecidos
        if ($stmt->rowCount() > 0) {
            // Obtém os dados do usuário
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Utiliza a mesma senha fornecida pelo usuário
            $novaSenha = $_POST['senha'];

            // Prepara a instrução SQL para atualizar a senha do usuário
            $stmtUpdate = $conn->prepare("UPDATE pcusuario SET senha = :senha WHERE email = :email");

            // Bind dos parâmetros
            $stmtUpdate->bindParam(':senha', $novaSenha);
            $stmtUpdate->bindParam(':email', $_POST['email']);

            // Executa a instrução SQL de atualização
            $stmtUpdate->execute();

            // Verifica se o update foi bem-sucedido
            if ($stmtUpdate->rowCount() > 0) {
                // Aqui você pode enviar a nova senha por email para o usuário
                $mensagem = "A senha foi atualizada com sucesso.";
                $imagem = "confirmação.png";
            } else {
                $mensagem = "Erro ao atualizar a senha do usuário.";
                $imagem = "erro.png";
            }
        } else {
            // Usuário com os dados fornecidos não encontrado
            $mensagem = "Usuário não encontrado. Verifique se os dados estão corretos.";
            $imagem = "erro.png";
        }
    } catch (PDOException $e) {
        $mensagem = "Erro ao processar a solicitação: " . $e->getMessage();
        $imagem = "erro.png";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="2;url=http://192.168.0.13/log/index.php">
    <title>Atualização de Senha</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .message-container {
            text-align: center;
        }

        .message-container p {
            font-weight: bold;
        }

        .message-container img {
            max-width: 250px;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="message-container">
        <?php if (!empty($mensagem)): ?>
            <?php if ($imagem === "confirmação.png"): ?>
                <img src="http://192.168.0.13/log/images/Confirmation.png" alt="Status">
            <?php elseif ($imagem === "erro.png"): ?>
                <img src="http://192.168.0.13/log/images/Error.png" alt="Status">
            <?php endif; ?>
            <p><?php echo $mensagem; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>