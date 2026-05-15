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

    <main>
        <section class="hero-carousel" aria-roledescription="carousel" aria-label="Destaques">
            <div class="hero-track">

                <div class="hero-slide">
                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c" alt="Bowl saudável com vegetais frescos">
                    <div class="hero-content">
                        <span class="hero-badge">Sabor e Saúde</span>
                        <h1>Energia natural para o seu dia</h1>
                        <p>Pratos balanceados com ingredientes frescos e naturais.</p>
                        <a href="produtos.php" class="btn btn-primary">Peça agora</a>
                    </div>
                </div>

                <div class="hero-slide">
                    <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd" alt="Salada colorida em bowl">
                    <div class="hero-content">
                        <span class="hero-badge">Novo menu</span>
                        <h1>Saladas que são um banquete</h1>
                        <p>Combinações únicas de sabores e nutrientes.</p>
                        <a href="produtos.php" class="btn btn-primary">Ver cardápio</a>
                    </div>
                </div>

                <div class="hero-slide">
                    <img src="https://images.unsplash.com/photo-1540420773420-3366772f4999" alt="Smoothie e ingredientes naturais">
                    <div class="hero-content">
                        <span class="hero-badge">Detox</span>
                        <h1>Smoothies que transformam</h1>
                        <p>Refresque-se com frutas naturais e superalimentos.</p>
                        <a href="produtos.php" class="btn btn-primary">Comprar</a>
                    </div>
                </div>

            </div>

            <button type="button" class="hero-btn hero-prev" aria-label="Slide anterior">
                <i class="ph-bold ph-caret-left" aria-hidden="true"></i>
            </button>
            <button type="button" class="hero-btn hero-next" aria-label="Próximo slide">
                <i class="ph-bold ph-caret-right" aria-hidden="true"></i>
            </button>
        </section>

        <section class="intro-band" aria-labelledby="intro-heading">
            <div class="container">
                <p class="pill-badge"><i class="ph-fill ph-leaf" aria-hidden="true"></i> 100% natural e saudável</p>
                <h1 id="intro-heading" class="intro-title">
                    Alimentação saudável <br>
                    <span class="text-gradient">feita com amor</span>
                </h1>
                <p class="intro-lead">
                    Descubra refeições deliciosas, nutritivas e preparadas com ingredientes frescos e naturais.
                </p>
                <div class="intro-actions">
                    <a href="produtos.php" class="btn btn-primary">
                        Ver cardápio <i class="ph-bold ph-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </section>

        <section class="container featured-section" aria-labelledby="featured-heading">
            <header class="section-head">
                <h2 id="featured-heading">Produtos em destaque</h2>
                <p>Confira alguns dos nossos pratos mais populares.</p>
            </header>

            <div class="carousel-container">
                <button type="button" class="carousel-btn prev-btn" aria-label="Anterior"><i class="ph-bold ph-caret-left" aria-hidden="true"></i></button>

                <div class="carousel-track">
                    <?php foreach ($produtosDestaque as $p): ?>
                        <?php
                        $alergiasDesteProduto = $p['alergias'] ?? [];
                        $incompativeisDesteProduto = $p['restricoes'] ?? [];

                        $conflitoAlergias = array_intersect($alergiasCliente, $alergiasDesteProduto);
                        $conflitoRestricao = ($restricaoCliente && in_array($restricaoCliente, $incompativeisDesteProduto));

                        $naoRecomendado = !empty($conflitoAlergias) || $conflitoRestricao;

                        // Se tiver restrição, ignoramos totalmente o produto da Home
                        if ($naoRecomendado) {
                            continue;
                        }
                        ?>
                        <div class="carousel-item">
                            <article class="card">
                                <div class="card-img-wrapper">
                                    <img src="<?php echo $p['img']; ?>" alt="<?php echo htmlspecialchars($p['nome']); ?>" class="card-img">
                                    <span class="card-badge"><?php echo $p['tag']; ?></span>
                                </div>
                                <div class="card-content">
                                    <h3 class="card-title"><?php echo $p['nome']; ?></h3>
                                    <p class="card-desc"><?php echo $p['desc']; ?></p>
                                    <div class="card-price" aria-label="Preço">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></div>
                                </div>
                                <div class="card-footer">
                                    <form action="carrinho_acoes.php?acao=adicionar" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="nome" value="<?php echo $p['nome']; ?>">
                                        <input type="hidden" name="preco" value="<?php echo $p['preco']; ?>">
                                        <input type="hidden" name="img" value="<?php echo $p['img']; ?>">
                                        <button type="submit" class="btn btn-primary btn-full">
                                            <i class="ph-bold ph-shopping-cart" aria-hidden="true"></i> Adicionar
                                        </button>
                                    </form>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="carousel-btn next-btn" aria-label="Próximo"><i class="ph-bold ph-caret-right" aria-hidden="true"></i></button>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container footer-grid">

            <div class="footer-brand">
                <a href="index.php" class="logo" aria-label="Nura — Início">
                    <img class="logo-img-footer" src="../assets/img/NURA_logo.png" alt="">
                </a>
                <p>Alimentação saudável feita com ingredientes naturais e muito amor.</p>
            </div>

            <div>
                <h4>Explorar</h4>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="produtos.php">Cardápio</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4>Contato</h4>
                <p><span aria-hidden="true">📍</span> São Paulo — SP</p>
                <p><span aria-hidden="true">📞</span> (11) 98765-4321</p>
                <p><span aria-hidden="true">✉</span> <a href="mailto:contato@nura.com.br">contato@nura.com.br</a></p>
            </div>

        </div>

        <div class="footer-bottom">
            <p>© 2026 Nura. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="../script.js"></script>
</body>

</html>