<?php
session_start();
$nomeCliente = $_SESSION['cliente_nome'] ?? null;
$carrinho = $_SESSION['carrinho'] ?? [];

if (empty($carrinho)) {
    header("Location: ../carrinho.php");
    exit;
}

if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../cadastro.php?nura_flash=" . urlencode("Faça login para finalizar a compra.") . "&nura_ft=info");
    exit;
}

$subtotal = floatval($_POST['subtotal'] ?? 0);
$frete = floatval($_POST['frete'] ?? 0);
$endereco = $_POST['endereco'] ?? '';
$total = $subtotal + $frete;

// Se não veio do carrinho corretamente com frete, voltar
if ($frete <= 0 && $subtotal > 0) {
    header("Location: ../carrinho.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura - Confirmação de Checkout</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .checkout-grid {
            display: grid;
            grid-template-columns: 2fr 350px; /* Aumentado a proporção da esquerda e diminuído a direita */
            gap: 2.5rem;
            align-items: start;
        }
        @media (max-width: 900px) {
            .checkout-grid { grid-template-columns: 1fr; }
        }
        .container-checkout {
            max-width: 1300px; /* Expande um pouco mais o limite desse container específico */
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        .summary-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            padding: 2rem;
            position: sticky;
            top: 120px;
            box-shadow: var(--shadow-sm);
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            color: var(--muted);
            font-size: 0.95rem;
        }
        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px dashed var(--border-strong);
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--foreground);
            font-family: 'Outfit';
        }
        .input-large {
            padding: 1rem 1.25rem;
            font-size: 1.05rem;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            cursor: pointer;
            transition: 0.3s;
            margin-bottom: 0.75rem;
        }
        .payment-option:hover {
            border-color: var(--primary);
            background: var(--surface-muted);
        }
        .payment-option.active {
            border-color: var(--primary);
            background: var(--primary-soft);
            box-shadow: 0 0 0 1px var(--primary);
        }
        .payment-option input[type="radio"] {
            display: none;
        }
        .payment-form-container {
            display: none;
            padding-top: 1rem;
            animation: fadeIn 0.4s ease;
        }
        .payment-form-container.active {
            display: block;
        }
    </style>
</head>
<body class="page-carrinho">

    <header>
        <div class="container header-inner">
            <a href="../index.php" class="logo" aria-label="Nura — Início">
                <img class="logo-img" src="../../assets/img/NURA_logo.png" alt="">
            </a>
            <nav class="nav-links" aria-label="Principal">
                <a href="../index.php">Início</a>
                <a href="../produtos.php">Produtos</a>
                <a href="../perfil.php">Minha Conta</a>
            </nav>

            <form class="header-search" action="../produtos.php" method="GET">
                <div class="search-input-wrapper">
                    <i class="ph ph-magnifying-glass search-icon" aria-hidden="true"></i>
                    <input type="text" name="busca" placeholder="Buscar pratos..." aria-label="Buscar" required>
                </div>
            </form>

            <div class="header-actions">
                <a href="../perfil.php" class="btn btn-ghost" aria-label="Conta">
                    <?php if ($nomeCliente): ?>
                        <span class="header-user-label">Olá, <?php echo htmlspecialchars(explode(' ', trim($nomeCliente))[0]); ?></span>
                    <?php endif; ?>
                    <i class="ph ph-user header-icon" aria-hidden="true"></i>
                </a>
            </div>
            <button type="button" class="mobile-menu-btn btn btn-ghost" aria-label="Abrir menu">
                <i class="ph ph-list header-icon" aria-hidden="true"></i>
            </button>
        </div>
    </header>

    <main class="container-checkout" style="padding-top: 3rem; padding-bottom: 5rem;">
        <h1 class="cart-page-title" style="margin-bottom: 3rem; font-size: 2.5rem; text-align: left;">Revise e Confirme</h1>

        <div class="checkout-grid">
            <!-- Coluna de Endereço -->
            <div class="profile-card" style="margin: 0; padding: 2.5rem; text-align: left; width: 100%;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                    <div style="width: 48px; height: 48px; background: var(--primary-soft); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="ph-fill ph-map-pin"></i>
                    </div>
                    <div>
                        <h2 style="margin: 0; font-family: 'Outfit'; font-size: 1.5rem;">Detalhes da Entrega</h2>
                        <p style="margin: 0; color: var(--muted); font-size: 0.9rem;">Complete as informações para o envio.</p>
                    </div>
                </div>

                <form id="form-checkout" action="../../Controller/PedidoController.php?acao=finalizar" method="POST">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label>Endereço Calculado</label>
                        <input type="text" name="endereco" class="input input-large" value="<?php echo htmlspecialchars($endereco); ?>" readonly style="background: var(--surface-hover); color: var(--muted);">
                    </div>
                    
                    <div class="checkout-form-grid-2">
                        <div class="form-group">
                            <label>Número *</label>
                            <input type="text" id="input-numero" name="numero" class="input input-large" required placeholder="Ex: 123">
                        </div>
                        <div class="form-group">
                            <label>Complemento</label>
                            <input type="text" name="complemento" class="input input-large" placeholder="Apto, Bloco, etc (Opcional)">
                        </div>
                    </div>
                    
                    <div style="margin-top: 3rem; margin-bottom: 2rem;">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                            <div style="width: 48px; height: 48px; background: var(--primary-soft); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                <i class="ph-fill ph-credit-card"></i>
                            </div>
                            <div>
                                <h2 style="margin: 0; font-family: 'Outfit'; font-size: 1.5rem;">Forma de Pagamento</h2>
                                <p style="margin: 0; color: var(--muted); font-size: 0.9rem;">Escolha como deseja pagar.</p>
                            </div>
                        </div>

                        <label class="payment-option active">
                            <input type="radio" name="metodo_pagamento" value="PIX" checked>
                            <i class="ph ph-qr-code" style="font-size: 1.5rem; color: var(--primary);"></i>
                            <span style="font-weight: 600;">PIX (Aprovação Imediata)</span>
                        </label>
                        
                        <label class="payment-option">
                            <input type="radio" name="metodo_pagamento" value="Crédito">
                            <i class="ph ph-credit-card" style="font-size: 1.5rem; color: var(--primary);"></i>
                            <span style="font-weight: 600;">Cartão de Crédito</span>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="metodo_pagamento" value="Débito">
                            <i class="ph ph-credit-card" style="font-size: 1.5rem; color: var(--primary);"></i>
                            <span style="font-weight: 600;">Cartão de Débito</span>
                        </label>

                        <div id="payment-form-card" class="payment-form-container">
                            <div class="form-group" style="margin-bottom: 1rem;">
                                <label>Número do Cartão (Fictício)</label>
                                <input type="text" name="cartao_numero" id="input-cartao" class="input" placeholder="0000 0000 0000 0000" maxlength="19">
                            </div>
                            <div class="form-group" style="margin-bottom: 1rem;">
                                <label>Nome Impresso no Cartão</label>
                                <input type="text" name="cartao_nome" class="input" placeholder="NOME DO TITULAR">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div class="form-group">
                                    <label>Validade</label>
                                    <input type="text" name="cartao_validade" class="input" placeholder="MM/AA" maxlength="5">
                                </div>
                                <div class="form-group">
                                    <label>CVV</label>
                                    <input type="text" name="cartao_cvv" class="input" placeholder="123" maxlength="4">
                                </div>
                            </div>
                            <div class="form-group" id="parcelas-container" style="display: none;">
                                <label>Parcelamento</label>
                                <select name="parcelas" id="select-parcelas" class="input" style="appearance: auto;">
                                    <!-- Opções geradas via JS -->
                                </select>
                            </div>
                        </div>

                        <div id="payment-pix-msg" style="padding: 1rem; background: var(--primary-soft); border-radius: var(--radius); margin-top: 1rem; color: var(--primary-deep); font-size: 0.9rem;">
                            <i class="ph-bold ph-info"></i> O QR Code do PIX será gerado na próxima tela, após a confirmação.
                        </div>

                    </div>
                    
                    <input type="hidden" name="frete" value="<?php echo $frete; ?>">
                    <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                </form>
            </div>

            <!-- Coluna Resumo -->
            <div class="summary-card">
                <h3 style="font-family: 'Outfit'; margin-bottom: 1.5rem; font-size: 1.25rem;">Seu Pedido</h3>
                <div class="cart-lines" style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem;">
                    <?php foreach ($carrinho as $item): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem;">
                            <span style="display: flex; gap: 0.5rem;"><strong style="color: var(--primary);"><?php echo $item['qtd']; ?>x</strong> <?php echo htmlspecialchars($item['nome']); ?></span>
                            <span style="font-weight: 500;">R$ <?php echo number_format($item['preco'] * $item['qtd'], 2, ',', '.'); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-item">
                    <span>Subtotal</span>
                    <span>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                </div>
                <div class="summary-item">
                    <span>Frete</span>
                    <span style="color: var(--foreground); font-weight: 600;">R$ <?php echo number_format($frete, 2, ',', '.'); ?></span>
                </div>
                <div class="summary-total">
                    <span>Total a Pagar</span>
                    <span style="color: var(--primary);">R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                </div>

                <button type="button" id="btn-submit-checkout" class="btn btn-primary btn-full" style="margin-top: 2rem; padding: 1.2rem; font-size: 1.1rem; border-radius: 1rem;">
                    Confirmar Compra Segura <i class="ph-bold ph-lock-key"></i>
                </button>
            </div>
        </div>
    </main>

    <script src="../../script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnSubmit = document.getElementById('btn-submit-checkout');
            const formCheckout = document.getElementById('form-checkout');
            const inputNumero = document.getElementById('input-numero');
            const paymentOptions = document.querySelectorAll('.payment-option');
            const paymentFormCard = document.getElementById('payment-form-card');
            const paymentPixMsg = document.getElementById('payment-pix-msg');
            const selectParcelas = document.getElementById('select-parcelas');
            const parcelasContainer = document.getElementById('parcelas-container');
            const inputCartao = document.getElementById('input-cartao');
            const total = <?php echo $total; ?>;

            // Mascara simples de cartao
            inputCartao.addEventListener('input', (e) => {
                let v = e.target.value.replace(/\D/g, '');
                v = v.replace(/(\d{4})/g, '$1 ').trim();
                e.target.value = v;
            });

            function updatePaymentUI() {
                const checked = document.querySelector('input[name="metodo_pagamento"]:checked').value;
                
                paymentOptions.forEach(opt => opt.classList.remove('active'));
                document.querySelector(`input[name="metodo_pagamento"]:checked`).closest('.payment-option').classList.add('active');

                if (checked === 'PIX') {
                    paymentFormCard.classList.remove('active');
                    paymentPixMsg.style.display = 'block';
                } else if (checked === 'Crédito' || checked === 'Débito') {
                    paymentPixMsg.style.display = 'none';
                    paymentFormCard.classList.add('active');
                    
                    if (checked === 'Crédito') {
                        parcelasContainer.style.display = 'block';
                        gerarParcelas(total);
                    } else {
                        parcelasContainer.style.display = 'none';
                    }
                }
            }

            function gerarParcelas(valorTotal) {
                selectParcelas.innerHTML = '';
                for (let i = 1; i <= 12; i++) {
                    let jurosText = i <= 3 ? 'sem juros' : 'c/ juros';
                    let fator = i <= 3 ? 1 : Math.pow(1.015, i); // juros simples fictício 1.5% ao mês pós 3x
                    let valorParcela = (valorTotal * fator) / i;
                    let option = document.createElement('option');
                    option.value = i;
                    option.textContent = `${i}x de R$ ${valorParcela.toFixed(2).replace('.', ',')} (${jurosText})`;
                    selectParcelas.appendChild(option);
                }
            }

            paymentOptions.forEach(opt => {
                opt.querySelector('input').addEventListener('change', updatePaymentUI);
            });

            btnSubmit.addEventListener('click', () => {
                if (!inputNumero.value.trim()) {
                    window.NuraNotify.toast('Por favor, preencha o número da residência.', 'error');
                    inputNumero.focus();
                    return;
                }
                
                if (typeof mostrarOverlayGlobal === 'function') {
                    mostrarOverlayGlobal('Processando pagamento seguro...', 'Criptografando seus dados. Por favor, aguarde.', true);
                }
                
                setTimeout(() => {
                    formCheckout.submit();
                }, 2000);
            });
            
            // Inicia interface
            updatePaymentUI();
        });
    </script>
</body>
</html>
