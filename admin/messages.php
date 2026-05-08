<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Messages';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'read';
    if ($id > 0 && in_array($status, ['new', 'read', 'archived'], true)) {
        $stmt = db()->prepare('UPDATE contact_messages SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
        flash('success', 'Statut du message modifie.');
    }
    redirect('admin/messages.php');
}

$messages = db()->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
require __DIR__ . '/../includes/header.php';
?>

<section class="section-padding admin-layout">
    <div class="container">
        <?php require __DIR__ . '/_menu.php'; ?>
        <?php show_flash(); ?>
        <div class="admin-card">
            <h1 class="h4 mb-4">Messages de contact</h1>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Client</th><th>Sujet</th><th>Message</th><th>Date</th><th>Statut</th></tr></thead>
                    <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td>
                                <strong><?= h($message['full_name']) ?></strong><br>
                                <small><?= h($message['phone']) ?></small><br>
                                <small><?= h($message['email'] ?: '-') ?></small>
                            </td>
                            <td><?= h($message['subject']) ?></td>
                            <td style="max-width:360px"><?= nl2br(h($message['message'])) ?></td>
                            <td><?= h($message['created_at']) ?></td>
                            <td>
                                <form method="post" class="d-flex gap-2">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
                                    <select name="status" class="form-select form-select-sm">
                                        <?php foreach (['new' => 'Nouveau', 'read' => 'Lu', 'archived' => 'Archive'] as $key => $label): ?>
                                            <option value="<?= $key ?>" <?= $message['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-sm btn-primary">OK</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$messages): ?>
                        <tr><td colspan="5" class="text-muted">Aucun message.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
