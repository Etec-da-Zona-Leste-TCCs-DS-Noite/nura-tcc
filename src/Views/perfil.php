<?php
session_start();
require_once __DIR__ . '/../Models/Usuario.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: cadastro.php");
    exit;
}

$dadosUsuario = Usuario::buscarPorId($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura - Meu Perfil</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>

    <header>
        <div class="container header-inner">
            <a href="index.php" class="logo">Nura<span>.</span></a>
            <div class="nav-links">
                <a href="index.php">Início</a>
                <a href="produtos.php">Produtos</a>
                <a href="carrinho.php">Carrinho</a>
            </div>

            <div class="header-actions">
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 500;">
                    <span id="header-user-name">Olá, <?php echo htmlspecialchars($dadosUsuario['nome']); ?></span>
                    <div
                        style="width: 35px; height: 35px; background: var(--secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary); font-weight: bold;">
                        <?php echo strtoupper(substr($dadosUsuario['nome'], 0, 1)); ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container" style="padding: 3rem 1.5rem;">
        <h1 style="font-size: 2rem; margin-bottom: 2rem;">Minha Conta</h1>

        <div class="profile-grid">
            <aside class="profile-sidebar">
                <nav class="sidebar-menu">
                    <button class="sidebar-link tab-btn active" data-target="personal-data"><i class="ph ph-user"></i>
                        Dados Pessoais</button>
                    <a href="#"
                        onclick="if(confirm('Tem certeza?')) window.location.href='../Controller/UsuarioController.php?acao=deletar';"
                        class="sidebar-link" style="color: #ef4444;">
                        <i class="ph ph-trash"></i> Excluir Conta
                    </a>
                    <a href="../Controller/UsuarioController.php?acao=sair" class="sidebar-link"
                        style="color: var(--muted);"><i class="ph ph-sign-out"></i> Sair</a>
                </nav>
            </aside>

            <section class="profile-content">
                <div id="personal-data" class="form-content active">
                    <div class="card" style="box-shadow: none; padding: 0; border: none;">
                        <div class="card-content" style="padding: 0;">
                            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Seus Dados</h2>

                            <form action="../Controller/UsuarioController.php?acao=atualizar" method="POST">
                                <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                                    <div class="form-group">
                                        <label for="input-nome">Nome Completo</label>
                                        <input type="text" name="nome" class="input"
                                            value="<?php echo htmlspecialchars($dadosUsuario['nome']); ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="input-email">Email</label>
                                    <input type="email" name="email" class="input"
                                        value="<?php echo htmlspecialchars($dadosUsuario['email']); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="input-senha">Nova Senha</label>
                                    <input type="password" name="senha" class="input"
                                        placeholder="Deixe em branco para manter a atual">
                                </div>

                                <div style="margin-top: 1rem;">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script src="../script.js"></script>
</body>

</html>