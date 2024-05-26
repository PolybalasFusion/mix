<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senha Incorreta</title>
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
            color: black; /* Altera a cor do texto para preto */
            font-family: Arial, sans-serif;
            margin-top: 20px; /* Adiciona um espaço entre a imagem e o texto */
        }
        .error-image {
            width: 300px; /* Ajuste o tamanho conforme necessário */
            height: auto;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <img src="http://192.168.0.13/log/images/Attention.png" alt="Attention Image" class="error-image">
        <p class="error-message">Senha incorreta.</p>
    </div>
    
    <script>
        // Redireciona a página após 2 segundos
        setTimeout(function () {
            window.location.href = "http://192.168.0.13/log/index.php";
        }, 2000); // 2000 milissegundos = 2 segundos
    </script>
</body>
</html>
