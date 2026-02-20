<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura - Acessar Conta</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>

    <header>
        <div class="container header-inner">
            <a href="index.php" class="logo">Nura<span>.</span></a>
            <a href="index.php" style="font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ph-bold ph-arrow-left"></i> Voltar
            </a>
        </div>
    </header>

    <div class="auth-wrapper">
        <div class="auth-card">

            <div class="auth-header">
                <h1 class="logo" style="font-size: 2rem; margin-bottom: 0.5rem;">Nura<span>.</span></h1>
                <p style="color: var(--muted); font-size: 0.9rem;">Acesse sua conta ou crie uma nova</p>
            </div>

            <div class="tabs">
                <button class="tab-btn active" data-target="login-form">Login</button>
                <button class="tab-btn" data-target="signup-form">Cadastro</button>
            </div>

            <div id="login-form" class="form-content active">
                <form action="../Controller/UsuarioController.php?acao=login" method="POST">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="input" placeholder="seu@email.com" required>
                    </div>
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" name="senha" class="input" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Entrar</button>
                </form>
            </div>

            <div id="signup-form" class="form-content">
                <form action="../Controller/UsuarioController.php?acao=cadastrar" method="POST">
                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" name="nome" class="input" placeholder="Seu nome" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="input" placeholder="seu@email.com" required>
                    </div>
                    <div class="form-group">
                        <label>Senha</label>
                        <input type="password" name="senha" class="input" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Criar Conta</button>
                </form>
            </div>

        </div>
    </div>

    <footer>
        <div class="container" style="text-align: center; padding: 2rem 0;">
            <p style="color: var(--muted); font-size: 0.85rem;">&copy; 2025 Nura. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="../script.js"></script>
</body>

</html>