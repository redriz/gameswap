<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Marketplace' ?></title>
</head>

<body>
    <h1><?= $title ?? 'Marketplace' ?></h1>

    <?php foreach ($listings as $listing): ?>
        <div style="display:inline-block; margin:10px;">
            <img src="<?= $listing['capsule_url'] ?>" width="150">
            <p><?= $listing['name'] ?></p>
            <p>€<?= $listing['price_gross'] ?></p>
            <p>Vendedor: <?= $listing['seller_username'] ?></p>
            <form method="POST" action="/listings/buy">
                <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                <button type="submit">Comprar</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>

</html>