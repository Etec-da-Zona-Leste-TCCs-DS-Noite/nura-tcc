<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../Controller/AdminController.php';
    $controller = new AdminController();
    $admin = $controller->login($_POST['email'] ?? '', $_POST['senha'] ?? '');

    if ($admin) {
        $_SESSION['admin_id'] = $admin->getId();
        $_SESSION['admin_nome'] = $admin->getNome();
        header("Location: dashboard.php");
        exit;
    } else {
        $erro = 'Credenciais inválidas.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura Admin - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .error-msg {
            color: var(--danger);
            background: var(--danger-soft);
            padding: 0.8rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            border: 1px solid var(--danger);
            text-align: center;
        }
    </style>
</head>

<body class="page-auth">

    <header>
        <div class="container header-inner">
            <a href="../index.php" class="logo" aria-label="Nura — Início">
                <img class="logo-img" src="../../assets/img/NURA_logo.png" alt="">
            </a>
            <a href="../index.php" class="back-link">
                <i class="ph-bold ph-arrow-left" aria-hidden="true"></i> Voltar ao Site
            </a>
        </div>
    </header>

    <div class="auth-split">
        <div class="auth-split-image" style="background-image: url('https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=2070&auto=format&fit=crop');">
            <div class="auth-image-content">
                <h2>Gestão inteligente e dados poderosos.</h2>
                <p>Controle os pedidos, analise vendas e gerencie o cardápio da Nura com eficiência.</p>
            </div>
        </div>

        <div class="auth-split-form">
            <div class="auth-card">

                <div class="auth-header">
                    <div class="auth-logo-wrap">
                        <h1 class="auth-logo-text">Nura.</h1>
                    </div>
                    <p class="auth-tagline">Bem-vindo ao Painel Administrativo. Acesso restrito.</p>
                </div>

                <?php if ($erro): ?>
                    <div class="error-msg"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>

                <div id="login-form" class="form-content active" role="tabpanel">
                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="login-email">Email Corporativo</label>
                            <div class="input-wrapper">
                                <i class="ph ph-envelope input-icon" aria-hidden="true"></i>
                                <input id="login-email" type="email" name="email" class="input input-with-icon" placeholder="admin@nura.com" required autocomplete="email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="login-senha">Senha de Acesso</label>
                            <div class="input-wrapper">
                                <i class="ph ph-lock input-icon" aria-hidden="true"></i>
                                <input id="login-senha" type="password" name="senha" class="input input-with-icon" placeholder="••••••••" required autocomplete="current-password">
                                <button type="button" class="toggle-password" aria-label="Mostrar ou ocultar senha">
                                    <i class="ph ph-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="auth-options">
                            <label class="remember-me">
                                <input type="checkbox" name="remember" id="remember-me">
                                <span class="checkbox-custom"></span>
                                Lembrar meu acesso
                            </label>
                            <a href="#" class="forgot-password">Esqueceu a senha?</a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full">Entrar no Painel</button>
                    </form>
                </div>

                <p class="auth-trust" style="margin-top: 1.5rem; text-align: center;"><i class="ph-fill ph-shield-check" aria-hidden="true"></i> Acesso Seguro e Monitorado</p>

            </div>
        </div>
    </div>

    <footer class="auth-footer">
        <div class="container">
            <p>&copy; 2026 Nura Admin. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="../../script.js"></script>
</body>

</html>
