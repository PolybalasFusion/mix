<?php
// Inicia a sessão no início do script
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se os dados foram submetidos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inclui o arquivo de conexão com o banco de dados
    include '../locals/conexao.php';

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
                // Armazena os dados do usuário na sessão
                $_SESSION['user_data'] = $usuario;
                // Redireciona para o caminho local
                header("Location: http://192.168.0.13/logindex.php");
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
if (isset($_SESSION['user_data'])) {
    // Inclui o arquivo de conexão com o banco de dados
    include '../locals/conexao.php';

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
    <link rel="stylesheet" href="http://192.168.0.13/mix/assents/dados.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
        integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="http://localhost/mix/assents/dados.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>
    <style>
        /* Estilo para a div suspensa de lista sugerida */
        .cart-container {
            position: fixed;
            top: 100px;
            left: 20px;
            /* Alterado para posicionar no lado esquerdo */
            width: 300px;
            max-height: 80%;
            overflow-y: auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
            z-index: 1000;
        }

        .cart-container h3 {
            color: #333;
            /* Cor do título */
            text-align: center;
            margin-bottom: 15px;
        }

        .cart-container ul {
            list-style: none;
            padding: 0;
        }

        .cart-container ul li {
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            /* Linha divisória entre itens */
            padding-bottom: 5px;
        }

        .cart-container ul li:last-child {
            border-bottom: none;
            /* Remove a linha divisória do último item */
        }

        .cart-container ul li span {
            display: block;
            margin-bottom: 5px;
        }

        .cart-container button {
            background-color: black;
            /* Cor de fundo verde */
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
        }

        .cart-container button:hover {
            background-color: #45a049;
            /* Cor de fundo verde mais escura no hover */
        }

        /* Estilo para o botão de fechar a div suspensa */
        .close-button {
            position: absolute;
            top: 5px;
            right: 5px;
            cursor: pointer;
        }

        .background-green {
            background-color: rgba(49, 155, 48, 0.5);
            /* Verde com 50% de opacidade */
            color: #166a15;
        }

        .background-red {
            background-color: rgba(197, 44, 44, 0.5);
            /* Vermelho com 50% de opacidade */
            color: #FDEEE7;
        }
    </style>


</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="main-header">
            <div class="main-header-container">
                <a href="http://192.168.0.13/mix/index.php" id="brand">Polybalas</a>
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
                                        <li><i class="fas fa-id-badge"></i> Matrícula: <?php echo $user_data['matricula']; ?>
                                        </li>
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
                    <!-- Icone Lista Sugerida -->
                    <div class="header-cart-container" onclick="toggleCart()">
                        <i class="fas fa-list"></i>
                        <span>Lista Sugerida</span>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Div Suspensa do Carrinho -->
    <div id="cart-container" class="cart-container">
        <h3>Lista Sugerida</h3>
        <ul id="cart-content"></ul>
        <button onclick='exportAllToPDF()'>Extrair PDF</button>
        <button onclick='exportAllToXLSX()'>Extrair XLSX</button>
    </div>
    <!-- Produtos do Cliente -->
    <?php
    // Inclui o arquivo de conexão com o banco de dados
    include '../locals/conexao.php';

    // Configuração de paginação
    $produtos_por_pagina = 10;
    $pagina_atual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
    $offset = ($pagina_atual - 1) * $produtos_por_pagina;

    if (isset($_SESSION['user_data']) && isset($_GET['codcli'])) {
        $codcli = $_GET['codcli'];
        $email = $_SESSION['user_data']['email']; // Assumindo que o email do usuário está disponível na sessão
    
        try {
            // Consulta SQL atualizada para obter o vendedor
            $vendedorStmt = $conn->prepare("SELECT tabela FROM pcusuario INNER JOIN pclog ON pcusuario.cpf = pclog.cpf WHERE pcusuario.email = :email");
            $vendedorStmt->bindParam(':email', $email);
            $vendedorStmt->execute();
            $vendedor = $vendedorStmt->fetch(PDO::FETCH_ASSOC);

            // Consulta SQL para obter os produtos com paginação
            $sql = "SELECT pcclient.gcgcent, 
                       pcusurcli.codcli, 
                       pcusurcli.cliente, 
                       pclog.tabela,
                       pcclient.nome_ramo_atividade,
                       CASE 
                           WHEN pclog.tabela = 'B' THEN 'RECKITT' 
                           ELSE pcprodutos.fornec 
                       END AS fornec,
                       pcprodutos.id,
                       pcprodutos.produtos,
                       pcprodutos.codauxiliar,
                       pcpedi.multiplo,
                       pcpedi.mes_retrasado,
                       pcpedi.mes_passado,
                       pcpedi.mes_atual
                FROM pcusuario
                INNER JOIN pclog ON pcusuario.cpf = pclog.cpf
                INNER JOIN pcusurcli ON pclog.id = pcusurcli.codusur
                INNER JOIN pcclient ON pcusurcli.codcli = pcclient.codcli
                INNER JOIN pcprodutos ON pcclient.nome_ramo_atividade = pcprodutos.nome_ramo_atividade
                INNER JOIN pcpedi ON pcclient.codcli = pcpedi.codcli AND pcprodutos.id = pcpedi.codprod
                WHERE pcusuario.email = :email
                  AND pcusurcli.codcli = :codcli
                  AND (pclog.tabela != 'B' OR 
                       (pclog.tabela = 'B' AND 
                        pcprodutos.fornec = 'RECKITT'))
                LIMIT :limit OFFSET :offset";

            // Bind dos parâmetros
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':codcli', $codcli);
            $stmt->bindValue(':limit', $produtos_por_pagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            // Executa a consulta SQL
            $stmt->execute();

            // Verifica se há produtos para exibir
            if ($stmt->rowCount() > 0) {
                echo "<div class='centered-table'>";
                echo "<table border='1'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>ID</th>";
                echo "<th>Produto</th>";
                echo "<th>EAN</th>";
                echo "<th>Múltiplo</th>";
                echo "<th>" . ucfirst(getPortugueseMonthName(date('F', strtotime('-2 months')))) . "</th>"; // Mês Retrasado
                echo "<th>" . ucfirst(getPortugueseMonthName(date('F', strtotime('-1 month')))) . "</th>";   // Mês Passado
                echo "<th>" . ucfirst(getPortugueseMonthName(date('F'))) . "</th>";                           // Mês Atual
                echo "<th>Adicionar a lista</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";

                // Loop através dos resultados
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Define as classes CSS com base nos valores
                    $class_mr = $row['mes_retrasado'] > 0 ? 'background-green' : 'background-red';
                    $class_mp = $row['mes_passado'] > 0 ? 'background-green' : 'background-red';
                    $class_ma = $row['mes_atual'] > 0 ? 'background-green' : 'background-red';

                    // Exibe os resultados
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['produtos']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['codauxiliar']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['multiplo']) . "</td>";
                    echo "<td class='$class_mr'>" . htmlspecialchars($row['mes_retrasado']) . "</td>";
                    echo "<td class='$class_mp'>" . htmlspecialchars($row['mes_passado']) . "</td>";
                    echo "<td class='$class_ma'>" . htmlspecialchars($row['mes_atual']) . "</td>";
                    echo "<td><img src='http://192.168.0.13/mix/images/adicionar-botao.png' width='25' height='25' onclick='addToCart(" . json_encode($row) . ")'></td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</div>";

                // Controles de navegação
                $total_produtos = $conn->query("SELECT COUNT(*) FROM pcusuario
                                            INNER JOIN pclog ON pcusuario.cpf = pclog.cpf
                                            INNER JOIN pcusurcli ON pclog.id = pcusurcli.codusur
                                            INNER JOIN pcclient ON pcusurcli.codcli = pcclient.codcli
                                            INNER JOIN pcprodutos ON pcclient.nome_ramo_atividade = pcprodutos.nome_ramo_atividade
                                            INNER JOIN pcpedi ON pcclient.codcli = pcpedi.codcli AND pcprodutos.id = pcpedi.codprod
                                            WHERE pcusuario.email = '$email'
                                              AND pcusurcli.codcli = '$codcli'
                                              AND (pclog.tabela != 'B' OR 
                                                   (pclog.tabela = 'B' AND 
                                                    pcprodutos.fornec = 'RECKITT'))")->fetchColumn();

                $total_paginas = ceil($total_produtos / $produtos_por_pagina);

                echo "<div class='pagination'>";
                if ($pagina_atual > 1) {
                    echo "<a href='?codcli=$codcli&pagina=" . ($pagina_atual - 1) . "'>Anterior</a>";
                }
                for ($i = 1; $i <= $total_paginas; $i++) {
                    if ($i == $pagina_atual) {
                        echo "<span>$i</span>";
                    } else {
                        echo "<a href='?codcli=$codcli&pagina=$i'>$i</a>";
                    }
                }
                if ($pagina_atual < $total_paginas) {
                    echo "<a href='?codcli=$codcli&pagina=" . ($pagina_atual + 1) . "'>Próxima</a>";
                }
                echo "</div>";
            } else {
                echo "<p>Nenhum produto encontrado para este cliente.</p>";
            }
        } catch (PDOException $e) {
            echo "Erro ao obter os produtos: " . $e->getMessage();
        }
    } else {
        echo "<p>Usuário não está logado ou código do cliente não foi fornecido.</p>";
    }

    function getPortugueseMonthName($month)
    {
        $portugueseMonths = array(
            "January" => "janeiro",
            "February" => "fevereiro",
            "March" => "março",
            "April" => "abril",
            "May" => "maio",
            "June" => "junho",
            "July" => "julho",
            "August" => "agosto",
            "September" => "setembro",
            "October" => "outubro",
            "November" => "novembro",
            "December" => "dezembro"
        );
        return $portugueseMonths[$month];
    }
    ?>
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="footer-bottom">
                <p>Copyright &copy; 2023 - Polybalas</p>
            </div>
        </div>
    </footer>
    <script src="http://192.168.0.13/mix/script/dado.js"></script>
</body>

</html>