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
    <style>
        body { 
            /* ALTERADO: Fundo azul claro com bolinhas */
            background-color: #e6f0fa; 
            background-image: radial-gradient(#b3d1ff 3px, transparent 0);
            background-size: 30px 30px;
        }
        .card { 
            border: none; 
            border-radius: 16px; 
            background-color: #ffffff;
        }
        .logo-img {
            width: 150px;         
            height: 150px;         
            border-radius: 50%;    
            object-fit: cover;     
            border: 2px solid #00274c;
        }
        
        .btn-elvi {
            background-color: #00274c;
            color: #ffffff;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-elvi:hover {
            background-color: #001b33;
            color: #ffffff;
            transform: translateY(-1px);
        }
        .form-control:focus {
            border-color: #00274c;
            box-shadow: 0 0 0 0.25rem rgba(0, 39, 76, 0.15);
        }
    </style>
</head>
<body class="d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 col-sm-8">
                <div class="card shadow-lg">
                    <div class="card-body p-4 text-center">
                        
                        <div class="mb-4">
                            <img src="logo-elvi.jpg" alt="Logo ELVI" class="logo-img mb-2">
                            <p class="text-muted small uppercase tracking-wider">Painel de Administração</p>
                        </div>
                        
                        <?php if($erro): ?>
                            <div class="alert alert-danger py-2 small text-start"><?php echo $erro; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" class="text-start">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Usuário</label>
                                <input type="text" name="login" class="form-control" placeholder="Ex: admin" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Senha</label>
                                <input type="password" name="senha" class="form-control" placeholder="Ex: 123" required>
                            </div>
                            <button type="submit" class="btn btn-elvi w-100 py-2 shadow-sm fw-bold">Entrar no Painel</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>