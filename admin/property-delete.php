<?php
require_once __DIR__ . '/../includes/auth.php';
$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = db()->prepare('DELETE FROM properties WHERE id = :id');
    $stmt->execute(['id' => $id]);
    flash('success', 'Bien supprime.');
}

redirect('admin/properties.php');
