<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$results = [];
$qLower = mb_strtolower($query, 'UTF-8');

// 1. Pesquisa nas páginas estáticas do site
$staticPages = [
    [
        'type' => 'page',
        'title' => 'Sobre a Nura',
        'description' => 'Conheça nossa história, nossa missão e o propósito de levar alimentação saudável para todos.',
        'url' => 'sobre.php',
        'icon' => 'ph-info'
    ],
    [
        'type' => 'page',
        'title' => 'Alimentação Saudável',
        'description' => 'Descubra os benefícios de uma dieta balanceada e aprenda mais sobre nutrição.',
        'url' => 'alimentacao.php',
        'icon' => 'ph-leaf'
    ],
    [
        'type' => 'page',
        'title' => 'Meu Perfil',
        'description' => 'Acesse seus dados, configure seu perfil clínico e veja seu histórico de pedidos.',
        'url' => 'perfil.php',
        'icon' => 'ph-user'
    ],
    [
        'type' => 'page',
        'title' => 'Cardápio Completo',
        'description' => 'Veja todos os nossos bowls, saladas, wraps e sucos naturais.',
        'url' => 'produtos.php',
        'icon' => 'ph-book-open'
    ]
];

foreach ($staticPages as $page) {
    if (strpos(mb_strtolower($page['title'], 'UTF-8'), $qLower) !== false || 
        strpos(mb_strtolower($page['description'], 'UTF-8'), $qLower) !== false) {
        $results[] = $page;
    }
}

// 2. Pesquisa nos produtos do Banco de Dados
try {
    $stmt = $pdo->prepare("SELECT id, nome, descricao, preco, img, tag FROM produtos WHERE nome LIKE :q OR descricao LIKE :q OR tag LIKE :q LIMIT 8");
    $searchTerm = '%' . $query . '%';
    $stmt->bindValue(':q', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();
    
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($produtos as $p) {
        $results[] = [
            'type' => 'product',
            'title' => $p['nome'],
            'description' => $p['descricao'],
            'price' => number_format($p['preco'], 2, ',', '.'),
            'tag' => $p['tag'],
            'url' => 'produtos.php?busca=' . urlencode($p['nome']), // Se houvesse página de detalhes seria produto.php?id=X
            'image' => $p['img']
        ];
    }
} catch (PDOException $e) {
    // Ignora silenciosamente o erro de banco na busca live e retorna o que já tem
}

echo json_encode($results);
?>
