<?php if (isset($error)): ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Create account' ?>></title>
</head>

<body>
    <h1><?= $title ?? 'Create account' ?></h1>

    <form action="/register" method="POST">
        <label for="name">Nome</label>
        <input type="text" name="first_name" id="first_name" value="<?= $old['first_name'] ?? '' ?>" require>
        <br>
        <label for="name">Sobrenome</label>
        <input type="text" name="last_name" id="last_name" value="<?= $old['last_name'] ?? '' ?>" require>
        <br>
        <label for="gender">Gênero</label>
        <select name="gender" id="gender" require>
            <option value="">Selecione...</option>
            <option value="male" <?= ($old['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Masculino</option>
            <option value="female" <?= ($old['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Feminino</option>
            <option value="prefer_not_to_say" <?= ($old['gender'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefiro não dizer</option>
        </select>
        <br>
        <label for="birth_date">Data de nascimento</label>
        <input type="date" name="birth_date" id="birth_date" value="<?= $old['birth_date'] ?? '' ?>" required>
        <br>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?= $old['username'] ?? '' ?>" require>
        <br>
        <label for="email">Email</label>
        <input type="text" name="email" id="email" value="<?= $old['email'] ?? '' ?>" require>
        <br>
        <label for="password">Palavra-passe</label>
        <input type="password" name="password" id="password" require minlength="8">
        <br>
        <label for="password_confirmation">Confirmar palavra-passe</label>
        <input type="password" name="password_confirmation" id="password_confirmation" require minlength="8">
        <br>
        <button type="submit">Criar conta</button>
    </form>
</body>

</html>