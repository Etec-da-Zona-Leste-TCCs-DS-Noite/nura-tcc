<?php
session_start();
require_once __DIR__ . '/../Models/Cliente.php';
require_once __DIR__ . '/../Models/Pedido.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: cadastro.php");
    exit;
}

$dadosCliente = Cliente::buscarPorId($_SESSION['cliente_id']);
$pedidos = Pedido::buscarPorClienteId($_SESSION['cliente_id']);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura - Meus Pedidos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body class="page-perfil">

    <header>
        <div class="container header-inner">
            <a href="index.php" class="logo" aria-label="Nura — Início">
                <img class="logo-img" src="../assets/img/NURA_logo.png" alt="">
            </a>
            <nav class="nav-links" aria-label="Principal">
                <a href="index.php">Início</a>
                <a href="produtos.php">Produtos</a>
                <a href="carrinho.php">Carrinho</a>
            </nav>

            <form class="header-search" action="produtos.php" method="GET">
                <div class="search-input-wrapper">
                    <i class="ph ph-magnifying-glass search-icon" aria-hidden="true"></i>
                    <input type="text" name="busca" placeholder="Buscar pratos..." aria-label="Buscar" required>
                </div>
            </form>

            <div class="header-actions">
                <div class="header-user-chip" onclick="window.location.href='perfil.php'" style="cursor: pointer;">
                    <span id="header-user-name">Olá, <?php echo htmlspecialchars(explode(' ', trim($dadosCliente['nome'] ?? ''))[0]); ?></span>
                    <div class="header-avatar" aria-hidden="true">
                        <?php echo strtoupper(substr($dadosCliente['nome'] ?? 'C', 0, 1)); ?>
                    </div>
                </div>
            </div>
            <button type="button" class="mobile-menu-btn btn btn-ghost" aria-label="Abrir menu">
                <i class="ph ph-list header-icon" aria-hidden="true"></i>
            </button>
        </div>
    </header>

    <main class="container main-profile">
        <div class="pedidos-page-header">
            <h1 class="perfil-page-title" style="margin-bottom: 0;">Meus Pedidos</h1>
            <a href="perfil.php" class="btn btn-outline"><i class="ph ph-arrow-left"></i> Voltar ao Perfil</a>
        </div>

        <div class="profile-grid">
            <section class="profile-content" style="grid-column: 1 / -1;">
                <?php if (empty($pedidos)): ?>
                <div class="empty-state" style="text-align: center; padding: 6rem 2rem; background: var(--surface); border: 1px dashed var(--border-strong); border-radius: var(--radius-lg);">
                    <div style="font-size: 4rem; color: var(--border-strong); margin-bottom: 1.5rem;">
                        <i class="ph ph-package"></i>
                    </div>
                    <h2 style="font-family: 'Outfit'; margin-bottom: 0.5rem;">Nenhum pedido ainda</h2>
                    <p style="color: var(--muted); margin-bottom: 2rem;">Você ainda não realizou nenhuma compra conosco.</p>
                    <a href="produtos.php" class="btn btn-primary">Ver Cardápio</a>
                </div>
            <?php else: ?>
                <!-- Abas de Filtro -->
                <div class="tabs pedido-filter-tabs">
                    <button class="tab-btn active" data-filter="Todos">Todos</button>
                    <button class="tab-btn" data-filter="Em Preparo">Em Preparo</button>
                    <button class="tab-btn" data-filter="Pagamento Pendente">Pagamento Pendente</button>
                    <button class="tab-btn" data-filter="Concluído">Concluídos</button>
                    <button class="tab-btn" data-filter="Reembolsado">Reembolsados</button>
                </div>

                <div class="pedidos-list" style="display: flex; flex-direction: column; gap: 2rem;">
                    <?php foreach ($pedidos as $pedido): ?>
                        <div class="profile-card pedido-item" data-status="<?php echo htmlspecialchars($pedido['status']); ?>" style="margin-bottom: 0; padding: 2rem; display: flex; flex-direction: column; gap: 1.5rem; transition: 0.3s;">
                            <div class="pedido-header">
                                    <div>
                                        <h3 style="margin: 0; font-family: 'Outfit'; font-size: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                                            <i class="ph-fill ph-package" style="color: var(--primary);"></i> Pedido #<?php echo str_pad($pedido['id'], 4, '0', STR_PAD_LEFT); ?>
                                        </h3>
                                        <p style="margin: 0; font-size: 0.875rem; color: var(--muted); margin-top: 0.25rem;">
                                            <i class="ph ph-calendar-blank"></i> Realizado em <?php echo date('d/m/Y \à\s H:i', strtotime($pedido['created_at'])); ?>
                                        </p>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; justify-content: flex-end;">
                                        <div style="background: var(--surface-hover); border: 1px solid var(--border); color: var(--foreground); padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 0.3rem;">
                                            <i class="ph ph-wallet"></i> <?php echo htmlspecialchars($pedido['metodo_pagamento'] ?? 'PIX'); ?>
                                        </div>
                                        <div style="background: var(--surface-hover); border: 1px solid var(--border); color: var(--foreground); padding: 0.35rem 0.85rem; border-radius: 999px; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 0.3rem;">
                                            <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--green-leaf);"></div>
                                            <?php echo htmlspecialchars($pedido['status']); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="pedido-details-grid">
                                    <div>
                                        <h4 style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); margin-bottom: 0.75rem;"><i class="ph ph-shopping-bag"></i> Itens</h4>
                                        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem; background: var(--surface-hover); padding: 1rem; border-radius: 1rem;">
                                            <?php foreach ($pedido['itens'] as $item): ?>
                                                <li style="display: flex; justify-content: space-between; align-items: center;">
                                                    <span style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem;">
                                                        <span style="font-weight: 700; color: var(--primary); background: rgba(var(--primary-rgb), 0.1); padding: 0.1rem 0.4rem; border-radius: 0.25rem;"><?php echo $item['qtd']; ?>x</span>
                                                        <span><?php echo htmlspecialchars($item['nome']); ?></span>
                                                    </span>
                                                    <span style="color: var(--muted); font-size: 0.9rem;">R$ <?php echo number_format($item['preco'] * $item['qtd'], 2, ',', '.'); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>

                                    <div>
                                        <h4 style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); margin-bottom: 0.75rem;"><i class="ph ph-map-pin"></i> Entrega</h4>
                                        <div style="background: var(--surface-hover); padding: 1rem; border-radius: 1rem;">
                                            <p style="margin: 0; font-size: 0.9rem; color: var(--foreground); line-height: 1.5;">
                                                <?php echo !empty($pedido['endereco']) ? htmlspecialchars($pedido['endereco']) : 'Endereço não registrado.'; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="pedido-total-block">
                                    <div style="display: flex; justify-content: space-between; width: 100%; max-width: 280px; font-size: 0.9rem; color: var(--muted);">
                                        <span>Subtotal</span>
                                        <span>R$ <?php echo number_format($pedido['subtotal'] ?? 0, 2, ',', '.'); ?></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; width: 100%; max-width: 280px; font-size: 0.9rem; color: var(--muted);">
                                        <span>Frete</span>
                                        <span>R$ <?php echo number_format($pedido['frete'] ?? 0, 2, ',', '.'); ?></span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; width: 100%; max-width: 280px; font-size: 1.1rem; color: var(--foreground); font-weight: 700; font-family: 'Outfit'; margin-top: 0.5rem;">
                                        <span>Total pago</span>
                                        <span style="color: var(--primary);">R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></span>
                                    </div>
                                    <div style="margin-top: 1rem; width: 100%; max-width: 280px;">
                                        <a href="Checkout/comprovante.php?id=<?php echo $pedido['id']; ?>" target="_blank" class="btn btn-outline" style="width: 100%; justify-content: center; font-size: 0.85rem; padding: 0.5rem;">
                                            <i class="ph ph-receipt"></i> Ver Nota Fiscal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.tabs .tab-btn');
            const items = document.querySelectorAll('.pedido-item');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active de todos
                    tabs.forEach(t => {
                        t.classList.remove('active');
                    });

                    // Adiciona no clicado
                    tab.classList.add('active');

                    const filter = tab.getAttribute('data-filter');

                    // Filtra itens
                    items.forEach(item => {
                        if (filter === 'Todos' || item.getAttribute('data-status') === filter) {
                            item.style.display = 'flex';
                            setTimeout(() => item.style.opacity = '1', 50);
                        } else {
                            item.style.opacity = '0';
                            setTimeout(() => item.style.display = 'none', 300);
                        }
                    });
                });
            });
        });
    </script>
    <script src="../script.js"></script>
</body>

</html>
