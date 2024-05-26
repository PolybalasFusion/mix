let container = document.getElementById('container')

toggle = () => {
	container.classList.toggle('sign-in')
	container.classList.toggle('sign-up')
}

setTimeout(() => {
	container.classList.add('sign-in')
}, 200)

function togglePasswordVisibility(inputId, toggleId) {
	var passwordInput = document.getElementById(inputId);
	var toggleIcon = document.getElementById(toggleId);

	if (passwordInput.type === "password") {
		passwordInput.type = "text";
		toggleIcon.classList.remove("fa-eye-slash");
		toggleIcon.classList.add("fa-eye");
	} else {
		passwordInput.type = "password";
		toggleIcon.classList.remove("fa-eye");
		toggleIcon.classList.add("fa-eye-slash");
	}
}

function validateAndFormatCPF(inputId) {
	var cpfInput = document.getElementById(inputId);
	var cpf = cpfInput.value.replace(/\D/g, '');
	if (cpf.length === 11) {
		cpf = cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
		cpfInput.value = cpf;
	}
}

function validateNameAndEmail(inputId) {
	var input = document.getElementById(inputId);
	input.value = input.value.toUpperCase();
}

function showForgotPassword() {
	document.getElementById("forgotPasswordModal").style.visibility = "visible";
	document.getElementById("forgotPasswordModal").style.opacity = "1";
}

function closeForgotPasswordModal() {
	document.getElementById("forgotPasswordModal").style.visibility = "hidden";
	document.getElementById("forgotPasswordModal").style.opacity = "0";
}

// Fechar o modal clicando fora dele
window.onclick = function (event) {
	var modal = document.getElementById("forgotPasswordModal");
	if (event.target == modal) {
		closeForgotPasswordModal();
	}
}
