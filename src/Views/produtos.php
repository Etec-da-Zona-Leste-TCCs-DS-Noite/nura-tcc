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
      <a href="index.php" class="logo"><img src="../assets/img/NURA_logo.png" alt="Nura Logo"
          style="height: 80px; object-fit: contain;"></a>
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
      <button class="mobile-menu-btn btn btn-ghost" aria-label="Abrir Menu">
        <i class="ph ph-list" style="font-size: 1.5rem;"></i>
      </button>
    </div>
  </header>

  <main class="container" style="padding: 3rem 1.5rem;">
    <div style="text-align: center; margin-bottom: 4rem;">
      <h1
        style="font-size: clamp(2rem, 6vw, 2.8rem); margin-bottom: 0.8rem; font-weight: 800; letter-spacing: -0.03em;">
        Cardápio Completo
      </h1>

      <!-- Mensagem de Adaptação Clínica e Métrica de Saúde -->
      <?php if ($cliente_id): ?>
        <?php
        $mostrarImc = false;
        if (!empty($pesoCliente) && !empty($alturaCliente) && $alturaCliente > 0) {
          $mostrarImc = true;
          $imc = $pesoCliente / ($alturaCliente * $alturaCliente);
          $imcFormatado = number_format($imc, 1, ',', '.');

          if ($imc < 18.5) {
            $classificacao = 'Abaixo do peso';
            $corImc = '#eab308'; // amarelo
          } elseif ($imc < 24.9) {
            $classificacao = 'Peso Saudável ❤️';
            $corImc = '#10b981'; // verde
          } elseif ($imc < 29.9) {
            $classificacao = 'Sobrepeso (Atenção)';
            $corImc = '#f59e0b'; // laranja
          } else {
            $classificacao = 'Acima do peso ideal';
            $corImc = '#ef4444'; // vermelho
          }
        }
        ?>
        <div
          style="background: white; border: 1px solid rgba(0,0,0,0.05); padding: 1.5rem; border-radius: 1rem; max-width: 580px; margin: 0 auto; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05); text-align: left;">

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
          <div
            style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: <?php echo $mostrarImc ? '1.5rem' : '0'; ?>;">
            <div
              style="background: rgba(16, 185, 129, 0.1); padding: 0.6rem; border-radius: 50%; display: flex; flex-shrink: 0;">
              <i class="ph-fill ph-check-circle" style="color: #10b981; font-size: 1.8rem;"></i>
            </div>
            <div>
              <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--foreground); margin: 0; line-height: 1.2;">
                Olá, <?php echo htmlspecialchars(explode(' ', trim($nomeCliente ?? 'Visitante'))[0]); ?>!
              </h3>
              <p style="font-size: 0.9rem; color: var(--muted); margin: 0; margin-top: 0.2rem;">Seu cardápio está
                customizado e seguro.</p>

              <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.8rem;">
                <?php if (empty($tagsUsuario)): ?>
                  <span
                    style="font-size: 0.75rem; background: #f3f4f6; color: #6b7280; padding: 0.2rem 0.6rem; border-radius: 20px; font-weight: 600;">Sem
                    restrições cadastradas</span>
                <?php else: ?>
                  <?php foreach ($tagsUsuario as $t): ?>
                    <span
                      style="font-size: 0.75rem; background: #fee2e2; color: #ef4444; padding: 0.2rem 0.6rem; border-radius: 20px; font-weight: 700; border: 1px solid #fca5a5; display: inline-flex; align-items: center; gap: 0.2rem;">
                      <i class="ph-bold ph-warning-circle"></i> <?php echo $t; ?>
                    </span>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <?php if ($mostrarImc): ?>
            <div
              style="display: flex; gap: 2rem; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 1rem; margin-top: 0.5rem;">
              <div>
                <span
                  style="display: block; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--muted); margin-bottom: 0.3rem;">Seu
                  IMC Atual</span>
                <strong style="font-size: 1.3rem; color: var(--foreground);"><?php echo $imcFormatado; ?></strong>
              </div>
              <div>
                <span
                  style="display: block; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--muted); margin-bottom: 0.3rem;">Classificação
                  Nutricional</span>
                <strong
                  style="font-size: 1.05rem; display: inline-block; padding: 0.1rem 0.6rem; border-radius: 20px; background: <?php echo $corImc; ?>20; color: <?php echo $corImc; ?>;"><?php echo $classificacao; ?></strong>
              </div>
            </div>
          <?php endif; ?>
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
          <h2 style="font-size: clamp(1.2rem, 5vw, 1.5rem); font-weight: 800; color: var(--foreground);">
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
        <a href="index.php" class="logo" style="display: inline-block; margin-bottom: 1rem;"><img
            src="../assets/img/NURA_logo.png" alt="Nura Logo" style="height: 105px; object-fit: contain;"></a>
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