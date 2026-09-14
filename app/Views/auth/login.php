<div class="row justify-content-center">
    <div class="col-md-5">
        <h1 class="mb-4"><?= $title ?></h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" action="/login">
            <div class="mb-3">
                <label class="form-label">Email ou username</label>
                <input type="text" name="identifier" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-danger w-100">Entrar</button>
        </form>
    </div>
</div>