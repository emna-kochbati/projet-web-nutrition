<?php
require_once 'config.php';

class EventType {
    public function getAll() {
        $db = Db::getConnexion();
        $req = $db->query('SELECT * FROM event_type');
        return $req->fetchAll();
    }

    public function getById($id) {
        $db = Db::getConnexion();
        $req = $db->prepare('SELECT * FROM event_type WHERE id = :id');
        $req->execute(['id' => $id]);
        return $req->fetch();
    }

    public function create($label) {
        $db = Db::getConnexion();
        $req = $db->prepare('INSERT INTO event_type (label) VALUES (:label)');
        $req->execute(['label' => $label]);
    }

    public function update($id, $label) {
        $db = Db::getConnexion();
        $req = $db->prepare('UPDATE event_type SET label = :label WHERE id = :id');
        $req->execute(['id' => $id, 'label' => $label]);
    }

    public function delete($id) {
        $db = Db::getConnexion();
        $req = $db->prepare('DELETE FROM event_type WHERE id = :id');
        $req->execute(['id' => $id]);
    }
}
