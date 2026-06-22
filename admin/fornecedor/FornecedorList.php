<?php
// Inclui o arquivo que contém a classe responsável pela conexão com o banco de dados
include '../db.class.php';
// Inclui o arquivo de cabeçalho padrão (HTML inicial, links CSS, menu superior)
include '../header.php';

// Invoca o método estático conectar() da classe DB para estabelecer a conexão PDO
$db = DB::conectar();
// Captura o termo digitado na pesquisa via parâmetro GET. Se estiver vazio, define como string vazia
$busca = $_GET['busca'] ?? '';

// Define a estrutura base da consulta SQL para selecionar dados da tabela fornecedor
$sql = "SELECT * FROM fornecedor";

// Condicional para verificar se o usuário submeteu uma palavra-chave no campo de busca
if ($busca) {
    // Acrescenta a cláusula WHERE filtrando por nome ou CNPJ através de parâmetros nomeados (:busca)
    $sql .= " WHERE nome_empresa LIKE :busca OR cnpj LIKE :busca";
    // Prepara a instrução SQL no banco de dados de maneira protegida contra injeção de SQL
    $stmt = $db->prepare($sql);
    // Vincula o valor da variável de busca ao parâmetro da query, adicionando as porcentagens (%) para busca parcial (LIKE)
    $stmt->bindValue(':busca', "%$busca%");
} else {
    // Caso nenhuma busca tenha sido feita, apenas prepara a query base que trará todos os registros
    $stmt = $db->prepare($sql);
}
// Executa a consulta configurada no banco de dados
$stmt->execute();
// Recupera todos os registros resultantes da consulta organizados em um array associativo
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica se existe uma requisição de exclusão enviada via parâmetro GET 'deletar' na URL
if (isset($_GET['deletar'])) {
    // Armazena o ID do fornecedor que se deseja apagar
    $id = $_GET['deletar'];
    // Prepara a instrução SQL de exclusão utilizando o marcador de posição (?) para segurança
    $del = $db->prepare("DELETE FROM fornecedor WHERE id = ?");
    // Executa a exclusão passando o ID capturado para dentro do array de execução
    $del->execute([$id]);
    // Redireciona o navegador de volta para a listagem limpa, atualizando a página e removendo o ID da URL
    header("Location: FornecedorList.php");
    // Encerra imediatamente a execução deste script PHP após o comando de redirecionamento
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

<?php 
// Inclui o arquivo de rodapé padrão (fechamento de tags HTML e inclusão de scripts JavaScript globais)
include '../footer.php'; 
?>