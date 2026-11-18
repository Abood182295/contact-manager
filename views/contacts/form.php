<?php
/** @var string $title */
/** @var string $action */
/** @var int|null $id */
/** @var array $contact */
/** @var array $errors */
$fields = [
    'name'  => ['Name',  'text',  'name',  100],
    'email' => ['Email', 'email', 'email', 255],
    'phone' => ['Phone', 'tel',   'tel',   20],
];
?>
<h1><?= e($title) ?></h1>

<form method="post" action="<?= e(url($action)) ?>" class="card form" novalidate>
    <?= Csrf::field() ?>
    <?php if ($id !== null): ?>
        <input type="hidden" name="id" value="<?= e((string) $id) ?>">
    <?php endif; ?>

    <?php foreach ($fields as $key => [$label, $type, $autocomplete, $max]): ?>
        <div class="field<?= isset($errors[$key]) ? ' has-error' : '' ?>">
            <label for="<?= e($key) ?>"><?= e($label) ?></label>
            <input id="<?= e($key) ?>" name="<?= e($key) ?>" type="<?= e($type) ?>"
                   value="<?= e($contact[$key] ?? '') ?>" maxlength="<?= (int) $max ?>"
                   autocomplete="<?= e($autocomplete) ?>" required>
            <?php if (isset($errors[$key])): ?>
                <p class="error"><?= e($errors[$key]) ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save</button>
        <a class="btn btn-ghost" href="<?= e(url('contacts')) ?>">Cancel</a>
    </div>
</form>
