<?php

class Exercise
{
    private $id = null;
    private $program_id = null;
    private $name = null;
    private $reps = null;
    private $sets = null;

    public function __construct($program_id, $name, $reps, $sets)
    {
        $this->program_id = $program_id;
        $this->name = $name;
        $this->reps = $reps;
        $this->sets = $sets;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getProgram_id()
    {
        return $this->program_id;
    }

    public function setProgram_id($program_id)
    {
        $this->program_id = $program_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getReps()
    {
        return $this->reps;
    }

    public function setReps($reps)
    {
        $this->reps = $reps;
    }

    public function getSets()
    {
        return $this->sets;
    }

    public function setSets($sets)
    {
        $this->sets = $sets;
    }
}
