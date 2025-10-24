<?php
require_once __DIR__ . '/../Controller/AdministradorController.php';
require_once __DIR__ . '/../Controller/ClienteController.php';
require_once __DIR__ . '/../Controller/ProdutoController.php';
require_once __DIR__ . '/../Controller/PedidoController.php';

$admController = new AdministradorController();
$clienteController = new ClienteController();
$produtoController = new ProdutoController();
$pedidoController = new PedidoController();

echo "<h1>Administrador:</h1>";
$adm = $admController->exibir();
$adm->ExibirInfo();

echo "<h1>Cliente:</h1>";
$cliente = $clienteController->exibir();
$cliente->ExibirInfo();

echo "<h1>Produto:</h1>";
$produto = $produtoController->exibir();
$produto->ExibirInfo();

echo "<h1>Pedido:</h1>";
$pedido = $pedidoController->exibir();
$pedido->ExibirInfo();
?>
