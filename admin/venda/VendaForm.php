<?php
include '../db.class.php';
include '../header.php';

$db = DB::conectar();
$id = $_GET['id'] ?? null;
$venda = ['data_compra' => date('Y-m-d'), 'forma_pagamento' => 'Pix', 'valor_total' => '', 'status_pedido' => 'Aprovado', 'produto_id' => ''];

// Busca todos os produtos cadastrados para listar na caixa de seleção
$stmt_produtos = $db->prepare("SELECT id, nome_peca, tamanho, preco_venda FROM produto ORDER BY nome_peca ASC");
$stmt_produtos->execute();
$produtos = $stmt_produtos->fetchAll(PDO::FETCH_ASSOC);

// CARREGAR DADOS PARA EDIÇÃO 
if ($id) {
    // Se existir um ID na URL, busca os dados da venda atual para preencher o formulário
    $stmt = $db->prepare("SELECT * FROM venda WHERE id = ?");
    $stmt->execute([$id]);
    $venda = $stmt->fetch(PDO::FETCH_ASSOC);
}

// PROCESSAR FORMULÁRIO (SALVAR VENDA) 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados enviados pelo formulário de venda
    $data = $_POST['data_compra'];
    $pagamento = $_POST['forma_pagamento'];
    $valor = $_POST['valor_total'];
    $status = $_POST['status_pedido'];
    $produto_id = $_POST['produto_id']; 

    if ($id) {
        // Bloco de Edição: Se a venda já existe, atualiza os dados dela no banco
        $sql = "UPDATE venda SET data_compra=?, forma_pagamento=?, valor_total=?, status_pedido=?, produto_id=? WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$data, $pagamento, $valor, $status, $produto_id, $id]);
    } else {
        // Bloco de Cadastro: Se for uma nova venda, insere o registro no banco
        $sql = "INSERT INTO venda (data_compra, forma_pagamento, valor_total, status_pedido, produto_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$data, $pagamento, $valor, $status, $produto_id]);
    }
    
    // Redireciona para a lista de vendas e encerra o script
    header("Location: VendaList.php");
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
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
                
                var firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php include '../footer.php'; ?>