<?php 
include 'header.php'; 
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="fw-bold">Painel Administrativo - Elvi</h2>
        <p class="text-muted">Visão geral do gerenciamento da loja.</p>
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

<div class="row mt-4">
    <div class="col-md-12 text-center">
        <div class="p-5 bg-white shadow-sm rounded">
            <h4>Bem-vindo(a) ao sistema da Loja Elvi!</h4>
            <p class="text-muted">Utilize o menu superior para navegar entre os módulos de cadastro e registrar suas vendas.</p>
            <a href="venda/VendaList.php" class="btn btn-primary mt-2">
                <i class="fa-solid fa-cart-plus me-1"></i> Registrar Nova Venda
            </a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>