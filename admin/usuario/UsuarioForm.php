<?php
include '../db.class.php';
include '../header.php';

$db = DB::conectar();
$id = $_GET['id'] ?? null;
$usuario = ['nome' => '', 'telefone' => '', 'email' => '', 'login' => '', 'senha' => ''];

if ($id) {
    $stmt = $db->prepare("SELECT * FROM usuario WHERE id = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    if ($id) {
        $sql = "UPDATE usuario SET nome=?, telefone=?, email=?, login=?, senha=? WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $telefone, $email, $login, $senha, $id]);
    } else {
        $sql = "INSERT INTO usuario (nome, telefone, email, login, senha) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $telefone, $email, $login, $senha]);
    }
    header("Location: UsuarioList.php");
    exit;
}
?>

<div class="mb-4">
    <h2><?php echo $id ? 'Editar' : 'Cadastrar'; ?> Usuário</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" name="nome" class="form-control" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="<?php echo htmlspecialchars($usuario['telefone']); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Login de Acesso</label>
                    <input type="text" name="login" class="form-control" value="<?php echo htmlspecialchars($usuario['login']); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" value="<?php echo htmlspecialchars($usuario['senha']); ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="UsuarioList.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>