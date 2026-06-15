<?php
include '../db.class.php';
include '../header.php';

// Estabelece a conexão com a base de dados
$db = DB::conectar();

// Captura o ID via GET. Se existir, indica Edição. Se não, indica um novo Cadastro.
$id = $_GET['id'] ?? null;

// Inicializa a estrutura do array $usuario com strings vazias.
// Essencial para o modo Cadastro não disparar erros de chaves inexistentes no HTML.
$usuario = ['nome' => '', 'telefone' => '', 'email' => '', 'login' => '', 'senha' => ''];

// Se o ID foi passado na URL, busca as informações do usuário específico (Modo Edição)
if ($id) {
    // Uso correto de prepared statements com o marcador "?" para blindar contra SQL Injection
    $stmt = $db->prepare("SELECT * FROM usuario WHERE id = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}


// PROCESSAMENTO DO FORMULÁRIO (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coleta as strings vindas dos campos do formulário
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    // PERSISTÊNCIA NO BANCO DE DADOS
    if ($id) {
        // Se o ID existe, atualiza os dados do usuário correspondente (UPDATE)
        $sql = "UPDATE usuario SET nome=?, telefone=?, email=?, login=?, senha=? WHERE id=?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $telefone, $email, $login, $senha, $id]);
    } else {
        // Se não há ID, registra um novo usuário no sistema (INSERT)
        $sql = "INSERT INTO usuario (nome, telefone, email, login, senha) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$nome, $telefone, $email, $login, $senha]);
    }
    
    // Redireciona de volta para a lista de usuários, limpando o fluxo de envio do POST
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