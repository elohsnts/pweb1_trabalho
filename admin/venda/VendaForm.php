<?php
// Inclui o arquivo de classe do banco de dados para gerenciar o acesso e a persistência
include '../db.class.php';
// Inclui o componente visual do cabeçalho do sistema (HTML inicial, CSS, barra de navegação)
include '../header.php';

// Estabelece a conexão com o banco de dados chamando o método estático conectar() da classe DB
$db = DB::conectar();
// Captura o 'id' da URL se existir (Modo Edição), caso contrário define como null
$id = $_GET['id'] ?? null;
// Inicializa o array de vendas com valores padrão predefinidos, trazendo a data atual do servidor no formato padrão do banco
$venda = ['data_compra' => date('Y-m-d'), 'forma_pagamento' => 'Pix', 'valor_total' => '', 'status_pedido' => 'Aprovado', 'produto_id' => ''];

// Prepara uma instrução SQL para selecionar os produtos que irão preencher a caixa de listagem (Select)
$stmt_produtos = $db->prepare("SELECT id, nome_peca, tamanho, preco_venda FROM produto ORDER BY nome_peca ASC");
// Executa a consulta de busca de produtos
$stmt_produtos->execute();
// Agrupa e armazena os produtos encontrados em formato de array associativo
$produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);

// Condicional para verificar a existência do ID na URL (Modo Edição)
if ($id) {
    // Prepara a consulta de forma segura usando placeholder para buscar a venda específica
    $stmt = $db->prepare("SELECT * FROM venda WHERE id = ?");
    // Executa a query injetando o ID recebido via parâmetro GET
    $stmt->execute([$id]);
    // Substitui os valores padrão do array pelos dados reais salvos no banco de dados
    $venda = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Verifica se a requisição atual é do tipo POST, indicando a submissão dos dados pelo usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura e armazena os valores preenchidos nos inputs do formulário
    $data = $_POST['data_compra'];
    $pagamento = $_POST['forma_pagamento'];
    $valor = $_POST['valor_total'];
    $status = $_POST['status_pedido'];
    $produto_id = $_POST['produto_id']; 

    // Se o ID existir, realiza a atualização do registro correspondente
    if ($id) {
        // Define a instrução SQL de alteração utilizando parâmetros protegidos por marcadores de posição (?)
        $sql = "UPDATE venda SET data_compra=?, forma_pagamento=?, valor_total=?, status_pedido=?, produto_id=? WHERE id=?";
        $stmt = $db->prepare($sql);
        // Executa a query passando todos os campos coletados e o ID da venda no final da sequência
        $stmt->execute([$data, $pagamento, $valor, $status, $produto_id, $id]);
    } else {
        // Caso não haja um ID, realiza a inserção de uma nova venda na base de dados
        $sql = "INSERT INTO venda (data_compra, forma_pagamento, valor_total, status_pedido, produto_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        // Executa a gravação inserindo as variáveis em sua ordem correspondente
        $stmt->execute([$data, $pagamento, $valor, $status, $produto_id]);
    }
    
    // Redireciona o fluxo do usuário para a tela de listagem de vendas cadastradas
    header("Location: VendaList.php");
    // Interrompe imediatamente o processamento do script atual após o redirecionamento
    exit;
}
?>

<div class="mb-4">
    <h2><?php echo $id ? 'Editar' : 'Registrar'; ?> Venda</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Produto Vendido <span class="text-danger">*</span></label>
                    <select name="produto_id" class="form-select" required>
                        <option value="" selected disabled>Selecione o produto...</option>
                        <?php foreach ($produtos as $prod): ?>
                            <option value="<?php echo $prod['id']; ?>" <?php echo $venda['produto_id'] == $prod['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($prod['nome_peca']) . " (" . htmlspecialchars($prod['tamanho']) . ") - R$ " . number_format($prod['preco_venda'], 2, ',', '.'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">É obrigatório selecionar um produto.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Data da Compra <span class="text-danger">*</span></label>
                    <input type="date" name="data_compra" class="form-control" value="<?php echo htmlspecialchars($venda['data_compra']); ?>" required>
                    <div class="invalid-feedback">Por favor, selecione a data da compra.</div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Forma de Pagamento <span class="text-danger">*</span></label>
                    <select name="forma_pagamento" class="form-select" required>
                        <option value="Pix" <?php echo $venda['forma_pagamento'] == 'Pix' ? 'selected' : ''; ?>>Pix</option>
                        <option value="Cartão de Crédito" <?php echo $venda['forma_pagamento'] == 'Cartão de Crédito' ? 'selected' : ''; ?>>Cartão de Crédito</option>
                        <option value="Cartão de Débito" <?php echo $venda['forma_pagamento'] == 'Cartão de Débito' ? 'selected' : ''; ?>>Cartão de Débito</option>
                        <option value="Dinheiro" <?php echo $venda['forma_pagamento'] == 'Dinheiro' ? 'selected' : ''; ?>>Dinheiro</option>
                    </select>
                    <div class="invalid-feedback">Selecione uma forma de pagamento.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Valor Total (R$) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="valor_total" class="form-control" value="<?php echo htmlspecialchars($venda['valor_total']); ?>" required>
                    <div class="invalid-feedback">Insira um valor maior que zero.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Status do Pedido <span class="text-danger">*</span></label>
                    <select name="status_pedido" class="form-select" required>
                        <option value="Aprovado" <?php echo $venda['status_pedido'] == 'Aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                        <option value="Em Separação" <?php echo $venda['status_pedido'] == 'Em Separação' ? 'selected' : ''; ?>>Em Separação</option>
                        <option value="Enviado" <?php echo $venda['status_pedido'] == 'Enviado' ? 'selected' : ''; ?>>Enviado</option>
                    </select>
                    <div class="invalid-feedback">Defina o status atual do pedido.</div>
                </div>
            </div>
            
            <hr>
            <div class="mt-3">
                <button type="submit" class="btn btn-success px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Salvar Venda
                </button>
                <a href="VendaList.php" class="btn btn-secondary px-4">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    'use strict' // Ativa o modo estrito para o processamento limpo e seguro do código JavaScript

    // Captura todos os elementos do formulário que requerem validação pela classe atribuída
    var forms = document.querySelectorAll('.needs-validation')
    // Mapeia e varre a lista de formulários interceptando o evento de envio
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            // Verifica se o formulário falhou em cumprir algum critério de obrigatoriedade (required, min, etc)
            if (!form.checkValidity()) {
                event.preventDefault() // Impede a continuação do envio e atualização da página
                event.stopPropagation() // Interrompe o borbulhamento do evento na árvore do DOM
                
                // Busca de forma interna o primeiro input inválido dentro do formulário
                var firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    // Posiciona automaticamente o cursor do teclado/foco visual diretamente no elemento com erro
                    firstInvalid.focus();
                }
            }
            // Insere a classe que ativa as cores visuais de sucesso (verde) ou falha (vermelho) nos inputs do Bootstrap
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php 
// Inclui o arquivo de fechamento e rodapé da página (HTML final, scripts JS globais)
include '../footer.php'; 
?>