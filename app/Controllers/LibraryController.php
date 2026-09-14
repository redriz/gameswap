<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\LedgerEntry;

class LibraryController extends BaseController
{
    public function buy()
    {
        $this->requireAuth();

        $gameId = (int) $_POST['game_id'];
        $userId = $_SESSION['user_id'];

        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM games WHERE id = :id");
        $stmt->execute(['id' => $gameId]);
        $game = $stmt->fetch();

        if (!$game) {
            echo "Jogo não encontrado.";
            return;
        }

        $stmt = $pdo->prepare("SELECT id FROM user_library WHERE user_id = :user_id AND game_id = :game_id");
        $stmt->execute(['user_id' => $userId, 'game_id' => $gameId]);
        if ($stmt->fetch()) {
            echo "Já possuis este jogo.";
            return;
        }

        $price = (float) $game['price_discounted'];
        $balance = LedgerEntry::getUserBalance($userId);

        if ($balance < $price) {
            echo "Saldo insuficiente.";
            return;
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO user_library (user_id, game_id, origin) VALUES (:user_id, :game_id, 'official_store')");
            $stmt->execute(['user_id' => $userId, 'game_id' => $gameId]);

            LedgerEntry::createForUser($pdo, $userId, -$price, 'official_store_purchase');
            LedgerEntry::createForBank($pdo, $price, 'official_store_income');

            $pdo->commit();
            echo "Compra realizada com sucesso! Jogo adicionado à tua biblioteca.";
        } catch (\Exception $e) {
            $pdo->rollBack();
            echo "Erro ao processar compra: " . $e->getMessage();
        }
    }

    public function index()
    {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
        SELECT ul.id as library_id, ul.status, g.name, g.capsule_url, g.id as game_id
        FROM user_library ul
        JOIN games g ON g.id = ul.game_id
        WHERE ul.user_id = :user_id
    ");
        $stmt->execute(['user_id' => $userId]);
        $library = $stmt->fetchAll();

        $this->render('library/index', ['title' => 'Minha Biblioteca', 'library' => $library]);
    }
}
