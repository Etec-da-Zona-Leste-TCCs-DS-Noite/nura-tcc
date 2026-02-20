<?php
session_start();

// Inicializa o carrinho
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$acao = $_GET['acao'] ?? null;
// Verifica se a chamada veio do Javascript (AJAX)
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';

// --- ADICIONAR AO CARRINHO ---
if ($acao == 'adicionar' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $preco = (float) $_POST['preco'];
    $img = $_POST['img'];

    // Adiciona ou incrementa
    if (isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id]['qtd']++;
    } else {
        $_SESSION['carrinho'][$id] = [
            'id' => $id,
            'nome' => $nome,
            'preco' => $preco,
            'img' => $img,
            'qtd' => 1
        ];
    }

    // SE FOR JAVASCRIPT: Retorna JSON e não redireciona
    if ($isAjax) {
        $totalItens = 0;
        foreach ($_SESSION['carrinho'] as $item) {
            $totalItens += $item['qtd'];
        }
        // Resposta para o JS
        header('Content-Type: application/json');
        echo json_encode(['sucesso' => true, 'novaQtd' => $totalItens]);
        exit;
    }

    // SE NÃO FOR JS (Fallback): Redireciona para a página anterior
    $origem = $_SERVER['HTTP_REFERER'] ?? 'produtos.php';
    header("Location: $origem");
    exit;
}

// --- REMOVER ---
if ($acao == 'remover' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if (isset($_SESSION['carrinho'][$id]))
        unset($_SESSION['carrinho'][$id]);
    header("Location: carrinho.php");
    exit;
}

// --- ATUALIZAR ---
if ($acao == 'atualizar' && isset($_GET['id']) && isset($_GET['qtd'])) {
    $id = $_GET['id'];
    $novaQtd = (int) $_GET['qtd'];
    if ($novaQtd <= 0)
        unset($_SESSION['carrinho'][$id]);
    elseif (isset($_SESSION['carrinho'][$id]))
        $_SESSION['carrinho'][$id]['qtd'] = $novaQtd;
    header("Location: carrinho.php");
    exit;
}

header("Location: produtos.php");
exit;
?>