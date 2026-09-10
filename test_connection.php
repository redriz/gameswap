<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = Database::getConnection();
    echo "Conexão bem-sucedida!";
} catch (Exception $e) {
    echo "Falha ao conectar à base de dados: " . $e->getMessage();
}

$resultado = $pdo->query("SELECT COUNT(*) as total FROM users");
$linha = $resultado->fetch();
echo "\nTotal de usuários: " . $linha['total'];
