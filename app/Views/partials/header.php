<?php

use App\Models\LedgerEntry;

$isLoggedIn = isset($_SESSION['user_id']);
$balance = $isLoggedIn ? LedgerEntry::getUserBalance($_SESSION['user_id']) : null;
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">GAME<span class="text-danger">SWAP</span></a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/games">Loja Oficial</a></li>
                <li class="nav-item"><a class="nav-link" href="/marketplace">Marketplace</a></li>
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item"><a class="nav-link" href="/library">Minha Biblioteca</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item me-3">
                        <span class="badge text-bg-success">€<?= number_format($balance, 2) ?></span>
                    </li>
                    <li class="nav-item me-3"><?= htmlspecialchars($_SESSION['username']) ?></li>
                    <li class="nav-item">
                        <a class="btn btn-outline-danger btn-sm" href="/logout">Sair</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2"><a class="btn btn-outline-light btn-sm" href="/login">Entrar</a></li>
                    <li class="nav-item"><a class="btn btn-danger btn-sm" href="/register">Criar conta</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>