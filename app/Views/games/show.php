<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Games' ?></title>
</head>

<body>
    <img src="<?= $game['hero_url'] ?>" alt="" style="width:100%;">
    <img src="<?= $game['logo_url'] ?>" alt="" style="max-width:300px;">

    <?php if (empty($game['logo_url'])): ?>
        <h1><?= $game['name'] ?></h1>
    <?php endif; ?>
    <p><?= $game['description'] ?></p>
    <p>Developer: <?= $game['developer'] ?></p>
    <p>Publisher: <?= $game['publisher'] ?></p>
    <p>Preço: €<?= $game['price_discounted'] ?></p>

    <form method="POST" action="/library/buy">
        <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
        <button type="submit">Comprar (Loja Oficial)</button>
    </form>
</body>

</html>