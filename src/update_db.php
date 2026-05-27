<?php
// src/update_db.php
require_once __DIR__ . '/config.php';

try {
    // 1. Criar tabela admin
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Tabela 'admin' criada ou já existente.\n";

    // 2. Inserir admin padrão
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE email = 'admin@nura.com'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmtInsert = $pdo->prepare("INSERT INTO admin (nome, email, senha) VALUES ('Administrador Nura', 'admin@nura.com', :senha)");
        $stmtInsert->execute([':senha' => $hash]);
        echo "Admin padrão inserido (admin@nura.com / admin123).\n";
    } else {
        echo "Admin padrão já existe.\n";
    }

    // 3. Adicionar coluna estoque se não existir
    $pdo->exec("ALTER TABLE produtos ADD COLUMN IF NOT EXISTS estoque INT NOT NULL DEFAULT 15");
    echo "Coluna 'estoque' adicionada ou já existente na tabela 'produtos'.\n";

    echo "Migração de banco de dados concluída com sucesso!\n";

} catch (Exception $e) {
    die("Erro na migração: " . $e->getMessage() . "\n");
}
?>
