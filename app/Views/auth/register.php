<div class="row justify-content-center">
    <div class="col-md-5">
        <h1><?= $title ?></h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form action="/register" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" class="form-control" name="first_name" id="first_name" value="<?= $old['first_name'] ?? '' ?>" require>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Sobrenome</label>
                <input type="text" class="form-control" name="last_name" id="last_name" value="<?= $old['last_name'] ?? '' ?>" require>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gênero</label>
                <select name="gender" id="gender" require>
                    <option value="">Selecione...</option>
                    <option value="male" <?= ($old['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Masculino</option>
                    <option value="female" <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Feminino</option>
                    <option value="prefer_not_to_say" <?= ($old['gender'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefiro não dizer</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="birth_date" class="form-label">Data de nascimento</label>
                <input type="date" class="form-control" name="birth_date" id="birth_date" value="<?= $old['birth_date'] ?? '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" value="<?= $old['username'] ?? '' ?>" require>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" name="email" id="email" value="<?= $old['email'] ?? '' ?>" require>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Palavra-passe</label>
                <input type="password" class="form-control" name="password" id="password" require minlength="8">
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar palavra-passe</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" require minlength="8">
            </div>

            <button type="submit" class="btn btn-danger w-100">Criar conta</button>
        </form>
    </div>
</div>