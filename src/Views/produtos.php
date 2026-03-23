<?php
session_start();
require_once __DIR__ . '/../Models/PerfilClinico.php';

$nomeCliente = $_SESSION['cliente_nome'] ?? null;
$cliente_id = $_SESSION['cliente_id'] ?? null;

// === BUSCA DE PERFIL PARA ALERTAS E EXIBIÇÃO NO TOPO ===
$alergiasCliente = [];
$restricaoCliente = '';
$pesoCliente = null;
$alturaCliente = null;

if ($cliente_id) {
  $perfilDb = PerfilClinico::buscarPorClienteId($cliente_id);
  if ($perfilDb) {
    $alergiasCliente = $perfilDb['alergias'] ?? [];
    $restricaoCliente = $perfilDb['restricao'] ?? '';
    $pesoCliente = $perfilDb['peso'] ?? null;
    $alturaCliente = $perfilDb['altura'] ?? null;
  }
}
// ==============================================================================

$qtdCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
  foreach ($_SESSION['carrinho'] as $item) {
    $qtdCarrinho += $item['qtd'];
  }
}

// --- LISTA DE PRODUTOS COMPLETAMENTE RENOVADA E CORRIGIDA ---
$produtos = [
  // --- BOWLS ---
  [
    'id' => 1,
    'nome' => 'Bowl Verde Vitality',
    'desc' => 'Mix de folhas frescas, abacate, quinoa e grão de bico.',
    'preco' => 32.90,
    'img' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500',
    'tag' => 'Bowls',
    'alergias' => [],
    'restricoes' => []
  ],
  [
    'id' => 2,
    'nome' => 'Poke de Salmão Defumado',
    'desc' => 'Salmão fresco, arroz gohan, manga, sunomono e molho tarê.',
    'preco' => 45.00,
    'img' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500',
    'tag' => 'Bowls',
    'alergias' => ['frutos_mar', 'soja'],
    'restricoes' => ['vegano', 'vegetariano', 'celiaco']
  ],
  [
    'id' => 3,
    'nome' => 'Bowl Proteico de Tofu',
    'desc' => 'Tofu marinado no shoyu, edamame, cogumelos salteados e arroz integral.',
    'preco' => 34.50,
    'img' => 'https://images.unsplash.com/photo-1543339308-43e59d6b73a6?w=500',
    'tag' => 'Bowls',
    'alergias' => ['soja'],
    'restricoes' => ['celiaco']
  ],
  [
    'id' => 4,
    'nome' => 'Mix Grelhado do Chef',
    'desc' => 'Cubos de frango orgânico, batata doce, brócolis e ovo cozido.',
    'preco' => 38.00,
    'img' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=500',
    'tag' => 'Bowls',
    'alergias' => ['ovo'],
    'restricoes' => ['vegano', 'vegetariano']
  ],
  [
    'id' => 18,
    'nome' => 'Bowl de Frango Teriyaki',
    'desc' => 'Frango ao molho teriyaki natural, edamame, gergelim e cenoura.',
    'preco' => 35.50,
    'img' => 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?w=500',
    'tag' => 'Bowls',
    'alergias' => ['soja'],
    'restricoes' => ['vegano', 'vegetariano', 'celiaco']
  ],

  // --- SALADAS ---
  [
    'id' => 5,
    'nome' => 'Salada Caesar Clássica',
    'desc' => 'Alface romana, croutons, queijo parmesão e autêntico molho caesar.',
    'preco' => 30.00,
    'img' => 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=500',
    'tag' => 'Saladas',
    'alergias' => ['ovo', 'soja'],
    'restricoes' => ['vegano', 'vegetariano', 'intolerancia_lactose', 'celiaco']
  ],
  [
    'id' => 6,
    'nome' => 'Salada Thai com Camarões',
    'desc' => 'Camarões grelhados, cenoura ralada, castanha-de-caju e molho sweet chili.',
    'preco' => 42.50,
    'img' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=500',
    'tag' => 'Saladas',
    'alergias' => ['frutos_mar', 'amendoim'],
    'restricoes' => ['vegano', 'vegetariano']
  ],
  [
    'id' => 7,
    'nome' => 'Salada Color Nura',
    'desc' => 'Tomate cereja, pepino, rabanete, repolho roxo e sementes de abóbora.',
    'preco' => 28.50,
    'img' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=500',
    'tag' => 'Saladas',
    'alergias' => [],
    'restricoes' => []
  ],
  [
    'id' => 14,
    'nome' => 'Salada Mediterrânea',
    'desc' => 'Grão de bico, rúcula, lascas de queijo feta, azeitonas e molho balsâmico.',
    'preco' => 31.00,
    'img' => 'https://images.unsplash.com/photo-1551248429-40975aa4de74?w=500',
    'tag' => 'Saladas',
    'alergias' => [],
    'restricoes' => ['vegano', 'intolerancia_lactose']
  ],
  [
    'id' => 19,
    'nome' => 'Salada Caprese Tostada',
    'desc' => 'Mussarela de búfala fresca, tomates adocicados ao azeite e manjericão.',
    'preco' => 33.00,
    'img' => 'https://images.unsplash.com/photo-1592417817098-8fd3d9eb14a5?w=500',
    'tag' => 'Saladas',
    'alergias' => [],
    'restricoes' => ['vegano', 'intolerancia_lactose']
  ],

  // --- WRAPS & SANDUÍCHES ---
  [
    'id' => 8,
    'nome' => 'Wrap Leve de Frango',
    'desc' => 'Frango magro grelhado, cream cheese verde na tortilha de trigo.',
    'preco' => 24.90,
    'img' => 'https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=500',
    'tag' => 'Wraps',
    'alergias' => [],
    'restricoes' => ['vegano', 'vegetariano', 'intolerancia_lactose', 'celiaco']
  ],
  [
    'id' => 9,
    'nome' => 'Wrap Doce de Amendoim',
    'desc' => 'Massa integral, fatias de maçã, canela e pasta de amendoim caseira.',
    'preco' => 22.90,
    'img' => 'https://www.receitasnestle.com.br/sites/default/files/styles/recipe_detail_desktop_new/public/srh_recipes/bca8119743e8c9eb43c7c78fb6bf36e0.webp?itok=VPZxIonw',
    'tag' => 'Wraps',
    'alergias' => ['amendoim'],
    'restricoes' => ['celiaco']
  ],
  [
    'id' => 22,
    'nome' => 'Sanduíche Caprese no Pão Sírio',
    'desc' => 'Pão sírio levemente tostado recheado com mussarela de búfala, rúcula e tomate.',
    'preco' => 26.90,
    'img' => 'https://images.unsplash.com/photo-1619096252214-ef06c45683e3?w=500',
    'tag' => 'Wraps',
    'alergias' => [],
    'restricoes' => ['vegano', 'intolerancia_lactose', 'celiaco']
  ],

  // --- SUCOS E BEBIDAS ---
  [
    'id' => 10,
    'nome' => 'Smoothie Antioxidante',
    'desc' => 'Maçã verde, abacaxi, couve, hortelã e um toque de limão.',
    'preco' => 18.00,
    'img' => 'https://images.unsplash.com/photo-1610970881699-44a5587cabec?w=500',
    'tag' => 'Sucos',
    'alergias' => [],
    'restricoes' => []
  ],
  [
    'id' => 12,
    'nome' => 'Limonada Suíça Fit',
    'desc' => 'Limão galego original batido em clara de ovo (espuma) e bastante gelo.',
    'preco' => 14.00,
    'img' => 'https://img.freepik.com/fotos-gratis/fatias-de-frutas-perto-de-copo-de-bebida-com-gelo-e-ervas-na-mesa_23-2148107706.jpg?semt=ais_hybrid&w=740&q=80',
    'tag' => 'Sucos',
    'alergias' => ['ovo'],
    'restricoes' => ['vegano']
  ],
  [
    'id' => 17,
    'nome' => 'Suco Sunshine Natural',
    'desc' => 'Mistura imbatível de suco de cenoura, laranja e um leve toque de gengibre.',
    'preco' => 15.50,
    'img' => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=500',
    'tag' => 'Sucos',
    'alergias' => [],
    'restricoes' => []
  ],
  [
    'id' => 20,
    'nome' => 'Suco Verde Metrópole',
    'desc' => 'Aipo puro, pepino congelado, maçã verde importada e couve.',
    'preco' => 17.50,
    'img' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=500',
    'tag' => 'Sucos',
    'alergias' => [],
    'restricoes' => []
  ]
];

// Configuração das Categorias
$categoriasDisplay = [
  'Bowls' => 'Nossos Bowls Favoritos',
  'Saladas' => 'Saladas Frescas',
  'Wraps' => 'Wraps & Sanduíches',
  'Sucos' => 'Sucos Naturais'
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
        <?php if ($nomeCliente): ?>
          <a href="perfil.php">Olá, <?php echo htmlspecialchars($nomeCliente); ?></a>
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
    <div style="text-align: center; margin-bottom: 4rem;">
      <h1 style="font-size: 2.8rem; margin-bottom: 0.8rem; font-weight: 800; letter-spacing: -0.03em;">Cardápio Completo
      </h1>

      <!-- Mensagem de Adaptação Clínica com Métrica de Peso e Altura Segura -->
      <?php if ($cliente_id): ?>
        <div
          style="display: inline-flex; align-items: center; justify-content: center; background: rgba(16, 185, 129, 0.1); padding: 0.8rem 1.5rem; border-radius: 2rem; border: 1px solid rgba(16, 185, 129, 0.2); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.05);">
          <i class="ph-fill ph-check-circle" style="color: #10b981; font-size: 1.25rem; margin-right: 0.6rem;"></i>
          <p style="color: #10b981; font-weight: 700; font-size: 0.95rem; margin: 0; letter-spacing: -0.01em;">
            O cardápio está adaptado para suas alergias e restrições!
            <?php if ($pesoCliente && $alturaCliente): ?>
              <span style="opacity: 0.9; margin-left: 0.2rem; font-weight: 600;">
                (<?php echo number_format($pesoCliente, 2, '.', ''); ?>kg
                <?php echo number_format($alturaCliente, 2, '.', ''); ?>m)
              </span>
            <?php endif; ?>
          </p>
        </div>
      <?php else: ?>
        <p style="color: var(--muted); font-size: 1.1rem;">Explore nossas opções e encontre a refeição ideal para a sua
          saúde.</p>
      <?php endif; ?>
    </div>

    <!-- Lista Viva de Produtos por Categoria -->
    <?php foreach ($categoriasDisplay as $tag => $titulo):
      $grupoProdutos = filtrarPorTag($produtos, $tag);
      if (empty($grupoProdutos)) {
        continue;
      }
      ?>

      <section class="category-section" style="margin-bottom: 4rem;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
          <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--foreground); white-space: nowrap;">
            <?php echo $titulo; ?>
          </h2>
          <div style="flex: 1; height: 1px; background: rgba(0,0,0,0.05);"></div>
        </div>

        <div class="carousel-container">
          <button class="carousel-btn prev-btn"><i class="ph-bold ph-caret-left"></i></button>

          <div class="carousel-track">
            <!-- FILTRAGEM PERMANECE EXATAMENTE IGUAL -->
            <?php foreach ($grupoProdutos as $p):
              $alergiasDesteProduto = $p['alergias'] ?? [];
              $incompativeisDesteProduto = $p['restricoes'] ?? [];

              $conflitoAlergias = array_intersect($alergiasCliente, $alergiasDesteProduto);
              $conflitoRestricao = ($restricaoCliente && in_array($restricaoCliente, $incompativeisDesteProduto));

              $naoRecomendado = !empty($conflitoAlergias) || $conflitoRestricao;

              $nomesConflito = [];
              // Tradução amigável
              $mapaAlergias = [
                'amendoim' => 'Amendoim/Castanhas',
                'frutos_mar' => 'Frutos do Mar',
                'soja' => 'Soja',
                'ovo' => 'Ovo'
              ];
              $mapaRestricoes = [
                'intolerancia_lactose' => 'Contém Lactose',
                'celiaco' => 'Contém Glúten',
                'vegano' => 'Contém Derivados Animais',
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

              // Estética do cartão com alertas de risco
              ?>
              <div class="carousel-item">
                <div class="card"
                  style="height: 100%; <?php echo $naoRecomendado ? 'background-color: #fafafa; border: 1px solid rgba(239, 68, 68, 0.4); box-shadow: inset 0 0 0 1px rgba(239, 68, 68, 0.1);' : ''; ?>">

                  <div class="card-img-wrapper" style="<?php echo $naoRecomendado ? 'opacity: 0.85;' : ''; ?>">
                    <img src="<?php echo $p['img']; ?>" alt="<?php echo $p['nome']; ?>" class="card-img"
                      style="<?php echo $naoRecomendado ? 'filter: grayscale(15%) contrast(85%);' : ''; ?>">
                    <span class="card-badge"
                      style="<?php echo $naoRecomendado ? 'background: #ef4444; color: white;' : ''; ?>"><?php echo $p['tag']; ?></span>
                  </div>

                  <div class="card-content" style="<?php echo $naoRecomendado ? 'background: transparent;' : ''; ?>">
                    <h3 class="card-title"><?php echo $p['nome']; ?></h3>

                    <?php if ($naoRecomendado): ?>
                      <div
                        style="background: rgba(239,68,68,0.08); padding: 0.5rem 0.8rem; border-radius: 0.5rem; margin: 0.5rem 0 1rem; border-left: 3px solid #ef4444;">
                        <span
                          style="color: #dc2626; font-size: 0.75rem; font-weight: 700; line-height: 1.3; display:flex; align-items:center; gap: 0.4rem;">
                          <i class="ph-fill ph-warning" style="font-size: 1rem;"></i> <span>Risco:
                            <?php echo $textoConflito; ?></span>
                        </span>
                      </div>
                    <?php endif; ?>

                    <p class="card-desc"><?php echo $p['desc']; ?></p>
                    <div class="card-price">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></div>
                  </div>

                  <div class="card-footer"
                    style="<?php echo $naoRecomendado ? 'background: transparent; border-top: 1px dashed rgba(0,0,0,0.05);' : ''; ?>">
                    <form action="carrinho_acoes.php?acao=adicionar" method="POST" style="margin: 0;">
                      <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                      <input type="hidden" name="nome" value="<?php echo $p['nome']; ?>">
                      <input type="hidden" name="preco" value="<?php echo $p['preco']; ?>">
                      <input type="hidden" name="img" value="<?php echo $p['img']; ?>">

                      <button type="submit" class="btn btn-full <?php echo $naoRecomendado ? '' : 'btn-primary'; ?>"
                        style="<?php echo $naoRecomendado ? 'background: white; color: #ef4444; border: 1px solid #fca5a5; font-weight: 700; box-shadow: none;' : ''; ?>">
                        <i class="ph-bold <?php echo $naoRecomendado ? 'ph-warning-circle' : 'ph-shopping-cart'; ?>"></i>
                        <?php echo $naoRecomendado ? 'Adicionar Mesmo Assim' : 'Adicionar'; ?>
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
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2025 Nura. Todos os direitos reservados.</p>
    </div>
  </footer>

  <script src="../script.js"></script>
</body>

</html>