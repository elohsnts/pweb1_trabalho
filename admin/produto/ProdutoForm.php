<?php
require_once '../header.php';
require_once '../db.class.php';

$db = new DB();
$pdo = $db->getConexao();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produto = ['id' => 0, 'nome_peca' => '', 'tamanho' => '', 'cor_predominante' => '', 'preco_venda' => ''];
$erros = [];

// Carregar dados para edição
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM produto WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$produto) {
        header('Location: ProdutoList.php');
        exit;
    }
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto['nome_peca']        = trim($_POST['nome_peca'] ?? '');
    $produto['tamanho']          = trim($_POST['tamanho'] ?? '');
    $produto['cor_predominante'] = trim($_POST['cor_predominante'] ?? '');
    $produto['preco_venda']      = trim($_POST['preco_venda'] ?? '');

    if (empty($produto['nome_peca']))        $erros[] = 'Nome da peça é obrigatório.';
    if (empty($produto['tamanho']))          $erros[] = 'Tamanho é obrigatório.';
    if (empty($produto['cor_predominante'])) $erros[] = 'Cor predominante é obrigatória.';
    if (empty($produto['preco_venda']) || !is_numeric($produto['preco_venda']))
        $erros[] = 'Preço de venda inválido.';

    if (empty($erros)) {
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE produto SET nome_peca=?, tamanho=?, cor_predominante=?, preco_venda=? WHERE id=?");
            $stmt->execute([$produto['nome_peca'], $produto['tamanho'], $produto['cor_predominante'], $produto['preco_venda'], $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO produto (nome_peca, tamanho, cor_predominante, preco_venda) VALUES (?,?,?,?)");
            $stmt->execute([$produto['nome_peca'], $produto['tamanho'], $produto['cor_predominante'], $produto['preco_venda']]);
        }
        header('Location: ProdutoList.php?msg=salvo');
        exit;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold" style="color:#c0397a;">
        <i class="fa-solid fa-box me-2"></i><?= $id > 0 ? 'Editar Produto' : 'Novo Produto' ?>
    </h2>
    <a href="ProdutoList.php" class="btn btn-outline-secondary">
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
                    <label class="form-label fw-semibold">Nome da Peça *</label>
                    <input type="text" name="nome_peca" class="form-control"
                           value="<?= htmlspecialchars($produto['nome_peca']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tamanho *</label>
                    <select name="tamanho" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach (['PP','P','M','G','GG','XGG'] as $tam): ?>
                            <option value="<?= $tam ?>" <?= $produto['tamanho'] === $tam ? 'selected' : '' ?>><?= $tam ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Cor Predominante *</label>
                    <input type="text" name="cor_predominante" class="form-control"
                           value="<?= htmlspecialchars($produto['cor_predominante']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Preço de Venda (R$) *</label>
                    <input type="number" name="preco_venda" class="form-control" step="0.01" min="0"
                           value="<?= htmlspecialchars($produto['preco_venda']) ?>" required>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn" style="background:#c0397a;color:#fff;">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Salvar
                </button>
                <a href="ProdutoList.php" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-xmark me-1"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../footer.php'; ?>