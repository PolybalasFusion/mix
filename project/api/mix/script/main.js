function formatCnpj(event) {
    let cnpj = event.target.value.replace(/\D/g, '');
    cnpj = cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    event.target.value = cnpj;
}

// Adiciona um ouvinte de evento para o formulário de pesquisa
document.getElementById('search-form').addEventListener('submit', function (event) {
    // Evita o envio do formulário padrão
    event.preventDefault();
    // Submete o formulário usando JavaScript
    this.submit();
});

document.getElementById('search-form').addEventListener('submit', function (event) {
    event.preventDefault(); // Impede o envio do formulário padrão

    // Captura os valores dos campos de pesquisa
    var formData = new FormData(this);

    // Envia os dados via AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'search.php?' + new URLSearchParams(formData).toString(), true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status == 200) {
                // Atualiza a tabela com os resultados da pesquisa
                document.getElementById('client-table').innerHTML = xhr.responseText;
            } else {
                console.error('Erro ao fazer a requisição.');
            }
        }
    };
    xhr.send();
});