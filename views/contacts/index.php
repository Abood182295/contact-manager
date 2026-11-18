<?php /** @var array $contacts */ /** @var bool $isAdmin */ ?>
<div class="page-head">
    <h1>Contacts <span class="count"><?= count($contacts) ?></span></h1>
    <?php if ($isAdmin): ?>
        <a class="btn btn-primary" href="<?= e(url('contacts/create')) ?>">+ Add contact</a>
    <?php endif; ?>
</div>

<?php if (!$contacts): ?>
    <p class="empty">No contacts yet.</p>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Added</th>
                <?php if ($isAdmin): ?><th class="actions">Actions</th><?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($contacts as $c): ?>
                <tr>
                    <td><?= e($c['name']) ?></td>
                    <td><?= e($c['email']) ?></td>
                    <td><?= e($c['phone']) ?></td>
                    <td><?= e(substr((string) $c['created_at'], 0, 10)) ?></td>
                    <?php if ($isAdmin): ?>
                        <td class="actions">
                            <a class="btn btn-small" href="<?= e(url('contacts/edit', ['id' => $c['id']])) ?>">Edit</a>
                            <a class="btn btn-small btn-danger" href="<?= e(url('contacts/delete', ['id' => $c['id']])) ?>">Delete</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
