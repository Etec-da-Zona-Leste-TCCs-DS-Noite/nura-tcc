<?php
// Configurações do Banco de Dados
$host = 'localhost';
$usuario = 'root';      // Usuário padrão do XAMPP
$senha = '';            // Senha padrão do XAMPP (geralmente vazia)
$dbname = 'nura_db';    // Nome do banco de dados (vamos criar esse nome abaixo)

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