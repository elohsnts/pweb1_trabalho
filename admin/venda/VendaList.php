<?php
include '../db.class.php';
include '../header.php';

// Inicializa a conexão estática com o banco de dados
$db = DB::conectar();

// Armazena o termo de filtragem vindo da URL via método GET (se vazio, inicia string vazia)
$busca = $_GET['busca'] ?? '';

// RELACIONAMENTO ENTRE TABELAS (INNER JOIN): 
// Une as informações da tabela 'venda' (apelidada de v) com a tabela 'produto' (apelidada de p).
// O critério de união é a correspondência entre a chave estrangeira (v.produto_id) e a chave primária (p.id).
// Isso permite que a consulta retorne campos como 'p.nome_peca' diretamente para a tabela.
$sql = "SELECT v.*, p.nome_peca FROM venda v 
        INNER JOIN produto p ON v.produto_id = p.id";

if ($busca) {
    // Caso haja busca, estende a query SQL usando parâmetros nomeados (:busca) para segurança.
    // O operador 'OR' permite estender o filtro tanto para as colunas da venda quanto do produto.
    $sql .= " WHERE v.status_pedido LIKE :busca 
              OR v.forma_pagamento LIKE :busca 
              OR p.nome_peca LIKE :busca";
    $stmt = $db->prepare($sql);
    
    // Vincula o parâmetro envolvendo o termo com os caracteres coringa (%) para busca parcial amigável
    $stmt->bindValue(':busca', "%$busca%");
} else {
    // Prepara a listagem geral sem restrições de filtro
    $stmt = $db->prepare($sql);
}
$stmt->execute();

// Coleta todos os registros combinados no formato de matriz associativa
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================================
// OPERAÇÃO DE EXCLUSÃO DE VENDA
// ==========================================
if (isset($_GET['deletar'])) {
    $id = $_GET['deletar'];
    
    // Remove o registro da venda utilizando o ID enviado por parâmetro posicional seguro (?)
    $del = $db->prepare("DELETE FROM venda WHERE id = ?");
    $del->execute([$id]);
    
    // Redireciona para o script sem parâmetros na barra de endereços para evitar repetição da exclusão
    header("Location: VendaList.php");
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
                    <th>Produto Vendido</th> 
                    <th>Forma de Pagamento</th>
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
                        
                        <td class="fw-semibold text-dark"><?php echo htmlspecialchars($v['nome_peca']); ?></td> 
                        <td><?php echo htmlspecialchars($v['forma_pagamento']); ?></td>
                        
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

<?php include '../footer.php'; ?>