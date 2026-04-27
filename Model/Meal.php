<?php
class Meal {
    private ?int    $id;
    private int     $restaurant_id;
    private string  $nom;
    private ?string $description;
    private float   $prix;
    private string  $categorie;
    private ?int    $calories;
    private int     $disponible;
    private ?string $image;
    private ?string $created_at;

    public function __construct(
        ?int    $id            = null,
        int     $restaurant_id = 0,
        string  $nom           = '',
        ?string $description   = null,
        float   $prix          = 0.0,
        string  $categorie     = 'plat_principal',
        ?int    $calories      = null,
        int     $disponible    = 1,
        ?string $image         = null,
        ?string $created_at    = null
    ) {
        $this->id            = $id;
        $this->restaurant_id = $restaurant_id;
        $this->nom           = $nom;
        $this->description   = $description;
        $this->prix          = $prix;
        $this->categorie     = $categorie;
        $this->calories      = $calories;
        $this->disponible    = $disponible;
        $this->image         = $image;
        $this->created_at    = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getRestaurantId(): int { return $this->restaurant_id; }
    public function setRestaurantId(int $restaurant_id): self { $this->restaurant_id = $restaurant_id; return $this; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getPrix(): float { return $this->prix; }
    public function setPrix(float $prix): self { $this->prix = $prix; return $this; }

    public function getCategorie(): string { return $this->categorie; }
    public function setCategorie(string $categorie): self { $this->categorie = $categorie; return $this; }

    public function getCalories(): ?int { return $this->calories; }
    public function setCalories(?int $calories): self { $this->calories = $calories; return $this; }

    public function getDisponible(): int { return $this->disponible; }
    public function setDisponible(int $disponible): self { $this->disponible = $disponible; return $this; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}
