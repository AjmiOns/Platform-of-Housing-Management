<?php
/**
 * api/properties.php
 *
 * Endpoint REST JSON — biens immobiliers
 *
 * GET  /api/properties.php              → liste tous les biens publiés
 * GET  /api/properties.php?id=5         → détail d'un bien
 * GET  /api/properties.php?q=tunis      → recherche full-text
 * GET  /api/properties.php?governorate= → filtre par gouvernorat
 * GET  /api/properties.php?category=    → filtre par catégorie (slug)
 * GET  /api/properties.php?max_price=   → filtre par prix max
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/PropertyRepository.php';

// ── Headers CORS + JSON ──────────────────────────────────
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Répondre aux preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Seules les requêtes GET sont acceptées
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// ── Instancier le repository ─────────────────────────────
require_once __DIR__ . '/../includes/PropertyRepository.php';
$repo = new PropertyRepository();

// ── Routage ──────────────────────────────────────────────

// GET /api/properties.php?id=X  →  détail d'un bien
if (!empty($_GET['id'])) {
    $id       = (int) $_GET['id'];
    $property = $repo->findById($id);

    if (!$property) {
        http_response_code(404);
        echo json_encode(['error' => 'Property not found']);
        exit;
    }

    $property['features'] = $repo->findFeatures($id);

    echo json_encode([
        'success' => true,
        'data'    => $property,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// GET /api/properties.php?...  →  liste avec filtres optionnels
$filters = [
    'category'    => $_GET['category']    ?? '',
    'governorate' => $_GET['governorate'] ?? '',
    'city'        => $_GET['city']        ?? '',
    'max_price'   => $_GET['max_price']   ?? '',
    'q'           => $_GET['q']           ?? '',
];

$properties = $repo->findPublished($filters);

echo json_encode([
    'success' => true,
    'count'   => count($properties),
    'data'    => $properties,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);