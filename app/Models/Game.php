<?php

namespace App\Models;

use App\Config\Database;

class Game
{
    public static function findBySteamAppId(int $steamAppId): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM games WHERE steam_app_id = :id");
        $stmt->execute(['id' => $steamAppId]);
        $game = $stmt->fetch();
        return $game !== false ? $game : null;
    }

    public static function createFromSteamData(int $steamAppId, array $data): int
    {
        $pdo = Database::getConnection();

        $price = isset($data['price_overview']) ? $data['price_overview']['initial'] / 100 : 0;
        $discount = $data['price_overview']['discount_percent'] ?? 0;
        $priceDiscounted = isset($data['price_overview']) ? $data['price_overview']['final'] / 100 : 0;

        $baseUrl = "https://shared.fastly.steamstatic.com/store_item_assets/steam/apps/{$steamAppId}";

        $stmt = $pdo->prepare("INSERT INTO games (name, steam_app_id, price_steam, discount_steam, price_discounted, capsule_url, hero_url, logo_url, header_url, developer, publisher, release_date, description) VALUES (:name, :steam_app_id, :price_steam, :discount_steam, :price_discounted, :capsule_url, :hero_url, :logo_url, :header_url, :developer, :publisher, :release_date, :description)");

        $stmt->execute([
            'name' => $data['name'],
            'steam_app_id' => $steamAppId,
            'price_steam' => $price,
            'discount_steam' => $discount,
            'price_discounted' => $priceDiscounted,
            'capsule_url' => "{$baseUrl}/library_600x900.jpg",
            'hero_url' => "{$baseUrl}/library_hero.jpg",
            'logo_url' => "{$baseUrl}/logo.png",
            'header_url' => "{$baseUrl}/header.jpg",
            'developer' => $data['developers'][0] ?? null,
            'publisher' => $data['publishers'][0] ?? null,
            'release_date' => !empty($data['release_date']['date']) ? date('Y-m-d', strtotime($data['release_date']['date'])) : null,
            'description' => $data['short_description'] ?? null,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function getOrFetch(int $steamAppId): ?array
    {
        $game = self::findBySteamAppId($steamAppId);
        if ($game !== null) {
            return $game;
        }

        $data = \App\Services\SteamApi::getDetails($steamAppId);
        if ($data === null) {
            return null;
        }

        $id = self::createFromSteamData($steamAppId, $data);
        return self::findBySteamAppId($steamAppId);
    }
}
