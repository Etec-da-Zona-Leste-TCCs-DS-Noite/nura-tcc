<?php
session_start();
require_once __DIR__ . '/../Models/Cliente.php';
require_once __DIR__ . '/../Models/PerfilClinico.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: cadastro.php");
    exit;
}

$dadosCliente = Cliente::buscarPorId($_SESSION['cliente_id']);

// === BUSCA OS DADOS DE PERFIL CLÍNICO PARA PREENCHER OS CAMPOS ===
// O Model fará a query via PDO. Se o perfil não existir, retorna array vazio ou null
$perfilDb = PerfilClinico::buscarPorClienteId($_SESSION['cliente_id']);

// Prepara as variáveis. Usamos ?? '' para que, se for a primeira vez, o campo fique vazio, blindando a view!
$peso = $perfilDb['peso'] ?? '';
$altura = $perfilDb['altura'] ?? '';
$restricao = $perfilDb['restricao'] ?? '';
$alergias = $perfilDb['alergias'] ?? [];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura - Meu Perfil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body class="page-perfil">

    <header>
        <div class="container header-inner">
            <a href="index.php" class="logo"><img src="../assets/img/NURA_logo.png" alt="Nura Logo"
                    style="height: 80px; object-fit: contain;"></a>
            <div class="nav-links">
                <a href="index.php">Início</a>
                <a href="produtos.php">Produtos</a>
                <a href="carrinho.php">Carrinho</a>
            </div>

            <div class="header-actions">
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 500;">
                    <span id="header-user-name">Olá, <?php echo htmlspecialchars($dadosCliente['nome'] ?? ''); ?></span>
                    <div
                        style="width: 35px; height: 35px; background: var(--secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary); font-weight: bold;">
                        <?php echo strtoupper(substr($dadosCliente['nome'] ?? 'C', 0, 1)); ?>
                    </div>
                </div>
            </div>
            <button class="mobile-menu-btn btn btn-ghost" aria-label="Abrir Menu">
                <i class="ph ph-list" style="font-size: 1.5rem;"></i>
            </button>
        </div>
    </header>

    <main class="container" style="padding: 3rem 1.5rem;">
        <h1 style="font-size: 2rem; margin-bottom: 2rem;">Minha Conta</h1>

        <div class="profile-grid">
            <aside class="profile-sidebar">
                <nav class="sidebar-menu">
                    <button class="sidebar-link tab-btn active" data-target="personal-data"><i class="ph ph-user"></i>
                        Dados Pessoais</button>

                    <!-- Aba de Perfil Clínico -->
                    <button class="sidebar-link tab-btn" data-target="clinical-profile"><i class="ph ph-heartbeat"></i>
                        Perfil Clínico</button>

                    <!-- Aba do Gêmeo Digital (Avatar) -->
                    <button class="sidebar-link tab-btn" data-target="digital-twin"><i class="ph ph-user-circle"></i>
                        Meu Avatar</button>

                    <a href="#"
                        onclick="if(confirm('Tem certeza?')) window.location.href='../Controller/ClienteController.php?acao=deletar';"
                        class="sidebar-link" style="color: #ef4444;">
                        <i class="ph ph-trash"></i> Excluir Conta
                    </a>
                    <a href="../Controller/ClienteController.php?acao=sair" class="sidebar-link"
                        style="color: var(--muted);"><i class="ph ph-sign-out"></i> Sair</a>
                </nav>
            </aside>

            <section class="profile-content">

                <!-- ABA 1: Dados Pessoais -->
                <div id="personal-data" class="form-content active">
                    <div class="profile-card">
                        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; line-height: 1.2;">Seus Dados</h2>

                        <form action="../Controller/ClienteController.php?acao=atualizar" method="POST">
                                <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                                    <div class="form-group">
                                        <label for="input-nome">Nome Completo</label>
                                        <input type="text" name="nome" class="input"
                                            value="<?php echo htmlspecialchars($dadosCliente['nome'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="input-email">Email</label>
                                    <input type="email" name="email" class="input"
                                        value="<?php echo htmlspecialchars($dadosCliente['email'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="input-telefone">Telefone</label>
                                    <input type="text" name="telefone" class="input input-telefone"
                                        value="<?php echo htmlspecialchars($dadosCliente['telefone'] ?? ''); ?>"
                                        placeholder="(11) 90000-0000">
                                </div>

                                <div class="form-group">
                                    <label for="input-senha">Nova Senha</label>
                                    <div style="position: relative;">
                                        <input type="password" name="senha" class="input"
                                            placeholder="Deixe em branco para manter a atual"
                                            style="padding-right: 2.5rem;">
                                        <button type="button" class="toggle-password"
                                            style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; color: var(--muted); padding: 5px; display: flex; align-items: center; justify-content: center;">
                                            <i class="ph ph-eye" style="font-size: 1.2rem;"></i>
                                        </button>
                                    </div>
                                </div>

                                <div style="margin-top: 1rem;">
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                </div>
                            </form>
                    </div>
                </div>

                <!-- ABA 2: Perfil Clínico -->
                <div id="clinical-profile" class="form-content">
                    <div class="profile-card">
                        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; line-height: 1.2;">Perfil Clínico</h2>
                        <p style="color: var(--muted); margin-bottom: 1.5rem;">Preencha seus dados para habilitarmos
                            as recomendações de alimentação.</p>

                        <form action="../Controller/PerfilClinicoController.php?acao=salvar" method="POST">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div class="form-group">
                                        <label>Peso (kg)</label>
                                        <input type="number" step="0.1" name="peso" class="input" placeholder="Ex: 70.5"
                                            value="<?php echo htmlspecialchars($peso); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Altura (m)</label>
                                        <input type="number" step="0.01" name="altura" class="input"
                                            placeholder="Ex: 1.75" value="<?php echo htmlspecialchars($altura); ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Restrição Alimentar</label>
                                    <select name="restricao" class="input">
                                        <option value="" <?php echo $restricao == '' ? 'selected' : ''; ?>>Nenhuma
                                        </option>
                                        <option value="intolerancia_lactose" <?php echo $restricao == 'intolerancia_lactose' ? 'selected' : ''; ?>>Intolerante à
                                            lactose</option>
                                        <option value="celiaco" <?php echo $restricao == 'celiaco' ? 'selected' : ''; ?>>
                                            Celíaco (Zero Glúten)</option>
                                        <option value="vegano" <?php echo $restricao == 'vegano' ? 'selected' : ''; ?>>
                                            Vegano</option>
                                        <option value="vegetariano" <?php echo $restricao == 'vegetariano' ? 'selected' : ''; ?>>Vegetariano</option>
                                    </select>
                                </div>

                                <!-- BLOCO ALERGIAS -->
                                <div class="form-group">
                                    <label>Alergias (Marque se possuir)</label>
                                    <div
                                        style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                        <label
                                            style="display: flex; align-items: center; gap: 0.5rem; font-weight: normal; cursor: pointer;">
                                            <input type="checkbox" name="alergias[]" value="amendoim" <?php echo in_array('amendoim', $alergias) ? 'checked' : ''; ?>> Amendoim /
                                            Castanhas
                                        </label>
                                        <label
                                            style="display: flex; align-items: center; gap: 0.5rem; font-weight: normal; cursor: pointer;">
                                            <input type="checkbox" name="alergias[]" value="frutos_mar" <?php echo in_array('frutos_mar', $alergias) ? 'checked' : ''; ?>> Frutos do Mar
                                        </label>
                                        <label
                                            style="display: flex; align-items: center; gap: 0.5rem; font-weight: normal; cursor: pointer;">
                                            <input type="checkbox" name="alergias[]" value="soja" <?php echo in_array('soja', $alergias) ? 'checked' : ''; ?>> Soja
                                        </label>
                                        <label
                                            style="display: flex; align-items: center; gap: 0.5rem; font-weight: normal; cursor: pointer;">
                                            <input type="checkbox" name="alergias[]" value="ovo" <?php echo in_array('ovo', $alergias) ? 'checked' : ''; ?>> Ovo
                                        </label>
                                    </div>
                                </div>

                                <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                                    <button type="submit" class="btn btn-primary">Salvar Perfil Clínico</button>

                                    <!-- Botão de exclusão (Apenas aparece se o usuário de fato já gravou perfil antes!) -->
                                    <?php if ($perfilDb): ?>
                                        <a href="../Controller/PerfilClinicoController.php?acao=excluir"
                                            onclick="return confirm('Tem certeza que deseja apagar os dados do seu perfil clínico?');"
                                            class="btn"
                                            style="background:var(--muted); color:white; border:none; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration:none;">Deletar
                                            Perfil</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                    </div>
                </div>

                <!-- ABA 3: Meu Avatar (Gêmeo Digital) -->
                <div id="digital-twin" class="form-content">
                    <div class="profile-card" style="text-align: center; position: relative; overflow: hidden;">
                        
                        <!-- Padrão tecnológico de fundo -->
                        <div style="position: absolute; inset: 0; background-image: radial-gradient(hsla(150, 20%, 60%, 0.15) 1px, transparent 1px); background-size: 24px 24px; opacity: 0.5; z-index: 0; pointer-events: none;"></div>
                        
                        <div style="position: relative; z-index: 1;">
                            <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem; line-height: 1.2; color: var(--foreground);">Seu Gêmeo Digital</h2>
                            <p style="color: var(--muted); margin-bottom: 2rem;">Uma representação visual do seu perfil clínico de saúde.</p>
                            
                            <!-- Visual do Boneco -->
                            <div style="background: linear-gradient(135deg, white 0%, var(--green-mist) 100%); border-radius: 1.5rem; padding: 3rem 1rem; border: 1px solid rgba(0,0,0,0.05); margin-bottom: 2rem; box-shadow: inset 0 0 20px rgba(0,0,0,0.02);">
                                
                                <div style="position: relative; width: 140px; height: 180px; margin: 0 auto; transition: transform 0.3s; cursor: pointer;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    <!-- Sombra do boneco -->
                                    <div style="position: absolute; bottom: -15px; left: 20px; width: 100px; height: 15px; background: rgba(0,0,0,0.05); border-radius: 50%; filter: blur(3px);"></div>
                                    
                                    <?php 
                                        // Lógica visual básica baseada no IMC
                                        $corpoLargura = "100px";
                                        $corpoCor = "var(--primary)";
                                        $rostoCor = "#fcd34d";
                                        $expressao = "M35,65 Q50,80 65,65"; // Sorriso padrão
                                        
                                        if(!empty($peso) && !empty($altura)) {
                                            $imc = $peso / ($altura * $altura);
                                            if($imc < 18.5) {
                                                $corpoLargura = "80px"; // Magrinho
                                                $corpoCor = "#38bdf8"; // Azul claro
                                                $expressao = "M35,65 Q50,80 65,65"; // Sorriso
                                            } elseif($imc >= 25 && $imc < 30) {
                                                $corpoLargura = "120px"; // Gordinho
                                                $corpoCor = "#f59e0b"; // Laranja
                                                $expressao = "M35,68 Q50,68 65,68"; // Neutro
                                            } elseif($imc >= 30) {
                                                $corpoLargura = "130px"; // Obeso
                                                $corpoCor = "#ef4444"; // Vermelho
                                                $expressao = "M35,70 Q50,55 65,70"; // Triste
                                            }
                                        } else {
                                            $corpoCor = "#cbd5e1"; // Cinza (Sem dados)
                                            $rostoCor = "#e2e8f0";
                                            $expressao = "M35,68 Q50,68 65,68"; // Neutro
                                        }
                                    ?>

                                    <!-- Cabeça SVG -->
                                    <svg viewBox="0 0 100 100" style="width: 70px; height: 70px; position: absolute; top: 0; left: 35px; z-index: 2; overflow: visible;">
                                        <circle cx="50" cy="50" r="45" fill="<?php echo $rostoCor; ?>" stroke="var(--foreground)" stroke-width="4"/>
                                        <!-- Olhos -->
                                        <circle cx="35" cy="45" r="5" fill="var(--foreground)"/>
                                        <circle cx="65" cy="45" r="5" fill="var(--foreground)"/>
                                        <!-- Boca dinâmica -->
                                        <path d="<?php echo $expressao; ?>" fill="none" stroke="var(--foreground)" stroke-width="4" stroke-linecap="round"/>
                                    </svg>

                                    <!-- Corpo SVG -->
                                    <svg viewBox="0 0 100 100" style="width: <?php echo $corpoLargura; ?>; height: 100px; position: absolute; bottom: 0; left: calc(50% - <?php echo (int)$corpoLargura/2; ?>px); z-index: 1; overflow: visible;" preserveAspectRatio="none">
                                        <rect x="5" y="10" width="90" height="90" rx="30" fill="<?php echo $corpoCor; ?>" stroke="var(--foreground)" stroke-width="4"/>
                                        <!-- Logo Peito -->
                                        <circle cx="50" cy="50" r="15" fill="white" stroke="var(--foreground)" stroke-width="3"/>
                                        <text x="50" y="55" font-family="Outfit" font-weight="900" font-size="16" fill="var(--foreground)" text-anchor="middle">N</text>
                                    </svg>
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: left;">
                                <div style="background: rgba(0,0,0,0.02); padding: 1.5rem; border-radius: 1rem;">
                                    <h4 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); margin-bottom: 0.5rem;">Status Físico</h4>
                                    <p style="font-weight: 700; color: var(--foreground); font-size: 1.1rem;">
                                        <?php 
                                            if(!empty($peso) && !empty($altura)) {
                                                echo number_format($imc, 1, ',', '.') . ' IMC';
                                            } else {
                                                echo 'Dados Incompletos';
                                            }
                                        ?>
                                    </p>
                                </div>
                                <div style="background: rgba(0,0,0,0.02); padding: 1.5rem; border-radius: 1rem;">
                                    <h4 style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); margin-bottom: 0.5rem;">Restrições Ativas</h4>
                                    <p style="font-weight: 700; color: var(--foreground); font-size: 1.1rem;">
                                        <?php 
                                            $totalRestricoes = (!empty($restricao) ? 1 : 0) + (is_array($alergias) ? count($alergias) : 0);
                                            echo $totalRestricoes > 0 ? $totalRestricoes . ' cadastradas' : 'Nenhuma';
                                        ?>
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </section>
        </div>
    </main>

    <script src="../script.js"></script>
</body>

</html>