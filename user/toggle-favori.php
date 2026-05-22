<?php
/**
 * user/toggle-favori.php
 * Action AJAX / form POST : ajouter ou supprimer un favori.
 *
 * Paramètres POST :
 *   property_id  (int)    — ID du bien
 *   csrf_token   (string) — token CSRF
 *   redirect_to  (string) — URL de retour (optionnel)
 *
 * Réponse JSON si en-tête Accept: application/json
 * Sinon redirection vers redirect_to ou property-details.php
 */
require_once __DIR__ . '/../includes/user_auth.php';
require_client();

$isAjax = (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
       || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) { http_response_code(405); echo json_encode(['error' => 'Method not allowed']); exit; }
    redirect('user/dashboard.php');
}

verify_csrf();

$propertyId = (int) ($_POST['property_id'] ?? 0);
$client = current_client();

if (!$client || !isset($client['id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$clientId = (int) $client['id'];

if ($propertyId <= 0) {
    if ($isAjax) { http_response_code(400); echo json_encode(['error' => 'Invalid property_id']); exit; }
    redirect('user/dashboard.php');
}

// Vérifier que le bien existe
$stmt = db()->prepare('SELECT id FROM properties WHERE id = :id AND published = 1 LIMIT 1');
$stmt->execute(['id' => $propertyId]);
if (!$stmt->fetch()) {
    if ($isAjax) { http_response_code(404); echo json_encode(['error' => 'Property not found']); exit; }
    flash('danger', 'Bien introuvable.');
    redirect('properties.php');
}

$wasFavorite = is_favorite($clientId, $propertyId);

if ($wasFavorite) {
    remove_favorite($clientId, $propertyId);
    $now = false;
    $msg = 'Bien retiré de vos favoris.';
    $type = 'info';
} else {
    add_favorite($clientId, $propertyId);
    $now = true;
    $msg = 'Bien ajouté à vos favoris !';
    $type = 'success';
}

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['favorite' => $now, 'message' => $msg]);
    exit;
}

flash($type, $msg);

$redirectTo = $_POST['redirect_to'] ?? '';
// Valider que l'URL est interne (sécurité open-redirect)
if ($redirectTo && preg_match('/^[\/a-zA-Z0-9_\-\.?=&]+$/', $redirectTo)) {
    header('Location: ' . (defined('APP_BASE') ? APP_BASE : '') . '/' . ltrim($redirectTo, '/'));
} else {
    header('Location: ' . (defined('APP_BASE') ? APP_BASE : '') . '/property-details.php?id=' . $propertyId);
}
exit;
