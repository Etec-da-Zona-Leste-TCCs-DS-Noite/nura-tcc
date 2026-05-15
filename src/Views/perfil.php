<?php
session_start();
require_once __DIR__ . '/../Models/Cliente.php';
require_once __DIR__ . '/../Models/PerfilClinico.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: cadastro.php");
    exit;
}

$dadosCliente = Cliente::buscarPorId($_SESSION['cliente_id']);

$mostrarBoasVindasConta = !empty($_SESSION['conta_nova']);
$toastBemVindoVolta = !empty($_SESSION['login_recente']);
if ($mostrarBoasVindasConta) {
    unset($_SESSION['conta_nova']);
}
if ($toastBemVindoVolta) {
    unset($_SESSION['login_recente']);
}
$primeiroNomeConta = htmlspecialchars(explode(' ', trim($dadosCliente['nome'] ?? 'Cliente'))[0], ENT_QUOTES, 'UTF-8');

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
            <a href="index.php" class="logo" aria-label="Nura — Início">
                <img class="logo-img" src="../assets/img/NURA_logo.png" alt="">
            </a>
            <nav class="nav-links" aria-label="Principal">
                <a href="index.php">Início</a>
                <a href="produtos.php">Produtos</a>
                <a href="carrinho.php">Carrinho</a>
            </nav>

            <div class="header-actions">
                <div class="header-user-chip">
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
        <h1 class="perfil-page-title">Minha conta</h1>

        <?php if ($mostrarBoasVindasConta): ?>
        <section class="welcome-hero" aria-labelledby="welcome-heading">
            <div class="welcome-hero__glow" aria-hidden="true"></div>
            <div class="welcome-hero__inner">
                <div class="welcome-hero__visual" aria-hidden="true">
                    <i class="ph-fill ph-confetti"></i>
                </div>
                <div class="welcome-hero__content">
                    <p class="welcome-hero__eyebrow">Conta criada com sucesso</p>
                    <h2 id="welcome-heading" class="welcome-hero__title">Bem-vindo(a), <?php echo $primeiroNomeConta; ?>!</h2>
                    <p class="welcome-hero__text">Sua conta Nura está pronta. Explore o cardápio, personalize seu perfil clínico para alertas inteligentes e monte seu pedido com segurança.</p>
                    <div class="welcome-hero__actions">
                        <a href="produtos.php" class="btn btn-primary"><i class="ph-bold ph-storefront" aria-hidden="true"></i> Ver cardápio</a>
                        <a href="carrinho.php" class="btn btn-outline"><i class="ph-bold ph-shopping-cart" aria-hidden="true"></i> Carrinho</a>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <div class="profile-grid">
            <aside class="profile-sidebar">
                <nav class="sidebar-menu" aria-label="Seções da conta">
                    <button type="button" class="sidebar-link tab-btn active" data-target="personal-data"><i class="ph ph-user" aria-hidden="true"></i>
                        Dados pessoais</button>

                    <button type="button" class="sidebar-link tab-btn" data-target="clinical-profile"><i class="ph ph-heartbeat" aria-hidden="true"></i>
                        Perfil clínico</button>

                    <button type="button" class="sidebar-link tab-btn" data-target="digital-twin"><i class="ph ph-user-circle" aria-hidden="true"></i>
                        Meu avatar</button>

                    <a href="pedidos.php" class="sidebar-link">
                        <i class="ph ph-receipt" aria-hidden="true"></i> Meus pedidos</a>

                    <button type="button" id="open-delete-account-modal" class="sidebar-link sidebar-link--danger sidebar-link--button">
                        <i class="ph ph-trash" aria-hidden="true"></i> Excluir conta
                    </button>
                    <a href="../Controller/ClienteController.php?acao=sair" class="sidebar-link">
                        <i class="ph ph-sign-out" aria-hidden="true"></i> Sair</a>
                </nav>
            </aside>

            <section class="profile-content">

                <!-- ABA 1: Dados Pessoais -->
                <div id="personal-data" class="form-content active">
                    <div class="profile-card">
                        <h2>Seus dados</h2>

                        <form action="../Controller/ClienteController.php?acao=atualizar" method="POST">
                                <div class="form-grid-1">
                                    <div class="form-group">
                                        <label for="input-nome">Nome completo</label>
                                        <input id="input-nome" type="text" name="nome" class="input"
                                            value="<?php echo htmlspecialchars($dadosCliente['nome'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="input-email">Email</label>
                                    <input id="input-email" type="email" name="email" class="input"
                                        value="<?php echo htmlspecialchars($dadosCliente['email'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="input-telefone">Telefone</label>
                                    <input id="input-telefone" type="text" name="telefone" class="input input-telefone"
                                        value="<?php echo htmlspecialchars($dadosCliente['telefone'] ?? ''); ?>"
                                        placeholder="(11) 90000-0000">
                                </div>

                                <div class="form-group">
                                    <label for="input-senha">Nova senha</label>
                                    <div class="password-field">
                                        <input id="input-senha" type="password" name="senha" class="input"
                                            placeholder="Deixe em branco para manter a atual" autocomplete="new-password">
                                        <button type="button" class="toggle-password" aria-label="Mostrar ou ocultar senha">
                                            <i class="ph ph-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="profile-actions">
                                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                                </div>
                            </form>
                    </div>
                </div>

                <!-- ABA 2: Perfil Clínico -->
                <div id="clinical-profile" class="form-content">
                    <div class="profile-card">
                        <h2>Perfil clínico</h2>
                        <p class="lead">Preencha seus dados para habilitarmos as recomendações de alimentação.</p>

                        <form action="../Controller/PerfilClinicoController.php?acao=salvar" method="POST">
                                <div class="form-grid-2">
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
                                    <label>Alergias (marque se possuir)</label>
                                    <div class="checkbox-list">
                                        <label>
                                            <input type="checkbox" name="alergias[]" value="amendoim" <?php echo in_array('amendoim', $alergias) ? 'checked' : ''; ?>>
                                            Amendoim / castanhas
                                        </label>
                                        <label>
                                            <input type="checkbox" name="alergias[]" value="frutos_mar" <?php echo in_array('frutos_mar', $alergias) ? 'checked' : ''; ?>>
                                            Frutos do mar
                                        </label>
                                        <label>
                                            <input type="checkbox" name="alergias[]" value="soja" <?php echo in_array('soja', $alergias) ? 'checked' : ''; ?>>
                                            Soja
                                        </label>
                                        <label>
                                            <input type="checkbox" name="alergias[]" value="ovo" <?php echo in_array('ovo', $alergias) ? 'checked' : ''; ?>>
                                            Ovo
                                        </label>
                                    </div>
                                </div>

                                <div class="profile-actions">
                                    <button type="submit" class="btn btn-primary">Salvar perfil clínico</button>

                                    <?php if ($perfilDb): ?>
                                        <button type="button" id="clinical-delete-trigger" class="btn-muted-solid"
                                            data-href="../Controller/PerfilClinicoController.php?acao=excluir">Deletar perfil</button>
                                    <?php endif; ?>
                                </div>
                            </form>
                    </div>
                </div>

                <!-- ABA 3: Meu Avatar (Gêmeo Digital) -->
                <div id="digital-twin" class="form-content">
                    <div class="profile-card digital-twin-card">
                        <div class="digital-twin-bg" aria-hidden="true"></div>

                        <div class="digital-twin-inner">
                            <h2>Seu gêmeo digital</h2>
                            <p class="lead" style="margin-bottom: 2rem;">Uma representação visual do seu perfil clínico de saúde.</p>

                            <div class="avatar-stage">

                                <div class="digital-twin-mascot">
                                    <div class="digital-twin-mascot-shadow" aria-hidden="true"></div>
                                    
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

                            <div class="avatar-stat-grid">
                                <div class="avatar-stat">
                                    <h4>Status físico</h4>
                                    <p>
                                        <?php
                                            if (!empty($peso) && !empty($altura)) {
                                                echo number_format($imc, 1, ',', '.') . ' IMC';
                                            } else {
                                                echo 'Dados incompletos';
                                            }
                                        ?>
                                    </p>
                                </div>
                                <div class="avatar-stat">
                                    <h4>Restrições ativas</h4>
                                    <p>
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

    <!-- Exclusão de conta — motivo (UX) -->
    <div class="modal-overlay" id="deleteAccountModal" role="dialog" aria-modal="true" aria-labelledby="delete-account-title">
        <div class="custom-modal custom-modal--wide delete-account-modal">
            <div class="modal-icon" style="background: var(--danger-soft); color: var(--danger);">
                <i class="ph-fill ph-warning-circle" aria-hidden="true"></i>
            </div>
            <h2 class="modal-title" id="delete-account-title">Encerrar sua conta</h2>
            <p class="modal-text">Esta ação é permanente: seus dados e pedidos associados a esta conta serão apagados. Para prosseguir, diga-nos o motivo.</p>
            <form id="delete-account-form">
                <fieldset class="delete-account-fieldset">
                    <legend class="delete-account-legend">Motivo da saída</legend>
                    <label class="delete-reason-option">
                        <input type="radio" name="motivo" value="nao_uso">
                        <span>Não uso mais o serviço</span>
                    </label>
                    <label class="delete-reason-option">
                        <input type="radio" name="motivo" value="privacidade">
                        <span>Preocupações com privacidade ou dados</span>
                    </label>
                    <label class="delete-reason-option">
                        <input type="radio" name="motivo" value="outra_opcao">
                        <span>Encontrei outra opção melhor</span>
                    </label>
                    <label class="delete-reason-option">
                        <input type="radio" name="motivo" value="experiencia">
                        <span>Experiência ou suporte insatisfatórios</span>
                    </label>
                    <label class="delete-reason-option">
                        <input type="radio" name="motivo" value="preco">
                        <span>Preço ou entrega</span>
                    </label>
                    <label class="delete-reason-option">
                        <input type="radio" name="motivo" value="outro">
                        <span>Outro motivo</span>
                    </label>
                    <div id="delete-account-detalhe-wrap" class="delete-account-detalhe-wrap">
                        <label for="delete-account-detalhe" class="delete-account-legend" style="margin-top: 0.75rem;">Descreva (obrigatório se “Outro”)</label>
                        <textarea id="delete-account-detalhe" name="detalhe" class="delete-account-detalhe" rows="3" maxlength="500" placeholder="Conte em uma frase..."></textarea>
                    </div>
                </fieldset>
                <div class="modal-actions">
                    <button type="submit" class="btn btn-danger-solid btn-full">Excluir minha conta</button>
                    <button type="button" class="btn btn-secondary btn-full" id="cancel-delete-account">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="clinicalDeleteModal" role="dialog" aria-modal="true" aria-labelledby="clinical-delete-title">
        <div class="custom-modal">
            <div class="modal-icon" style="background: var(--danger-soft); color: var(--danger);">
                <i class="ph-fill ph-heartbeat" aria-hidden="true"></i>
            </div>
            <h2 class="modal-title" id="clinical-delete-title">Apagar perfil clínico?</h2>
            <p class="modal-text">Suas alergias, restrições e medidas usadas no cardápio serão removidas do sistema. Você pode cadastrar de novo depois.</p>
            <div class="modal-actions">
                <button type="button" class="btn btn-danger-solid btn-full" id="confirm-clinical-delete">Sim, apagar dados clínicos</button>
                <button type="button" class="btn btn-secondary btn-full" id="cancel-clinical-delete">Não, manter</button>
            </div>
        </div>
    </div>

    <?php if (!empty($toastBemVindoVolta)): ?>
    <script>
        window.__NURA_SESSION_TOAST__ = <?php echo json_encode([
            'msg' => 'Que bom ter você de volta, ' . explode(' ', trim($dadosCliente['nome'] ?? 'Cliente'))[0] . '!',
            'type' => 'success',
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE); ?>;
    </script>
    <?php endif; ?>
    <script src="../script.js"></script>
</body>

</html>