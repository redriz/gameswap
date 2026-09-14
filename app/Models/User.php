<?php

namespace App\Models;

use App\Config\Database;
use App\Models\LedgerEntry;
use PDO;

class User
{
    public static function emailOrUsernameExists(string $email, string $username): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
        $stmt->execute(['email' => $email, 'username' => $username]);
        $result = $stmt->fetch();
        return $result !== false;
    }

    public static function create(
        string $firstName,
        string $lastName,
        string $username,
        string $email,
        string $password,
        string $gender,
        string $birthDate
    ): int {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo = Database::getConnection();

        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, email, password_hash, gender, birth_date) VALUES (:first_name, :last_name, :username, :email, :password_hash, :gender, :birth_date)");
            $stmt->execute([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
                'email' => $email,
                'password_hash' => $hash,
                'gender' => $gender,
                'birth_date' => $birthDate,
            ]);

            $id = (int) $pdo->lastInsertId();

            LedgerEntry::createForUser($pdo, $id, 15.00, 'account_creation_bonus');
            LedgerEntry::createForBank($pdo, 145.00, 'bank_new_user');

            $pdo->commit();

            return $id;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
