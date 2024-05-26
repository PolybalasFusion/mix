<!DOCTYPE html>
<html>

<head>
	<meta charset='utf-8'>
	<meta http-equiv='X-UA-Compatible' content='IE=edge'>
	<title>Tela de Login</title>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<link rel='stylesheet' type='text/css' media='screen' href='assets/main.css'>
	<link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
</head>

<body>
	<div id="container" class="container">
		<!-- FORM SECTION -->
		<div class="row">
			<!-- Formulario de Criar conta -->
			<div class="col align-items-center flex-col sign-up">
				<div class="form-wrapper align-items-center">
					<form action="locals/criar_usuario.php" method="POST">
						<div class="form sign-up">
							<div class="input-group">
								<i class='fa fa-user'></i>
								<input id="nameInput" type="text" placeholder="Nome" name="nome">
							</div>
							<div class="input-group">
								<i class='fa fa-envelope'></i>
								<input id="emailInput" type="email" placeholder="E-mail" name="email">
							</div>
							<div class="input-group">
								<i class='fa fa-user'></i>
								<input id="cpfInput1" type="text" placeholder="CPF" name="cpf" minlength="11"
									maxlength="11" oninput="validateAndFormatCPF('cpfInput1')">
							</div>
							<div class="input-group">
								<i class='fa fa-lock'></i>
								<input type="password" placeholder="Senha" name="senha" minlength="8">
							</div>
							<button>
								Criar Conta
							</button>
							<p>
								<span>
									Já tem uma Conta
								</span>
								<b onclick="toggle()" class="pointer">
									Entre Aqui
								</b>
							</p>
						</div>
					</form>
				</div>
			</div>
			<!-- Fim do formulário de registro-->

			<!-- Formulário de logar -->
			<div class="col align-items-center flex-col sign-in">
				<div class="form-wrapper align-items-center">
					<form action="locals/login.php" method="POST">
						<div class="form sign-in">
							<div class="input-group">
								<i class='fa fa-envelope'></i>
								<input id="emailInput" type="email" placeholder="E-mail" name="email">
							</div>
							<div class="input-group">
								<i class='fa fa-lock'></i>
								<input type="password" placeholder="Senha" name="senha" minlength="8">
							</div>
							<button>
								Entrar
							</button>
							<p>
								<b onclick="showForgotPassword()">Esqueceu sua Senha?</b>
							</p>
							<p>
								<span>
									Não tem conta?
								</span>
								<b onclick="toggle()" class="pointer">
									CRIE SUA CONTA AQUI AQUI
								</b>
							</p>
						</div>
					</form>
				</div>
				<div class="form-wrapper">
				</div>
			</div>
			<!-- Fim do formulário de logar-->
		</div>

		<!-- Animação-->
		<div class="row content-row">
			<!-- Texto da Animação bem vindo-->
			<div class="col align-items-center flex-col">
				<div class="text sign-in">
					<h2>
						Bem Vindo!
					</h2>
				</div>
				<div class="img sign-in">
					<img src="images/login.png">
				</div>
			</div>
			<!-- fim do conteudo-->

			<!-- Animação de criação de conta-->
			<div class="col align-items-center flex-col">
				<div class="text sign-up">
					<h2>
						Criar Conta
					</h2>
				</div>
				<div class="img sign-up">
					<img src="images/criarconta.png">
				</div>
			</div>
		</div>
	</div>

	<!-- Esqueceu sua Senha Modal -->
	<div id="forgotPasswordModal" class="modal">
		<span class="close" onclick="closeForgotPasswordModal()">&times;</span>
		<div class="modal-content">
			<div class="input-container">
				<form action="locals/esqueceu_senha.php" method="POST">
					<div class="input-group input-group-small">
						<i class='fa fa-user'></i>
						<input id="nameInput" type="text" placeholder="Nome" name="nome">
					</div>
					<div class="input-group input-group-small">
						<i class='fa fa-envelope'></i>
						<input id="emailInput" type="email" placeholder="E-mail" name="email">
					</div>
					<div class="input-group">
						<i class='fa fa-user'></i>
						<input id="cpfInput2" type="text" placeholder="CPF" name="cpf" minlength="11" maxlength="11"
							oninput="validateAndFormatCPF('cpfInput2')">

					</div>
					<div class="input-group input-group-small">
						<i class='fa fa-lock'></i>
						<input type="password" placeholder="Senha" name="senha" minlength="8">
					</div>
					<button>
						Atualizar Senha
					</button>
				</form>
			</div>
		</div>
	</div>

	<!-- Fim do modal de Esqueceu sua Senha -->

	<script src='script/main.js'></script>
</body>

</html>