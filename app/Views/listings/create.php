<div class="row justify-content-center">
    <div class="col-md-5">
        <h1 class="mb-4">Anunciar: <?= $item['name'] ?></h1>
        <p class="text-secondary">Preço de referência (Loja Oficial): €<?= $item['price_discounted'] ?></p>
        <form method="POST" action="/listings/create">
            <input type="hidden" name="library_id" value="<?= $item['library_id'] ?>">
            <div class="mb-3">
                <label class="form-label">Quanto queres receber (€)</label>
                <input type="number" step="0.01" name="price_net" class="form-control" min="1" max="1000" required>
            </div>
            <button type="submit" class="btn btn-danger w-100">Publicar anúncio</button>
        </form>
    </div>
</div>