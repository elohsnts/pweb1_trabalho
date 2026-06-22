Aqui está o código da listagem de vendas com as anotações antigas removidas e substituídas por comentários detalhados linha por linha e bloco por bloco, mantendo toda a estrutura original intacta:

```php
<?php
// Inclui o arquivo de classe do banco de dados para gerenciar o acesso e a persistência
include '../db.class.php';
// Inclui o componente visual do cabeçalho da página (HTML inicial, CSS, menu de navegação)
include '../header.php';

// Ativa e armazena a conexão com o banco de dados chamando o método estático conectar() da classe DB
$db = DB::conectar();
// Captura o termo digitado no campo de busca via parâmetro GET. Se estiver vazio, assume uma string vazia
$busca = $_GET['busca'] ?? '';

// Define a consulta base utilizando INNER JOIN para cruzar a tabela de vendas com a tabela de produtos
// Seleciona todas as colunas de vendas (v.*) e apenas a coluna 'nome_peca' da tabela produto (p)
$sql = "SELECT v.*, p.nome_peca FROM venda v 
        INNER JOIN produto p ON v.produto_id = p.id";

// Condicional para verificar se o usuário digitou algum termo de filtragem na busca
if ($busca) {
    // Adiciona as cláusulas WHERE relacionando as colunas das duas tabelas através de parâmetros nomeados (:busca)
    $sql .= " WHERE v.status_pedido LIKE :busca 
              OR v.forma_pagamento LIKE :busca 
              OR p.nome_peca LIKE :busca";
    // Prepara a consulta SQL de maneira segura contra vulnerabilidades de injeção de código
    $stmt = $db->prepare($sql);
    // Vincula o valor do filtro cercando a variável por caracteres curinga (%) para busca parcial
    $stmt->bindValue(':busca', "%$busca%");
} else {
    // Caso o campo de busca esteja em branco, prepara a consulta inicial completa sem restrições
    $stmt = $db->prepare($sql);
}
// Executa a query montada no banco de dados
$stmt->execute();
// Agrupa os registros encontrados e os converte em um array associativo dentro de $vendas
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Verifica se existe o parâmetro 'deletar' na URL da página enviado via método GET
if (isset($_GET['deletar'])) {
    // Captura o número identificador (ID) da venda que deve ser excluída
    $id = $_GET['deletar'];
    
    // Prepara de maneira protegida a exclusão do registro na tabela venda utilizando placeholder (?)
    $del = $db->prepare("DELETE FROM venda WHERE id = ?");
    // Executa a deleção passando a variável do ID mapeada dentro de um array de execução
    $del->execute([$id]);
    
    // Recarrega o navegador redirecionando o fluxo limpo para o arquivo de listagem de vendas
    header("Location: VendaList.php");
    // Interrompe na mesma hora o processamento e a leitura deste arquivo PHP pelo servidor
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gerenciar Vendas</h2>
    <a href="VendaForm.php" class="btn btn-primary">+ Nova Venda</a>
</div>

<form method="GET" class="mb-4">
    <div class="input-group w-50">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por produto, status ou pagamento..." value="<?php echo htmlspecialchars($busca); ?>">
        <button class="btn btn-outline-secondary" type="submit">Pesquisar</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Data da Compra</th>
                    <th>Produto Vendido</th> <th>Forma de Pagamento</th>
                    <th>Valor Total</th>
                    <th>Status do Pedido</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($vendas) > 0): ?>
                    <?php foreach ($vendas as $v): ?> 
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($v['data_compra'])); ?></td>
                        <td class="fw-semibold text-dark"><?php echo htmlspecialchars($v['nome_peca']); ?></td> <td><?php echo htmlspecialchars($v['forma_pagamento']); ?></td>
                        <td class="fw-bold text-success">R$ <?php echo number_format($v['valor_total'], 2, ',', '.'); ?></td>
                        <td>
                            <span class="badge <?php echo $v['status_pedido'] == 'Enviado' ? 'bg-success' : ($v['status_pedido'] == 'Aprovado' ? 'bg-primary' : 'bg-warning text-dark'); ?>">
                                <?php echo htmlspecialchars($v['status_pedido']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="VendaForm.php?id=<?php echo $v['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="VendaList.php?deletar=<?php echo $v['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?');">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Nenhum registro de venda encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
// Inclui os componentes estruturais do rodapé do sistema (HTML final e tags de fechamento)
include '../footer.php'; 
?>

```