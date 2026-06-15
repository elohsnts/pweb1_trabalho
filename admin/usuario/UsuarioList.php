<?php

include '../db.class.php';
include '../header.php';

// Estabelece a conexão estática com a base de dados através da classe PDO empacotada
$db = DB::conectar();

// Armazena o termo pesquisado usando o operador de coalescência nula (padrão é vazio)
$busca = $_GET['busca'] ?? '';

// Construção da instrução SQL para busca geral ou com critérios de filtragem
$sql = "SELECT * FROM usuario";
if ($busca) {
    // Se houver busca, adiciona cláusulas OR usando Named Parameters (:busca) para evitar injeções SQL
    $sql .= " WHERE nome LIKE :busca OR login LIKE :busca";
    $stmt = $db->prepare($sql);
    
    // Vincula a variável à instrução SQL envolvendo o termo com '%' para permitir busca parcial
    $stmt->bindValue(':busca', "%$busca%");
} else {
    // Prepara a consulta limpa para trazer todos os registros caso o filtro esteja vazio
    $stmt = $db->prepare($sql);
}
$stmt->execute();

// Retorna todos os usuários encontrados em formato de array associativo
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);


// OPERAÇÃO DE EXCLUSÃO DE USUÁRIO
if (isset($_GET['deletar'])) {
    $id = $_GET['deletar'];
    
    // Executa diretamente a remoção através de Prepared Statements informando o ID por posição (?)
    $del = $db->prepare("DELETE FROM usuario WHERE id = ?");
    $del->execute([$id]);
    
    // Redireciona para a própria página limpa, eliminando a query string '?deletar=' da barra de endereços
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