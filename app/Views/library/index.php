<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Library' ?></title>
</head>

<body>
    <h1><?= $title ?? 'Library' ?></h1>

    <?php foreach ($library as $item): ?>
        <div style="display:inline-block; margin:10px;">
            <img src="<?= $item['capsule_url'] ?>" width="150">
            <p><?= $item['name'] ?></p>
            <p>Status: <?= $item['status'] ?></p>
            <?php if ($item['status'] === 'available'): ?>
                <a href="/listings/create?library_id=<?= $item['library_id'] ?>">Anunciar</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>

</html>