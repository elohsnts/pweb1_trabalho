<?php
include '../db.class.php';
include '../header.php';

$db = DB::conectar();
$id = $_GET['id'] ?? null;
$fornecedor = ['nome_empresa' => '', 'cnpj' => '', 'prazo_entrega_dias' => '', 'telefone_contato' => ''];

if ($id) {
    $stmt = $db->prepare("SELECT * FROM fornecedor WHERE id = ?");
    $stmt->execute([$id]);
    $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome_empresa'];
    $cnpj = $_POST['cnpj'];
    $prazo = $_POST['prazo_entrega_dias'];
    $telefone = $_POST['telefone_contato'];

    if ($id) {
        $sql = "UPDATE fornecedor SET nome_empresa=?, cnpj=?, prazo_entrega_dias=?, telefone_contato=? WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $cnpj, $prazo, $telefone, $id]);
    } else {
        $sql = "INSERT INTO fornecedor (nome_empresa, cnpj, prazo_entrega_dias, telefone_contato) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $cnpj, $prazo, $telefone]);
    }
    header("Location: FornecedorList.php");
    exit;
}
?>

<div class="mb-4">
    <h2><?php echo $id ? 'Editar' : 'Cadastrar'; ?> Fornecedor</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Nome da Empresa</label>
                    <input type="text" name="nome_empresa" class="form-control" value="<?php echo htmlspecialchars($fornecedor['nome_empresa']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label>CNPJ</label>
                    <input type="text" name="cnpj" class="form-control" value="<?php echo htmlspecialchars($fornecedor['cnpj']); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Prazo Médio de Entrega (em dias)</label>
                    <input type="number" name="prazo_entrega_dias" class="form-control" value="<?php echo htmlspecialchars($fornecedor['prazo_entrega_dias']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label>Telefone de Contato</label>
                    <input type="text" name="telefone_contato" class="form-control" value="<?php echo htmlspecialchars($fornecedor['telefone_contato']); ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="FornecedorList.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>