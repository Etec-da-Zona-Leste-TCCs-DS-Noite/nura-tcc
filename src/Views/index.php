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

// --- ARRAY DE PRODUTOS EM DESTAQUE (Com Alergias e Restrições injetados) ---
$produtosDestaque = [
    [
        'id' => 1,
        'nome' => 'Bowl Verde Vitality',
        'desc' => 'Mix de folhas, abacate, quinoa, grão de bico e molho especial.',
        'preco' => 32.90,
        'img' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500',
        'tag' => 'Bowls',
        'alergias' => [],
        'restricoes' => []
    ],
    [
        'id' => 2,
        'nome' => 'Salada Color Nura',
        'desc' => 'Tomate cereja, pepino, rabanete, sementes e proteína vegetal.',
        'preco' => 28.50,
        'img' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500',
        'tag' => 'Saladas',
        'alergias' => [],
        'restricoes' => []
    ],
    [
        'id' => 3,
        'nome' => 'Smoothie Detox',
        'desc' => 'Couve, maçã, gengibre e limão. Energia pura para o seu dia.',
        'preco' => 18.00,
        'img' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=500',
        'tag' => 'Bebidas',
        'alergias' => [],
        'restricoes' => []
    ],
    [
        'id' => 4,
        'nome' => 'Wrap de Frango',
        'desc' => 'Frango grelhado, alface americana e molho de iogurte natural.',
        'preco' => 24.90,
        'img' => 'https://images.unsplash.com/photo-1623428187969-5da2dcea5ebf?w=500',
        'tag' => 'Wraps',
        'alergias' => [],
        'restricoes' => ['vegano', 'vegetariano', 'intolerancia_lactose', 'celiaco']
    ]
];
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
                <?php if ($nomeCliente): ?>
                    <a href="perfil.php" style="display: flex; align-items: center; gap: 0.5rem; color: var(--foreground);">
                        <i class="ph-fill ph-user-circle" style="font-size: 1.2rem; color: var(--primary);"></i>
                        Olá, <?php echo htmlspecialchars($nomeCliente); ?>
                    </a>
                <?php else: ?>
                    <a href="cadastro.php">Minha Conta</a>
                <?php endif; ?>
            </nav>

            <div class="header-actions">
                <a href="<?php echo $nomeCliente ? 'perfil.php' : 'cadastro.php'; ?>" class="btn btn-ghost"
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

                <!-- CONVERSÃO DO CARROSSEL ESTÁTICO DE DESTAQUES PARA RENDERIZAÇÃO INTELIGENTE -->
                <div class="carousel-track">
                    <?php foreach ($produtosDestaque as $p): ?>
                        <?php
                            $alergiasDesteProduto = $p['alergias'] ?? [];
                            $incompativeisDesteProduto = $p['restricoes'] ?? [];
                            
                            $conflitoAlergias = array_intersect($alergiasCliente, $alergiasDesteProduto);
                            $conflitoRestricao = ($restricaoCliente && in_array($restricaoCliente, $incompativeisDesteProduto));
                            
                            $naoRecomendado = !empty($conflitoAlergias) || $conflitoRestricao;

                            $nomesConflito = [];
                            $mapaAlergias = [
                              'amendoim' => 'Amendoim/Castanhas',
                              'frutos_mar' => 'Frutos do Mar',
                              'soja' => 'Soja',
                              'ovo' => 'Ovo'
                            ];
                            $mapaRestricoes = [
                              'intolerancia_lactose' => 'Contém Lactose',
                              'celiaco' => 'Contém Glúten',
                              'vegano' => 'Contém Animais',
                              'vegetariano' => 'Contém Carne M/T'
                            ];

                            if (!empty($conflitoAlergias)) {
                              foreach ($conflitoAlergias as $c) {
                                $nomesConflito[] = $mapaAlergias[$c] ?? $c;
                              }
                            }

                            if ($conflitoRestricao) {
                              $nomesConflito[] = $mapaRestricoes[$restricaoCliente] ?? 'Restrição à sua Dieta';
                            }

                            $textoConflito = implode(', ', $nomesConflito);
                        ?>
                        <div class="carousel-item">
                            <div class="card" style="height: 100%; transition: 0.3s; <?php echo $naoRecomendado ? 'opacity: 0.65; border: 2px solid #ef4444;' : ''; ?>">
                                <div class="card-img-wrapper">
                                    <img src="<?php echo $p['img']; ?>" alt="<?php echo $p['nome']; ?>" class="card-img">
                                    <span class="card-badge"><?php echo $p['tag']; ?></span>
                                </div>
                                <div class="card-content">
                                    <h3 class="card-title"><?php echo $p['nome']; ?></h3>
                                    
                                    <?php if ($naoRecomendado): ?>
                                      <span style="color: #ef4444; font-size: 0.75rem; font-weight: bold; display: block; margin-bottom: 0.5rem; line-height: 1.2;">
                                        ⚠️  ALERTA<br>(<?php echo $textoConflito; ?>)
                                      </span>
                                    <?php endif; ?>

                                    <p class="card-desc"><?php echo $p['desc']; ?></p>
                                    <div class="card-price">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></div>
                                </div>
                                <div class="card-footer">
                                    <form action="carrinho_acoes.php?acao=adicionar" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="nome" value="<?php echo $p['nome']; ?>">
                                        <input type="hidden" name="preco" value="<?php echo $p['preco']; ?>">
                                        <input type="hidden" name="img" value="<?php echo $p['img']; ?>">
                                        <button type="submit" class="btn btn-primary btn-full" <?php echo $naoRecomendado ? 'style="background: #ef4444;"' : ''; ?>>
                                            <i class="ph-bold ph-shopping-cart"></i> 
                                            <?php echo $naoRecomendado ? 'Adicionar Assim Mesmo' : 'Adicionar'; ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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