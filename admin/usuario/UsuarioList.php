<?php
include '../db.class.php';
include '../header.php';

$db = DB::conectar();
$busca = $_GET['busca'] ?? '';

$sql = "SELECT * FROM usuario";
if ($busca) {
    // Se houver busca, filtra por nome ou login usando LIKE (busca parcial)
    $sql .= " WHERE nome LIKE :busca OR login LIKE :busca";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':busca', "%$busca%");
} else {
    // Se não houver busca, prepara para trazer todos os usuários
    $stmt = $db->prepare($sql);
}
// Executa a consulta e guarda a lista de usuários na variável $usuarios
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (isset($_GET['deletar'])) {
    // Se a URL tiver o parâmetro ?deletar=ID, captura o ID enviado
    $id = $_GET['deletar'];
    
    // Deleta o registro do usuário diretamente do banco de dados
    $del = $db->prepare("DELETE FROM usuario WHERE id = ?");
    $del->execute([$id]);
    
    // Redireciona de volta para a lista e encerra a execução do script
    header("Location: UsuarioList.php");
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gerenciar Usuários (Administradores)</h2>
    <a href="UsuarioForm.php" class="btn btn-primary">+ Novo Usuário</a>
</div>

<form method="GET" class="mb-4">
    <div class="input-group w-50">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou login..." value="<?php echo htmlspecialchars($busca); ?>">
        <button class="btn btn-outline-secondary" type="submit">Pesquisar</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                    <th>Login</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['nome']); ?></td>
                    <td><?php echo htmlspecialchars($u['telefone']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo htmlspecialchars($u['login']); ?></td>
                    <td>
                        <a href="UsuarioForm.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="UsuarioList.php?deletar=<?php echo $u['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este usuário?');">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../footer.php'; ?>