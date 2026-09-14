<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class LedgerEntry
{
    public static function createForUser(PDO $pdo, int $userId, float $amount, string $reason, ?int $listingId = null): void
    {
        $stmt = $pdo->prepare("INSERT INTO ledger_entries (user_id, amount, reason, related_listing_id) VALUES (:user_id, :amount, :reason, :listing_id)");
        $stmt->execute([
            'user_id' => $userId,
            'amount' => $amount,
            'reason' => $reason,
            'listing_id' => $listingId,
        ]);
    }

    public static function createForBank(PDO $pdo, float $amount, string $reason, ?int $listingId = null): void
    {
        $stmt = $pdo->prepare("INSERT INTO ledger_entries (bank_id, amount, reason, related_listing_id) VALUES (1, :amount, :reason, :listing_id)");
        $stmt->execute([
            'amount' => $amount,
            'reason' => $reason,
            'listing_id' => $listingId,
        ]);
    }

    public static function getUserBalance(int $userId): float
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as balance FROM ledger_entries WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();

        return (float) $result['balance'];
    }

    public static function getBankBalance(): float
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as balance FROM ledger_entries WHERE bank_id = 1");
        $stmt->execute();
        $result = $stmt->fetch();

        return (float) $result['balance'];
    }

    public static function hasReceivedBonusToday(int $userId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id FROM ledger_entries WHERE user_id = :user_id AND reason = 'daily_bonus' AND created_at::date = CURRENT_DATE");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch() !== false;
    }
}
