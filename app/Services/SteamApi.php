<?php

namespace App\Services;

class SteamApi
{
    public static function search(string $term): array
    {
        $url = "https://steamcommunity.com/actions/SearchApps/" . urlencode($term);
        $response = file_get_contents($url);
        return json_decode($response, true) ?? [];
    }

    public static function getDetails(int $appId): ?array
    {
        $url = "https://store.steampowered.com/api/appdetails?appids={$appId}&cc=us&l=english";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (!isset($data[$appId]['success']) || $data[$appId]['success'] !== true) {
            return null;
        }

        return $data[$appId]['data'];
    }
}
