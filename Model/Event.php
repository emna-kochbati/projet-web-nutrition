<?php

class Event {
    private ?int $id;
    private string $title;
    private int $idType;
    private string $date;
    private string $location;
    private int $numberOfParticipants;

    public function __construct(
        ?int $id = null,
        string $title = '',
        int $idType = 0,
        string $date = '',
        string $location = '',
        int $numberOfParticipants = 0
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->idType = $idType;
        $this->date = $date;
        $this->location = $location;
        $this->numberOfParticipants = $numberOfParticipants;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): self {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;
        return $this;
    }

    public function getIdType(): int {
        return $this->idType;
    }

    public function setIdType(int $idType): self {
        $this->idType = $idType;
        return $this;
    }

    public function getDate(): string {
        return $this->date;
    }

    public function setDate(string $date): self {
        $this->date = $date;
        return $this;
    }

    public function getLocation(): string {
        return $this->location;
    }

    public function setLocation(string $location): self {
        $this->location = $location;
        return $this;
    }

    public function getNumberOfParticipants(): int {
        return $this->numberOfParticipants;
    }

    public function setNumberOfParticipants(int $numberOfParticipants): self {
        $this->numberOfParticipants = $numberOfParticipants;
        return $this;
    }
}
