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

        $stmt = $pdo->prepare(
            "INSERT INTO users (first_name, last_name, username, email, password_hash, gender, birth_date) VALUES (:first_name, :last_name, :username, :email, :password_hash, :gender, :birth_date)"
        );
        $stmt->execute([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $username,
            'email' => $email,
            'password_hash' => $hash,
            'gender' => $gender,
            'birth_date' => $birthDate
        ]);

        return (int) $pdo->lastInsertId();
    }
}
