<?php
require_once '../header.php';
require_once '../db.class.php';

$db = new DB();
$pdo = $db->getConexao();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$v = ['id' => 0, 'data_compra' => '', 'forma_pagamento' => '', 'valor_total' => '', 'status_pedido' => ''];
$erros = [];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM venda WHERE id = ?");
    $stmt->execute([$id]);
    $v = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$v) { header('Location: VendaList.php'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['data_compra']      = trim($_POST['data_compra'] ?? '');
    $v['forma_pagamento']  = trim($_POST['forma_pagamento'] ?? '');
    $v['valor_total']      = trim($_POST['valor_total'] ?? '');
    $v['status_pedido']    = trim($_POST['status_pedido'] ?? '');

    if (empty($v['data_compra']))     $erros[] = 'Data da compra é obrigatória.';
    if (empty($v['forma_pagamento'])) $erros[] = 'Forma de pagamento é obrigatória.';
    if (empty($v['valor_total']) || !is_numeric($v['valor_total']))
        $erros[] = 'Valor total inválido.';
    if (empty($v['status_pedido']))   $erros[] = 'Status do pedido é obrigatório.';

    if (empty($erros)) {
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE venda SET data_compra=?, forma_pagamento=?, valor_total=?, status_pedido=? WHERE id=?");
            $stmt->execute([$v['data_compra'], $v['forma_pagamento'], $v['valor_total'], $v['status_pedido'], $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO venda (data_compra, forma_pagamento, valor_total, status_pedido) VALUES (?,?,?,?)");
            $stmt->execute([$v['data_compra'], $v['forma_pagamento'], $v['valor_total'], $v['status_pedido']]);
        }
        header('Location: VendaList.php?msg=salvo');
        exit;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold" style="color:#c0397a;">
        <i class="fa-solid fa-cart-shopping me-2"></i><?= $id > 0 ? 'Editar Venda' : 'Nova Venda' ?>
    </h2>
    <a href="VendaList.php" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<?php if (!empty($erros)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($erros as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Data da Compra *</label>
                    <input type="date" name="data_compra" class="form-control"
                           value="<?= htmlspecialchars($v['data_compra']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Forma de Pagamento *</label>
                    <select name="forma_pagamento" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach (['Dinheiro','Cartão de Crédito','Cartão de Débito','PIX','Boleto'] as $fp): ?>
                            <option value="<?= $fp ?>" <?= $v['forma_pagamento'] === $fp ? 'selected' : '' ?>><?= $fp ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Valor Total (R$) *</label>
                    <input type="number" name="valor_total" class="form-control" step="0.01" min="0"
                           value="<?= htmlspecialchars($v['valor_total']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status do Pedido *</label>
                    <select name="status_pedido" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach (['Pendente','Pago','Enviado','Entregue','Cancelado'] as $st): ?>
                            <option value="<?= $st ?>" <?= $v['status_pedido'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn" style="background:#c0397a;color:#fff;">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Salvar
                </button>
                <a href="VendaList.php" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-xmark me-1"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../footer.php'; ?>