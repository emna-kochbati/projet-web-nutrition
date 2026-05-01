<?php

class EventType {
    private ?int $id;
    private string $label;
    private ?string $image;

    public function __construct(?int $id = null, string $label = '', ?string $image = null) {
        $this->id = $id;
        $this->label = $label;
        $this->image = $image;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): self {
        $this->id = $id;
        return $this;
    }

    public function getLabel(): string {
        return $this->label;
    }

    public function setLabel(string $label): self {
        $this->label = $label;
        return $this;
    }

    public function getImage(): ?string {
        return $this->image;
    }

    public function setImage(?string $image): self {
        $this->image = $image;
        return $this;
    }
}

