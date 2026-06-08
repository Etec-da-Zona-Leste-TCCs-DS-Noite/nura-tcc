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

require_once __DIR__ . '/../Controller/ProdutoController.php';
$produtoController = new ProdutoController();
$produtos = $produtoController->listarTodos();

$buscaQuery = isset($_GET['busca']) ? trim($_GET['busca']) : '';
if ($buscaQuery !== '') {
    $qLower = mb_strtolower($buscaQuery, 'UTF-8');
    $produtos = array_filter($produtos, function ($item) use ($qLower) {
        return (strpos(mb_strtolower($item['nome'], 'UTF-8'), $qLower) !== false ||
                strpos(mb_strtolower($item['descricao'], 'UTF-8'), $qLower) !== false ||
                strpos(mb_strtolower($item['tag'], 'UTF-8'), $qLower) !== false);
    });
}

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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../style.css">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body class="page-cardapio">

  <header>
    <div class="container header-inner">
      <a href="index.php" class="logo" aria-label="Nura — Início">
        <img class="logo-img" src="../assets/img/NURA_logo.png" alt="">
      </a>
      <nav class="nav-links" aria-label="Principal">
        <a href="index.php">Início</a>
        <a href="produtos.php" class="nav-link--current">Produtos</a>
        <a href="<?php echo $nomeCliente ? 'perfil.php' : 'cadastro.php'; ?>">Minha Conta</a>
      </nav>

      <form class="header-search" action="produtos.php" method="GET">
          <div class="search-input-wrapper">
              <i class="ph ph-magnifying-glass search-icon" aria-hidden="true"></i>
              <input type="text" name="busca" placeholder="Buscar pratos..." aria-label="Buscar" required>
          </div>
      </form>

      <div class="header-actions">
        <a href="<?php echo $nomeCliente ? 'perfil.php' : 'cadastro.php'; ?>" class="btn btn-ghost" aria-label="Conta">
          <?php if ($nomeCliente): ?>
            <span class="header-user-label">Olá,
              <?php echo htmlspecialchars(explode(' ', trim($nomeCliente))[0]); ?></span>
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
      <button type="button" class="mobile-menu-btn btn btn-ghost" aria-label="Abrir menu">
        <i class="ph ph-list header-icon" aria-hidden="true"></i>
      </button>
    </div>
  </header>

  <main class="container cardapio-main">
    <div class="cardapio-page-title">
      <h1>Cardápio completo</h1>

      <?php if ($cliente_id): ?>
        <?php
        $mostrarImc = false;
        if (!empty($pesoCliente) && !empty($alturaCliente) && $alturaCliente > 0) {
          $mostrarImc = true;
          $imc = $pesoCliente / ($alturaCliente * $alturaCliente);
          $imcFormatado = number_format($imc, 1, ',', '.');

          if ($imc < 18.5) {
            $classificacao = 'Abaixo do peso';
            $corImc = '#eab308';
          } elseif ($imc < 24.9) {
            $classificacao = 'Peso Saudável ❤️';
            $corImc = '#10b981';
          } elseif ($imc < 29.9) {
            $classificacao = 'Sobrepeso (Atenção)';
            $corImc = '#f59e0b';
          } else {
            $classificacao = 'Acima do peso ideal';
            $corImc = '#ef4444';
          }
        }
        ?>
        <div class="clinical-banner">

          <?php
          $mapaAlergiasCard = [
            'amendoim' => 'Amendoim/Castanhas',
            'frutos_mar' => 'Frutos do Mar',
            'soja' => 'Soja',
            'ovo' => 'Ovo'
          ];
          $mapaRestricoesCard = [
            'intolerancia_lactose' => 'Intolerância à Lactose',
            'celiaco' => 'Celíaco (Zero Glúten)',
            'vegano' => 'Dieta Vegana',
            'vegetariano' => 'Dieta Vegetariana'
          ];

          $tagsUsuario = [];
          if (!empty($restricaoCliente) && isset($mapaRestricoesCard[$restricaoCliente])) {
            $tagsUsuario[] = $mapaRestricoesCard[$restricaoCliente];
          }
          if (!empty($alergiasCliente)) {
            foreach ($alergiasCliente as $al) {
              if (isset($mapaAlergiasCard[$al]))
                $tagsUsuario[] = "Alergia: " . $mapaAlergiasCard[$al];
            }
          }
          ?>
          <div class="clinical-banner__row" style="margin-bottom: <?php echo $mostrarImc ? '1.5rem' : '0'; ?>;">
            <div class="clinical-banner__icon" aria-hidden="true">
              <i class="ph-fill ph-check-circle"></i>
            </div>
            <div>
              <h3 class="clinical-banner__title">
                Olá, <?php echo htmlspecialchars(explode(' ', trim($nomeCliente ?? 'Visitante'))[0]); ?>!
              </h3>
              <p class="clinical-banner__sub">Seu cardápio está customizado e seguro.</p>

              <div class="tag-row">
                <?php if (empty($tagsUsuario)): ?>
                  <span class="tag-pill">Sem restrições cadastradas</span>
                <?php else: ?>
                  <?php foreach ($tagsUsuario as $t): ?>
                    <span class="tag-pill tag-pill--alert">
                      <i class="ph-bold ph-warning-circle" aria-hidden="true"></i> <?php echo htmlspecialchars($t); ?>
                    </span>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <?php if ($mostrarImc): ?>
            <div class="clinical-imc-row">
              <div>
                <span class="clinical-imc-label">Seu IMC atual</span>
                <span class="clinical-imc-value"><?php echo $imcFormatado; ?></span>
              </div>
              <div>
                <span class="clinical-imc-label">Classificação nutricional</span>
                <span class="clinical-imc-badge" style="background: <?php echo $corImc; ?>22; color: <?php echo $corImc; ?>;"><?php echo $classificacao; ?></span>
              </div>
            </div>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <p class="cardapio-lead">Explore nossas opções e encontre a refeição ideal para a sua saúde.</p>
      <?php endif; ?>
    </div>

    <?php if (empty($produtos)): ?>
      <div style="text-align: center; padding: 4rem 1rem;">
          <i class="ph ph-magnifying-glass" style="font-size: 3rem; color: var(--muted); margin-bottom: 1rem;"></i>
          <h3 style="margin-bottom: 1rem;">Nenhum produto encontrado</h3>
          <p style="color: var(--muted); margin-bottom: 2rem;">Não encontramos produtos correspondentes à sua busca.</p>
          <a href="produtos.php" class="btn btn-outline">Ver todo o cardápio</a>
      </div>
    <?php endif; ?>

    <?php foreach ($categoriasDisplay as $tag => $titulo):
      $grupoProdutos = filtrarPorTag($produtos, $tag);
      if (empty($grupoProdutos)) {
        continue;
      }
      ?>

      <section class="category-section" aria-labelledby="cat-<?php echo htmlspecialchars($tag); ?>">
        <div class="category-heading">
          <h2 id="cat-<?php echo htmlspecialchars($tag); ?>"><?php echo htmlspecialchars($titulo); ?></h2>
          <span class="rule" aria-hidden="true"></span>
        </div>

        <div class="carousel-container">
          <button type="button" class="carousel-btn prev-btn" aria-label="Anterior"><i class="ph-bold ph-caret-left" aria-hidden="true"></i></button>

          <div class="carousel-track">
            <?php foreach ($grupoProdutos as $p):
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
              ?>
              <div class="carousel-item">
                <article class="card" style="<?php echo $naoRecomendado ? 'background-color: hsl(0, 0%, 98%); border: 1px solid rgba(239, 68, 68, 0.45); box-shadow: inset 0 0 0 1px rgba(239, 68, 68, 0.08);' : ''; ?>">

                  <div class="card-img-wrapper" style="<?php echo $naoRecomendado ? 'opacity: 0.88;' : ''; ?>">
                    <img src="<?php echo $p['img']; ?>" alt="<?php echo htmlspecialchars($p['nome']); ?>" class="card-img"
                      style="<?php echo $naoRecomendado ? 'filter: grayscale(12%) contrast(88%);' : ''; ?>">
                    <span class="card-badge"
                      style="<?php echo $naoRecomendado ? 'background: #ef4444; color: white;' : ''; ?>"><?php echo htmlspecialchars($p['tag']); ?></span>
                  </div>

                  <div class="card-content" style="<?php echo $naoRecomendado ? 'background: transparent;' : ''; ?>">
                    <h3 class="card-title"><?php echo htmlspecialchars($p['nome']); ?></h3>

                    <?php if ($naoRecomendado): ?>
                      <div class="risk-callout" role="status">
                        <div class="risk-callout__inner">
                          <i class="ph-fill ph-warning" aria-hidden="true"></i>
                          <span>Risco: <?php echo htmlspecialchars($textoConflito); ?></span>
                        </div>
                      </div>
                    <?php endif; ?>

                    <p class="card-desc"><?php echo $p['desc']; ?></p>
                    <div class="card-price">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></div>
                  </div>

                  <div class="card-footer"
                    style="<?php echo $naoRecomendado ? 'background: transparent; border-top: 1px dashed rgba(0,0,0,0.06);' : ''; ?>">
                    <form action="carrinho_acoes.php?acao=adicionar" method="POST">
                      <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                      <input type="hidden" name="nome" value="<?php echo $p['nome']; ?>">
                      <input type="hidden" name="preco" value="<?php echo $p['preco']; ?>">
                      <input type="hidden" name="img" value="<?php echo $p['img']; ?>">

                      <button type="submit" class="btn btn-full <?php echo $naoRecomendado ? '' : 'btn-primary'; ?>"
                        style="<?php echo $naoRecomendado ? 'background: white; color: #ef4444; border: 1px solid #fca5a5; font-weight: 700; box-shadow: none;' : ''; ?>">
                        <i class="ph-bold <?php echo $naoRecomendado ? 'ph-warning-circle' : 'ph-shopping-cart'; ?>" aria-hidden="true"></i>
                        <?php echo $naoRecomendado ? 'Adicionar mesmo assim' : 'Adicionar'; ?>
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

    <?php endforeach; ?>

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
                    <li><a href="promocoes.php">Promoções</a></li>
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

     <!-- Renderiza o widget flutuante do chat no canto inferior direito -->
    <?php include __DIR__ . '/chat_widget.php'; ?>

  <script src="../script.js"></script>
</body>

</html>