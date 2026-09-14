<?php

namespace App\Controllers;

use App\Models\Game;
use App\Config\Database;

class GameController extends BaseController
{
    public function list()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM games ORDER BY name ASC");
        $games = $stmt->fetchAll();

        $this->render('games/list', ['title' => 'Loja', 'games' => $games]);
    }

    public function show()
    {
        $steamAppId = (int) ($_GET['steam_app_id'] ?? 0);
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM games WHERE steam_app_id = :steam_app_id");
        $stmt->execute(['steam_app_id' => $steamAppId]);
        $game = $stmt->fetch();

        if (!$game) {
            http_response_code(404);
            echo "Jogo não encontrado.";
            return;
        }

        $this->render('games/show', ['title' => $game['name'], 'game' => $game]);
    }
}
