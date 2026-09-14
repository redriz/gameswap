<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\LedgerEntry;

class ListingController extends BaseController
{
    public function createForm()
    {
        $this->requireAuth();
        $libraryId = (int) ($_GET['library_id'] ?? 0);

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT ul.id as library_id, g.name, g.price_discounted
            FROM user_library ul
            JOIN games g ON g.id = ul.game_id
            WHERE ul.id = :id AND ul.user_id = :user_id AND ul.status = 'available'
        ");
        $stmt->execute(['id' => $libraryId, 'user_id' => $_SESSION['user_id']]);
        $item = $stmt->fetch();

        if (!$item) {
            echo "Item não encontrado ou já anunciado.";
            return;
        }

        $this->render('listings/create', ['title' => 'Criar anúncio', 'item' => $item]);
    }

    public function create()
    {
        $this->requireAuth();

        $libraryId = (int) $_POST['library_id'];
        $priceNet = (float) $_POST['price_net'];
        $userId = $_SESSION['user_id'];

        if ($priceNet < 1 || $priceNet > 1000) {
            echo "Preço inválido (deve ser entre 1 e 1000 euros).";
            return;
        }

        $priceGross = round($priceNet / 0.85, 2);

        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT id FROM user_library WHERE id = :id AND user_id = :user_id AND status = 'available'");
        $stmt->execute(['id' => $libraryId, 'user_id' => $userId]);
        if (!$stmt->fetch()) {
            echo "Item inválido.";
            return;
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO listings (user_library_id, allows_sale, price_net, price_gross) VALUES (:library_id, TRUE, :price_net, :price_gross)");
            $stmt->execute(['library_id' => $libraryId, 'price_net' => $priceNet, 'price_gross' => $priceGross]);

            $stmt = $pdo->prepare("UPDATE user_library SET status = 'listed' WHERE id = :id");
            $stmt->execute(['id' => $libraryId]);

            $pdo->commit();
            echo "Anúncio criado com sucesso!";
        } catch (\Exception $e) {
            $pdo->rollBack();
            echo "Erro: " . $e->getMessage();
        }
    }

    public function marketplace()
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("
        SELECT l.id, l.price_gross, g.name, g.capsule_url, u.username as seller_username
        FROM listings l
        JOIN user_library ul ON ul.id = l.user_library_id
        JOIN games g ON g.id = ul.game_id
        JOIN users u ON u.id = l.seller_id
        WHERE l.status = 'active' AND l.allows_sale = TRUE
    ");
        $listings = $stmt->fetchAll();

        $this->render('listings/marketplace', ['title' => 'Marketplace', 'listings' => $listings]);
    }

    public function buy()
    {
        $this->requireAuth();
        $listingId = (int) $_POST['listing_id'];
        $buyerId = $_SESSION['user_id'];

        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT l.*, ul.id as library_id, ul.user_id as seller_id FROM listings l JOIN user_library ul ON ul.id = l.user_library_id WHERE l.id = :id AND l.status = 'active'");
        $stmt->execute(['id' => $listingId]);
        $listing = $stmt->fetch();

        if (!$listing) {
            echo "Anúncio não encontrado ou já vendido.";
            return;
        }

        if ($listing['seller_id'] == $buyerId) {
            echo "Não podes comprar o teu próprio anúncio.";
            return;
        }

        $priceGross = (float) $listing['price_gross'];
        $priceNet = (float) $listing['price_net'];
        $fee = round($priceGross - $priceNet, 2);

        $balance = LedgerEntry::getUserBalance($buyerId);
        if ($balance < $priceGross) {
            echo "Saldo insuficiente.";
            return;
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE user_library SET user_id = :buyer_id, status = 'available' WHERE id = :library_id");
            $stmt->execute(['buyer_id' => $buyerId, 'library_id' => $listing['library_id']]);

            $stmt = $pdo->prepare("UPDATE listings SET status = 'completed' WHERE id = :id");
            $stmt->execute(['id' => $listingId]);

            LedgerEntry::createForUser($pdo, $buyerId, -$priceGross, 'sale_income', $listingId);
            LedgerEntry::createForUser($pdo, $listing['seller_id'], $priceNet, 'sale_income', $listingId);
            LedgerEntry::createForBank($pdo, $fee, 'platform_fee', $listingId);

            $stmt = $pdo->prepare("INSERT INTO deals (listing_id, seller_id, buyer_id, type, price_net, price_gross, platform_fee) VALUES (:listing_id, :seller_id, :buyer_id, 'sale', :price_net, :price_gross, :fee)");
            $stmt->execute([
                'listing_id' => $listingId,
                'seller_id' => $listing['seller_id'],
                'buyer_id' => $buyerId,
                'price_net' => $priceNet,
                'price_gross' => $priceGross,
                'fee' => $fee,
            ]);

            $pdo->commit();
            echo "Compra realizada com sucesso!";
        } catch (\Exception $e) {
            $pdo->rollBack();
            echo "Erro: " . $e->getMessage();
        }
    }
}
