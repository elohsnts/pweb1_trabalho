<?php
// Inclui o arquivo de classe do banco de dados para gerenciar a conexão
include '../db.class.php';
// Inclui o cabeçalho padrão do layout do sistema (HTML inicial, links CSS, menu de navegação)
include '../header.php';

// Ativa e armazena a conexão com o banco de dados chamando o método estático da classe DB
$db = DB::conectar();
// Captura o termo digitado no campo de busca via parâmetro GET. Se estiver vazio, assume uma string vazia
$busca = $_GET['busca'] ?? '';

// Define a estrutura base da consulta SQL para selecionar dados da tabela usuario
$sql = "SELECT * FROM usuario";

// Condicional para verificar se o usuário realizou uma filtragem por palavra-chave
if ($busca) {
    // Concatena as cláusulas WHERE na consulta para filtrar por nome ou login usando parâmetros nomeados (:busca)
    $sql .= " WHERE nome LIKE :busca OR login LIKE :busca";
    // Prepara a instrução SQL no banco de dados de maneira protegida contra injeção de SQL
    $stmt = $db->prepare($sql);
    // Vincula o termo de busca envolvendo-o em caracteres curinga (%) para correspondências parciais (LIKE)
    $stmt->bindValue(':busca', "%$busca%");
} else {
    // Caso nenhuma busca tenha sido feita, apenas prepara a query base que trará todos os registros
    $stmt = $db->prepare($sql);
}
// Executa a consulta estruturada no banco de dados
$stmt->execute();
// Recupera todos os registros encontrados e os armazena na variável como um array associativo
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Verifica se existe uma requisição de exclusão enviada via parâmetro GET 'deletar' na URL
if (isset($_GET['deletar'])) {
    // Armazena o ID do usuário que se deseja apagar
    $id = $_GET['deletar'];
    
    // Prepara de forma segura a exclusão do registro na tabela usuario usando um placeholder (?)
    $del = $db->prepare("DELETE FROM usuario WHERE id = ?");
    // Executa a exclusão passando o ID capturado para dentro do array de execução
    $del->execute([$id]);
    
    // Redireciona o navegador de volta para a listagem limpa, atualizando a página e removendo o ID da URL
    header("Location: UsuarioList.php");
    // Encerra imediatamente a execução deste script PHP após o comando de redirecionamento
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

<?php 
// Inclui o arquivo de rodapé padrão (fechamento de tags HTML e inclusão de scripts JavaScript globais)
include '../footer.php'; 
?>