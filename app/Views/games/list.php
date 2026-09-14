<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Games' ?></title>
</head>

<body>
    <h1><?= $title ?? 'Games' ?></h1>

    <div>
        <?php foreach ($games as $game): ?>
            <div style="display:inline-block; margin:10px;">
                <a href="/game?steam_app_id=<?= $game['steam_app_id'] ?>">
                    <img src="<?= $game['capsule_url'] ?>" alt="<?= $game['name'] ?>" width="200">
                    <p><?= $game['name'] ?></p>
                    <p>€<?= $game['price_discounted'] ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>