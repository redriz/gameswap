<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\Config\Database;
use App\Models\LedgerEntry;

$pdo = Database::getConnection();

$stmt = $pdo->query("SELECT id FROM users WHERE active = TRUE");
$users = $stmt->fetchAll();

foreach ($users as $user) {
    $userId = $user['id'];

    if (LedgerEntry::hasReceivedBonusToday($userId)) {
        continue;
    }

    $balance = LedgerEntry::getUserBalance($userId);

    if ($balance < 140) {
        $pdo->beginTransaction();
        try {
            LedgerEntry::createForUser($pdo, $userId, 5.00, 'daily_bonus');
            LedgerEntry::createForBank($pdo, -5.00, 'daily_bonus_payout');
            $pdo->commit();
            echo "Bónus dado ao utilizador $userId\n";
        } catch (\Exception $e) {
            $pdo->rollBack();
            echo "Erro no utilizador $userId: " . $e->getMessage() . "\n";
        }
    }
}

echo "Concluído.\n";
