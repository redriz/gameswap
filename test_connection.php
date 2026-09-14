<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use App\Config\Database;

try {
    $pdo = Database::getConnection();
    echo "Conexão bem-sucedida!";
} catch (Exception $e) {
    echo "Falha ao conectar à base de dados: " . $e->getMessage();
}

$pdo = Database::getConnection();

$resultado = $pdo->query("SELECT COUNT(*) as total FROM users");
$linha = $resultado->fetch();
echo "\nTotal de usuários: " . $linha['total'] . "\n";

use App\Models\LedgerEntry;

echo "User Balance: " . LedgerEntry::getUserBalance(1) . "\n"; // deve dar 15
echo "Bank Balance: " . LedgerEntry::getBankBalance();  // deve dar 145