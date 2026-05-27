<?php
// src/Views/admin/dashboard.php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Models/Pedido.php';
require_once __DIR__ . '/../../Models/Produto.php';

// Filtro de Período
$period = isset($_GET['period']) ? $_GET['period'] : '30';
$validPeriods = [
    '7' => '-7 days',
    '30' => '-30 days',
    '180' => '-6 months',
    '365' => '-1 year'
];
if (!array_key_exists($period, $validPeriods)) {
    $period = '30';
}
$thresholdDate = date('Y-m-d H:i:s', strtotime($validPeriods[$period]));

$todosPedidos = Pedido::buscarTodos();

// Filtra
$pedidos = [];
foreach ($todosPedidos as $p) {
    if ($p['created_at'] >= $thresholdDate) {
        $pedidos[] = $p;
    }
}

// Buscar dados não sensíveis do perfil clínico de todos os clientes
$perfisClinicos = [];
$clientesAtivos = [];
try {
    $stmtPerf = $pdo->query("SELECT cliente_id, peso, altura, restricao, alergias FROM perfil_clinico");
    while ($row = $stmtPerf->fetch(PDO::FETCH_ASSOC)) {
        $perfisClinicos[$row['cliente_id']] = $row;
    }
} catch (Exception $e) {}

$totalFaturamento = 0.0;
$totalPedidos = count($pedidos);
$totalItensVendidos = 0;
$ticketMedio = 0.0;
$ticketMedioItens = 0.0;

// Agrupamentos
$salesByDate = [];
$salesByCategory = [];
$salesByStatus = [];
$salesByMethod = [];
$salesByTurno = [
    'Manhã' => 0,
    'Tarde' => 0,
    'Noite' => 0
];
$productSales = []; 

$produtosTodos = Produto::buscarTodos();
$produtoInfo = [];
foreach ($produtosTodos as $prod) {
    $produtoInfo[$prod['id']] = [
        'nome' => $prod['nome'],
        'tag' => $prod['tag'],
        'img' => $prod['img']
    ];
    $salesByCategory[$prod['tag']] = 0;
}

// Para manter dados de clientes na tela (para o modal)
$clientsDataForModal = [];

foreach ($pedidos as $p) {
    
    $st = $p['status'] ?? 'Em Preparo';
    if (!isset($salesByStatus[$st])) $salesByStatus[$st] = 0;
    $salesByStatus[$st]++;

    $mt = $p['metodo_pagamento'] ?? 'PIX';
    if (!isset($salesByMethod[$mt])) $salesByMethod[$mt] = 0;
    $salesByMethod[$mt] += $p['total'];

    // Calculo do Turno
    $horaStr = date('H', strtotime($p['created_at']));
    $hora = (int)$horaStr;
    if ($hora >= 6 && $hora < 12) {
        $salesByTurno['Manhã']++;
    } elseif ($hora >= 12 && $hora < 18) {
        $salesByTurno['Tarde']++;
    } else {
        $salesByTurno['Noite']++;
    }

    if ($p['status'] !== 'Reembolsado') {
        $totalFaturamento += $p['total'];
        
        $dateStr = date('d/m/Y', strtotime($p['created_at']));
        if (!isset($salesByDate[$dateStr])) $salesByDate[$dateStr] = 0.0;
        $salesByDate[$dateStr] += $p['total'];

        $itens = $p['itens'] ?? [];
        foreach ($itens as $item) {
            $pId = (int) ($item['id'] ?? 0);
            $q = (int) ($item['qtd'] ?? 0);
            $totalItensVendidos += $q;
            
            $cat = $produtoInfo[$pId]['tag'] ?? 'Outros';
            if (!isset($salesByCategory[$cat])) $salesByCategory[$cat] = 0;
            $salesByCategory[$cat] += $q;

            $pNome = $produtoInfo[$pId]['nome'] ?? 'Produto Removido';
            $pImg = $produtoInfo[$pId]['img'] ?? '';
            if (!isset($productSales[$pId])) {
                $productSales[$pId] = [
                    'nome' => $pNome,
                    'categoria' => $cat,
                    'img' => $pImg,
                    'qtd_vendida' => 0,
                    'receita_gerada' => 0.0
                ];
            }
            $productSales[$pId]['qtd_vendida'] += $q;
            $productSales[$pId]['receita_gerada'] += ($item['preco'] * $q);
        }
    }

    // Preparar dados do cliente para o modal baseado no ID do cliente real
    $cId = $p['cliente_real_id'] ?? 0;
    if ($cId > 0 && !isset($clientsDataForModal[$cId])) {
        $perfil = $perfisClinicos[$cId] ?? null;
        $clientsDataForModal[$cId] = [
            'id' => $cId,
            'nome' => $p['cliente_nome'] ?? 'Visitante',
            'email' => $p['cliente_email'] ?? 'visitante@nura.com',
            'telefone' => $p['cliente_telefone'] ?? 'Não informado',
            'perfil' => $perfil
        ];
    }
}

if ($totalPedidos > 0) {
    $ticketMedio = $totalFaturamento / $totalPedidos;
    $ticketMedioItens = $totalItensVendidos / $totalPedidos;
}

// Ordenações
uksort($salesByDate, function($a, $b) {
    $d1 = DateTime::createFromFormat('d/m/Y', $a);
    $d2 = DateTime::createFromFormat('d/m/Y', $b);
    return $d1 <=> $d2;
});

uasort($productSales, function($a, $b) {
    return $b['qtd_vendida'] <=> $a['qtd_vendida'];
});
$topProducts = array_slice($productSales, 0, 5, true);

// Prepara JS
$chartLabels = array_keys($salesByDate);
$chartData = array_values($salesByDate);
$catLabels = array_keys($salesByCategory);
$catData = array_values($salesByCategory);
$statLabels = array_keys($salesByStatus);
$statData = array_values($salesByStatus);
$metLabels = array_keys($salesByMethod);
$metData = array_values($salesByMethod);
$turLabels = array_keys($salesByTurno);
$turData = array_values($salesByTurno);

$clientsDataJson = json_encode($clientsDataForModal);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura Admin - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <style>
        body.page-perfil {
            overflow-x: hidden;
            width: 100vw;
        }

        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .kpi-card { background: #fff; border: 1px solid var(--border); border-radius: 1rem; padding: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .kpi-card h4 { margin: 0; font-size: 0.85rem; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.05em; font-family: 'DM Sans', sans-serif; }
        .kpi-card .val { margin: 0; font-size: 1.8rem; font-weight: 700; color: var(--foreground); font-family: 'Outfit', sans-serif; }
        .kpi-card .val.primary { color: var(--primary); }
        
        .chart-grid-3 { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) minmax(0, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
        .chart-grid-2 { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
        
        @media (max-width: 1200px) { .chart-grid-3 { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); } }
        @media (max-width: 900px) { .chart-grid-2, .chart-grid-3 { grid-template-columns: minmax(0, 1fr); } }
        
        .chart-wrap { position: relative; height: 300px; width: 100%; margin-top: 1rem; }
        .admin-table-wrapper { overflow-x: auto; width: 100%; margin-top: 1rem; border-radius: 1rem; }
        
        .table-light { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; min-width: 600px; }
        .table-light th { padding: 1rem; border-bottom: 2px solid var(--border); color: var(--text-light); font-weight: 600; }
        .table-light td { padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        
        /* Product List Styles (Substitui Tabela) */
        .product-list { display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem; }
        .product-item { display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid var(--border); border-radius: 1rem; background: #fff; transition: 0.2s; }
        .product-item:hover { border-color: var(--primary-soft); box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1); }
        .product-img { width: 60px; height: 60px; border-radius: 0.75rem; object-fit: cover; background: #f3f4f6; }
        .product-info { flex: 1; min-width: 0; }
        .product-name { font-weight: 600; font-size: 1rem; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .product-cat { font-size: 0.8rem; color: var(--text-light); }
        .product-stats { text-align: right; }
        .product-qty { font-weight: 700; font-size: 1.1rem; }
        .product-rev { color: var(--primary); font-size: 0.9rem; font-weight: 600; }

        .status-pill { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .status-pill.preparo { background: var(--primary-soft); color: var(--primary); }
        .status-pill.pendente { background: #fef3c7; color: #d97706; }
        .status-pill.concluido { background: #e0e7ff; color: #4338ca; }
        .status-pill.reembolsado { background: var(--danger-soft); color: var(--danger); }
        
        .dash-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; width: 100%; }
        .dash-actions { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        .filter-select { padding: 0.6rem 1rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: 'DM Sans', sans-serif; font-size: 0.9rem; outline: none; cursor: pointer; }
        .btn-pdf { background: var(--primary); color: #fff; border: none; padding: 0.6rem 1.25rem; border-radius: 0.5rem; font-family: 'DM Sans', sans-serif; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-small { background: var(--bg-secondary); border: 1px solid var(--border); padding: 0.4rem 0.8rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; }
        .btn-small:hover { background: var(--primary-soft); color: var(--primary); border-color: var(--primary); }

        /* Modal Perfil do Cliente */
        .client-modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 9999; display: none; align-items: center; justify-content: center; padding: 1rem; }
        .client-modal-overlay.active { display: flex; }
        .client-modal { background: #fff; border-radius: 1rem; width: 100%; max-width: 500px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .client-modal-header { padding: 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .client-modal-header h3 { margin: 0; font-family: 'Outfit'; font-size: 1.2rem; }
        .client-modal-close { background: none; border: none; cursor: pointer; font-size: 1.5rem; }
        .client-modal-body { padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
        .modal-info-row { display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px dashed var(--border); }
        .modal-info-row:last-child { border-bottom: none; }
        .modal-info-label { color: var(--text-light); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; }
        .modal-info-val { font-weight: 500; text-align: right; }

        /* PDF Styles */
        .pdf-mode { padding: 20px; background: #fff; width: 1400px !important; max-width: none !important; margin: 0; }
        .pdf-mode .profile-card { border: 1px solid #e5e5e5; box-shadow: none; margin-bottom: 2rem; }
        .pdf-mode .chart-grid-3, .pdf-mode .chart-grid-2 { display: flex; flex-wrap: nowrap; gap: 20px; }
        .pdf-mode .chart-grid-3 > div, .pdf-mode .chart-grid-2 > div { flex: 1; }
        .pdf-mode .kpi-grid { display: flex; flex-wrap: nowrap; gap: 20px; }
        .pdf-mode .kpi-grid > div { flex: 1; }
    </style>
</head>
<body class="page-perfil">

    <!-- Modal do Cliente -->
    <div class="client-modal-overlay" id="clientModal">
        <div class="client-modal">
            <div class="client-modal-header">
                <h3>Perfil do Cliente</h3>
                <button class="client-modal-close" onclick="closeModal()"><i class="ph ph-x"></i></button>
            </div>
            <div class="client-modal-body" id="clientModalBody">
                <!-- Preenchido via JS -->
            </div>
        </div>
    </div>

    <header data-html2canvas-ignore>
        <div class="container header-inner">
            <a href="../index.php" class="logo"><img class="logo-img" src="../../assets/img/NURA_logo.png" alt=""></a>
            <div style="flex: 1;"></div>
            <div class="header-actions">
                <div class="header-user-chip">
                    <span id="header-user-name">Admin: <?php echo htmlspecialchars($_SESSION['admin_nome']); ?></span>
                    <div class="header-avatar" style="background: var(--primary);"><i class="ph ph-shield-check" style="color:#fff;"></i></div>
                </div>
            </div>
        </div>
    </header>

    <main class="container main-profile">
        
        <div class="dash-header" data-html2canvas-ignore>
            <h1 class="perfil-page-title" style="margin: 0;">Painel Administrativo</h1>
            <div class="dash-actions">
                <form action="dashboard.php" method="GET" style="display:flex; gap:0.5rem; align-items:center;">
                    <select name="period" class="filter-select" onchange="this.form.submit()">
                        <option value="7" <?php echo $period == '7' ? 'selected' : ''; ?>>Últimos 7 dias</option>
                        <option value="30" <?php echo $period == '30' ? 'selected' : ''; ?>>Últimos 30 dias</option>
                        <option value="180" <?php echo $period == '180' ? 'selected' : ''; ?>>Últimos 6 meses</option>
                        <option value="365" <?php echo $period == '365' ? 'selected' : ''; ?>>Último 1 ano</option>
                    </select>
                </form>
                <button class="btn-pdf" id="btn-export-pdf">
                    <i class="ph-bold ph-file-pdf"></i> Baixar Relatório
                </button>
            </div>
        </div>

        <div class="profile-grid">
            <aside class="profile-sidebar" data-html2canvas-ignore>
                <nav class="sidebar-menu">
                    <a href="dashboard.php" class="sidebar-link active"><i class="ph ph-chart-pie-slice"></i> Dashboard</a>
                    <a href="produtos.php" class="sidebar-link"><i class="ph ph-package"></i> Produtos</a>
                    <a href="../index.php" class="sidebar-link" target="_blank"><i class="ph ph-browser"></i> Ver Site</a>
                    <a href="../../Controller/AdminController.php?acao=sair" class="sidebar-link"><i class="ph ph-sign-out"></i> Sair</a>
                </nav>
            </aside>

            <section class="profile-content" id="dashboard-content">
                
                <div style="display:none; text-align:center; margin-bottom:2rem;" id="pdf-header">
                    <img src="../../assets/img/NURA_logo.png" alt="Nura Logo" style="height:40px; margin-bottom:10px; filter: brightness(0);">
                    <h2 style="margin:0; font-family:'Outfit';">Relatório Gerencial de Vendas</h2>
                    <p style="margin:5px 0 0 0; color:#666;">Período Analisado: Últimos <?php echo $period; ?> dias</p>
                    <hr style="border:none; border-top:1px solid #eee; margin-top:1rem;">
                </div>

                <div class="kpi-grid">
                    <div class="kpi-card">
                        <h4>Faturamento</h4>
                        <p class="val primary">R$ <?php echo number_format($totalFaturamento, 2, ',', '.'); ?></p>
                    </div>
                    <div class="kpi-card">
                        <h4>Pedidos Feitos</h4>
                        <p class="val"><?php echo $totalPedidos; ?></p>
                    </div>
                    <div class="kpi-card">
                        <h4>Ticket Médio R$</h4>
                        <p class="val">R$ <?php echo number_format($ticketMedio, 2, ',', '.'); ?></p>
                    </div>
                    <div class="kpi-card">
                        <h4>Média Itens/Pedido</h4>
                        <p class="val"><?php echo number_format($ticketMedioItens, 1, ',', '.'); ?></p>
                    </div>
                </div>

                <div class="chart-grid-3">
                    <div class="profile-card">
                        <h2>Faturamento Diário</h2>
                        <div class="chart-wrap">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    <div class="profile-card">
                        <h2>Picos por Turno</h2>
                        <div class="chart-wrap">
                            <canvas id="turnoChart"></canvas>
                        </div>
                    </div>
                    <div class="profile-card">
                        <h2>Status</h2>
                        <div class="chart-wrap">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="chart-grid-2">
                    <div class="profile-card">
                        <h2>Top Produtos Mais Vendidos</h2>
                        <div class="product-list">
                            <?php if(empty($topProducts)): ?>
                                <p style="text-align:center; color:var(--text-light); margin-top:2rem;">Nenhum produto vendido.</p>
                            <?php else: ?>
                                <?php foreach($topProducts as $tp): ?>
                                    <div class="product-item">
                                        <img src="<?php echo htmlspecialchars($tp['img']); ?>" alt="Foto" class="product-img" onerror="this.src='../../assets/img/placeholder.png'">
                                        <div class="product-info">
                                            <div class="product-name"><?php echo htmlspecialchars($tp['nome']); ?></div>
                                            <div class="product-cat"><?php echo htmlspecialchars($tp['categoria']); ?></div>
                                        </div>
                                        <div class="product-stats">
                                            <div class="product-qty"><?php echo $tp['qtd_vendida']; ?> un</div>
                                            <div class="product-rev">R$ <?php echo number_format($tp['receita_gerada'], 2, ',', '.'); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="profile-card">
                        <h2>Categorias Mais Vendidas</h2>
                        <div class="chart-wrap">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="profile-card" style="margin-bottom: 2rem;">
                    <h2>Pedidos do Período</h2>
                    <div class="admin-table-wrapper">
                        <table class="table-light">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Data / Hora</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                    <th>Cliente</th>
                                    <th data-html2canvas-ignore>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pedidos)): ?>
                                    <tr><td colspan="6" style="text-align:center;">Nenhum pedido no período.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($pedidos as $pd): ?>
                                        <?php 
                                            $stClass = '';
                                            switch($pd['status']) {
                                                case 'Em Preparo': $stClass = 'preparo'; break;
                                                case 'Concluído': $stClass = 'concluido'; break;
                                                case 'Pagamento Pendente': $stClass = 'pendente'; break;
                                                case 'Reembolsado': $stClass = 'reembolsado'; break;
                                            }
                                        ?>
                                        <tr>
                                            <td style="font-weight:700;">#<?php echo str_pad($pd['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                            <td><?php echo date('d/m H:i', strtotime($pd['created_at'])); ?></td>
                                            <td><span class="status-pill <?php echo $stClass; ?>"><?php echo htmlspecialchars($pd['status']); ?></span></td>
                                            <td style="font-weight:600;">R$ <?php echo number_format($pd['total'], 2, ',', '.'); ?></td>
                                            <td>
                                                <div style="font-weight:600;"><?php echo htmlspecialchars($pd['cliente_nome'] ?? 'Visitante'); ?></div>
                                            </td>
                                            <td data-html2canvas-ignore>
                                                <?php if(!empty($pd['cliente_real_id'])): ?>
                                                    <button class="btn-small" onclick="openClientModal(<?php echo $pd['cliente_real_id']; ?>)">
                                                        <i class="ph-bold ph-file-text"></i> Ver Perfil
                                                    </button>
                                                <?php else: ?>
                                                    <span style="font-size:0.8rem;color:var(--text-light);">Visitante</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>
        </div>
    </main>

    <script>
        const clientsDataDb = <?php echo $clientsDataJson; ?>;

        function openClientModal(cId) {
            const c = clientsDataDb[cId];
            if(!c) return;

            const modalBody = document.getElementById('clientModalBody');
            
            const peso = c.perfil && c.perfil.peso ? c.perfil.peso + ' kg' : 'Não informado';
            const altura = c.perfil && c.perfil.altura ? c.perfil.altura + ' m' : 'Não informado';
            const rest = c.perfil && c.perfil.restricao ? c.perfil.restricao : 'Nenhuma';
            
            let alergias = 'Nenhuma';
            if (c.perfil && c.perfil.alergias) {
                try {
                    const arr = JSON.parse(c.perfil.alergias);
                    if (arr.length > 0) alergias = arr.join(', ');
                } catch(e) {}
            }

            modalBody.innerHTML = `
                <div class="modal-info-row">
                    <span class="modal-info-label">Nome Completo</span>
                    <span class="modal-info-val">${c.nome}</span>
                </div>
                <div class="modal-info-row">
                    <span class="modal-info-label">Email</span>
                    <span class="modal-info-val">${c.email}</span>
                </div>
                <div class="modal-info-row">
                    <span class="modal-info-label">Telefone</span>
                    <span class="modal-info-val">${c.telefone}</span>
                </div>
                
                <h4 style="margin: 1rem 0 0.5rem 0; font-family:'Outfit'; border-bottom:2px solid var(--primary-soft); display:inline-block;">Perfil Clínico de Produção</h4>
                
                <div class="modal-info-row">
                    <span class="modal-info-label">Peso / Altura</span>
                    <span class="modal-info-val">${peso} / ${altura}</span>
                </div>
                <div class="modal-info-row">
                    <span class="modal-info-label">Restrição Alimentar</span>
                    <span class="modal-info-val">${rest}</span>
                </div>
                <div class="modal-info-row">
                    <span class="modal-info-label">Alergias AVISO</span>
                    <span class="modal-info-val" style="color:var(--danger); font-weight:700;">${alergias}</span>
                </div>
            `;
            
            document.getElementById('clientModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('clientModal').classList.remove('active');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('clientModal');
            if (event.target == modal) closeModal();
        }

        document.addEventListener("DOMContentLoaded", function() {
            
            const chartLabels = <?php echo json_encode($chartLabels); ?>;
            const chartData = <?php echo json_encode($chartData); ?>;
            const catLabels = <?php echo json_encode($catLabels); ?>;
            const catData = <?php echo json_encode($catData); ?>;
            const statLabels = <?php echo json_encode($statLabels); ?>;
            const statData = <?php echo json_encode($statData); ?>;
            const turLabels = <?php echo json_encode($turLabels); ?>;
            const turData = <?php echo json_encode($turData); ?>;

            Chart.defaults.font.family = "'DM Sans', sans-serif";
            Chart.defaults.color = '#737373';
            Chart.defaults.scale.grid.color = '#e5e5e5';

            // 1. Faturamento
            const revCtx = document.getElementById('revenueChart').getContext('2d');
            const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); 
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
            new Chart(revCtx, {
                type: 'line',
                data: { labels: chartLabels, datasets: [{ data: chartData, borderColor: '#10b981', backgroundColor: gradient, borderWidth: 3, pointBackgroundColor: '#fff', pointBorderColor: '#10b981', pointRadius: 3, fill: true, tension: 0.4 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });

            // 2. Turnos (Bar)
            const turCtx = document.getElementById('turnoChart').getContext('2d');
            new Chart(turCtx, {
                type: 'bar',
                data: { labels: turLabels, datasets: [{ data: turData, backgroundColor: ['#f59e0b', '#3b82f6', '#6366f1'], borderRadius: 6 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });

            // 3. Status (Pie)
            const statCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statCtx, {
                type: 'pie',
                data: { labels: statLabels, datasets: [{ data: statData, backgroundColor: ['#f59e0b', '#10b981', '#6366f1', '#ef4444'], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });

            // 4. Categorias (Doughnut)
            const catCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(catCtx, {
                type: 'doughnut',
                data: { labels: catLabels, datasets: [{ data: catData, backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'bottom' } } }
            });

            // PDF Export
            document.getElementById('btn-export-pdf').addEventListener('click', function() {
                const element = document.getElementById('dashboard-content');
                const headerObj = document.getElementById('pdf-header');
                
                element.classList.add('pdf-mode');
                headerObj.style.display = 'block';

                setTimeout(() => {
                    const pxHeight = element.scrollHeight + 60; 
                    const pxWidth = 1400; 
                    const mmHeight = pxHeight * 0.264583;
                    const mmWidth = pxWidth * 0.264583;

                    const opt = {
                        margin:       10,
                        filename:     'Relatorio_Dashboard_Nura.pdf',
                        image:        { type: 'jpeg', quality: 1.0 },
                        html2canvas:  { scale: 2, useCORS: true, windowWidth: 1400 }, 
                        jsPDF:        { unit: 'mm', format: [mmWidth + 20, mmHeight + 20], orientation: 'portrait' }
                    };

                    const orgBtn = document.getElementById('btn-export-pdf').innerHTML;
                    document.getElementById('btn-export-pdf').innerHTML = '<i class="ph-bold ph-spinner"></i> Processando...';
                    document.getElementById('btn-export-pdf').style.opacity = '0.5';

                    html2pdf().set(opt).from(element).save().then(() => {
                        element.classList.remove('pdf-mode');
                        headerObj.style.display = 'none';
                        document.getElementById('btn-export-pdf').innerHTML = orgBtn;
                        document.getElementById('btn-export-pdf').style.opacity = '1';
                    });
                }, 100);
            });
        });
    </script>
</body>
</html>
