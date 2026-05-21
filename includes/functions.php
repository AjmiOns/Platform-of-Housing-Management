<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/Database.php';

/**
 * Raccourci global vers la connexion PDO (Singleton OOP)
 * Maintient la compatibilité avec tout le code existant.
 */
function db(): PDO
{
    return Database::getInstance()->getConnection();
}

/**
 * Fonction de sécurisation des textes HTML
 * Empêche les attaques XSS
 */
function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
/**
 * Générer une URL complète du projet
 */
function url(string $path = ''): string
{
    return APP_BASE . '/' . ltrim($path, '/');
}
/**
 * Rediriger vers une autre page
 */
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}
/**
 * Formater un montant en dinar tunisien
 */
function format_tnd(float $amount): string
{
    return number_format($amount, 0, ',', ' ') . ' TND';
}
/**
 * Récupérer les paramètres de l'agence
 */
function app_settings(): array
{
    $stmt = db()->query('SELECT * FROM agency_settings WHERE id = 1');
      // Retourner les paramètres depuis la base
    // Sinon retourner des valeurs par défaut
    return $stmt->fetch() ?: [
        'agency_name' => APP_NAME,
        'slogan' => 'Location immobiliere en Tunisie',
        'email' => 'contact@example.tn',
        'phone' => '+216 00 000 000',
        'whatsapp' => '+216 00 000 000',
        'address' => 'Tunis',
        'city' => 'Tunis',
        'governorate' => 'Tunis',
        'map_embed_url' => '',
        'facebook' => '#',
        'instagram' => '#',
        'working_hours' => 'Lundi - Samedi : 09:00 - 18:00'
    ];
}
/**
 * Récupérer toutes les catégories
 */
function categories(): array
{
    return db()->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();
}
/**
 * Récupérer tous les propriétaires
 */
function owners(): array
{
    return db()->query('SELECT * FROM owners ORDER BY full_name ASC')->fetchAll();
}
/**
 * Retourner la liste des gouvernorats tunisiens
 */
function governorates(): array
{
    return [
        'Ariana', 'Beja', 'Ben Arous', 'Bizerte', 'Gabes', 'Gafsa', 'Jendouba',
        'Kairouan', 'Kasserine', 'Kebili', 'Kef', 'Mahdia', 'Manouba', 'Medenine',
        'Monastir', 'Nabeul', 'Sfax', 'Sidi Bouzid', 'Siliana', 'Sousse', 'Tataouine',
        'Tozeur', 'Tunis', 'Zaghouan'
    ];
}
/**
 * Retourner le texte correspondant au statut du bien
 */
function status_label(string $status): string
{
    return match ($status) {
        'available' => 'Disponible',
        'reserved' => 'Reserve',
        'rented' => 'Loue',
        default => $status,
    };
}
/**
 * Retourner la classe Bootstrap correspondant au statut
 */
function status_class(string $status): string
{
    return match ($status) {
        'available' => 'success',
        'reserved' => 'warning',
        'rented' => 'secondary',
        default => 'secondary',
    };
}
/**
 * Générer un token CSRF sécurisé
 */
function csrf_token(): string
{
    // Créer le token s'il n'existe pas
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
/**
 * Générer le champ HTML CSRF
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}
/**
 * Vérifier la validité du token CSRF
 */
function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
     // Vérification du token
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        die('Session expiree ou formulaire invalide. Rechargez la page.');
    }
}
/**
 * Créer un message flash temporaire
 */
function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
/**
 * Afficher les messages flash
 */
function show_flash(): void
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
         // Supprimer le message après affichage
        unset($_SESSION['flash']);
         // Affichage Bootstrap
        echo '<div class="alert alert-' . h($flash['type']) . ' alert-dismissible fade show" role="alert">'
            . h($flash['message']) .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}
/**
 * Vérifier si un administrateur est connecté
 */
function is_admin(): bool
{
    return !empty($_SESSION['user']);
}
/**
 * Retourner les informations de l'utilisateur connecté
 */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}
/**
 * Forcer l'accès administrateur
 */
function require_admin(): void
{
    if (!is_admin()) {
          // Redirection si non connecté
        redirect('login.php');
    }
}
/**
 * Retourner l'image d'un bien immobilier
 */
function property_image(?string $imageUrl): string
{
    // Image par défaut si aucune image
    if (!$imageUrl) {
        return url('public/assets/images/property-placeholder.svg');
    }
    // Si image externe
    if (str_starts_with($imageUrl, 'http')) {
        return $imageUrl;
    }
    // Sinon image locale
    return url($imageUrl);
}

/**
 * Gérer le téléchargement d'une image
 */
function handle_upload(string $fieldName): ?string
{
    // Vérifier si aucun fichier envoyé
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
 // Vérifier les erreurs d'upload
    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Erreur pendant le telechargement de l\'image.');
    }
 // Extensions autorisées
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
     // Récupérer l'extension du fichier
    $extension = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
// Vérifier l'extension
    if (!in_array($extension, $allowed, true)) {
        throw new RuntimeException('Format image non autorise. Utilisez JPG, PNG ou WEBP.');
    }
// Vérifier la taille maximale : 3 MB
    if ($_FILES[$fieldName]['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('Image trop grande. Taille maximale: 3 MB.');
    }
// Créer le dossier upload s'il n'existe pas
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }
// Générer un nom unique pour l'image
    $filename = uniqid('bien_', true) . '.' . $extension;
     // Chemin destination
    $destination = UPLOAD_DIR . $filename;
 // Déplacer le fichier uploadé
    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $destination)) {
        throw new RuntimeException('Impossible d\'enregistrer l\'image.');
    }
// Retourner le chemin enregistré
    return 'public/uploads/' . $filename;
}
/**
 * Récupérer un bien immobilier par ID
 */
function get_property(int $id): ?array
{
    $stmt = db()->prepare(
        // Jointure avec catégories et propriétaires
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug, o.full_name AS owner_name
         FROM properties p
         JOIN categories c ON c.id = p.category_id
         LEFT JOIN owners o ON o.id = p.owner_id
         WHERE p.id = :id'
    );
     // Exécuter la requête
    $stmt->execute(['id' => $id]);
     // Retourner le résultat
    return $stmt->fetch() ?: null;
}
/**
 * Récupérer les caractéristiques d'un bien
 */
function get_property_features(int $propertyId): array
{
    $stmt = db()->prepare('SELECT feature FROM property_features WHERE property_id = :property_id ORDER BY id ASC');
    // Exécuter la requête
    $stmt->execute(['property_id' => $propertyId]);
    // Retourner uniquement les features
    return array_column($stmt->fetchAll(), 'feature');
}