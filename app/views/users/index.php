<div class="card">
    <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if (empty($users)): ?>
        <p>No users found. Insert some rows into the <code>users</code> table.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
