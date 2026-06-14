<?php
session_start();
require 'db.class.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    try {
        $db = DB::conectar();
        $sql = "SELECT * FROM usuario WHERE login = ? AND senha = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$login, $senha]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $_SESSION['usuario_logado'] = true;
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header("Location: index.php");
            exit;
        } else {
            $erro = "Login ou senha incorretos!";
        }
    } catch (Exception $e) {
        $erro = "Erro de conexão: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Loja Elvi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 10px; }
    </style>
</head>
<body class="d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fa-solid fa-shirt text-info fa-3x mb-2"></i>
                            <h4 class="fw-bold text-dark">Loja Elvi</h4>
                            <p class="text-muted small">Painel de Administração</p>
                        </div>
                        
                        <?php if($erro): ?>
                            <div class="alert alert-danger py-2 small"><?php echo $erro; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Usuário</label>
                                <input type="text" name="login" class="form-control" placeholder="Ex: admin" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Senha</label>
                                <input type="password" name="senha" class="form-control" placeholder="Ex: 123" required>
                            </div>
                            <button type="submit" class="btn btn-dark w-100 shadow-sm">Entrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>