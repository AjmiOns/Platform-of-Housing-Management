<?php
/**
 * includes/user_auth.php
 * Fonctions d'authentification pour les clients (utilisateurs publics).
 * Distinct de l'auth admin (includes/functions.php → require_admin).
 */

require_once __DIR__ . '/functions.php';

// ── Session client ─────────────────────────────────────────

/**
 * Vérifie si un client est connecté.
 */
function is_client(): bool
{
    return !empty($_SESSION['client']);
}

/**
 * Retourne les données du client connecté ou null.
 */
function current_client(): ?array
{
    return $_SESSION['client'] ?? null;
}

/**
 * Redirige vers la page de connexion client si non authentifié.
 */
function require_client(): void
{
    if (!is_client()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        redirect('user/login.php');
    }
}

/**
 * Connecte un client : régénère la session et stocke ses infos.
 */
function login_client(array $client): void
{
    session_regenerate_id(true);
    $_SESSION['client'] = [
        'id'        => (int) $client['id'],
        'nom'       => $client['nom'],
        'email'     => $client['email'],
        'telephone' => $client['telephone'] ?? '',
    ];
}

/**
 * Déconnecte le client.
 */
function logout_client(): void
{
    unset($_SESSION['client']);
    session_regenerate_id(true);
}

// ── Favoris ────────────────────────────────────────────────

/**
 * Vérifie si une propriété est dans les favoris du client.
 */
function is_favorite(int $clientId, int $propertyId): bool
{
    $stmt = db()->prepare(
        'SELECT 1 FROM favorites WHERE client_id = :c AND property_id = :p LIMIT 1'
    );
    $stmt->execute(['c' => $clientId, 'p' => $propertyId]);
    return (bool) $stmt->fetchColumn();
}

/**
 * Ajoute un favori. Silencieux si déjà présent.
 */
function add_favorite(int $clientId, int $propertyId): void
{
    $stmt = db()->prepare(
        'INSERT IGNORE INTO favorites (client_id, property_id) VALUES (:c, :p)'
    );
    $stmt->execute(['c' => $clientId, 'p' => $propertyId]);
}

/**
 * Supprime un favori.
 */
function remove_favorite(int $clientId, int $propertyId): void
{
    $stmt = db()->prepare(
        'DELETE FROM favorites WHERE client_id = :c AND property_id = :p'
    );
    $stmt->execute(['c' => $clientId, 'p' => $propertyId]);
}

/**
 * Retourne tous les biens favoris d'un client.
 */
function get_favorites(int $clientId): array
{
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, f.created_at AS favorited_at
         FROM favorites f
         JOIN properties p ON p.id = f.property_id
         JOIN categories c ON c.id = p.category_id
         WHERE f.client_id = :c AND p.published = 1
         ORDER BY f.created_at DESC'
    );
    $stmt->execute(['c' => $clientId]);
    return $stmt->fetchAll();
}

// ── Visites ─────────────────────────────────────────────────

/**
 * Retourne les visites demandées par un client.
 */
function get_client_visits(int $clientId): array
{
    $stmt = db()->prepare(
        'SELECT v.*, p.title AS property_title, p.image_url, p.city, p.governorate
         FROM visit_requests v
         JOIN properties p ON p.id = v.property_id
         WHERE v.client_id = :c
         ORDER BY v.visit_date DESC, v.created_at DESC'
    );

    $stmt->execute(['c' => $clientId]);
    return $stmt->fetchAll();
}

// ── Utilitaires ─────────────────────────────────────────────

/**
 * Libellé couleur Bootstrap pour le statut de visite.
 */
function visit_status_badge(string $status): string
{
    return match ($status) {
        'new'       => '<span class="badge bg-primary">Nouvelle</span>',
        'confirmed' => '<span class="badge bg-success">Confirmée</span>',
        'cancelled' => '<span class="badge bg-danger">Annulée</span>',
        'done'      => '<span class="badge bg-secondary">Effectuée</span>',
        default     => '<span class="badge bg-light text-dark">' . h($status) . '</span>',
    };
}

/**
 * Valide et sanitize les données du formulaire de profil.
 * Retourne ['errors' => [...], 'data' => [...]]
 */
function validate_profile_update(array $post): array
{
    $errors = [];
    $data   = [];

    $nom = trim($post['nom'] ?? '');
    if (mb_strlen($nom) < 2) {
        $errors[] = 'Le nom doit contenir au moins 2 caractères.';
    } else {
        $data['nom'] = $nom;
    }

    $email = trim($post['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse e-mail invalide.';
    } else {
        $data['email'] = $email;
    }

    $tel = trim($post['telephone'] ?? '');
    if ($tel !== '' && !preg_match('/^[+\d\s\-]{7,20}$/', $tel)) {
        $errors[] = 'Numéro de téléphone invalide.';
    } else {
        $data['telephone'] = $tel ?: null;
    }

    return ['errors' => $errors, 'data' => $data];
}
