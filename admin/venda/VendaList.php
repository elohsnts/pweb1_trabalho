<?php
require_once '../header.php';
require_once '../db.class.php';

$db = new DB();
$pdo = $db->getConexao();

$busca = trim($_GET['busca'] ?? '');

if ($busca !== '') {
    $stmt = $pdo->prepare("SELECT * FROM venda WHERE forma_pagamento LIKE ? OR status_pedido LIKE ? ORDER BY id DESC");
    $termo = "%$busca%";
    $stmt->execute([$termo, $termo]);
} else {
    $stmt = $pdo->query("SELECT * FROM venda ORDER BY id DESC");
}
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = $_GET['msg'] ?? '';

$statusCores = [
    'Pendente'   => 'warning',
    'Pago'       => 'success',
    'Cancelado'  => 'danger',
    'Enviado'    => 'info',
    'Entregue'   => 'primary',
];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold" style="color:#c0397a;"><i class="fa-solid fa-cart-shopping me-2"></i>Vendas</h2>
    <a href="VendaForm.php" class="btn" style="background:#c0397a;color:#fff;">
        <i class="fa-solid fa-plus me-1"></i>Registrar
    </a>
</div>

<?php if ($msg === 'salvo'): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa-solid fa-check-circle me-2"></i>Venda salva com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif ($msg === 'excluido'): ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <i class="fa-solid fa-trash me-2"></i>Venda excluída com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="GET" class="d-flex gap-2 mb-4">
    <input type="text" name="busca" class="form-control" placeholder="Buscar por forma de pagamento ou status..."
           value="<?= htmlspecialchars($busca) ?>" style="max-width:320px;">
    <button type="submit" class="btn" style="background:#c0397a;color:#fff;">
        <i class="fa-solid fa-magnifying-glass me-1"></i>Buscar
    </button>
    <?php if ($busca): ?>
        <a href="VendaList.php" class="btn btn-outline-secondary">Limpar</a>
    <?php endif; ?>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#c0397a;color:#fff;">
                <tr>
                    <th>#</th>
                    <th>Data da Compra</th>
                    <th>Forma de Pagamento</th>
                    <th>Valor Total</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($vendas) === 0): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Nenhuma venda encontrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($vendas as $v): ?>
                    <tr>
                        <td><?= $v['id'] ?></td>
                        <td><?= date('d/m/Y', strtotime($v['data_compra'])) ?></td>
                        <td><?= htmlspecialchars($v['forma_pagamento']) ?></td>
                        <td>R$ <?= number_format($v['valor_total'], 2, ',', '.') ?></td>
                        <td>
                            <?php $cor = $statusCores[$v['status_pedido']] ?? 'secondary'; ?>
                            <span class="badge bg-<?= $cor ?>"><?= htmlspecialchars($v['status_pedido']) ?></span>
                        </td>
                        <td class="text-center">
                            <a href="VendaForm.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="VendaExcluir.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-outline-danger"
                               title="Excluir" onclick="return confirm('Deseja excluir esta venda?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../footer.php'; ?>