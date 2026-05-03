<?php
require_once 'Config/database.php';

// ── Entité Recette ────────────────────────────────────────────────────────────
class Recette {

    private ?int    $id;
    private ?string $nom;
    private ?string $description;
    private ?string $categorie;
    private ?int    $duree;
    private ?string $difficulte;
    private ?int    $calories;
    private ?string $image;
    private ?string $createdAt;

    // ── Constructeur ──────────────────────────────────────────────────────────
    public function __construct(
        ?int    $id          = null,
        ?string $nom         = null,
        ?string $description = null,
        ?string $categorie   = null,
        ?int    $duree       = null,
        ?string $difficulte  = null,
        ?int    $calories    = null,
        ?string $image       = null,
        ?string $createdAt   = null
    ) {
        $this->id          = $id;
        $this->nom         = $nom;
        $this->description = $description;
        $this->categorie   = $categorie;
        $this->duree       = $duree;
        $this->difficulte  = $difficulte;
        $this->calories    = $calories;
        $this->image       = $image;
        $this->createdAt   = $createdAt;
    }

    // ── Getters ───────────────────────────────────────────────────────────────
    public function getId(): ?int       { return $this->id; }
    public function getNom(): ?string   { return $this->nom; }
    public function getDescription(): ?string { return $this->description; }
    public function getCategorie(): ?string   { return $this->categorie; }
    public function getDuree(): ?int    { return $this->duree; }
    public function getDifficulte(): ?string  { return $this->difficulte; }
    public function getCalories(): ?int { return $this->calories; }
    public function getImage(): ?string { return $this->image; }
    public function getCreatedAt(): ?string   { return $this->createdAt; }

    // ── Setters ───────────────────────────────────────────────────────────────
    public function setId(?int $id): void               { $this->id = $id; }
    public function setNom(?string $nom): void          { $this->nom = $nom; }
    public function setDescription(?string $d): void    { $this->description = $d; }
    public function setCategorie(?string $c): void      { $this->categorie = $c; }
    public function setDuree(?int $d): void             { $this->duree = $d; }
    public function setDifficulte(?string $d): void     { $this->difficulte = $d; }
    public function setCalories(?int $c): void          { $this->calories = $c; }
    public function setImage(?string $i): void          { $this->image = $i; }
    public function setCreatedAt(?string $c): void      { $this->createdAt = $c; }

    // ── Convertir un tableau PDO en objet Recette ─────────────────────────────
    public static function fromArray(array $row): self {
        return new self(
            (int)$row['id'],
            $row['nom'],
            $row['description'] ?? null,
            $row['categorie'],
            (int)$row['duree'],
            $row['difficulte'],
            (int)$row['calories'],
            $row['image'] ?? null,
            $row['created_at'] ?? null
        );
    }

    // ── Convertir en tableau (pour les vues) ──────────────────────────────────
    public function toArray(): array {
        return [
            'id'          => $this->id,
            'nom'         => $this->nom,
            'description' => $this->description,
            'categorie'   => $this->categorie,
            'duree'       => $this->duree,
            'difficulte'  => $this->difficulte,
            'calories'    => $this->calories,
            'image'       => $this->image,
            'created_at'  => $this->createdAt,
        ];
    }

    // =========================================================================
    // Méthodes d'accès à la base de données (PDO)
    // =========================================================================

    public function getAll(): array {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("SELECT * FROM recette ORDER BY created_at DESC");
            $query->execute();
            return array_map(fn($r) => self::fromArray($r)->toArray(), $query->fetchAll());
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    public function getById(int $id): array|false {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("SELECT * FROM recette WHERE id = :id");
            $query->execute([':id' => $id]);
            $row = $query->fetch();
            return $row ? self::fromArray($row)->toArray() : false;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function create(array $d): int {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare(
                "INSERT INTO recette (nom, description, categorie, duree, difficulte, calories, image)
                 VALUES (:nom, :description, :categorie, :duree, :difficulte, :calories, :image)"
            );
            $query->execute([
                ':nom'         => $d['nom'],
                ':description' => $d['description'] ?? null,
                ':categorie'   => $d['categorie'],
                ':duree'       => $d['duree'],
                ':difficulte'  => $d['difficulte'],
                ':calories'    => $d['calories'],
                ':image'       => $d['image'] ?? null,
            ]);
            return (int)$pdo->lastInsertId();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return 0;
        }
    }

    public function update(int $id, array $d): bool {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare(
                "UPDATE recette SET nom=:nom, description=:description, categorie=:categorie,
                 duree=:duree, difficulte=:difficulte, calories=:calories, image=:image WHERE id=:id"
            );
            return $query->execute([
                ':nom'         => $d['nom'],
                ':description' => $d['description'] ?? null,
                ':categorie'   => $d['categorie'],
                ':duree'       => $d['duree'],
                ':difficulte'  => $d['difficulte'],
                ':calories'    => $d['calories'],
                ':image'       => $d['image'] ?? null,
                ':id'          => $id,
            ]);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function delete(int $id): bool {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("DELETE FROM recette WHERE id = :id");
            return $query->execute([':id' => $id]);
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function search(string $q): array {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("SELECT * FROM recette WHERE nom LIKE :q ORDER BY created_at DESC");
            $query->execute([':q' => '%' . $q . '%']);
            return array_map(fn($r) => self::fromArray($r)->toArray(), $query->fetchAll());
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    public function countFilter(string $search, string $categorie, string $difficulte): int {
        try {
            $pdo        = Database::getConnection();
            $conditions = [];
            $params     = [];
            if ($search !== '')     { $conditions[] = 'nom LIKE :search';         $params[':search']     = '%'.$search.'%'; }
            if ($categorie !== '')  { $conditions[] = 'categorie = :categorie';   $params[':categorie']  = $categorie; }
            if ($difficulte !== '') { $conditions[] = 'difficulte = :difficulte'; $params[':difficulte'] = $difficulte; }
            $sql = "SELECT COUNT(*) FROM recette";
            if (!empty($conditions)) $sql .= " WHERE ".implode(' AND ', $conditions);
            $query = $pdo->prepare($sql);
            $query->execute($params);
            return (int)$query->fetchColumn();
        } catch (PDOException $e) { echo $e->getMessage(); return 0; }
    }

    public function filterPaginated(string $search, string $categorie, string $difficulte, int $limit, int $offset): array {
        try {
            $pdo        = Database::getConnection();
            $conditions = [];
            $params     = [];
            if ($search !== '')     { $conditions[] = 'nom LIKE :search';         $params[':search']     = '%'.$search.'%'; }
            if ($categorie !== '')  { $conditions[] = 'categorie = :categorie';   $params[':categorie']  = $categorie; }
            if ($difficulte !== '') { $conditions[] = 'difficulte = :difficulte'; $params[':difficulte'] = $difficulte; }
            $sql = "SELECT * FROM recette";
            if (!empty($conditions)) $sql .= " WHERE ".implode(' AND ', $conditions);
            $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $query = $pdo->prepare($sql);
            foreach ($params as $k => $v) $query->bindValue($k, $v);
            $query->bindValue(':limit',  $limit,  \PDO::PARAM_INT);
            $query->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $query->execute();
            return array_map(fn($r) => self::fromArray($r)->toArray(), $query->fetchAll());
        } catch (PDOException $e) { echo $e->getMessage(); return []; }
    }

    public function filter(string $search, string $categorie, string $difficulte): array {
        try {
            $pdo        = Database::getConnection();
            $conditions = [];
            $params     = [];

            if ($search !== '') {
                $conditions[] = 'nom LIKE :search';
                $params[':search'] = '%' . $search . '%';
            }
            if ($categorie !== '') {
                $conditions[] = 'categorie = :categorie';
                $params[':categorie'] = $categorie;
            }
            if ($difficulte !== '') {
                $conditions[] = 'difficulte = :difficulte';
                $params[':difficulte'] = $difficulte;
            }

            $sql = "SELECT * FROM recette";
            if (!empty($conditions)) $sql .= " WHERE " . implode(' AND ', $conditions);
            $sql .= " ORDER BY created_at DESC";

            $query = $pdo->prepare($sql);
            $query->execute($params);
            return array_map(fn($r) => self::fromArray($r)->toArray(), $query->fetchAll());
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }

    // ── Calcul du Nutri-Score de la recette ──────────────────────────────────
    public function calculerNutriScore(int $recetteId): array {
        try {
            $pdo   = Database::getConnection();
            // Récupérer les totaux nutritionnels de tous les ingrédients de la recette
            $query = $pdo->prepare("
                SELECT
                    SUM(i.proteines * ri.quantite / 100) AS total_proteines,
                    SUM(i.calcium   * ri.quantite / 100) AS total_calcium,
                    SUM(i.glucides  * ri.quantite / 100) AS total_glucides,
                    SUM(i.lipides   * ri.quantite / 100) AS total_lipides,
                    SUM(ri.quantite)                     AS poids_total
                FROM recette_ingredient ri
                JOIN ingredient i ON i.id = ri.ingredient_id
                WHERE ri.recette_id = :id
            ");
            $query->execute([':id' => $recetteId]);
            $row = $query->fetch();

            $poids = (float)($row['poids_total'] ?? 0);
            if ($poids <= 0) {
                return ['lettre' => '?', 'score' => 0, 'couleur' => '#9e9e9e', 'bg' => '#f5f5f5', 'label' => 'Non calculable'];
            }

            // Ramener à 100g de recette
            $prot = round((float)($row['total_proteines'] ?? 0) / $poids * 100, 2);
            $cal  = round((float)($row['total_calcium']   ?? 0) / $poids * 100, 2);
            $gluc = round((float)($row['total_glucides']  ?? 0) / $poids * 100, 2);
            $lip  = round((float)($row['total_lipides']   ?? 0) / $poids * 100, 2);
            $kcal = round(($prot * 4) + ($gluc * 4) + ($lip * 9), 1);

            // ── Points négatifs ───────────────────────────────────────────────
            $ptsNeg = 0;

            // Calories
            if      ($kcal <= 80)  $ptsNeg += 0;
            elseif  ($kcal <= 160) $ptsNeg += 1;
            elseif  ($kcal <= 240) $ptsNeg += 2;
            elseif  ($kcal <= 320) $ptsNeg += 3;
            elseif  ($kcal <= 400) $ptsNeg += 4;
            else                   $ptsNeg += 5;

            // Lipides
            if      ($lip <= 1)  $ptsNeg += 0;
            elseif  ($lip <= 3)  $ptsNeg += 1;
            elseif  ($lip <= 6)  $ptsNeg += 2;
            elseif  ($lip <= 10) $ptsNeg += 3;
            else                 $ptsNeg += 4;

            // Glucides
            if      ($gluc <= 5)  $ptsNeg += 0;
            elseif  ($gluc <= 10) $ptsNeg += 1;
            elseif  ($gluc <= 20) $ptsNeg += 2;
            elseif  ($gluc <= 40) $ptsNeg += 3;
            else                  $ptsNeg += 4;

            // ── Points positifs ───────────────────────────────────────────────
            $ptsPos = 0;

            // Protéines
            if      ($prot <= 2)  $ptsPos += 0;
            elseif  ($prot <= 5)  $ptsPos += 1;
            elseif  ($prot <= 10) $ptsPos += 2;
            else                  $ptsPos += 3;

            // Calcium
            if      ($cal <= 50)  $ptsPos += 0;
            elseif  ($cal <= 150) $ptsPos += 1;
            else                  $ptsPos += 2;

            // ── Score final ───────────────────────────────────────────────────
            $score = $ptsNeg - $ptsPos;

            if      ($score <= -1) $lettre = 'A';
            elseif  ($score <= 2)  $lettre = 'B';
            elseif  ($score <= 7)  $lettre = 'C';
            elseif  ($score <= 11) $lettre = 'D';
            else                   $lettre = 'E';

            $config = [
                'A' => ['couleur' => '#fff', 'bg' => '#1a7a1a', 'label' => 'Excellent pour la santé'],
                'B' => ['couleur' => '#fff', 'bg' => '#5aab1f', 'label' => 'Bon pour la santé'],
                'C' => ['couleur' => '#333', 'bg' => '#f5c800', 'label' => 'Qualité nutritionnelle moyenne'],
                'D' => ['couleur' => '#fff', 'bg' => '#e07800', 'label' => 'Qualité nutritionnelle médiocre'],
                'E' => ['couleur' => '#fff', 'bg' => '#d32f2f', 'label' => 'Mauvais pour la santé'],
            ];

            return [
                'lettre'     => $lettre,
                'score'      => $score,
                'couleur'    => $config[$lettre]['couleur'],
                'bg'         => $config[$lettre]['bg'],
                'label'      => $config[$lettre]['label'],
                'details'    => [
                    'proteines' => $prot,
                    'calcium'   => $cal,
                    'glucides'  => $gluc,
                    'lipides'   => $lip,
                    'calories'  => $kcal,
                    'ptsNeg'    => $ptsNeg,
                    'ptsPos'    => $ptsPos,
                ],
            ];
        } catch (PDOException $e) {
            return ['lettre' => '?', 'score' => 0, 'couleur' => '#9e9e9e', 'bg' => '#f5f5f5', 'label' => 'Erreur de calcul'];
        }
    }

    // ── Jointure : recettes avec leurs ingrédients ────────────────────────────
    public function getRecettesAvecIngredients(): array {
        try {
            $pdo   = Database::getConnection();
            $query = $pdo->prepare("
                SELECT r.*, i.nom AS ingredient_nom, i.type AS ingredient_type,
                       ri.quantite, ri.unite
                FROM recette r
                LEFT JOIN recette_ingredient ri ON r.id = ri.recette_id
                LEFT JOIN ingredient i ON i.id = ri.ingredient_id
                ORDER BY r.created_at DESC, i.nom ASC
            ");
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
