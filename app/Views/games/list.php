<?php

/** 
 * @var string $title
 * @var array $games 
 */

?>

<h1 class="mb-4"><?= $title ?></h1>
<div class="row row-cols-2 row-cols-md-4 g-4">
    <?php foreach ($games as $game): ?>
        <div class="col">
            <a href="/game?steam_app_id=<?= $game['steam_app_id'] ?>" class="text-decoration-none text-body">
                <div class="card h-100 bg-dark-subtle">
                    <img src="<?= $game['capsule_url'] ?>" class="card-img-top">
                    <div class="card-body">
                        <p class="card-text small fw-bold"><?= $game['name'] ?></p>
                        <p class="card-text text-success">€<?= $game['price_discounted'] ?></p>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>