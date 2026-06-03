<?php
require_once '../header.php';
require_once '../db.class.php';

$db = new DB();
$pdo = $db->getConexao();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$f = ['id' => 0, 'nome_empresa' => '', 'cnpj' => '', 'prazo_entrega_dias' => '', 'telefone_contato' => ''];
$erros = [];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM fornecedor WHERE id = ?");
    $stmt->execute([$id]);
    $f = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$f) { header('Location: FornecedorList.php'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f['nome_empresa']      = trim($_POST['nome_empresa'] ?? '');
    $f['cnpj']              = trim($_POST['cnpj'] ?? '');
    $f['prazo_entrega_dias']= trim($_POST['prazo_entrega_dias'] ?? '');
    $f['telefone_contato']  = trim($_POST['telefone_contato'] ?? '');

    if (empty($f['nome_empresa']))       $erros[] = 'Nome da empresa é obrigatório.';
    if (empty($f['cnpj']))               $erros[] = 'CNPJ é obrigatório.';
    if (empty($f['prazo_entrega_dias']) || !is_numeric($f['prazo_entrega_dias']))
        $erros[] = 'Prazo de entrega deve ser um número válido.';
    if (empty($f['telefone_contato']))   $erros[] = 'Telefone de contato é obrigatório.';

    if (empty($erros)) {
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE fornecedor SET nome_empresa=?, cnpj=?, prazo_entrega_dias=?, telefone_contato=? WHERE id=?");
            $stmt->execute([$f['nome_empresa'], $f['cnpj'], $f['prazo_entrega_dias'], $f['telefone_contato'], $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO fornecedor (nome_empresa, cnpj, prazo_entrega_dias, telefone_contato) VALUES (?,?,?,?)");
            $stmt->execute([$f['nome_empresa'], $f['cnpj'], $f['prazo_entrega_dias'], $f['telefone_contato']]);
        }
        header('Location: FornecedorList.php?msg=salvo');
        exit;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold" style="color:#c0397a;">
        <i class="fa-solid fa-truck me-2"></i><?= $id > 0 ? 'Editar Fornecedor' : 'Novo Fornecedor' ?>
    </h2>
    <a href="FornecedorList.php" class="btn btn-outline-secondary">
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
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nome da Empresa *</label>
                    <input type="text" name="nome_empresa" class="form-control"
                           value="<?= htmlspecialchars($f['nome_empresa']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">CNPJ *</label>
                    <input type="text" name="cnpj" class="form-control" placeholder="00.000.000/0000-00"
                           value="<?= htmlspecialchars($f['cnpj']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Prazo de Entrega (dias) *</label>
                    <input type="number" name="prazo_entrega_dias" class="form-control" min="1"
                           value="<?= htmlspecialchars($f['prazo_entrega_dias']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Telefone de Contato *</label>
                    <input type="text" name="telefone_contato" class="form-control"
                           value="<?= htmlspecialchars($f['telefone_contato']) ?>" required>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn" style="background:#c0397a;color:#fff;">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Salvar
                </button>
                <a href="FornecedorList.php" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-xmark me-1"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../footer.php'; ?>