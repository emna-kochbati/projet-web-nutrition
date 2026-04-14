<?php

class Program
{
    private $id = null;
    private $name = null;
    private $description = null;
    private $level = null;

    public function __construct($name, $description, $level)
    {
        $this->name = $name;
        $this->description = $description;
        $this->level = $level;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }
}
