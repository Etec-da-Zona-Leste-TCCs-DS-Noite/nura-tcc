<?php
// Configurações do Banco de Dados (Docker)
$host = 'db';           // nome do serviço do banco no docker-compose
$usuario = 'root';
$senha = 'aluno123';    // mesma senha definida no docker-compose
$dbname = 'nura_db';    // nome do banco

try {
    // Cria a conexão usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha);

    // Configura para o PHP mostrar erros caso o banco falhe
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Se der erro, para tudo e mostra a mensagem
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>