<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
</head>

<body>
    <h1>Anunciar: <?= $item['name'] ?></h1>
    <p>Preço de referência (Loja Oficial): €<?= $item['price_discounted'] ?></p>

    <form method="POST" action="/listings/create">
        <input type="hidden" name="library_id" value="<?= $item['library_id'] ?>">

        <label for="price_net">Quanto queres receber (€)</label>
        <input type="number" step="0.01" name="price_net" id="price_net" min="1" max="1000" required>

        <button type="submit">Publicar anúncio</button>
    </form>
</body>

</html>