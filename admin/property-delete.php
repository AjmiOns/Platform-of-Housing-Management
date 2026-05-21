<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/PropertyRepository.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$id) {
    flash('danger', 'Identifiant manquant.');
    redirect('admin/properties.php');
}

$repo = new PropertyRepository(db());

$property = $repo->findById($id);

if (!$property) {
    flash('danger', 'Bien introuvable.');
    redirect('admin/properties.php');
}

// Suppression via Repository Pattern
$repo->delete($id);

flash('success', 'Le bien « ' . $property['title'] . ' » a été supprimé.');
redirect('admin/properties.php');