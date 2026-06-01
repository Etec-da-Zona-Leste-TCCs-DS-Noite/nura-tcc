<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Models/Pedido.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../cadastro.php");
    exit;
}

$pedidoId = $_GET['id'] ?? null;
if (!$pedidoId) {
    header("Location: ../pedidos.php");
    exit;
}

$pedido = Pedido::buscarPorId($pedidoId);
if (!$pedido || $pedido['cliente_id'] != $_SESSION['cliente_id']) {
    header("Location: ../pedidos.php");
    exit;
}

$metodo = $pedido['metodo_pagamento'] ?? 'PIX';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nura - Processando Pagamento</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .processing-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            padding: 3rem;
            max-width: 600px;
            margin: 4rem auto;
            text-align: center;
            box-shadow: var(--shadow-md);
        }
        .pix-code {
            width: 250px;
            height: 250px;
            margin: 2rem auto;
            background: #fff;
            padding: 1rem;
            border-radius: 1rem;
            border: 1px solid var(--border-strong);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pix-code img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .timer-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 6px solid var(--primary-soft);
            border-top-color: var(--primary);
            margin: 0 auto 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            font-family: 'Outfit';
            color: var(--primary);
            animation: spin 1s linear infinite;
        }
        .timer-circle span {
            animation: counterSpin 1s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        @keyframes counterSpin { 100% { transform: rotate(-360deg); } }

        .btn-simulate {
            background: transparent;
            border: 1px dashed var(--border-strong);
            color: var(--muted);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            margin-top: 2rem;
            cursor: pointer;
            font-size: 0.85rem;
        }
        .btn-simulate:hover {
            background: var(--surface-hover);
            color: var(--foreground);
        }
    </style>
</head>
<body class="page-carrinho">

    <header>
        <div class="container header-inner">
            <a href="../index.php" class="logo" aria-label="Nura — Início">
                <img class="logo-img" src="../../assets/img/NURA_logo.png" alt="">
            </a>
        </div>
    </header>

    <main class="container">
        <div class="processing-card">
            <?php if ($metodo === 'PIX'): ?>
                <h1 style="font-family: 'Outfit'; font-size: 2rem; margin-bottom: 0.5rem;">Pague com PIX</h1>
                <p style="color: var(--muted); margin-bottom: 1.5rem;">Aponte a câmera do seu celular para o QR Code abaixo para aprovar o seu pedido.</p>
                
                <div class="pix-code">
                    <!-- QR Code Falso gerado pela API do QuickChart para o texto 'NURA-TCC-PIX-SIMULADO' -->
                    <img src="https://quickchart.io/qr?text=NURA-TCC-PIX-SIMULADO&size=250" alt="QR Code PIX">
                </div>

                <div style="background: var(--surface-hover); padding: 1rem; border-radius: var(--radius); margin-bottom: 2rem; font-family: monospace; font-size: 0.9rem; color: var(--muted); word-break: break-all;">
                    00020126580014br.gov.bcb.pix0136nura-tcc-simulacao-chave-aleatoria5204000053039865802BR5915NURA TCC LTDA6009SAO PAULO62070503***6304ABCD
                </div>
            <?php else: ?>
                <h1 style="font-family: 'Outfit'; font-size: 2rem; margin-bottom: 0.5rem;">Processando Cartão</h1>
                <p style="color: var(--muted); margin-bottom: 1.5rem;">Aguardando retorno da operadora do cartão...</p>
            <?php endif; ?>

            <div class="timer-circle">
                <span id="timer-count">15</span>
            </div>
            
            <p style="color: var(--primary); font-weight: 500;" id="status-text">Aguardando pagamento...</p>

            <div style="display: flex; gap: 1rem; justify-content: center;">
                <!-- Botão de simulação escondidinho para testes -->
                <button type="button" class="btn-simulate" id="btn-force-pay"><i class="ph ph-magic-wand"></i> Simular: Pagar Agora</button>
            </div>
        </div>
    </main>

    <script src="../../script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let timeLeft = 15;
            const timerEl = document.getElementById('timer-count');
            const statusText = document.getElementById('status-text');
            const btnForce = document.getElementById('btn-force-pay');
            const pedidoId = <?php echo $pedidoId; ?>;
            
            const interval = setInterval(() => {
                timeLeft--;
                timerEl.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(interval);
                    statusText.textContent = "Tempo esgotado. Redirecionando...";
                    statusText.style.color = "var(--danger)";
                    // Redireciona para pedidos, deixando como 'Pagamento Pendente'
                    setTimeout(() => {
                        window.location.href = '../pedidos.php?nura_flash=' + encodeURIComponent('Tempo de pagamento expirado. Você pode tentar novamente mais tarde.') + '&nura_ft=warning';
                    }, 1500);
                }
            }, 1000);

            // Botão de Forçar Pagamento (Aprovação simulada)
            btnForce.addEventListener('click', () => {
                clearInterval(interval);
                timerEl.textContent = "OK";
                statusText.textContent = "Pagamento aprovado!";
                statusText.style.color = "var(--green-leaf)";
                btnForce.style.display = "none";
                
                // Redireciona para o controller atualizar para Em Preparo
                setTimeout(() => {
                    window.location.href = '../../Controller/PedidoController.php?acao=aprovar_pagamento&id=' + pedidoId;
                }, 1000);
            });
        });
    </script>
</body>
</html>
