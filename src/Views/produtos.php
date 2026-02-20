<?php
session_start();
$nomeUsuario = $_SESSION['usuario_nome'] ?? null;

// Lógica do Contador do Carrinho
$qtdCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
  foreach ($_SESSION['carrinho'] as $item) {
    $qtdCarrinho += $item['qtd'];
  }
}

// --- LISTA DE PRODUTOS ---
$produtos = [
  // --- BOWLS ---
  [
    'id' => 1,
    'nome' => 'Bowl Verde Vitality',
    'desc' => 'Mix de folhas, abacate, quinoa e grão de bico.',
    'preco' => 32.90,
    'img' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500',
    'tag' => 'Bowls'
  ],
  [
    'id' => 5,
    'nome' => 'Poke de Salmão',
    'desc' => 'Salmão fresco, arroz gohan, manga e sunomono.',
    'preco' => 42.00,
    'img' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500',
    'tag' => 'Bowls'
  ],
  [
    'id' => 7,
    'nome' => 'Bowl Mexicano',
    'desc' => 'Feijão, milho, abacate e pimenta suave.',
    'preco' => 35.00,
    'img' => 'https://images.unsplash.com/photo-1543339308-43e59d6b73a6?w=500',
    'tag' => 'Bowls'
  ],
  [
    'id' => 10,
    'nome' => 'Bowl Frango Grelhado',
    'desc' => 'Frango em cubos, brócolis, arroz e molho especial.',
    'preco' => 38.00,
    'img' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=500',
    'tag' => 'Bowls'
  ],

  // --- SALADAS ---
  [
    'id' => 2,
    'nome' => 'Salada Color Nura',
    'desc' => 'Tomate cereja, pepino, rabanete e sementes.',
    'preco' => 28.50,
    'img' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=500',
    'tag' => 'Saladas'
  ],
  [
    'id' => 8,
    'nome' => 'Salada Caesar',
    'desc' => 'Alface romana, croutons, parmesão e molho caesar.',
    'preco' => 30.00,
    'img' => 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=500',
    'tag' => 'Saladas'
  ],
  [
    'id' => 11,
    'nome' => 'Salada Grega',
    'desc' => 'Pepino, tomate, azeitonas e queijo feta.',
    'preco' => 31.50,
    'img' => 'https://images.unsplash.com/photo-1551248429-40975aa4de74?w=500',
    'tag' => 'Saladas'
  ],

  // --- WRAPS ---
  [
    'id' => 4,
    'nome' => 'Wrap de Frango',
    'desc' => 'Frango grelhado, alface e molho de iogurte.',
    'preco' => 24.90,
    'img' => 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=500',
    'tag' => 'Wraps'
  ],
  [
    'id' => 15,
    'nome' => 'Wrap Vegetariano',
    'desc' => 'Hummus, vegetais grelhados e rúcula.',
    'preco' => 22.90,
    // Nova imagem garantida
    'img' => 'https://www.receitasnestle.com.br/sites/default/files/styles/recipe_detail_desktop_new/public/srh_recipes/bca8119743e8c9eb43c7c78fb6bf36e0.webp?itok=VPZxIonw',
    'tag' => 'Wraps'
  ],

  // --- BEBIDAS (Sucos) ---
  [
    'id' => 3,
    'nome' => 'Smoothie Detox',
    'desc' => 'Couve, maçã, gengibre e limão.',
    'preco' => 18.00,
    'img' => 'https://images.unsplash.com/photo-1610970881699-44a5587cabec?w=500',
    'tag' => 'Sucos'
  ],
  [
    'id' => 6,
    'nome' => 'Suco de Laranja',
    'desc' => 'Laranja natural espremida na hora.',
    'preco' => 12.00,
    'img' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?w=500',
    'tag' => 'Sucos'
  ],
  [
    'id' => 9,
    'nome' => 'Suco de Melancia',
    'desc' => 'Refrescante e natural com hortelã.',
    'preco' => 14.00,
    'img' => 'https://images.unsplash.com/photo-1589733955941-5eeaf752f6dd?w=500',
    'tag' => 'Sucos'
  ],
  [
    'id' => 14,
    'nome' => 'Suco de Limão',
    'desc' => 'Refrescante e natural.',
    'preco' => 12.00,
    'img' => 'https://img.freepik.com/fotos-gratis/fatias-de-frutas-perto-de-copo-de-bebida-com-gelo-e-ervas-na-mesa_23-2148107706.jpg?semt=ais_hybrid&w=740&q=80',
    'tag' => 'Sucos'
  ],
];

// Configuração das Categorias
$categoriasDisplay = [
  'Bowls' => 'Nossos Bowls Favoritos',
  'Saladas' => 'Saladas Frescas',
  'Wraps' => 'Wraps & Sanduíches',
  'Sucos' => 'Bebidas Naturais'
];

function filtrarPorTag($lista, $tag)
{
  return array_filter($lista, function ($item) use ($tag) {
    return $item['tag'] === $tag;
  });
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nura - Cardápio</title>
  <link rel="stylesheet" href="../style.css">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body>

  <header>
    <div class="container header-inner">
      <a href="index.php" class="logo">Nura<span>.</span></a>
      <nav class="nav-links">
        <a href="index.php">Início</a>
        <a href="produtos.php" style="color: var(--primary); font-weight: bold;">Produtos</a>
        <?php if ($nomeUsuario): ?>
          <a href="perfil.php">Olá, <?php echo htmlspecialchars($nomeUsuario); ?></a>
        <?php else: ?>
          <a href="cadastro.php">Minha Conta</a>
        <?php endif; ?>
      </nav>
      <div class="header-actions">
        <a href="carrinho.php" class="btn btn-ghost" style="position: relative;" aria-label="Carrinho">
          <i class="ph ph-shopping-cart" style="font-size: 1.2rem;"></i>
          <?php if ($qtdCarrinho > 0): ?>
            <span class="cart-badge" style="
                    position: absolute; top: -5px; right: -5px; background: var(--primary); color: white; font-size: 0.7rem; font-weight: bold; min-width: 18px; height: 18px; border-radius: 99px; display: flex; align-items: center; justify-content: center; padding: 0 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                ">
              <?php echo $qtdCarrinho; ?>
            </span>
          <?php endif; ?>
        </a>
      </div>
    </div>
  </header>

  <main class="container" style="padding: 3rem 1.5rem;">
    <div style="text-align: center; margin-bottom: 3rem;">
      <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Cardápio Completo</h1>
      <p style="color: var(--muted);">Explore nossas opções separadas especialmente para você.</p>
    </div>

    <?php foreach ($categoriasDisplay as $tag => $titulo):
      $grupoProdutos = filtrarPorTag($produtos, $tag);
      if (empty($grupoProdutos))
        continue;
      ?>

      <section class="category-section" style="margin-bottom: 4rem;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
          <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--foreground); white-space: nowrap;">
            <?php echo $titulo; ?></h2>
          <div style="flex: 1; height: 1px; background: var(--border);"></div>
          <a href="#"
            style="font-size: 0.85rem; color: var(--primary); font-weight: 600; white-space: nowrap; display: flex; align-items: center; gap: 0.2rem;">
            Ver todos <i class="ph-bold ph-caret-right"></i>
          </a>
        </div>

        <div class="carousel-container">
          <button class="carousel-btn prev-btn"><i class="ph-bold ph-caret-left"></i></button>

          <div class="carousel-track">
            <?php foreach ($grupoProdutos as $p): ?>
              <div class="carousel-item">
                <div class="card" style="height: 100%;">
                  <div class="card-img-wrapper">
                    <img src="<?php echo $p['img']; ?>" alt="<?php echo $p['nome']; ?>" class="card-img">
                    <span class="card-badge"><?php echo $p['tag']; ?></span>
                  </div>
                  <div class="card-content">
                    <h3 class="card-title"><?php echo $p['nome']; ?></h3>
                    <p class="card-desc"><?php echo $p['desc']; ?></p>
                    <div class="card-price">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></div>
                  </div>
                  <div class="card-footer">
                    <form action="carrinho_acoes.php?acao=adicionar" method="POST">
                      <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                      <input type="hidden" name="nome" value="<?php echo $p['nome']; ?>">
                      <input type="hidden" name="preco" value="<?php echo $p['preco']; ?>">
                      <input type="hidden" name="img" value="<?php echo $p['img']; ?>">
                      <button type="submit" class="btn btn-primary btn-full">
                        <i class="ph-bold ph-shopping-cart"></i> Adicionar
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

    <?php endforeach; ?>

  </main>

  <script src="../script.js"></script>
</body>

</html>