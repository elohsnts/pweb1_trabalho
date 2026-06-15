<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// URL Base dinâmica para evitar quebras de layout e links nas subpastas
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/PWEB1_TRABALHO/admin/";

// Bloqueia o acesso direto caso o usuário tente pular a tela de login
if (!isset($_SESSION['usuario_logado'])) {
    header("Location: " . $base_url . "login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elvi - Sistema de Gestão de Moda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        
        /* Estilização customizada para a miniatura da logo no menu */
        .navbar-logo-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #ffffff; /* Destaca a logo contra o fundo escuro do menu */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo $base_url; ?>index.php">
            <img src="<?php echo $base_url; ?>logo-elvi.jpg" alt="Logo Elvi" class="navbar-logo-circle">
            <span>Elvi</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url; ?>index.php"><i class="fa-solid fa-house me-1"></i> Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url; ?>produto/ProdutoList.php"><i class="fa-solid fa-tags me-1"></i> Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url; ?>fornecedor/FornecedorList.php"><i class="fa-solid fa-truck me-1"></i> Fornecedores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url; ?>venda/VendaList.php"><i class="fa-solid fa-bag-shopping me-1"></i> Vendas</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-circle-user me-1"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?php echo $base_url; ?>usuario/UsuarioList.php">Gerenciar Usuários</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?php echo $base_url; ?>logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i> Sair</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">