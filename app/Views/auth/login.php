<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Login' ?></title>
</head>

<body>
    <h1><?= $title ?? 'Login' ?></h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" action="/login">
        <label for="identifier">Email ou username</label>
        <input type="text" name="identifier" id="identifier" required>
        <br>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
        <br>
        <button type="submit">Entrar</button>
    </form>
</body>

</html>