<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../Models/Pedido.php';
require_once __DIR__ . '/../../Models/Cliente.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: ../cadastro.php");
    exit;
}

$pedidoId = $_GET['id'] ?? null;
if (!$pedidoId) {
    echo "Pedido inválido.";
    exit;
}

$pedido = Pedido::buscarPorId($pedidoId);
if (!$pedido || $pedido['cliente_id'] != $_SESSION['cliente_id']) {
    echo "Pedido não encontrado ou sem permissão.";
    exit;
}

$cliente = Cliente::buscarPorId($_SESSION['cliente_id']);
$itens = json_decode($pedido['itens'] ?? '[]', true) ?: [];
$dadosPagamento = Pedido::descriptografarDadosPagamento($pedido['dados_pagamento']);

// Gera uma chave de acesso aleatória simulada de 44 dígitos
$chaveAcesso = '';
for ($i=0; $i<44; $i++) {
    $chaveAcesso .= mt_rand(0, 9);
}

// Formatação da data
$dataEmissao = date('d/m/Y H:i:s', strtotime($pedido['created_at']));
$numeroRecibo = str_pad($pedido['id'], 6, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Fiscal Eletrônica - Pedido #<?php echo $numeroRecibo; ?></title>
    <!-- Fonte Monoespaçada para simular cupom -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body {
            background-color: #f3f4f6;
            margin: 0;
            padding: 2rem 1rem;
            font-family: 'Courier Prime', monospace;
            color: #000;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .receipt-container {
            background-color: #fdfae3; /* Amarelinho de bobina térmica */
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            position: relative;
        }
        
        /* Borda serrilhada superior e inferior simulando corte de papel */
        .receipt-container::before, .receipt-container::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            height: 10px;
            background-size: 20px 20px;
        }
        .receipt-container::before {
            top: -10px;
            background-image: radial-gradient(circle at 10px 0, transparent 10px, #fdfae3 11px);
        }
        .receipt-container::after {
            bottom: -10px;
            background-image: radial-gradient(circle at 10px 10px, transparent 10px, #fdfae3 11px);
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        
        h1, h2, p { margin: 0; padding: 0; }
        
        .logo-text { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; }
        
        .divider {
            border-top: 1px dashed #333;
            margin: 1rem 0;
        }

        .info-line {
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .items-table {
            width: 100%;
            font-size: 0.85rem;
            border-collapse: collapse;
        }
        .items-table th {
            text-align: left;
            border-bottom: 1px dashed #333;
            padding-bottom: 0.25rem;
            font-weight: 700;
        }
        .items-table td {
            padding: 0.25rem 0;
            vertical-align: top;
        }
        
        .item-name {
            display: block;
            width: 160px;
        }

        .totals {
            width: 100%;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        .totals td { padding: 0.15rem 0; }
        .totals .bold td { font-weight: 700; font-size: 1rem; }

        .qr-placeholder {
            width: 120px;
            height: 120px;
            margin: 1rem auto;
            border: 1px solid #333;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .chave-acesso {
            font-size: 0.7rem;
            word-break: break-all;
            text-align: center;
            margin-top: 0.5rem;
            line-height: 1.2;
        }

        .actions {
            margin-top: 3rem;
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-family: 'Courier Prime', monospace;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid #10b981;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
        }
        
        .btn-print {
            background-color: #10b981;
            color: #fff;
        }
        .btn-back {
            background-color: transparent;
            color: #10b981;
        }

        /* Oculta botões e background extra na hora de imprimir */
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                border: none;
                max-width: 100%;
            }
            .receipt-container::before, .receipt-container::after {
                display: none;
            }
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="receipt-container" id="receipt">
        <div class="text-center">
            <div class="logo-text">NURA ALIMENTACAO SAUDAVEL</div>
            <div class="info-line">CNPJ: 45.123.456/0001-99</div>
            <div class="info-line">Rua Fictícia de Exemplo, 123 - Centro</div>
            <div class="info-line">São Paulo - SP | CEP: 01000-000</div>
            <div class="info-line">Documento Auxiliar da Nota Fiscal de Consumidor Eletrônica</div>
        </div>

        <div class="divider"></div>

        <div class="info-line">
            <b>NFC-e Nº:</b> <?php echo $numeroRecibo; ?> <br>
            <b>Emissão:</b> <?php echo $dataEmissao; ?> <br>
            <b>Consumidor:</b> <?php echo htmlspecialchars($cliente['nome']); ?> <br>
            <b>CPF/CNPJ:</b> 000.***.***-00 <br> <!-- Simulando anonimização LGPD -->
        </div>

        <div class="divider"></div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-left">Cód</th>
                    <th class="text-left">Descrição</th>
                    <th class="text-center">Qtd</th>
                    <th class="text-right">Vl.Un</th>
                    <th class="text-right">Vl.Tot</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                foreach ($itens as $item): 
                    $vTot = $item['preco'] * $item['qtd'];
                ?>
                <tr>
                    <td><?php echo str_pad($item['id'], 3, '0', STR_PAD_LEFT); ?></td>
                    <td><span class="item-name"><?php echo htmlspecialchars($item['nome']); ?></span></td>
                    <td class="text-center"><?php echo $item['qtd']; ?></td>
                    <td class="text-right"><?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                    <td class="text-right"><?php echo number_format($vTot, 2, ',', '.'); ?></td>
                </tr>
                <?php $i++; endforeach; ?>
            </tbody>
        </table>

        <div class="divider"></div>

        <table class="totals">
            <tr>
                <td>QTD. TOTAL DE ITENS</td>
                <td class="text-right"><?php echo array_sum(array_column($itens, 'qtd')); ?></td>
            </tr>
            <tr>
                <td>VALOR TOTAL PRODUTOS</td>
                <td class="text-right">R$ <?php echo number_format($pedido['subtotal'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>FRETE/ENTREGA</td>
                <td class="text-right">R$ <?php echo number_format($pedido['frete'], 2, ',', '.'); ?></td>
            </tr>
            <?php if (isset($dadosPagamento['juros']) && $dadosPagamento['juros'] > 0): ?>
            <tr>
                <td>JUROS PARCELAMENTO (<?php echo $dadosPagamento['parcelas']; ?>x)</td>
                <td class="text-right">R$ <?php echo number_format($dadosPagamento['juros'], 2, ',', '.'); ?></td>
            </tr>
            <?php endif; ?>
            <tr class="bold">
                <td>VALOR A PAGAR</td>
                <td class="text-right">R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
            </tr>
        </table>

        <div class="divider"></div>

        <table class="totals">
            <tr>
                <td>FORMA DE PAGAMENTO</td>
                <td class="text-right">VALOR PAGO</td>
            </tr>
            <tr>
                <td>
                    <?php 
                    echo htmlspecialchars($pedido['metodo_pagamento']); 
                    if (isset($dadosPagamento['parcelas']) && $pedido['metodo_pagamento'] === 'Crédito') {
                        echo ' (' . $dadosPagamento['parcelas'] . 'x)';
                    }
                    ?>
                </td>
                <td class="text-right">R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="text-center info-line">
            <b>Consulte pela Chave de Acesso em</b><br>
            http://www.nfce.fazenda.sp.gov.br/consulta
            
            <div class="chave-acesso">
                <?php echo chunk_split($chaveAcesso, 4, ' '); ?>
            </div>
        </div>

        <div class="qr-placeholder">
            <!-- QR Code falso gerado via api QuickChart para simbolizar a nota -->
            <img src="https://quickchart.io/qr?text=<?php echo $chaveAcesso; ?>&size=120" alt="QR Code NFC-e">
        </div>
        
        <div class="text-center info-line" style="margin-top:1rem;">
            Obrigado pela preferência!<br>
            Acesse: nura.com.br
        </div>
    </div>

    <div class="actions">
        <a href="../pedidos.php" class="btn btn-back"><i class="ph ph-arrow-left"></i> Voltar</a>
        <button onclick="window.print()" class="btn btn-print"><i class="ph ph-printer"></i> Imprimir Nota Fiscal</button>
    </div>

</body>
</html>
