<?php
class Restaurant {
    private ?int    $id;
    private string  $nom;
    private ?string $description;
    private string  $adresse;
    private ?string $telephone;
    private ?string $email;
    private string  $type_cuisine;
    private ?string $image;
    private ?string $created_at;

    public function __construct(
        ?int    $id           = null,
        string  $nom          = '',
        ?string $description  = null,
        string  $adresse      = '',
        ?string $telephone    = null,
        ?string $email        = null,
        string  $type_cuisine = '',
        ?string $image        = null,
        ?string $created_at   = null
    ) {
        $this->id           = $id;
        $this->nom          = $nom;
        $this->description  = $description;
        $this->adresse      = $adresse;
        $this->telephone    = $telephone;
        $this->email        = $email;
        $this->type_cuisine = $type_cuisine;
        $this->image        = $image;
        $this->created_at   = $created_at;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): self { $this->id = $id; return $this; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getAdresse(): string { return $this->adresse; }
    public function setAdresse(string $adresse): self { $this->adresse = $adresse; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): self { $this->telephone = $telephone; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getTypeCuisine(): string { return $this->type_cuisine; }
    public function setTypeCuisine(string $type_cuisine): self { $this->type_cuisine = $type_cuisine; return $this; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): self { $this->created_at = $created_at; return $this; }
}
