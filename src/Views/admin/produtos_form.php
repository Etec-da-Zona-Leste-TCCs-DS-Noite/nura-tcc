<?php
// src/Views/admin/produtos_form.php
session_start();

// Verifica autenticação
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../Models/Produto.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$produto = null;
$tituloPagina = "Cadastrar Produto";
$acaoForm = "../../Controller/AdminController.php?acao=criar_produto";

// Se for edição, busca o produto
if ($id > 0) {
    $produto = Produto::buscarPorId($id);
    if ($produto) {
        $tituloPagina = "Editar Produto";
        $acaoForm = "../../Controller/AdminController.php?acao=atualizar_produto";
    } else {
        header("Location: produtos.php?nura_ft=error&nura_flash=" . urlencode("Produto não encontrado."));
        exit;
    }
}

// Valores iniciais ou do produto a ser editado
$nome = $produto ? $produto['nome'] : '';
$descricao = $produto ? ($produto['descricao'] ?? $produto['desc'] ?? '') : '';
$preco = $produto ? $produto['preco'] : '';
$tag = $produto ? $produto['tag'] : '';
$estoque = $produto ? ($produto['estoque'] ?? 15) : 15;
$img = $produto ? $produto['img'] : '';
$alergiasSelecionadas = $produto ? ($produto['alergias'] ?? []) : [];
$restricoesSelecionadas = $produto ? ($produto['restricoes'] ?? []) : [];

// Lista de alergias e restrições para checkboxes
$alergiasOptions = [
    'amendoim' => 'Amendoim/Castanhas',
    'frutos_mar' => 'Frutos do Mar',
    'soja' => 'Soja',
    'ovo' => 'Ovo'
];
$restricoesOptions = [
    'intolerancia_lactose' => 'Intolerância à Lactose (Contém Lactose)',
    'celiaco' => 'Celíacos (Contém Glúten)',
    'vegano' => 'Contém Origem Animal',
    'vegetariano' => 'Contém Carnes'
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura Admin - <?php echo $tituloPagina; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .form-grid-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; }
        @media (max-width: 900px) { .form-grid-2 { grid-template-columns: 1fr; } }
        
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: var(--foreground); }
        .form-control { width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--border); border-radius: 0.5rem; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 1rem; color: var(--foreground); transition: 0.3s; }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-soft); }
        textarea.form-control { resize: vertical; min-height: 120px; }
        
        .checkbox-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem; background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 0.5rem; padding: 1rem; }
        .checkbox-item { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem; color: var(--foreground); }
        .checkbox-item input { accent-color: var(--primary); width: 18px; height: 18px; cursor: pointer; }
        
        .image-upload-card { background: var(--bg-secondary); border: 1px dashed var(--border); border-radius: 0.5rem; padding: 1.5rem; text-align: center; }
        .image-preview-box { width: 100%; aspect-ratio: 4/3; border-radius: 0.5rem; overflow: hidden; background: #eaeaea; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
        .image-preview-box img { width: 100%; height: 100%; object-fit: cover; }
        .image-placeholder { color: var(--text-light); display: flex; flex-direction: column; align-items: center; gap: 0.5rem; }
        .image-placeholder i { font-size: 3rem; }
        .btn-upload { background: #fff; border: 1px solid var(--border); color: var(--foreground); padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer; font-size: 0.9rem; font-weight: 600; transition: 0.3s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-upload:hover { border-color: var(--primary); color: var(--primary); }
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
        <h1 class="perfil-page-title"><?php echo $tituloPagina; ?></h1>

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
                            <h2 style="margin:0;">Informações do Produto</h2>
                            <p style="margin:0.25rem 0 0 0; color:var(--text-light); font-size:0.9rem;">Preencha os dados abaixo.</p>
                        </div>
                        <a href="produtos.php" class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:0.5rem; padding: 0.5rem 1rem;">
                            <i class="ph-bold ph-arrow-left"></i> Voltar
                        </a>
                    </div>

                    <form action="<?php echo $acaoForm; ?>" method="POST" enctype="multipart/form-data">
                        <?php if ($produto): ?>
                            <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
                            <input type="hidden" name="img_existente" value="<?php echo htmlspecialchars($img); ?>">
                        <?php endif; ?>

                        <div class="form-grid-2">
                            <!-- Coluna Esquerda -->
                            <div style="display:flex; flex-direction:column; gap:1.5rem;">
                                
                                <div class="form-group">
                                    <label for="nome">Nome do Produto</label>
                                    <input type="text" id="nome" name="nome" class="form-control" placeholder="Ex: Bowl Verde Vitality" value="<?php echo htmlspecialchars($nome); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="descricao">Descrição / Ingredientes</label>
                                    <textarea id="descricao" name="descricao" class="form-control" placeholder="Ingredientes..." required><?php echo htmlspecialchars($descricao); ?></textarea>
                                </div>

                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                                    <div class="form-group">
                                        <label for="preco">Preço (R$)</label>
                                        <input type="number" id="preco" name="preco" class="form-control" placeholder="0.00" step="0.01" min="0" value="<?php echo htmlspecialchars($preco); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="tag">Categoria</label>
                                        <select id="tag" name="tag" class="form-control" required>
                                            <option value="" disabled <?php echo !$tag ? 'selected' : ''; ?>>Selecione...</option>
                                            <option value="Bowls" <?php echo $tag === 'Bowls' ? 'selected' : ''; ?>>Bowls</option>
                                            <option value="Saladas" <?php echo $tag === 'Saladas' ? 'selected' : ''; ?>>Saladas</option>
                                            <option value="Wraps" <?php echo $tag === 'Wraps' ? 'selected' : ''; ?>>Wraps</option>
                                            <option value="Sucos" <?php echo $tag === 'Sucos' ? 'selected' : ''; ?>>Sucos</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="estoque">Estoque Inicial</label>
                                    <input type="number" id="estoque" name="estoque" class="form-control" placeholder="0" min="0" value="<?php echo htmlspecialchars($estoque); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Alerta de Alergias</label>
                                    <div class="checkbox-grid">
                                        <?php foreach ($alergiasOptions as $val => $label): ?>
                                            <label class="checkbox-item">
                                                <input type="checkbox" name="alergias[]" value="<?php echo $val; ?>" <?php echo in_array($val, $alergiasSelecionadas) ? 'checked' : ''; ?>>
                                                <?php echo $label; ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Restrições Alimentares</label>
                                    <div class="checkbox-grid">
                                        <?php foreach ($restricoesOptions as $val => $label): ?>
                                            <label class="checkbox-item">
                                                <input type="checkbox" name="restricoes[]" value="<?php echo $val; ?>" <?php echo in_array($val, $restricoesSelecionadas) ? 'checked' : ''; ?>>
                                                <?php echo $label; ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                            </div>

                            <!-- Coluna Direita -->
                            <div>
                                <div class="form-group">
                                    <label>Imagem do Produto</label>
                                    <div class="image-upload-card">
                                        <div class="image-preview-box">
                                            <img id="img-preview" src="<?php echo $img ? htmlspecialchars($img) : ''; ?>" alt="" style="<?php echo !$img ? 'display:none;' : ''; ?>">
                                            <div class="image-placeholder" id="preview-placeholder" style="<?php echo $img ? 'display:none;' : ''; ?>">
                                                <i class="ph ph-image-square"></i>
                                                <span>Sem imagem prévia</span>
                                            </div>
                                        </div>
                                        
                                        <label class="btn-upload" for="imagem_upload">
                                            <i class="ph-bold ph-upload-simple"></i> Escolher Arquivo
                                        </label>
                                        <input type="file" id="imagem_upload" name="imagem_upload" accept="image/*" style="display:none;">
                                        
                                        <div style="margin-top:1rem; text-align:left;">
                                            <label style="font-size:0.8rem; color:var(--text-light);">Ou cole uma URL:</label>
                                            <input type="text" id="img_url" name="img_url" class="form-control" style="font-size:0.9rem; padding:0.5rem;" placeholder="https://..." value="<?php echo htmlspecialchars($img); ?>">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:1.5rem; justify-content:center; gap:0.5rem;">
                                    <i class="ph-bold ph-floppy-disk"></i> Salvar Produto
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </section>
        </div>
    </main>

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
        const fileInput = document.getElementById('imagem_upload');
        const urlInput = document.getElementById('img_url');
        const imgPreview = document.getElementById('img-preview');
        const placeholder = document.getElementById('preview-placeholder');

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                    placeholder.style.display = 'none';
                    urlInput.value = '';
                }
                reader.readAsDataURL(file);
            }
        });

        urlInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url) {
                imgPreview.src = url;
                imgPreview.style.display = 'block';
                placeholder.style.display = 'none';
            } else if (!fileInput.files[0]) {
                imgPreview.style.display = 'none';
                placeholder.style.display = 'flex';
            }
        });
    </script>
</body>
</html>
