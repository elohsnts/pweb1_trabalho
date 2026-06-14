<?php
include '../db.class.php';
include '../header.php';

$db = DB::conectar();
$busca = $_GET['busca'] ?? '';

$sql = "SELECT * FROM venda";
if ($busca) {
    $sql .= " WHERE status_pedido LIKE :busca OR forma_pagamento LIKE :busca";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':busca', "%$busca%");
} else {
    $stmt = $db->prepare($sql);
}
$stmt->execute();
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['deletar'])) {
    $id = $_GET['deletar'];
    $del = $db->prepare("DELETE FROM venda WHERE id = ?");
    $del->execute([$id]);
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
        <input type="text" name="busca" class="form-control" placeholder="Buscar por status ou pagamento..." value="<?php echo htmlspecialchars($busca); ?>">
        <button class="btn btn-outline-secondary" type="submit">Pesquisar</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Data da Compra</th>
                    <th>Forma de Pagamento</th>
                    <th>Valor Total</th>
                    <th>Status do Pedido</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendas as $v): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($v['data_compra'])); ?></td>
                    <td><?php echo htmlspecialchars($v['forma_pagamento']); ?></td>
                    <td class="fw-bold text-primary">R$ <?php echo number_format($v['valor_total'], 2, ',', '.'); ?></td>
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
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>