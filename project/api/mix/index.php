<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Polybalas</title>
  <!-- FONT OPEN SANS -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
    integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- CSS -->
  <link rel="stylesheet" href="http://192.168.0.13/mix/assents/style.css">
</head>
<?php
// Verifica se os dados foram submetidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Inclui o arquivo de conexão com o banco de dados
  include 'C:/xampp/1/htdocs/final/locals/conexao.php';

  try {
    // Consulta SQL para verificar as credenciais do usuário
    $stmt = $conn->prepare("SELECT pclog.*, pcusuario.email
    FROM pcusuario
    INNER JOIN pclog ON pcusuario.cpf = pclog.cpf
    WHERE pcusuario.email = :email");

    // Bind do parâmetro
    $stmt->bindParam(':email', $_POST['email']);

    // Executa a consulta SQL
    $stmt->execute();

    // Verifica se encontrou um usuário com o email fornecido
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
        $error_message = "Senha incorreta.";
      }
    } else {
      // Usuário com o email fornecido não encontrado
      $error_message = "Email não encontrado.";
    }
  } catch (PDOException $e) {
    $error_message = "Erro ao realizar login: " . $e->getMessage();
  }
}

// Verifica se o usuário está logado
session_start();
if (isset($_SESSION['user_data'])) {
  // Inclui o arquivo de conexão com o banco de dados
  include "..\log\locals\conexao.php";
  // Consulta SQL para recuperar os dados do usuário usando o email
  $stmt = $conn->prepare("SELECT pclog.*, pcusuario.email
  FROM pcusuario
  INNER JOIN pclog ON pcusuario.cpf = pclog.cpf
  WHERE pcusuario.email = :email");
  $stmt->bindParam(':email', $_SESSION['user_data']['email']);

  $stmt->execute();
  $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<body>
  <!-- Header -->
  <header class="header">
    <div class="main-header">
      <div class="main-header-container">
        <a href="#" id="brand">Polybalas</a>
        <div class="header-actions-menu">
          <div class="account-dropdown" id="account-dropdown">
            <div class="header-cart-container">
              <i class="fas fa-user"></i>
              <span>Minha Conta</span>
            </div>
            <?php if (isset($_SESSION['user_data'])): ?>
              <!-- Div para exibir os detalhes da conta -->
              <div class="account-dropdown-content">
                <?php if (isset($user_data)): ?>
                  <ul>
                    <li><i class="fas fa-id-badge"></i> Matrícula: <?php echo $user_data['matricula']; ?></li>
                    <li><i class="fas fa-id-card"></i> ID: <?php echo $user_data['id']; ?></li>
                    <li><i class="fas fa-route"></i> Rota: <?php echo $user_data['rota']; ?></li>
                    <li><i class="fas fa-table"></i> Tabela: <?php echo $user_data['tabela']; ?></li>
                    <li><i class="fas fa-user"></i> Nome: <?php echo $user_data['nome']; ?></li>
                    <li><i class="fas fa-envelope"></i> Email: <?php echo $user_data['email']; ?></li>
                    <li><i class="fas fa-id-card-alt"></i> CPF: <?php echo $user_data['cpf']; ?></li>
                  </ul>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
          <a href="http://192.168.0.13/log/index.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Sair
          </a>
        </div>
      </div>
    </div>
  </header>
  <!-- Newsletter -->
  <div class="centered-table" id="client-table">
    <form id="search-form" method="GET" action="">
      <input type="text" name="cnpj_cliente" placeholder="CNPJ"
        oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 14)" onkeypress="formatCnpj(event)"
        maxlength="18">
      <input type="text" name="codigo_cliente" placeholder="Código do Cliente"
        oninput="this.value = this.value.toUpperCase()">
      <input type="text" name="nome_cliente" placeholder="Nome do Cliente"
        oninput="this.value = this.value.toUpperCase()">
      <input type="text" name="ramo_atividade" placeholder="Ramo de Atividade"
        oninput="this.value = this.value.toUpperCase()">
      <button type="submit" id="search-button">Pesquisar</button>
    </form>
    <?php
    // Se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
      // Verifica se o usuário está logado
      if (isset($_SESSION['user_data'])) {
        // Inclui o arquivo de conexão com o banco de dados
        include 'locals/conexao.php';

        // Inicializa a consulta SQL
        $sql = "SELECT 
                pcclient.gcgcent, 
                pcusurcli.codcli, 
                pcusurcli.cliente, 
                pclog.tabela, 
                pcclient.nome_ramo_atividade
            FROM 
                pcusuario
            INNER JOIN 
                pclog ON pcusuario.cpf = pclog.cpf
            INNER JOIN 
                pcusurcli ON pclog.id = pcusurcli.codusur
            INNER JOIN 
                pcclient ON pcusurcli.codcli = pcclient.codcli
            WHERE 
                pcusuario.email = :email
                AND (pclog.tabela != 'B' OR 
                     (pclog.tabela = 'B' AND 
                      pcclient.nome_ramo_atividade IN (
                          'ALIMENTAR - 01 A 04 CHECK-OUTS', 
                          'ALIMENTAR - 05 A 09 CHECK-OUTS',
                          'ALIMENTAR - 10 A 19 CHECK-OUTS', 
                          'ALIMENTAR - MAIS 20 CHECK-OUTS', 
                          'ALIMENTAR TRADICIONAL', 
                          'CASH CARRY'
                      )))
            ";

        // Inicializa um array para armazenar os parâmetros de ligação
        $bindParams = array(':email' => $_SESSION['user_data']['email']);

        // Inicializa um array para armazenar as cláusulas WHERE
        $whereClauses = array();

        // Função para converter valores em maiúsculas
        function toUpperCase($value)
        {
          return strtoupper($value);
        }

        // Verifica e adiciona as condições de pesquisa ao array de cláusulas WHERE
        if (!empty($_GET['cnpj_cliente'])) {
          $whereClauses[] = "pcclient.gcgcent LIKE :cnpj_cliente";
          $bindParams[':cnpj_cliente'] = '%' . $_GET['cnpj_cliente'] . '%';
        }
        if (!empty($_GET['codigo_cliente'])) {
          $whereClauses[] = "pcclient.codcli = :codigo_cliente";
          $bindParams[':codigo_cliente'] = toUpperCase($_GET['codigo_cliente']);
        }
        if (!empty($_GET['nome_cliente'])) {
          $whereClauses[] = "pcclient.cliente LIKE :nome_cliente";
          $bindParams[':nome_cliente'] = '%' . toUpperCase($_GET['nome_cliente']) . '%';
        }
        if (!empty($_GET['ramo_atividade'])) {
          $whereClauses[] = "pcclient.nome_ramo_atividade LIKE :ramo_atividade";
          $bindParams[':ramo_atividade'] = '%' . toUpperCase($_GET['ramo_atividade']) . '%';
        }

        // Se houver cláusulas WHERE, adiciona à consulta SQL
        if (!empty($whereClauses)) {
          $sql .= " AND " . implode(" AND ", $whereClauses);
        }

        // Prepara a consulta SQL
        $stmt = $conn->prepare($sql);

        // Executa a consulta SQL com os parâmetros de ligação
        $stmt->execute($bindParams);

        // Verifica se há clientes para exibir
        if ($stmt->rowCount() > 0) {
          // Exibe a tabela de clientes
          echo "<table border='1'>";
          echo "<thead>";
          echo "<tr>";
          echo "<th>CNPJ</th>";
          echo "<th>Código do Cliente</th>";
          echo "<th>Nome do Cliente</th>";
          echo "<th>Tabela</th>";
          echo "<th>Ramo de Atividade</th>";
          echo "<th style='background-color: black; color: white;'></th>";
          echo "</tr>";
          echo "</thead>";
          echo "<tbody>";

          // Loop através dos resultados e exibir cada cliente em uma linha da tabela
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['gcgcent'] . "</td>";
            echo "<td>" . $row['codcli'] . "</td>";
            echo "<td>" . $row['cliente'] . "</td>";
            echo "<td>" . $row['tabela'] . "</td>";
            echo "<td>" . $row['nome_ramo_atividade'] . "</td>";
            // Botão de redirecionamento
            echo "<td><a href='locals/dados.php?codcli=" . $row['codcli'] . "'><button style='width: 25px; height: 25px; border: none; background: none; padding: 0; cursor: pointer;'><img src='images/pedido.png' alt='Pedido' style='width: 100%; height: 100%;'></button></a></td>";
            echo "</tr>";
          }

          echo "</tbody>";
          echo "</table>";
        } else {
          echo "<p>Nenhum cliente encontrado.</p>";
        }
      } else {
        echo "<p>Usuário não está logado.</p>";
      }
    }
    ?>
  </div>
  <!-- Footer -->
  <footer class="footer">
    <div class="footer-top">
      <div class="footer-bottom">
        <p>Copyright &copy; 2023 - Polybalas</p>
      </div>
    </div>
  </footer>
  <script src="http://192.168.0.13/mix/script/main.js"></script>
</body>

</html>