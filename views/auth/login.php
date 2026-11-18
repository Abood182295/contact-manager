<?php /** @var string $username */ ?>
<h1>Admin login</h1>

<form method="post" action="<?= e(url('login')) ?>" class="card form narrow">
    <?= Csrf::field() ?>

    <div class="field">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" value="<?= e($username) ?>"
               maxlength="50" autocomplete="username" required autofocus>
    </div>

    <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Log in</button>
    </div>
</form>
