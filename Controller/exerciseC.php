<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/exercise.php';

class ExerciseC
{

    public function create($exercise)
    {
        $sql = 'INSERT INTO exercises (program_id, name, reps, sets) VALUES (:program_id, :name, :reps, :sets)';
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(array(
                'program_id' => $exercise->getProgram_id(),
                'name' => $exercise->getName(),
                'reps' => $exercise->getReps(),
                'sets' => $exercise->getSets(),
            ));
            header('Location: exercises.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function read()
    {
        $sql = 'SELECT * FROM exercises';
        $db = config::getConnexion();

        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function readWithPrograms()
    {
        $sql = 'SELECT exercises.*, programs.name AS program_name, programs.level AS program_level FROM exercises INNER JOIN programs ON exercises.program_id = programs.id';
        $db = config::getConnexion();

        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function readByProgram($programId)
    {
        $sql = 'SELECT exercises.*, programs.name AS program_name, programs.level AS program_level FROM exercises INNER JOIN programs ON exercises.program_id = programs.id WHERE exercises.program_id = :program_id';
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(array('program_id' => $programId));
            return $query;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function findone($id)
    {
        $sql = 'SELECT * FROM exercises WHERE id = :id';
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(array('id' => $id));
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function update($exercise, $id)
    {
        $sql = 'UPDATE exercises SET program_id = :program_id, name = :name, reps = :reps, sets = :sets WHERE id = :id';
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(array(
                'program_id' => $exercise->getProgram_id(),
                'name' => $exercise->getName(),
                'reps' => $exercise->getReps(),
                'sets' => $exercise->getSets(),
                'id' => $id,
            ));
            header('Location: exercises.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function delete()
    {
        if (isset($_GET['delete'])) {
            $db = config::getConnexion();
            $sql = 'DELETE FROM exercises WHERE id = :id';

            try {
                $query = $db->prepare($sql);
                $query->execute(array('id' => $_GET['delete']));
                header('Location: exercises.php');
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
    }
}
