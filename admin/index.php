<?php 
// Inclui a classe do banco e faz a conexão para buscar os produtos dinamicamente
include 'db.class.php';
include 'header.php'; 

$db = DB::conectar();
// Busca os últimos 6 produtos cadastrados para compor a vitrine
$stmt = $db->prepare("SELECT * FROM produto ORDER BY id DESC LIMIT 6");
$stmt->execute();
$produtos_vitrine = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* Moldura elegante para as fotos do estoque de roupas */
    .stock-preview-img {
        width: 100%;
        height: 280px;
        object-fit: cover;
        border-radius: 12px 12px 0 0;
        transition: transform 0.3s ease;
    }
    .card-vitrine:hover .stock-preview-img {
        transform: scale(1.02);
    }

    /* Rodapé fixado na parte inferior em azul claro com bolinhas */
    .custom-footer-elvi {
        background-color: #e6f0fa; 
        background-image: radial-gradient(#b3d1ff 2px, transparent 0);
        background-size: 20px 20px;
        color: #00274c;
        border-top: 1px solid #b3d1ff;
        padding: 1.5rem 0;
        margin-top: 5rem;
    }
    
    /* Layout do container padrão sem imagem */
    .placeholder-vitrine {
        width: 100%;
        height: 280px;
        background-color: #f8f9fa;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }
</style>

<div class="row align-items-center mb-4 pb-3 border-bottom">
    <div class="col-md-8 col-sm-12">
        <h2 class="fw-bold m-0" style="color: #00274c;">Painel Administrativo - Elvi</h2>
        <p class="text-muted m-0 small">Visão geral do gerenciamento da loja.</p>
    </div>
    <div class="col-md-4 col-sm-12 text-md-end text-start mt-3 mt-md-0">
        <a href="venda/VendaList.php" class="btn btn-dark px-4 py-2 fw-bold shadow-sm" style="background-color: #00274c; border: none;">
            <i class="fa-solid fa-cart-plus me-1"></i> Registrar Nova Venda
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-2">Estoque</h6>
                        <h3 class="mb-0 text-primary">Produtos</h3>
                    </div>
                    <i class="fa-solid fa-shirt fa-2x text-primary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0 border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-2">Parceiros</h6>
                        <h3 class="mb-0 text-warning">Fornecedores</h3>
                    </div>
                    <i class="fa-solid fa-truck fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0 border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-2">Faturamento</h6>
                        <h3 class="mb-0 text-success">Vendas</h3>
                    </div>
                    <i class="fa-solid fa-bag-shopping fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="p-5 bg-white shadow-sm rounded text-center">
            <h4 class="fw-bold mb-3" style="color: #00274c;">Bem-vindo(a) ao sistema da Loja Elvi!</h4>
            <p class="text-muted mb-5">Utilize o menu superior para navegar entre os módulos de cadastro e gerenciar suas vendas em tempo real.</p>
            
            <div class="row row-cols-1 row-cols-md-3 g-4 text-start">
                <?php if (count($produtos_vitrine) > 0): ?>
                    <?php foreach ($produtos_vitrine as $prod): ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden card-vitrine bg-light">
                                
                                <?php if (!empty($prod['imagem']) && file_exists('uploads/' . $prod['imagem'])): ?>
                                    <img src="uploads/<?php echo $prod['imagem']; ?>" alt="<?php echo htmlspecialchars($prod['nome_peca']); ?>" class="stock-preview-img">
                                <?php else: ?>
                                    <div class="placeholder-vitrine border-bottom">
                                        <div class="text-center">
                                            <i class="fa-solid fa-shirt fa-3x opacity-25 mb-2"></i>
                                            <p class="mb-0 small text-muted">Sem foto cadastrada</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body p-3 bg-white">
                                    <span class="badge bg-secondary float-end"><?php echo htmlspecialchars($prod['tamanho']); ?></span>
                                    <h5 class="card-title fw-bold text-truncate mb-1" style="color: #00274c; max-width: 80%;">
                                        <?php echo htmlspecialchars($prod['nome_peca']); ?>
                                    </h5>
                                    <p class="text-muted small mb-2"><i class="fa-solid fa-palette me-1"></i> Cor: <?php echo htmlspecialchars($prod['cor_predominante']); ?></p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="text-success fw-bold fs-5">R$ <?php echo number_format($prod['preco_venda'], 2, ',', '.'); ?></span>
                                        <a href="produto/ProdutoForm.php?id=<?php echo $prod['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-eye"></i> Detalhes
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 w-100 d-flex flex-column align-items-center justify-content-center py-5">
                        <div class="text-center">
                            <i class="fa-solid fa-boxes-stacked fa-3x mb-3 opacity-25 text-muted d-block mx-auto"></i>
                            <p class="mb-0 text-muted fw-semibold">Nenhum produto cadastrado no momento para exibir na vitrine.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</div>

<footer class="custom-footer-elvi text-center mt-5">
    <div class="container">
        <p class="m-0 small fw-bold">© 2026 Loja Elvi - Painel Administrativo Interno.</p>
    </div>
</footer>

<?php include 'footer.php'; ?>