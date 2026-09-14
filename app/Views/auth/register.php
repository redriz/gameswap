<?php if (isset($error)): ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?>></title>
</head>

<body>
    <h1><?= $title ?></h1>

    <form action="/register" method="POST">
        <label for="name">Nome</label>
        <input type="text" name="name" id="name" require>

        <label for="username">Username</label>
        <input type="text" name="username" id="username" require>

        <label for="email">Email</label>
        <input type="text" name="name" id="name" require>

        <label for="password">Palavra-passe</label>
        <input type="password" name="password" id="password" require minlength="8">

        <label for="password_confirmation">Confirmar palavra-passe</label>
        <input type="password" name="password_confirmation" id="password_confirmation" require minlength="8">

        <button type="submit">Criar conta</button>
    </form>
</body>

</html>