<?php
// Inclui o arquivo de classe do banco de dados para gerenciar a conexão
include '../db.class.php';
// Inclui o cabeçalho padrão do layout do sistema (HTML inicial, CSS, barra de navegação)
include '../header.php';

// Ativa e armazena a conexão com o banco de dados na variável $db chamando o método estático da classe DB
$db = DB::conectar();
// Captura o 'id' da URL via método GET. Se não existir, define como null (operador de coalescência nula)
$id = $_GET['id'] ?? null;
// Inicializa um array com campos vazios para evitar erros de índice indefinido na renderização inicial do formulário
$usuario = ['nome' => '', 'telefone' => '', 'email' => '', 'login' => '', 'senha' => ''];

// Verifica se um ID foi passado na URL para carregar as informações do usuário atual (Modo Edição)
if ($id) {
    // Prepara de forma segura a consulta SQL usando placeholders (?) para evitar ataques de SQL Injection
    $stmt = $db->prepare("SELECT * FROM usuario WHERE id = ?");
    // Executa a consulta passando o ID capturado para a substituição do marcador
    $stmt->execute([$id]);
    // Sobrescreve o array vazio com os dados reais do usuário vindos diretamente do banco
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Verifica se a requisição foi feita através do método POST, indicando o envio do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados inseridos pelo usuário nos campos de texto do formulário
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    // Se houver um ID, executa a atualização (UPDATE) de um registro que já existe
    if ($id) {
        // Define a instrução SQL para modificar as informações do usuário no banco com base no ID
        $sql = "UPDATE usuario SET nome=?, telefone=?, email=?, login=?, senha=? WHERE id=?";
        $stmt = $db->prepare($sql);
        // Executa o comando passando os valores na ordem exata dos marcadores de interrogação (?)
        $stmt->execute([$nome, $telefone, $email, $login, $senha, $id]);
    } else {
        // Se NÃO houver ID, realiza a inserção (INSERT) de um novo usuário na tabela
        $sql = "INSERT INTO usuario (nome, telefone, email, login, senha) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        // Executa a inserção dos dados informados no banco de dados
        $stmt->execute([$nome, $telefone, $email, $login, $senha]);
    }

    // Redireciona o navegador de volta para a tela de listagem de usuários cadastrados
    header("Location: UsuarioList.php");
    // Finaliza imediatamente o processamento deste script PHP
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

<?php 
// Inclui o arquivo de rodapé padrão da aplicação para fechar as tags abertas e carregar arquivos JavaScript globais
include '../footer.php'; 
?>