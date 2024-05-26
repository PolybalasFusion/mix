function addToCart(product) {
    var cart = document.getElementById('cart-content');
    var existingItem = document.querySelector(`#cart-content li[data-id="${product.id}"]`);

    if (!existingItem) {
        var item = document.createElement('li');
        item.setAttribute('data-id', product.id);
        item.innerHTML = "<span>ID: " + product.id + "</span>" +
            "<span>Nome: " + product.produtos + "</span>" +
            "<span>EAN: " + product.codauxiliar + "</span>" +
            "<span>Quantidade: <input type='number' value='" + product.multiplo + "' min='1' /></span>" +
            "<button onclick='removeFromCart(this.parentNode)'>Remover</button>";
        cart.appendChild(item);
    } else {
        alert('O produto já está na lista.');
    }
}

function toggleCart() {
    var cartContainer = document.getElementById('cart-container');
    cartContainer.style.display = cartContainer.style.display === 'none' ? 'block' : 'none';
}

// Função para remover um item do carrinho
function removeFromCart(item) {
    item.parentNode.removeChild(item);
}

// Função para extrair todos os itens do carrinho para PDF
function exportAllToPDF() {
    var cartItems = document.querySelectorAll('#cart-content li');
    if (cartItems.length === 0) {
        alert('O carrinho está vazio.');
        return;
    }

    var content = [];

    // Adiciona cabeçalho da tabela
    content.push([
        { text: 'EAN', bold: true },
        { text: 'Quantidade', bold: true }
    ]);

    cartItems.forEach(function (item) {
        var ean = item.querySelector('span:nth-of-type(3)').textContent.split(": ")[1];
        var quantidade = item.querySelector('input').value;

        // Adiciona os dados de EAN e quantidade na linha da tabela
        content.push([ean, quantidade]);
    });

    // Define o layout da tabela
    var table = {
        headerRows: 1,
        widths: ['auto', 'auto'],
        body: content
    };

    // Define o documento PDF
    var docDefinition = {
        content: [
            { table: table }
        ]
    };

    pdfMake.createPdf(docDefinition).download('produtos_carrinho.pdf');
}

// Função para extrair todos os itens do carrinho para XLSX
function exportAllToXLSX() {
    var cartItems = document.querySelectorAll('#cart-content li');
    if (cartItems.length === 0) {
        alert('O carrinho está vazio.');
        return;
    }

    var data = [['EAN', 'Quantidade']];

    cartItems.forEach(function (item) {
        var ean = item.querySelector('span:nth-of-type(3)').textContent.split(": ")[1];
        var quantidade = item.querySelector('input').value;

        data.push([ean, quantidade]);
    });

    var ws = XLSX.utils.aoa_to_sheet(data);
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Produtos Carrinho');
    XLSX.writeFile(wb, 'produtos_carrinho.xlsx');
}