<?php

namespace App\Controllers;

class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }

    protected function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}
