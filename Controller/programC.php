<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/program.php';

class ProgramC
{

    public function create($program)
    {
        $sql = 'INSERT INTO programs (name, description, level) VALUES (:name, :description, :level)';
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(array(
                'name' => $program->getName(),
                'description' => $program->getDescription(),
                'level' => $program->getLevel(),
            ));
            header('Location: programs.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function read()
    {
        $sql = 'SELECT * FROM programs';
        $db = config::getConnexion();

        try {
            return $db->query($sql);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function findone($id)
    {
        $sql = 'SELECT * FROM programs WHERE id = :id';
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(array('id' => $id));
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    public function update($program, $id)
    {
        $sql = 'UPDATE programs SET name = :name, description = :description, level = :level WHERE id = :id';
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(array(
                'name' => $program->getName(),
                'description' => $program->getDescription(),
                'level' => $program->getLevel(),
                'id' => $id,
            ));
            header('Location: programs.php');
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    public function delete()
    {
        if (isset($_GET['delete'])) {
            $db = config::getConnexion();
            $sql = 'DELETE FROM programs WHERE id = :id';

            try {
                $query = $db->prepare($sql);
                $query->execute(array('id' => $_GET['delete']));
                header('Location: programs.php');
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
    }
}
