<?php /** @var array $contact */ ?>
<h1>Delete contact</h1>

<div class="card">
    <p>Are you sure you want to delete <strong><?= e($contact['name']) ?></strong> (<?= e($contact['email']) ?>)?
        This cannot be undone.</p>

    <form method="post" action="<?= e(url('contacts/delete')) ?>" class="form-actions">
        <?= Csrf::field() ?>
        <input type="hidden" name="id" value="<?= e((string) $contact['id']) ?>">
        <button type="submit" class="btn btn-danger">Yes, delete</button>
        <a class="btn btn-ghost" href="<?= e(url('contacts')) ?>">Cancel</a>
    </form>
</div>
