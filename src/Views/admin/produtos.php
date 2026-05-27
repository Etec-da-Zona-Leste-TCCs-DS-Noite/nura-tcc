<?php
// src/Views/admin/produtos.php
session_start();

// Verifica autenticação
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../Models/Produto.php';
$produtos = Produto::buscarTodos();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura Admin - Gestão de Produtos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .admin-table-wrapper { overflow-x: auto; margin-top: 1rem; }
        .table-light { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; }
        .table-light th { padding: 1rem; border-bottom: 2px solid var(--border); color: var(--text-light); font-weight: 600; }
        .table-light td { padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        
        .product-img { width: 50px; height: 50px; border-radius: 0.5rem; object-fit: cover; border: 1px solid var(--border); }
        .status-pill { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .status-pill.ok { background: var(--primary-soft); color: var(--primary); }
        .status-pill.low { background: #fef3c7; color: #d97706; }
        .status-pill.empty { background: var(--danger-soft); color: var(--danger); }
        .tag-pill { display: inline-block; padding: 0.2rem 0.6rem; background: var(--bg-secondary); border-radius: 0.5rem; font-size: 0.8rem; border: 1px solid var(--border); }
        
        .action-cell { display: flex; gap: 0.5rem; }
        .btn-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.5rem; border: 1px solid var(--border); color: var(--text-light); transition: 0.2s; text-decoration: none; cursor: pointer; background: transparent; }
        .btn-icon:hover { background: var(--bg-secondary); color: var(--foreground); }
        .btn-icon.delete:hover { border-color: var(--danger); color: var(--danger); background: var(--danger-soft); }
        
        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
            z-index: 100; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: 0.3s;
        }
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        .custom-modal { background: #fff; border-radius: 1.25rem; padding: 2.25rem; max-width: 420px; width: 90%; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transform: scale(0.9); transition: 0.3s; border: 1px solid var(--border); }
        .modal-overlay.active .custom-modal { transform: scale(1); }
        .modal-icon { font-size: 3rem; color: var(--danger); margin-bottom: 1rem; }
        .modal-title { font-family: 'Outfit'; margin: 0 0 0.5rem 0; font-size: 1.4rem; }
        .modal-text { color: var(--text-light); font-size: 0.9rem; line-height: 1.5; margin-bottom: 2rem; }
        .modal-actions { display: flex; gap: 0.75rem; }
        .modal-actions .btn { flex: 1; text-align: center; }
    </style>
</head>
<body class="page-perfil">

    <header>
        <div class="container header-inner">
            <a href="../index.php" class="logo" aria-label="Nura — Início">
                <img class="logo-img" src="../../assets/img/NURA_logo.png" alt="">
            </a>
            <div style="flex: 1;"></div>
            <div class="header-actions">
                <div class="header-user-chip">
                    <span id="header-user-name">Admin: <?php echo htmlspecialchars($_SESSION['admin_nome']); ?></span>
                    <div class="header-avatar" aria-hidden="true" style="background: var(--primary);">
                        <i class="ph ph-shield-check" style="color:#fff;"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container main-profile">
        <h1 class="perfil-page-title">Gestão de Produtos</h1>

        <div class="profile-grid">
            <aside class="profile-sidebar">
                <nav class="sidebar-menu" aria-label="Menu Admin">
                    <a href="dashboard.php" class="sidebar-link">
                        <i class="ph ph-chart-pie-slice" aria-hidden="true"></i> Dashboard</a>
                    <a href="produtos.php" class="sidebar-link active">
                        <i class="ph ph-package" aria-hidden="true"></i> Produtos</a>
                    <a href="../index.php" class="sidebar-link" target="_blank">
                        <i class="ph ph-browser" aria-hidden="true"></i> Ver Site</a>
                    <a href="../../Controller/AdminController.php?acao=sair" class="sidebar-link">
                        <i class="ph ph-sign-out" aria-hidden="true"></i> Sair</a>
                </nav>
            </aside>

            <section class="profile-content">
                <div class="profile-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                        <div>
                            <h2 style="margin:0;">Catálogo</h2>
                            <p style="margin:0.25rem 0 0 0; color:var(--text-light); font-size:0.9rem;">Adicione, modifique ou exclua os pratos cadastrados no cardápio.</p>
                        </div>
                        <a href="produtos_form.php" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:0.5rem;">
                            <i class="ph-bold ph-plus"></i> Novo
                        </a>
                    </div>

                    <div class="admin-table-wrapper">
                        <table class="table-light">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Categoria</th>
                                    <th>Preço</th>
                                    <th>Alergias</th>
                                    <th>Restrições</th>
                                    <th>Estoque</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($produtos)): ?>
                                    <tr><td colspan="7" style="text-align:center; padding:2rem;">Nenhum produto cadastrado no momento.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($produtos as $p): ?>
                                        <?php
                                        $stock = $p['estoque'];
                                        $stockClass = 'ok'; $stockIcon = 'ph-check-circle'; $stockLabel = "Estoque OK ($stock)";
                                        if ($stock === 0) { $stockClass = 'empty'; $stockIcon = 'ph-warning-circle'; $stockLabel = 'Sem Estoque'; }
                                        elseif ($stock <= 5) { $stockClass = 'low'; $stockIcon = 'ph-warning-circle'; $stockLabel = "Baixo ($stock)"; }

                                        $mapAlergias = ['amendoim'=>'Amendoim', 'frutos_mar'=>'Frutos do Mar', 'soja'=>'Soja', 'ovo'=>'Ovo'];
                                        $mapRestricoes = ['intolerancia_lactose'=>'S/ Lactose', 'celiaco'=>'S/ Glúten', 'vegano'=>'Vegano', 'vegetariano'=>'Vegetariano'];
                                        
                                        $alList = []; foreach($p['alergias'] as $al) { if(isset($mapAlergias[$al])) $alList[]=$mapAlergias[$al]; }
                                        $resList = []; foreach($p['restricoes'] as $res) { if(isset($mapRestricoes[$res])) $resList[]=$mapRestricoes[$res]; }
                                        ?>
                                        <tr>
                                            <td>
                                                <div style="display:flex; align-items:center; gap:0.75rem;">
                                                    <img src="<?php echo htmlspecialchars($p['img']); ?>" class="product-img" alt="">
                                                    <div>
                                                        <div style="font-weight:600;"><?php echo htmlspecialchars($p['nome']); ?></div>
                                                        <div style="font-size:0.75rem; color:var(--text-light); max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo htmlspecialchars($p['desc']); ?>">
                                                            <?php echo htmlspecialchars($p['desc']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="tag-pill"><?php echo htmlspecialchars($p['tag']); ?></span></td>
                                            <td style="font-weight:600;">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?></td>
                                            <td style="font-size:0.8rem; color:var(--text-light);"><?php echo empty($alList) ? '—' : implode(', ', $alList); ?></td>
                                            <td style="font-size:0.8rem; color:var(--text-light);"><?php echo empty($resList) ? '—' : implode(', ', $resList); ?></td>
                                            <td>
                                                <span class="status-pill <?php echo $stockClass; ?>">
                                                    <i class="ph-bold <?php echo $stockIcon; ?>"></i> <?php echo $stockLabel; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-cell">
                                                    <a href="produtos_form.php?id=<?php echo $p['id']; ?>" class="btn-icon" title="Editar"><i class="ph ph-pencil"></i></a>
                                                    <button type="button" class="btn-icon delete" title="Excluir" onclick="confirmarDelecao(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars(addslashes($p['nome'])); ?>')"><i class="ph ph-trash"></i></button>
                                                </div>
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

    <!-- Modal de Exclusão -->
    <div class="modal-overlay" id="deleteModal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="custom-modal">
            <div class="modal-icon">
                <i class="ph-fill ph-warning-circle"></i>
            </div>
            <h2 class="modal-title" id="modal-title">Excluir Produto?</h2>
            <p class="modal-text">Deseja mesmo excluir o produto <strong id="delete-product-name" style="color:var(--foreground);"></strong>? Esta ação é irreversível.</p>
            <div class="modal-actions">
                <a href="#" class="btn btn-danger-solid" id="confirmDeleteLink">Sim, Excluir</a>
                <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
            </div>
        </div>
    </div>

    <footer class="footer" style="margin-top: 4rem;">
        <div class="container footer-grid">
            <div class="footer-brand">
                <img class="logo-img-footer" src="../../assets/img/NURA_logo.png" alt="Nura">
                <p>Painel Administrativo Nura</p>
            </div>
            <p>© 2026 Nura. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="../../script.js"></script>
    <script>
        const modal = document.getElementById('deleteModal');
        const confirmBtn = document.getElementById('confirmDeleteLink');
        const nameEl = document.getElementById('delete-product-name');

        function confirmarDelecao(id, nome) {
            nameEl.textContent = nome;
            confirmBtn.href = `../../Controller/AdminController.php?acao=deletar_produto&id=${id}`;
            modal.classList.add('active');
        }

        function fecharModal() {
            modal.classList.remove('active');
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                fecharModal();
            }
        });
    </script>
</body>
</html>
