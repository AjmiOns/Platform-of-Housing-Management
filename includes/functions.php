<?php
require_once __DIR__ . '/../config/database.php';

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return APP_BASE . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function format_tnd(float $amount): string
{
    return number_format($amount, 0, ',', ' ') . ' TND';
}

function app_settings(): array
{
    $stmt = db()->query('SELECT * FROM agency_settings WHERE id = 1');
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

function categories(): array
{
    return db()->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();
}

function owners(): array
{
    return db()->query('SELECT * FROM owners ORDER BY full_name ASC')->fetchAll();
}

function governorates(): array
{
    return [
        'Ariana', 'Beja', 'Ben Arous', 'Bizerte', 'Gabes', 'Gafsa', 'Jendouba',
        'Kairouan', 'Kasserine', 'Kebili', 'Kef', 'Mahdia', 'Manouba', 'Medenine',
        'Monastir', 'Nabeul', 'Sfax', 'Sidi Bouzid', 'Siliana', 'Sousse', 'Tataouine',
        'Tozeur', 'Tunis', 'Zaghouan'
    ];
}

function status_label(string $status): string
{
    return match ($status) {
        'available' => 'Disponible',
        'reserved' => 'Reserve',
        'rented' => 'Loue',
        default => $status,
    };
}

function status_class(string $status): string
{
    return match ($status) {
        'available' => 'success',
        'reserved' => 'warning',
        'rented' => 'secondary',
        default => 'secondary',
    };
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        die('Session expiree ou formulaire invalide. Rechargez la page.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function show_flash(): void
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . h($flash['type']) . ' alert-dismissible fade show" role="alert">'
            . h($flash['message']) .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}

function is_admin(): bool
{
    return !empty($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect('login.php');
    }
}

function property_image(?string $imageUrl): string
{
    if (!$imageUrl) {
        return url('public/assets/images/property-placeholder.svg');
    }
    if (str_starts_with($imageUrl, 'http')) {
        return $imageUrl;
    }
    return url($imageUrl);
}

function handle_upload(string $fieldName): ?string
{
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Erreur pendant le telechargement de l\'image.');
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowed, true)) {
        throw new RuntimeException('Format image non autorise. Utilisez JPG, PNG ou WEBP.');
    }

    if ($_FILES[$fieldName]['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('Image trop grande. Taille maximale: 3 MB.');
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }

    $filename = uniqid('bien_', true) . '.' . $extension;
    $destination = UPLOAD_DIR . $filename;

    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $destination)) {
        throw new RuntimeException('Impossible d\'enregistrer l\'image.');
    }

    return 'public/uploads/' . $filename;
}

function get_property(int $id): ?array
{
    $stmt = db()->prepare(
        'SELECT p.*, c.name AS category_name, c.slug AS category_slug, o.full_name AS owner_name
         FROM properties p
         JOIN categories c ON c.id = p.category_id
         LEFT JOIN owners o ON o.id = p.owner_id
         WHERE p.id = :id'
    );
    $stmt->execute(['id' => $id]);
    return $stmt->fetch() ?: null;
}

function get_property_features(int $propertyId): array
{
    $stmt = db()->prepare('SELECT feature FROM property_features WHERE property_id = :property_id ORDER BY id ASC');
    $stmt->execute(['property_id' => $propertyId]);
    return array_column($stmt->fetchAll(), 'feature');
}
