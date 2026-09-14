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
}
