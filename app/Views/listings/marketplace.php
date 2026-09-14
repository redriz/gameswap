<?php

/** 
 * @var string $title
 * @var array $listings
 */

?>

<h1 class="mb-4"><?= $title ?></h1>
<div class="row row-cols-2 row-cols-md-4 g-4">
    <?php foreach ($listings as $listing): ?>
        <div class="col">
            <div class="card h-100 bg-dark-subtle">
                <img src="<?= $listing['capsule_url'] ?>" class="card-img-top">
                <div class="card-body">
                    <p class="card-text small fw-bold"><?= $listing['name'] ?></p>
                    <p class="text-success">€<?= $listing['price_gross'] ?></p>
                    <p class="small text-secondary">Vendedor: <?= $listing['seller_username'] ?></p>
                    <form method="POST" action="/listings/buy">
                        <input type="hidden" name="listing_id" value="<?= $listing['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger w-100">Comprar</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>