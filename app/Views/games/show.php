<?php

/** 
 * @var array $game
 */

?>

<img src="<?= $game['hero_url'] ?>" class="img-fluid rounded mb-3 w-100">
<?php if (!empty($game['logo_url'])): ?>
    <img src="<?= $game['logo_url'] ?>" style="max-width:300px;" class="mb-3">
<?php else: ?>
    <h1><?= $game['name'] ?></h1>
<?php endif; ?>

<p><?= $game['description'] ?></p>
<p><strong>Developer:</strong> <?= $game['developer'] ?></p>
<p><strong>Publisher:</strong> <?= $game['publisher'] ?></p>
<p class="fs-4 text-success">€<?= $game['price_discounted'] ?></p>

<form method="POST" action="/library/buy">
    <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
    <button type="submit" class="btn btn-danger">Comprar (Loja Oficial)</button>
</form>