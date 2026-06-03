<?php
require_once '../header.php';
require_once '../db.class.php';

$db = new DB();
$pdo = $db->getConexao();

$busca = trim($_GET['busca'] ?? '');

if ($busca !== '') {
    $stmt = $pdo->prepare("SELECT * FROM fornecedor WHERE nome_empresa LIKE ? OR cnpj LIKE ? OR telefone_contato LIKE ? ORDER BY id DESC");
    $termo = "%$busca%";
    $stmt->execute([$termo, $termo, $termo]);
} else {
    $stmt = $pdo->query("SELECT * FROM fornecedor ORDER BY id DESC");
}
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$msg = $_GET['msg'] ?? '';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold" style="color:#c0397a;"><i class="fa-solid fa-truck me-2"></i>Fornecedores</h2>
    <a href="FornecedorForm.php" class="btn" style="background:#c0397a;color:#fff;">
        <i class="fa-solid fa-plus me-1"></i>Cadastrar
    </a>
</div>

<?php if ($msg === 'salvo'): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa-solid fa-check-circle me-2"></i>Fornecedor salvo com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif ($msg === 'excluido'): ?>
    <div class="alert alert-warning alert-dismissible fade show">
        <i class="fa-solid fa-trash me-2"></i>Fornecedor excluído com sucesso!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="GET" class="d-flex gap-2 mb-4">
    <input type="text" name="busca" class="form-control" placeholder="Buscar por nome, CNPJ ou telefone..."
           value="<?= htmlspecialchars($busca) ?>" style="max-width:320px;">
    <button type="submit" class="btn" style="background:#c0397a;color:#fff;">
        <i class="fa-solid fa-magnifying-glass me-1"></i>Buscar
    </button>
    <?php if ($busca): ?>
        <a href="FornecedorList.php" class="btn btn-outline-secondary">Limpar</a>
    <?php endif; ?>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#c0397a;color:#fff;">
                <tr>
                    <th>#</th>
                    <th>Nome da Empresa</th>
                    <th>CNPJ</th>
                    <th>Prazo Entrega (dias)</th>
                    <th>Telefone Contato</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($fornecedores) === 0): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Nenhum fornecedor encontrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($fornecedores as $f): ?>
                    <tr>
                        <td><?= $f['id'] ?></td>
                        <td><?= htmlspecialchars($f['nome_empresa']) ?></td>
                        <td><?= htmlspecialchars($f['cnpj']) ?></td>
                        <td><?= $f['prazo_entrega_dias'] ?> dias</td>
                        <td><?= htmlspecialchars($f['telefone_contato']) ?></td>
                        <td class="text-center">
                            <a href="FornecedorForm.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="FornecedorExcluir.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger"
                               title="Excluir" onclick="return confirm('Deseja excluir este fornecedor?')">
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