<?php
class Recette {
    private ?int    $id;
    private string  $nom;
    private ?string $description;
    private ?string $ingredients;
    private ?string $categorie;
    private ?int    $temps_preparation;
    private ?int    $calories;
    private ?string $image;
    private ?string $created_at;

    public function __construct(
        ?int    $id                = null,
        string  $nom               = '',
        ?string $description       = null,
        ?string $ingredients       = null,
        ?string $categorie         = null,
        ?int    $temps_preparation = null,
        ?int    $calories          = null,
        ?string $image             = null,
        ?string $created_at        = null
    ) {
        $this->id                = $id;
        $this->nom               = $nom;
        $this->description       = $description;
        $this->ingredients       = $ingredients;
        $this->categorie         = $categorie;
        $this->temps_preparation = $temps_preparation;
        $this->calories          = $calories;
        $this->image             = $image;
        $this->created_at        = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getIngredients(): ?string { return $this->ingredients; }
    public function setIngredients(?string $ingredients): self { $this->ingredients = $ingredients; return $this; }

    public function getCategorie(): ?string { return $this->categorie; }
    public function setCategorie(?string $categorie): self { $this->categorie = $categorie; return $this; }

    public function getTempsPreparation(): ?int { return $this->temps_preparation; }
    public function setTempsPreparation(?int $temps_preparation): self { $this->temps_preparation = $temps_preparation; return $this; }

    public function getCalories(): ?int { return $this->calories; }
    public function setCalories(?int $calories): self { $this->calories = $calories; return $this; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}
