<?php
session_start();
$nomeUsuario = $_SESSION['usuario_nome'] ?? null;

// Lógica inicial do contador (para quando carrega a página)
$qtdCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        $qtdCarrinho += $item['qtd'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura - Alimentação Saudável</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>
    <header>
        <div class="container header-inner">
            <a href="index.php" class="logo">Nura<span>.</span></a>

            <nav class="nav-links">
                <a href="index.php" style="color: var(--primary); font-weight: bold;">Início</a>
                <a href="produtos.php">Produtos</a>
                <?php if ($nomeUsuario): ?>
                    <a href="perfil.php" style="display: flex; align-items: center; gap: 0.5rem; color: var(--foreground);">
                        <i class="ph-fill ph-user-circle" style="font-size: 1.2rem; color: var(--primary);"></i>
                        Olá,
                            <?php echo htmlspecialchars($nomeUsuario); ?>
                    </a>
                <?php else: ?>
                    <a href="cadastro.php">Minha Conta</a>
                <?php endif; ?>
            </nav>

            <div class="header-actions">
                <a href="<?php echo $nomeUsuario ? 'perfil.php' : 'cadastro.php'; ?>" class="btn btn-ghost"
                    aria-label="Conta">
                    <i class="ph ph-user" style="font-size: 1.2rem;"></i>
                </a>

                <a href="carrinho.php" class="btn btn-ghost" style="position: relative;" aria-label="Carrinho">
                    <i class="ph ph-shopping-cart" style="font-size: 1.2rem;"></i>

                    <?php if ($qtdCarrinho > 0): ?>
                        <span class="cart-badge" style="
                            position: absolute;
                            top: -5px;
                            right: -5px;
                            background: var(--primary);
                            color: white;
                            font-size: 0.7rem;
                            font-weight: bold;
                            min-width: 18px;
                            height: 18px;
                            border-radius: 99px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 0 4px;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        ">
                                <?php echo $qtdCarrinho; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero-carousel">
            <div class="hero-track">

                <div class="hero-slide">
                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c" alt="Bowl saudável">
                    <div class="hero-content">
                        <span class="hero-badge">Sabor e Saúde</span>
                        <h1>Energia Natural para o seu Dia</h1>
                        <p>Pratos balanceados com ingredientes frescos e naturais.</p>
                        <a href="produtos.php" class="btn btn-primary">Peça Agora</a>
                    </div>
                </div>

                <div class="hero-slide">
                    <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd" alt="Salada">
                    <div class="hero-content">
                        <span class="hero-badge">Novo Menu</span>
                        <h1>Saladas que são um Banquete</h1>
                        <p>Combinações únicas de sabores e nutrientes.</p>
                        <a href="produtos.php" class="btn btn-primary">Ver Cardápio</a>
                    </div>
                </div>

                <div class="hero-slide">
                    <img src="https://images.unsplash.com/photo-1540420773420-3366772f4999" alt="Smoothie">
                    <div class="hero-content">
                        <span class="hero-badge">Detox</span>
                        <h1>Smoothies que Transformam</h1>
                        <p>Refresque-se com frutas naturais e superalimentos.</p>
                        <a href="produtos.php" class="btn btn-primary">Comprar</a>
                    </div>
                </div>

            </div>

            <button class="hero-btn hero-prev">❮</button>
            <button class="hero-btn hero-next">❯</button>
        </section>
        <section
            style="padding: 4rem 0; text-align: center; background: linear-gradient(to bottom, white, var(--secondary));">
            <div class="container">
                <div
                    style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(22,163,74,0.1); color: var(--primary); padding: 0.4rem 1rem; border-radius: 2rem; font-size: 0.85rem; font-weight: 600; margin-bottom: 1.5rem;">
                    <i class="ph-fill ph-leaf"></i> 100% Natural e Saudável
                </div>
                <h1
                    style="font-size: clamp(2.5rem, 5vw, 4rem); line-height: 1.1; font-weight: 800; margin-bottom: 1.5rem;">
                    Alimentação Saudável <br>
                    <span class="text-gradient">Feita com Amor</span>
                </h1>
                <p style="max-width: 600px; margin: 0 auto 2rem; color: var(--muted); font-size: 1.1rem;">
                    Descubra refeições deliciosas, nutritivas e preparadas com ingredientes frescos e naturais.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <a href="produtos.php" class="btn btn-primary">
                        Ver Cardápio <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>

        <section class="container" style="padding: 4rem 1.5rem;">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Produtos em Destaque</h2>
                <p style="color: var(--muted);">Confira alguns dos nossos pratos mais populares.</p>
            </div>

            <div class="carousel-container">
                <button class="carousel-btn prev-btn"><i class="ph-bold ph-caret-left"></i></button>

                <div class="carousel-track">
                    <div class="carousel-item">
                        <div class="card">
                            <div class="card-img-wrapper">
                                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500" alt="Bowl"
                                    class="card-img">
                                <span class="card-badge">Bowls</span>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title">Bowl Verde Vitality</h3>
                                <p class="card-desc">Mix de folhas, abacate, quinoa, grão de bico e molho especial.</p>
                                <div class="card-price">R$ 32,90</div>
                            </div>
                            <div class="card-footer">
                                <form action="carrinho_acoes.php?acao=adicionar" method="POST">
                                    <input type="hidden" name="id" value="1">
                                    <input type="hidden" name="nome" value="Bowl Verde Vitality">
                                    <input type="hidden" name="preco" value="32.90">
                                    <input type="hidden" name="img"
                                        value="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500">
                                    <button type="submit" class="btn btn-primary btn-full"><i
                                            class="ph-bold ph-shopping-cart"></i> Adicionar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="card">
                            <div class="card-img-wrapper">
                                <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500"
                                    alt="Salada" class="card-img">
                                <span class="card-badge">Saladas</span>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title">Salada Color Nura</h3>
                                <p class="card-desc">Tomate cereja, pepino, rabanete, sementes e proteína vegetal.</p>
                                <div class="card-price">R$ 28,50</div>
                            </div>
                            <div class="card-footer">
                                <form action="carrinho_acoes.php?acao=adicionar" method="POST">
                                    <input type="hidden" name="id" value="2">
                                    <input type="hidden" name="nome" value="Salada Color Nura">
                                    <input type="hidden" name="preco" value="28.50">
                                    <input type="hidden" name="img"
                                        value="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500">
                                    <button type="submit" class="btn btn-primary btn-full"><i
                                            class="ph-bold ph-shopping-cart"></i> Adicionar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="card">
                            <div class="card-img-wrapper">
                                <img src="https://images.unsplash.com/photo-1540420773420-3366772f4999?w=500"
                                    alt="Smoothie" class="card-img">
                                <span class="card-badge">Bebidas</span>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title">Smoothie Detox</h3>
                                <p class="card-desc">Couve, maçã, gengibre e limão. Energia pura para o seu dia.</p>
                                <div class="card-price">R$ 18,00</div>
                            </div>
                            <div class="card-footer">
                                <form action="carrinho_acoes.php?acao=adicionar" method="POST">
                                    <input type="hidden" name="id" value="3">
                                    <input type="hidden" name="nome" value="Smoothie Detox">
                                    <input type="hidden" name="preco" value="18.00">
                                    <input type="hidden" name="img"
                                        value="https://images.unsplash.com/photo-1540420773420-3366772f4999?w=500">
                                    <button type="submit" class="btn btn-primary btn-full"><i
                                            class="ph-bold ph-shopping-cart"></i> Adicionar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="card">
                            <div class="card-img-wrapper">
                                <img src="https://images.unsplash.com/photo-1623428187969-5da2dcea5ebf?w=500" alt="Wrap"
                                    class="card-img">
                                <span class="card-badge">Wraps</span>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title">Wrap de Frango</h3>
                                <p class="card-desc">Frango grelhado, alface americana e molho de iogurte natural.</p>
                                <div class="card-price">R$ 24,90</div>
                            </div>
                            <div class="card-footer">
                                <form action="carrinho_acoes.php?acao=adicionar" method="POST">
                                    <input type="hidden" name="id" value="4">
                                    <input type="hidden" name="nome" value="Wrap de Frango">
                                    <input type="hidden" name="preco" value="24.90">
                                    <input type="hidden" name="img"
                                        value="https://images.unsplash.com/photo-1623428187969-5da2dcea5ebf?w=500">
                                    <button type="submit" class="btn btn-primary btn-full"><i
                                            class="ph-bold ph-shopping-cart"></i> Adicionar</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                <button class="carousel-btn next-btn"><i class="ph-bold ph-caret-right"></i></button>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container footer-grid">

            <div>
                <h3 class="logo">Nura<span>.</span></h3>
                <p>Alimentação saudável feita com ingredientes naturais e muito amor.</p>
            </div>

            <div>
                <h4>Explorar</h4>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="produtos.php">Cardápio</a></li>
                    <li><a href="#">Sobre Nós</a></li>
                </ul>
            </div>

            <div>
                <h4>Suporte</h4>
                <ul>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Privacidade</a></li>
                    <li><a href="#">Termos</a></li>
                </ul>
            </div>

            <div>
                <h4>Contato</h4>
                <p>📍 São Paulo - SP</p>
                <p>📞 (11) 98765-4321</p>
                <p>✉ contato@nura.com.br</p>
            </div>

        </div>

        <div class="footer-bottom">
            <p>© 2025 Nura. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="../script.js"></script>
</body>

</html>