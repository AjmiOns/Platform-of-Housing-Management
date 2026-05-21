<?php
// Charger les constantes DB si elles ne sont pas encore définies
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/config.php';
}

/**
 * Database.php
 *
 * Pattern : Singleton OOP
 * Garantit une seule instance PDO durant toute l'exécution.
 * Remplace la fonction procédurale db() de config/database.php.
 */
class Database
{
    // ── Instance unique (Singleton) ───────────────────────
    private static ?Database $instance = null;

    // ── Connexion PDO encapsulée ──────────────────────────
    private PDO $pdo;

    // ── Constructeur privé : interdit new Database() ──────
    private function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST
             . ';dbname='    . DB_NAME
             . ';charset='   . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Erreur de connexion : vérifiez config/config.php et importez database/schema.sql.');
        }
    }

    // ── Interdit le clonage de l'instance ─────────────────
    private function __clone() {}

    // ── Point d'accès global à l'instance unique ──────────
    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    // ── Accès à la connexion PDO ──────────────────────────
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // ── Raccourci : prépare une requête directement ───────
    public function prepare(string $sql): \PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    // ── Raccourci : exécute une requête simple ────────────
    public function query(string $sql): \PDOStatement
    {
        return $this->pdo->query($sql);
    }

    // ── Raccourci : dernier ID inséré ─────────────────────
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    // ── Transactions ──────────────────────────────────────
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }
}