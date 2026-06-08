<?php
// api/chat.php

// Ativa a sessão caso o Controller precise de dados do cliente logado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Importa o arquivo da Inteligência Artificial
require_once __DIR__ . '/../Controller/IaController.php';

// Instancia e roda o processamento
$iaController = new IaController();
$iaController->processarChat();