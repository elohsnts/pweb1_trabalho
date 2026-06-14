<?php
include '../db.class.php';
include '../header.php';

$db = DB::conectar();
$id = $_GET['id'] ?? null;
$produto = ['nome_peca' => '', 'tamanho' => 'M', 'cor_predominante' => '', 'preco_venda' => ''];

if ($id) {
    $stmt = $db->prepare("SELECT * FROM produto WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome_peca'];
    $tamanho = $_POST['tamanho'];
    $cor = $_POST['cor_predominante'];
    $preco = $_POST['preco_venda'];

    if ($id) {
        $sql = "UPDATE produto SET nome_peca=?, tamanho=?, cor_predominante=?, preco_venda=? WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $tamanho, $cor, $preco, $id]);
    } else {
        $sql = "INSERT INTO produto (nome_peca, tamanho, cor_predominante, preco_venda) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $tamanho, $cor, $preco]);
    }
    header("Location: ProdutoList.php");
    exit;
}
?>

<div class="mb-4">
    <h2><?php echo $id ? 'Editar' : 'Cadastrar'; ?> Produto</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Nome da Peça</label>
                    <input type="text" name="nome_peca" class="form-control" value="<?php echo htmlspecialchars($produto['nome_peca']); ?>" required>
                </div>
                <div class="col-md-3">
                    <label>Tamanho</label>
                    <select name="tamanho" class="form-control" required>
                        <option value="P" <?php echo $produto['tamanho'] == 'P' ? 'selected' : ''; ?>>P</option>
                        <option value="M" <?php echo $produto['tamanho'] == 'M' ? 'selected' : ''; ?>>M</option>
                        <option value="G" <?php echo $produto['tamanho'] == 'G' ? 'selected' : ''; ?>>G</option>
                        <option value="GG" <?php echo $produto['tamanho'] == 'GG' ? 'selected' : ''; ?>>GG</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Cor Predominante</label>
                    <input type="text" name="cor_predominante" class="form-control" value="<?php echo htmlspecialchars($produto['cor_predominante']); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Preço de Venda (R$)</label>
                    <input type="number" step="0.01" name="preco_venda" class="form-control" value="<?php echo htmlspecialchars($produto['preco_venda']); ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="ProdutoList.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>