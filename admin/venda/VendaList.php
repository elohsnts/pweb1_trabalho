<?php
include '../db.class.php';
include '../header.php';

$db = DB::conectar();
$busca = $_GET['busca'] ?? '';

// INNER JOIN adicionado para buscar o nome do produto vinculado à venda
$sql = "SELECT v.*, p.nome_peca FROM venda v 
        INNER JOIN produto p ON v.produto_id = p.id";

// FILTRO E BUSCA DE VENDAS 
if ($busca) {
    // Se houver busca, filtra pelo status da venda, forma de pagamento ou nome do produto associado
    $sql .= " WHERE v.status_pedido LIKE :busca 
              OR v.forma_pagamento LIKE :busca 
              OR p.nome_peca LIKE :busca";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':busca', "%$busca%");
} else {
    // Se não houver busca, prepara a consulta base (traz todas as vendas)
    $stmt = $db->prepare($sql);
}
// Executa a consulta no banco e guarda a lista de vendas na variável $vendas
$stmt->execute();
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);


// --- BLOCO 2: EXCLUSÃO DE VENDA ---
if (isset($_GET['deletar'])) {
    // Se a URL tiver o parâmetro ?deletar=ID, captura o ID da venda
    $id = $_GET['deletar'];
    
    // Deleta o registro da venda diretamente do banco de dados
    $del = $db->prepare("DELETE FROM venda WHERE id = ?");
    $del->execute([$id]);
    
    // Redireciona de volta para a lista de vendas e encerra o script
    header("Location: VendaList.php");
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gerenciar Vendas</h2>
    <a href="VendaForm.php" class="btn btn-primary">+ Nova Venda</a>
</div>

<form method="GET" class="mb-4">
    <div class="input-group w-50">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por produto, status ou pagamento..." value="<?php echo htmlspecialchars($busca); ?>">
        <button class="btn btn-outline-secondary" type="submit">Pesquisar</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Data da Compra</th>
                    <th>Produto Vendido</th> <th>Forma de Pagamento</th>
                    <th>Valor Total</th>
                    <th>Status do Pedido</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($vendas) > 0): ?>
                    <?php foreach ($vendas as $v): ?> <!-- percorre (repete) uma lista ou array de dados automaticamente, item por item. -->
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($v['data_compra'])); ?></td>
                        <td class="fw-semibold text-dark"><?php echo htmlspecialchars($v['nome_peca']); ?></td> <td><?php echo htmlspecialchars($v['forma_pagamento']); ?></td>
                        <td class="fw-bold text-success">R$ <?php echo number_format($v['valor_total'], 2, ',', '.'); ?></td>
                        <td>
                            <span class="badge <?php echo $v['status_pedido'] == 'Enviado' ? 'bg-success' : ($v['status_pedido'] == 'Aprovado' ? 'bg-primary' : 'bg-warning text-dark'); ?>">
                                <?php echo htmlspecialchars($v['status_pedido']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="VendaForm.php?id=<?php echo $v['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="VendaList.php?deletar=<?php echo $v['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?');">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Nenhum registro de venda encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>