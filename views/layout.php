<?php /** @var string $title */ /** @var string $content */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · Contact Manager</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= e(url('contacts')) ?>">Contact Manager</a>
    <nav>
        <?php if (Auth::check()): ?>
            <span class="who">Signed in as <strong><?= e(Auth::username()) ?></strong></span>
            <form method="post" action="<?= e(url('logout')) ?>" class="inline">
                <?= Csrf::field() ?>
                <button type="submit" class="btn btn-ghost">Log out</button>
            </form>
        <?php else: ?>
            <a class="btn btn-ghost" href="<?= e(url('login')) ?>">Admin login</a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">
    <?php foreach (Flash::pull() as $flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
    <?php endforeach; ?>

    <?= $content ?>
</main>
</body>
</html>
