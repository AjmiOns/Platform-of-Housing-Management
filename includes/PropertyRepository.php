<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * PropertyRepository
 *
 * Pattern : Repository + Singleton
 * Le constructeur récupère automatiquement la connexion
 * via la classe Database (Singleton OOP) — plus besoin de passer db() en paramètre.
 */
class PropertyRepository
{
    private PDO $db;

    public function __construct()
    {
        // Utilisation du Singleton Database au lieu de la fonction procédurale db()
        $this->db = Database::getInstance()->getConnection();
    }

    // ── Lecture ────────────────────────────────────────────

    /**
     * Retourne tous les biens publiés avec filtres optionnels.
     * @param array $filters  ['category'=>'', 'governorate'=>'', 'city'=>'', 'max_price'=>0, 'q'=>'']
     */
    public function findPublished(array $filters = []): array
    {
        $where  = ['p.published = 1'];
        $params = [];

        if (!empty($filters['category'])) {
            $where[]              = 'c.slug = :category';
            $params['category']   = $filters['category'];
        }
        if (!empty($filters['governorate'])) {
            $where[]                = 'p.governorate = :governorate';
            $params['governorate']  = $filters['governorate'];
        }
        if (!empty($filters['city'])) {
            $where[]        = 'p.city LIKE :city';
            $params['city'] = '%' . $filters['city'] . '%';
        }
        if (!empty($filters['max_price'])) {
            $where[]              = 'p.rent_price <= :max_price';
            $params['max_price']  = (float) $filters['max_price'];
        }
        if (!empty($filters['q'])) {
            $where[]      = '(p.title LIKE :q1 OR p.description LIKE :q2 OR p.address LIKE :q3)';
            $qVal         = '%' . $filters['q'] . '%';
            $params['q1'] = $qVal;
            $params['q2'] = $qVal;
            $params['q3'] = $qVal;
        }

        $sql = 'SELECT p.*, c.name AS category_name
                FROM properties p
                JOIN categories c ON c.id = p.category_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY p.availability_status ASC, p.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Retourne un bien par son ID (avec catégorie et propriétaire).
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name, o.full_name AS owner_name
             FROM properties p
             JOIN categories c ON c.id = p.category_id
             LEFT JOIN owners o ON o.id = p.owner_id
             WHERE p.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Retourne les équipements d'un bien.
     */
    public function findFeatures(int $propertyId): array
    {
        $stmt = $this->db->prepare(
            'SELECT feature FROM property_features WHERE property_id = :pid'
        );
        $stmt->execute(['pid' => $propertyId]);
        return array_column($stmt->fetchAll(), 'feature');
    }

    /**
     * Retourne les N derniers biens publiés (page d'accueil).
     */
    public function findLatest(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name
             FROM properties p
             JOIN categories c ON c.id = p.category_id
             WHERE p.published = 1
             ORDER BY p.created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ── Stats ──────────────────────────────────────────────

    /**
     * Retourne les compteurs globaux pour le dashboard.
     */
    public function counts(): array
    {
        return [
            'total'     => (int) $this->db->query('SELECT COUNT(*) FROM properties')->fetchColumn(),
            'available' => (int) $this->db->query("SELECT COUNT(*) FROM properties WHERE availability_status='available'")->fetchColumn(),
            'reserved'  => (int) $this->db->query("SELECT COUNT(*) FROM properties WHERE availability_status='reserved'")->fetchColumn(),
            'rented'    => (int) $this->db->query("SELECT COUNT(*) FROM properties WHERE availability_status='rented'")->fetchColumn(),
        ];
    }

    // ── Écriture ───────────────────────────────────────────

    /**
     * Insère un nouveau bien et retourne son ID.
     */
    public function create(array $data): int
    {
        $this->db->prepare(
            'INSERT INTO properties
                (owner_id, category_id, title, description, governorate, city, address,
                 rent_price, area, rooms, bedrooms, bathrooms, floor, parking, furnished,
                 availability_status, contract_ready, payment_method, image_url, published)
             VALUES
                (:owner_id, :category_id, :title, :description, :governorate, :city, :address,
                 :rent_price, :area, :rooms, :bedrooms, :bathrooms, :floor, :parking, :furnished,
                 :availability_status, :contract_ready, :payment_method, :image_url, :published)'
        )->execute($data);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Met à jour un bien existant.
     */
    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $this->db->prepare(
            'UPDATE properties SET
                owner_id=:owner_id, category_id=:category_id, title=:title,
                description=:description, governorate=:governorate, city=:city,
                address=:address, rent_price=:rent_price, area=:area,
                rooms=:rooms, bedrooms=:bedrooms, bathrooms=:bathrooms,
                floor=:floor, parking=:parking, furnished=:furnished,
                availability_status=:availability_status, contract_ready=:contract_ready,
                payment_method=:payment_method, image_url=:image_url, published=:published
             WHERE id=:id'
        )->execute($data);
    }

    /**
     * Supprime un bien et ses équipements.
     */
    public function delete(int $id): void
    {
        $this->db->prepare('DELETE FROM property_features WHERE property_id = :id')->execute(['id' => $id]);
        $this->db->prepare('DELETE FROM properties WHERE id = :id')->execute(['id' => $id]);
    }

    /**
     * Remplace les équipements d'un bien.
     */
    public function syncFeatures(int $propertyId, array $features): void
    {
        $this->db->prepare('DELETE FROM property_features WHERE property_id = :pid')
                 ->execute(['pid' => $propertyId]);

        $stmt = $this->db->prepare(
            'INSERT INTO property_features (property_id, feature) VALUES (:pid, :f)'
        );
        foreach ($features as $feature) {
            if ($feature !== '') {
                $stmt->execute(['pid' => $propertyId, 'f' => $feature]);
            }
        }
    }
}