<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/Pedido.php';

$acao = $_GET['acao'] ?? '';

class PedidoController
{
    public function finalizar()
    {
        if (!isset($_SESSION['cliente_id'])) {
            header("Location: ../Views/cadastro.php?nura_flash=" . urlencode("Faça login para realizar pedidos.") . "&nura_ft=info");
            exit;
        }

        $carrinho = $_SESSION['carrinho'] ?? [];
        if (empty($carrinho)) {
            header("Location: ../Views/carrinho.php?nura_flash=" . urlencode("Seu carrinho está vazio.") . "&nura_ft=error");
            exit;
        }

        $subtotalForm = floatval($_POST['subtotal'] ?? 0);
        $freteForm = floatval($_POST['frete'] ?? 0);
        $total = $subtotalForm + $freteForm;

        $enderecoBase = $_POST['endereco'] ?? '';
        $numero = $_POST['numero'] ?? '';
        $complemento = $_POST['complemento'] ?? '';
        $enderecoCompleto = trim("$enderecoBase, $numero" . ($complemento ? " - $complemento" : ''));

        $metodoPagamento = $_POST['metodo_pagamento'] ?? 'PIX';
        
        // Simulação de agrupamento de dados do cartão apenas para criptografar
        $dadosPagamentoStr = '';
        if ($metodoPagamento === 'Crédito' || $metodoPagamento === 'Débito') {
            $cartaoNum = $_POST['cartao_numero'] ?? '';
            $cartaoNome = $_POST['cartao_nome'] ?? '';
            $cartaoVal = $_POST['cartao_validade'] ?? '';
            $cartaoCvv = $_POST['cartao_cvv'] ?? '';
            $parcelas = $_POST['parcelas'] ?? '1';
            
            // Mascarando para não salvar tudo, mantendo apenas os 4 últimos dígitos
            $cartaoMascarado = str_pad(substr($cartaoNum, -4), 16, '*', STR_PAD_LEFT);
            $dadosPagamentoStr = json_encode([
                'nome' => $cartaoNome,
                'cartao' => $cartaoMascarado,
                'validade' => $cartaoVal,
                'cvv' => '***',
                'parcelas' => $parcelas
            ]);
        }

        $pedido = new Pedido();
        $pedido->setClienteId($_SESSION['cliente_id']);
        $pedido->setSubtotal($subtotalForm);
        $pedido->setFrete($freteForm);
        $pedido->setTotal($total);
        $pedido->setEndereco($enderecoCompleto);
        $pedido->setMetodoPagamento($metodoPagamento);
        $pedido->setDadosPagamento($dadosPagamentoStr);
        $pedido->setItens($carrinho);
        $pedido->setStatus('Pagamento Pendente');

        $pedidoId = $pedido->salvar();

        if ($pedidoId) {
            unset($_SESSION['carrinho']);
            header("Location: ../Views/Checkout/processando_pagamento.php?id=" . $pedidoId);
            exit;
        } else {
            header("Location: ../Views/Checkout/checkout.php?nura_flash=" . urlencode("Erro ao criar pedido.") . "&nura_ft=error");
            exit;
        }
    }

    public function aprovarPagamento()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            // Em um sistema real, validaria se o usuário tem permissão para isso, 
            // mas como é TCC/simulação, atualizamos direto.
            Pedido::atualizarStatus($id, 'Em Preparo');
            header("Location: ../Views/pedidos.php?nura_flash=" . urlencode("Pagamento aprovado com sucesso!") . "&nura_ft=success");
            exit;
        }
        header("Location: ../Views/pedidos.php");
        exit;
    }
}

$controller = new PedidoController();
$acao = $_GET['acao'] ?? '';

switch ($acao) {
    case 'finalizar':
        $controller->finalizar();
        break;
    case 'aprovar_pagamento':
        $controller->aprovarPagamento();
        break;
}
?>
