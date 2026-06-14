<?php
include '../db.class.php';
include '../header.php';

$db = DB::conectar();
$busca = $_GET['busca'] ?? '';

$sql = "SELECT * FROM produto";
if ($busca) {
    $sql .= " WHERE nome_peca LIKE :busca OR cor_predominante LIKE :busca";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':busca', "%$busca%");
} else {
    $stmt = $db->prepare($sql);
}
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['deletar'])) {
    $id = $_GET['deletar'];
    $del = $db->prepare("DELETE FROM produto WHERE id = ?");
    $del->execute([$id]);
    header("Location: ProdutoList.php");
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gerenciar Produtos</h2>
    <a href="ProdutoForm.php" class="btn btn-primary">+ Novo Produto</a>
</div>

<form method="GET" class="mb-4">
    <div class="input-group w-50">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por peça ou cor..." value="<?php echo htmlspecialchars($busca); ?>">
        <button class="btn btn-outline-secondary" type="submit">Pesquisar</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome da Peça</th>
                    <th>Tamanho</th>
                    <th>Cor</th>
                    <th>Preço de Venda</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo htmlspecialchars($p['nome_peca']); ?></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($p['tamanho']); ?></span></td>
                    <td><?php echo htmlspecialchars($p['cor_predominante']); ?></td>
                    <td class="text-success fw-bold">R$ <?php echo number_format($p['preco_venda'], 2, ',', '.'); ?></td>
                    <td>
                        <a href="ProdutoForm.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="ProdutoList.php?deletar=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta peça?');">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>