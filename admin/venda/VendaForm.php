<?php
include '../db.class.php';
include '../header.php';

$db = DB::conectar();
$id = $_GET['id'] ?? null;
$venda = ['data_compra' => date('Y-m-d'), 'forma_pagamento' => 'Pix', 'valor_total' => '', 'status_pedido' => 'Aprovado'];

if ($id) {
    $stmt = $db->prepare("SELECT * FROM venda WHERE id = ?");
    $stmt->execute([$id]);
    $venda = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST['data_compra'];
    $pagamento = $_POST['forma_pagamento'];
    $valor = $_POST['valor_total'];
    $status = $_POST['status_pedido'];

    if ($id) {
        $sql = "UPDATE venda SET data_compra=?, forma_pagamento=?, valor_total=?, status_pedido=? WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$data, $pagamento, $valor, $status, $id]);
    } else {
        $sql = "INSERT INTO venda (data_compra, forma_pagamento, valor_total, status_pedido) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$data, $pagamento, $valor, $status]);
    }
    header("Location: VendaList.php");
    exit;
}
?>

<div class="mb-4">
    <h2><?php echo $id ? 'Editar' : 'Registrar'; ?> Venda</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Data da Compra</label>
                    <input type="date" name="data_compra" class="form-control" value="<?php echo htmlspecialchars($venda['data_compra']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label>Forma de Pagamento</label>
                    <select name="forma_pagamento" class="form-control" required>
                        <option value="Pix" <?php echo $venda['forma_pagamento'] == 'Pix' ? 'selected' : ''; ?>>Pix</option>
                        <option value="Cartão de Crédito" <?php echo $venda['forma_pagamento'] == 'Cartão de Crédito' ? 'selected' : ''; ?>>Cartão de Crédito</option>
                        <option value="Cartão de Débito" <?php echo $venda['forma_pagamento'] == 'Cartão de Débito' ? 'selected' : ''; ?>>Cartão de Débito</option>
                        <option value="Dinheiro" <?php echo $venda['forma_pagamento'] == 'Dinheiro' ? 'selected' : ''; ?>>Dinheiro</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Valor Total (R$)</label>
                    <input type="number" step="0.01" name="valor_total" class="form-control" value="<?php echo htmlspecialchars($venda['valor_total']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label>Status do Pedido</label>
                    <select name="status_pedido" class="form-control" required>
                        <option value="Aprovado" <?php echo $venda['status_pedido'] == 'Aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                        <option value="Em Separação" <?php echo $venda['status_pedido'] == 'Em Separação' ? 'selected' : ''; ?>>Em Separação</option>
                        <option value="Enviado" <?php echo $venda['status_pedido'] == 'Enviado' ? 'selected' : ''; ?>>Enviado</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="VendaList.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>