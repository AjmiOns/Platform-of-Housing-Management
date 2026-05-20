<?php
require_once __DIR__ . '/../includes/auth.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$id) {
    flash('danger', 'Identifiant manquant.');
    redirect('admin/properties.php');
}

// Vérifier que le bien existe
$stmt = db()->prepare('SELECT id, title FROM properties WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$property = $stmt->fetch();

if (!$property) {
    flash('danger', 'Bien introuvable.');
    redirect('admin/properties.php');
}

// Suppression (les features sont supprimées en CASCADE)
db()->prepare('DELETE FROM properties WHERE id = :id')->execute(['id' => $id]);

flash('success', 'Le bien « ' . $property['title'] . ' » a été supprimé.');
redirect('admin/properties.php');