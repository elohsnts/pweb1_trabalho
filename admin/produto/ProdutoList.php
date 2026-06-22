<?php
// Inclui o arquivo de classe do banco de dados para gerenciar a conexão
include '../db.class.php';
// Inclui o cabeçalho padrão (HTML inicial, CSS do Bootstrap, barra de navegação)
include '../header.php';

// Invoca o método estático conectar() da classe DB para estabelecer a conexão PDO
$db = DB::conectar();
// Captura o termo digitado no campo de busca via parâmetro GET. Se estiver vazio, assume uma string vazia
$busca = $_GET['busca'] ?? '';

// Define a consulta base para listar todos os produtos.
// Se houver um termo de busca, filtra por 'nome_peca' ou 'cor_predominante' usando LIKE.
// Executa a consulta de forma segura (Prepared Statement) e armazena os resultados em $produtos.
$sql = "SELECT * FROM produto";
// Condicional para verificar se o usuário realizou uma filtragem por palavra-chave
if ($busca) {
    // Concatena as cláusulas WHERE na consulta utilizando parâmetros nomeados (:busca)
    $sql .= " WHERE nome_peca LIKE :busca OR cor_predominante LIKE :busca";
    // Prepara a instrução SQL no banco de dados de maneira protegida contra injeção de SQL
    $stmt = $db->prepare($sql);
    // Vincula o termo de busca envolvendo-o em caracteres curinga (%) para correspondências parciais
    $stmt->bindValue(':busca', "%$busca%");
} else {
    // Caso nenhuma busca tenha sido feita, apenas prepara a query base que trará todos os registros
    $stmt = $db->prepare($sql);
}
// Executa a consulta estruturada no banco de dados
$stmt->execute();
// Recupera todos os registros encontrados e os armazena na variável como um array associativo
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica se foi solicitada a exclusão de um produto via URL (?deletar=ID).
// 1. Busca o nome da imagem do produto no banco de dados.
// 2. Se o arquivo da imagem existir na pasta '../uploads/', remove-o do servidor (unlink).
// 3. Deleta o registro do produto do banco de dados e redireciona para a lista.
 
if (isset($_GET['deletar'])) {
    // Armazena o ID do produto que será excluído
    $id = $_GET['deletar'];
    
    // Apaga o arquivo físico de imagem associado para não acumular lixo no servidor
    // Prepara a consulta para descobrir o nome da imagem cadastrada para este produto específico
    $imgStmt = $db->prepare("SELECT imagem FROM produto WHERE id = ?");
    // Executa a consulta passando o ID capturado
    $imgStmt->execute([$id]);
    // Obtém o resultado da busca da imagem em formato associativo
    $imgProd = $imgStmt->fetch(PDO::FETCH_ASSOC);
    // Verifica se o campo imagem não está vazio e se o arquivo físico realmente existe na pasta uploads
    if (!empty($imgProd['imagem']) && file_exists('../uploads/' . $imgProd['imagem'])) {
        // Remove fisicamente o arquivo do diretório do servidor
        unlink('../uploads/' . $imgProd['imagem']);
    }

    // Prepara de forma segura a exclusão do registro na tabela produto usando um placeholder (?)
    $del = $db->prepare("DELETE FROM produto WHERE id = ?");
    // Executa o comando de exclusão passando o ID capturado para dentro do array de execução
    $del->execute([$id]);
    // Redireciona o navegador de volta para a listagem limpa, atualizando a página e removendo parâmetros da URL
    header("Location: ProdutoList.php");
    // Interrompe imediatamente o processamento do script atual
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gerenciar Produtos</h2>
    <a href="ProdutoForm.php" class="btn btn-primary fw-bold"><i class="fa-solid fa-plus me-1"></i> Novo Produto</a>
</div>

<form method="GET" class="mb-4">
    <div class="input-group w-50">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por peça ou cor..." value="<?php echo htmlspecialchars($busca); ?>">
        <button class="btn btn-outline-secondary" type="submit">Pesquisar</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th style="width: 100px;">Foto</th>
                    <th>Nome da Peça</th>
                    <th>Tamanho</th>
                    <th>Cor</th>
                    <th>Preço de Venda</th>
                    <th class="text-center" style="width: 150px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td>
                        <?php if (!empty($p['imagem']) && file_exists('../uploads/' . $p['imagem'])): ?>
                            <img src="../uploads/<?php echo $p['imagem']; ?>" alt="Foto do Produto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" class="border shadow-sm">
                        <?php else: ?>
                            <div class="bg-light text-muted d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px; border-radius: 8px;">
                                <i class="fa-solid fa-shirt opacity-40"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="fw-bold text-secondary"><?php echo htmlspecialchars($p['nome_peca']); ?></td>
                    <td><span class="badge bg-secondary px-2 py-1"><?php echo htmlspecialchars($p['tamanho']); ?></span></td>
                    <td><?php echo htmlspecialchars($p['cor_predominante']); ?></td>
                    <td class="text-success fw-bold">R$ <?php echo number_format($p['preco_venda'], 2, ',', '.'); ?></td>
                    <td class="text-center">
                        <a href="ProdutoForm.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning text-white"><i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="ProdutoList.php?deletar=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta peça?');"><i class="fa-solid fa-trash-can"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (count($produtos) === 0): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Nenhum produto em estoque.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
// Inclui o arquivo de rodapé padrão para fechar o layout HTML e renderizar scripts globais
include '../footer.php'; 
?>