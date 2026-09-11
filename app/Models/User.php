<?php

namespace App\Models;

use App\Config\Database;
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

    public static function create(string $name, string $username, string $email, string $password): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password_hash) VALUES (:name, :username, :email, :password_hash)");
        $stmt->execute(['name' => $name, 'username' => $username, 'email' => $email, 'password_hash' => $hash]);
        return (int) $pdo->lastInsertId();
    }
}
