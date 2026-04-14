<?php
require_once 'config.php';

class Event {
    public function getAll() {
        $db = Db::getConnexion();
        $req = $db->query('SELECT e.*, t.label as type_label FROM event e JOIN event_type t ON e.id_type = t.id');
        return $req->fetchAll();
    }

    public function getById($id) {
        $db = Db::getConnexion();
        $req = $db->prepare('SELECT e.*, t.label as type_label FROM event e JOIN event_type t ON e.id_type = t.id WHERE e.id = :id');
        $req->execute(['id' => $id]);
        return $req->fetch();
    }

    public function create($name, $id_type, $date, $location, $number_of_participants) {
        $db = Db::getConnexion();
        $req = $db->prepare('INSERT INTO event (name, id_type, date, location, number_of_participants) VALUES (:name, :id_type, :date, :location, :number_of_participants)');
        $req->execute([
            'name' => $name,
            'id_type' => $id_type,
            'date' => $date,
            'location' => $location,
            'number_of_participants' => $number_of_participants
        ]);
    }

    public function update($id, $name, $id_type, $date, $location, $number_of_participants) {
        $db = Db::getConnexion();
        $req = $db->prepare('UPDATE event SET name = :name, id_type = :id_type, date = :date, location = :location, number_of_participants = :number_of_participants WHERE id = :id');
        $req->execute([
            'id' => $id,
            'name' => $name,
            'id_type' => $id_type,
            'date' => $date,
            'location' => $location,
            'number_of_participants' => $number_of_participants
        ]);
    }

    public function delete($id) {
        $db = Db::getConnexion();
        $req = $db->prepare('DELETE FROM event WHERE id = :id');
        $req->execute(['id' => $id]);
    }

    public function incrementParticipants($id) {
        $db = Db::getConnexion();
        $req = $db->prepare('UPDATE event SET number_of_participants = number_of_participants + 1 WHERE id = :id');
        $req->execute(['id' => $id]);
    }
}