<h1 class="mb-4"><?= $title ?></h1>
<div class="row row-cols-2 row-cols-md-4 g-4">
    <?php foreach ($library as $item): ?>
        <div class="col">
            <div class="card h-100 bg-dark-subtle">
                <img src="<?= $item['capsule_url'] ?>" class="card-img-top">
                <div class="card-body">
                    <p class="card-text small fw-bold"><?= $item['name'] ?></p>
                    <span class="badge text-bg-secondary"><?= $item['status'] ?></span>
                    <?php if ($item['status'] === 'available'): ?>
                        <a href="/listings/create?library_id=<?= $item['library_id'] ?>" class="btn btn-sm btn-outline-danger d-block mt-2">Anunciar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>