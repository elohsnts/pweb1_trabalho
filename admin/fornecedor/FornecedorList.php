<?php
include '../db.class.php';
include '../header.php';

$db = DB::conectar();
$busca = $_GET['busca'] ?? '';

$sql = "SELECT * FROM fornecedor";
if ($busca) {
    $sql .= " WHERE nome_empresa LIKE :busca OR cnpj LIKE :busca";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':busca', "%$busca%");
} else {
    $stmt = $db->prepare($sql);
}
$stmt->execute();
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['deletar'])) {
    $id = $_GET['deletar'];
    $del = $db->prepare("DELETE FROM fornecedor WHERE id = ?");
    $del->execute([$id]);
    header("Location: FornecedorList.php");
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gerenciar Fornecedores</h2>
    <a href="FornecedorForm.php" class="btn btn-primary">+ Novo Fornecedor</a>
</div>

<form method="GET" class="mb-4">
    <div class="input-group w-50">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por empresa ou CNPJ..." value="<?php echo htmlspecialchars($busca); ?>">
        <button class="btn btn-outline-secondary" type="submit">Pesquisar</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>CNPJ</th>
                    <th>Prazo de Entrega</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fornecedores as $f): ?>
                <tr>
                    <td><?php echo htmlspecialchars($f['nome_empresa']); ?></td>
                    <td><?php echo htmlspecialchars($f['cnpj']); ?></td>
                    <td><?php echo $f['prazo_entrega_dias']; ?> dias</td>
                    <td><?php echo htmlspecialchars($f['telefone_contato']); ?></td>
                    <td>
                        <a href="FornecedorForm.php?id=<?php echo $f['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="FornecedorList.php?deletar=<?php echo $f['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?');">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>