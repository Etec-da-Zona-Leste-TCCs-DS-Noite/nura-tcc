<?php
session_start();
require_once __DIR__ . '/../Models/PerfilClinico.php';

$nomeCliente = $_SESSION['cliente_nome'] ?? null;
$cliente_id = $_SESSION['cliente_id'] ?? null;

// Lógica inicial do contador
$qtdCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        $qtdCarrinho += $item['qtd'];
    }
}

// === BUSCA PERFIL CLÍNICO PARA ALERTAS NOS DESTAQUES ===
// Se não logado ou sem perfil preenchido, retorna vazio normalmente e página flui.
$alergiasCliente = [];
$restricaoCliente = '';
if ($cliente_id) {
    $perfilDb = PerfilClinico::buscarPorClienteId($cliente_id);
    if ($perfilDb) {
        $alergiasCliente = $perfilDb['alergias'] ?? [];
        $restricaoCliente = $perfilDb['restricao'] ?? '';
    }
}

require_once __DIR__ . '/../Controller/ProdutoController.php';
$produtoController = new ProdutoController();
// Buscamos 12 produtos para preencher bem a tela inicial
$produtosDestaque = array_slice($produtoController->listarTodos(), 0, 12);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura - Alimentação Saudável</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body class="page-home">

    <header>
        <div class="container header-inner">
            <a href="index.php" class="logo" aria-label="Nura — Início">
                <img class="logo-img" src="../assets/img/NURA_logo.png" alt="">
            </a>

            <nav class="nav-links" aria-label="Principal">
                <a href="index.php" class="nav-link--current">Início</a>
                <a href="produtos.php">Produtos</a>
                <a href="<?php echo $nomeCliente ? 'perfil.php' : 'cadastro.php'; ?>">Minha Conta</a>
            </nav>

            <form class="header-search" action="produtos.php" method="GET">
                <div class="search-input-wrapper">
                    <i class="ph ph-magnifying-glass search-icon" aria-hidden="true"></i>
                    <input type="text" name="busca" placeholder="Buscar pratos..." aria-label="Buscar" required>
                </div>
            </form>

            <div class="header-actions">
                <a href="<?php echo $nomeCliente ? 'perfil.php' : 'cadastro.php'; ?>" class="btn btn-ghost"
                    aria-label="Conta">
                    <?php if ($nomeCliente): ?>
                        <span class="header-user-label">Olá, <?php echo htmlspecialchars(explode(' ', trim($nomeCliente))[0]); ?></span>
                    <?php endif; ?>
                    <i class="ph ph-user header-icon" aria-hidden="true"></i>
                </a>

                <a href="carrinho.php" class="btn btn-ghost header-cart" aria-label="Carrinho">
                    <i class="ph ph-shopping-cart header-icon" aria-hidden="true"></i>
                    <?php if ($qtdCarrinho > 0): ?>
                        <span class="cart-badge"><?php echo $qtdCarrinho; ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <button type="button" class="mobile-menu-btn btn btn-ghost" aria-label="Abrir menu" aria-expanded="false">
                <i class="ph ph-list header-icon" aria-hidden="true"></i>
            </button>
        </div>
    </header>

    <main class="container" style="padding-top: 4rem; padding-bottom: 4rem;">
        <div style="max-width: 800px; margin: 0 auto; line-height: 1.8; color: var(--foreground);">
            <header style="text-align: center; margin-bottom: 4rem;">
                <h1 style="font-family: var(--font-heading); font-size: 3rem; font-weight: 900; color: var(--primary-deep); margin-bottom: 1rem;">Alimentação Saudável</h1>
                <p style="font-size: 1.25rem; color: var(--muted);">Entenda o que torna nossas refeições essenciais para uma vida plena.</p>
            </header>

            <section style="margin-bottom: 3rem;">
                <h2 style="font-family: var(--font-heading); font-size: 2rem; color: var(--primary); margin-bottom: 1rem;">O que é comer bem?</h2>
                <p>Uma alimentação saudável vai muito além de contar calorias. Trata-se de fornecer ao seu corpo os nutrientes, vitaminas e minerais que ele precisa para funcionar em sua melhor forma. É buscar o equilíbrio entre o que é saboroso, nutritivo e que traga energia de qualidade para o seu dia a dia.</p>
            </section>

            <section style="margin-bottom: 3rem;">
                <h2 style="font-family: var(--font-heading); font-size: 1.75rem; color: var(--foreground); margin-bottom: 1rem;">Os Benefícios</h2>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 1rem; padding-left: 1.5rem; position: relative;">
                        <i class="ph-bold ph-lightning" style="position: absolute; left: 0; top: 5px; color: var(--primary);"></i>
                        <strong>Mais energia e disposição:</strong> Carboidratos complexos e nutrientes frescos garantem que seu corpo tenha combustível duradouro, sem picos de glicemia.
                    </li>
                    <li style="margin-bottom: 1rem; padding-left: 1.5rem; position: relative;">
                        <i class="ph-bold ph-shield-check" style="position: absolute; left: 0; top: 5px; color: var(--primary);"></i>
                        <strong>Imunidade fortalecida:</strong> Um prato colorido é sinônimo de uma rica variedade de vitaminas e antioxidantes que ajudam o corpo a se proteger.
                    </li>
                    <li style="margin-bottom: 1rem; padding-left: 1.5rem; position: relative;">
                        <i class="ph-bold ph-brain" style="position: absolute; left: 0; top: 5px; color: var(--primary);"></i>
                        <strong>Foco e clareza mental:</strong> Seu cérebro precisa de nutrientes de qualidade para manter a concentração, a memória e reduzir o estresse diário.
                    </li>
                </ul>
            </section>

            <div style="background: hsla(150, 40%, 95%, 1); padding: 2.5rem; border-radius: 1rem; border-left: 4px solid var(--primary); margin-bottom: 3rem;">
                <h3 style="font-family: var(--font-heading); font-size: 1.5rem; color: var(--primary-deep); margin-bottom: 1rem;">Nossa Abordagem na Nura</h3>
                <p style="margin-bottom: 0; color: var(--foreground);">Todos os pratos da Nura são pensados para aliar a nutrição que você precisa com o sabor que você merece. Utilizamos ingredientes selecionados, sem conservantes ou aditivos químicos pesados, garantindo comida de verdade e balanceada em todas as opções do nosso cardápio.</p>
            </div>

            <div style="text-align: center; margin-top: 4rem;">
                <a href="produtos.php" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;">Monte Sua Refeição Saudável</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-grid">

            <div class="footer-brand">
                <a href="index.php" class="logo" aria-label="Nura — Início">
                    <img class="logo-img-footer" src="../assets/img/NURA_logo.png" alt="">
                </a>
                <p>Alimentação saudável feita com ingredientes naturais e muito amor.</p>
            </div>

            <div class="footer-nav-col">
                <h4>Explorar</h4>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="produtos.php">Cardápio</a></li>
                    <li><a href="produtos.php?categoria=promocoes">Promoções</a></li>
                    <li><a href="perfil.php">Minha Conta</a></li>
                    <li><a href="carrinho.php">Meu Carrinho</a></li>
                </ul>
            </div>

            <div class="footer-about-col">
                <h4>Sobre Nós</h4>
                <ul>
                    <li><a href="sobre.php">Sobre a Nura</a></li>
                    <li><a href="alimentacao.php">Alimentação Saudável</a></li>
                    <li><a href="sustentabilidade.php">Sustentabilidade</a></li>
                    <li><a href="trabalhe-conosco.php">Trabalhe Conosco</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4>Contato</h4>
                <ul class="footer-contact-list">
                    <li>
                        <span class="fc-icon" aria-hidden="true">📍</span>
                        <span>São Paulo — SP</span>
                    </li>
                    <li>
                        <span class="fc-icon" aria-hidden="true">📞</span>
                        <span>(11) 98765-4321</span>
                    </li>
                    <li>
                        <span class="fc-icon" aria-hidden="true">✉</span>
                        <a href="mailto:contato@nura.com.br">contato@nura.com.br</a>
                    </li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom">
            <div class="footer-trust">
                <!-- Formas de Pagamento -->
                <div class="footer-payments">
                    <div class="footer-payments-icons">
                        <!-- Visa -->
                        <span class="payment-badge" title="Visa" aria-label="Visa">
                            <svg viewBox="0 0 48 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="payment-svg">
                                <text x="0" y="13" font-family="Arial" font-weight="900" font-size="14" fill="#1A1F71">VISA</text>
                            </svg>
                        </span>
                        <!-- Mastercard -->
                        <span class="payment-badge" title="Mastercard" aria-label="Mastercard">
                            <svg viewBox="0 0 38 24" xmlns="http://www.w3.org/2000/svg" class="payment-svg">
                                <circle cx="14" cy="12" r="10" fill="#EB001B"/>
                                <circle cx="24" cy="12" r="10" fill="#F79E1B"/>
                                <path d="M19 4.8a10 10 0 0 1 0 14.4A10 10 0 0 1 19 4.8z" fill="#FF5F00"/>
                            </svg>
                        </span>
                        <!-- Elo -->
                        <span class="payment-badge" title="Elo" aria-label="Elo">
                            <svg viewBox="0 0 42 18" xmlns="http://www.w3.org/2000/svg" class="payment-svg">
                                <rect x="0" y="0" width="42" height="18" rx="3" fill="#fff" opacity="0"/>
                                <text x="1" y="14" font-family="Arial" font-weight="900" font-size="14" fill="#FFB800">elo</text>
                            </svg>
                        </span>
                        <!-- Amex -->
                        <span class="payment-badge payment-badge--amex" title="American Express" aria-label="American Express">
                            <svg viewBox="0 0 50 18" xmlns="http://www.w3.org/2000/svg" class="payment-svg">
                                <text x="0" y="14" font-family="Arial" font-weight="900" font-size="11" fill="#2E77BC">AMEX</text>
                            </svg>
                        </span>
                        <!-- PIX -->
                        <span class="payment-badge payment-badge--pix" title="Pix" aria-label="Pix">
                            <svg viewBox="0 0 36 24" xmlns="http://www.w3.org/2000/svg" class="payment-svg">
                                <path d="M17.4 5.6l3 3a1 1 0 0 0 1.4 0l3-3a2.5 2.5 0 0 1 3.5 0l.1.1-4.8 4.8a2 2 0 0 1-2.8 0l-4.8-4.8.1-.1a2.5 2.5 0 0 1 3.3 0z" fill="#32BCAD"/>
                                <path d="M28.4 8.2l.1.1a2.5 2.5 0 0 1 0 3.5l-3 3a1 1 0 0 0 0 1.4l3 3a2.5 2.5 0 0 1 0 3.5l-.1.1-4.8-4.8a2 2 0 0 1 0-2.8l4.8-4.8z" fill="#32BCAD"/>
                                <path d="M18.6 18.4l-3-3a1 1 0 0 0-1.4 0l-3 3a2.5 2.5 0 0 1-3.5 0l-.1-.1 4.8-4.8a2 2 0 0 1 2.8 0l4.8 4.8-.1.1a2.5 2.5 0 0 1-3.3 0z" fill="#32BCAD"/>
                                <path d="M7.6 15.8l-.1-.1a2.5 2.5 0 0 1 0-3.5l3-3a1 1 0 0 0 0-1.4l-3-3a2.5 2.5 0 0 1 0-3.5l.1-.1 4.8 4.8a2 2 0 0 1 0 2.8L7.6 15.8z" fill="#32BCAD"/>
                            </svg>
                        </span>
                        <!-- Débito genérico -->
                        <span class="payment-badge payment-badge--debit" title="Débito" aria-label="Débito">
                            <svg viewBox="0 0 38 16" xmlns="http://www.w3.org/2000/svg" class="payment-svg">
                                <text x="0" y="13" font-family="Arial" font-weight="700" font-size="11" fill="#5A6B7B">Débito</text>
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- Selo Site Seguro -->
                <div class="footer-safe-badge">
                    <div class="safe-badge-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="safe-badge-icon" aria-hidden="true">
                            <path d="M12 2L3 5.5v6c0 5.25 3.75 10.15 9 11.35C17.25 21.65 21 16.75 21 11.5v-6L12 2z" fill="#34A853"/>
                            <path d="M9 12l2 2 4-4" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="safe-badge-text">
                            <span class="safe-badge-title">Site Seguro</span>
                            <span class="safe-badge-sub">Google Safe Browsing</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <p>© 2026 Nura. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="../script.js"></script>
</body>

</html>