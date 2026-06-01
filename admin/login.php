<?php
session_start();

// Se já estiver logado, redireciona para o index
if (isset($_SESSION['usuario_logado'])) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db.class.php';

    $login = trim($_POST['login']);
    $senha = trim($_POST['senha']);

    if (empty($login) || empty($senha)) {
        $erro = 'Preencha todos os campos.';
    } else {
        try {
            $db = new DB();
            $pdo = $db->getConexao();

            $stmt = $pdo->prepare("SELECT * FROM usuario WHERE login = ? AND senha = ?");
            $stmt->execute([$login, $senha]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                $_SESSION['usuario_logado'] = $usuario['id'];
                $_SESSION['usuario_nome']   = $usuario['nome'];
                header('Location: index.php');
                exit;
            } else {
                $erro = 'Login ou senha inválidos.';
            }
        } catch (Exception $e) {
            $erro = 'Erro ao conectar ao banco de dados.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Elvi Loja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f0f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-login {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            max-width: 400px;
            width: 100%;
        }
        .card-login .card-header {
            background-color: #c0397a;
            border-radius: 16px 16px 0 0;
            text-align: center;
            padding: 28px;
        }
        .card-login .card-header h3 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            letter-spacing: 1px;
        }
        .card-login .card-header p {
            color: #f7d6ea;
            margin: 4px 0 0;
            font-size: 0.9rem;
        }
        .btn-entrar {
            background-color: #c0397a;
            border: none;
            color: #fff;
            font-weight: 600;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .btn-entrar:hover {
            background-color: #a02f66;
            color: #fff;
        }
        .form-control:focus {
            border-color: #c0397a;
            box-shadow: 0 0 0 0.2rem rgba(192,57,122,0.15);
        }
    </style>
</head>
<body>
    <div class="card card-login p-0">
        <div class="card-header">
            <h3><i class="fa-solid fa-shirt me-2"></i>Elvi Loja</h3>
            <p>Sistema de Gerenciamento</p>
        </div>
        <div class="card-body p-4">
            <?php if ($erro): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label for="login" class="form-label fw-semibold">Usuário</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                        <input type="text" class="form-control" id="login" name="login"
                               placeholder="Digite seu usuário" required
                               value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="senha" class="form-label fw-semibold">Senha</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" id="senha" name="senha"
                               placeholder="Digite sua senha" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-entrar">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Entrar
                </button>
            </form>
        </div>
    </div>
</body>
</html>