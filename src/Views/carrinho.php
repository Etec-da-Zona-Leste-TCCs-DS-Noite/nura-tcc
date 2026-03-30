<?php
session_start();
$nomeCliente = $_SESSION['cliente_nome'] ?? null;
$carrinho = $_SESSION['carrinho'] ?? [];

// Calcula Totais
$subtotal = 0;
foreach ($carrinho as $item) {
    $subtotal += $item['preco'] * $item['qtd'];
}
$frete = $subtotal > 0 ? 10.00 : 0; // Frete fixo se tiver itens
$total = $subtotal + $frete;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Nura - Carrinho</title>
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
                <a href="produtos.php">Produtos</a>
                <?php if ($nomeCliente): ?>
                    <a href="perfil.php">Olá, <?php echo htmlspecialchars($nomeCliente); ?></a>
                    <?php
                else: ?>
                    <a href="cadastro.php">Minha Conta</a>
                    <?php
                endif; ?>
            </nav>
            <button class="mobile-menu-btn btn btn-ghost" aria-label="Abrir Menu">
                <i class="ph ph-list" style="font-size: 1.5rem;"></i>
            </button>
        </div>
    </header>

    <main class="container" style="padding: 3rem 0; max-width: 800px;">
        <h1 style="margin-bottom: 2rem;">Seu Carrinho</h1>

        <?php if (empty($carrinho)): ?>
            <div style="text-align: center; padding: 4rem; border: 2px dashed var(--border); border-radius: var(--radius);">
                <i class="ph ph-shopping-cart" style="font-size: 3rem; color: var(--muted); margin-bottom: 1rem;"></i>
                <p style="color: var(--muted); margin-bottom: 1.5rem;">Seu carrinho está vazio.</p>
                <a href="produtos.php" class="btn btn-primary">Ver Cardápio</a>
            </div>
            <?php
        else: ?>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($carrinho as $item): ?>
                    <div class="order-card" style="display: flex; gap: 1rem; align-items: center; padding: 1rem;">
                        <img src="<?php echo $item['img']; ?>" alt="Foto"
                            style="width: 80px; height: 80px; object-fit: cover; border-radius: var(--radius);">

                        <div style="flex: 1;">
                            <h3 style="font-size: 1rem; font-weight: 600;"><?php echo $item['nome']; ?></h3>
                            <div style="color: var(--primary); font-weight: 700;">
                                R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <a href="carrinho_acoes.php?acao=atualizar&id=<?php echo $item['id']; ?>&qtd=<?php echo $item['qtd'] - 1; ?>"
                                class="btn btn-ghost" style="border: 1px solid var(--border); padding: 0.3rem 0.6rem;">-</a>

                            <span style="font-weight: 600; width: 20px; text-align: center;"><?php echo $item['qtd']; ?></span>

                            <a href="carrinho_acoes.php?acao=atualizar&id=<?php echo $item['id']; ?>&qtd=<?php echo $item['qtd'] + 1; ?>"
                                class="btn btn-ghost" style="border: 1px solid var(--border); padding: 0.3rem 0.6rem;">+</a>

                            <a href="carrinho_acoes.php?acao=remover&id=<?php echo $item['id']; ?>" class="btn btn-ghost"
                                style="color: #ef4444; margin-left: 0.5rem;" title="Remover item">
                                <i class="ph ph-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php
                endforeach; ?>
            </div>

            <div style="background: var(--secondary); padding: 2rem; border-radius: var(--radius); margin-top: 2rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.8rem;">
                    <span>Subtotal</span>
                    <span>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.8rem;">
                    <span>Entrega</span>
                    <span>R$ <?php echo number_format($frete, 2, ',', '.'); ?></span>
                </div>
                <div
                    style="display: flex; justify-content: space-between; border-top: 1px solid rgba(0,0,0,0.1); padding-top: 1rem; margin-top: 1rem; font-size: 1.5rem; font-weight: 700; color: var(--primary);">
                    <span>Total</span>
                    <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                </div>

                <?php if ($nomeCliente): ?>
                    <button class="btn btn-primary btn-full" style="margin-top: 1.5rem; padding: 1rem;"
                        onclick="alert('Compra finalizada! (Simulação)')">Finalizar Pedido</button>
                    <?php
                else: ?>
                    <button class="btn btn-primary btn-full" style="margin-top: 1.5rem; padding: 1rem;"
                        onclick="window.location.href='cadastro.php'">Faça Login para Finalizar</button>
                    <?php
                endif; ?>
            </div>

            <?php
        endif; ?>
    </main>
</body>

</html>