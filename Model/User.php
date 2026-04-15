<?php

require_once __DIR__ . '/../Config/database.php';

class User {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /* =========================
       LOGIN
    ========================== */
    public function login($email, $password) {

        $stmt = $this->db->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // ✔ login simple (cours)
        if ($user && $password === $user['password']) {
            return $user;
        }

        return false;
    }

    /* =========================
       CREATE USER
    ========================== */
    public function create($data) {

        $stmt = $this->db->prepare("
            INSERT INTO user (nom, email, password, poids, taille, objectif)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['nom'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['poids'] ?? null,
            $data['taille'] ?? null,
            $data['objectif'] ?? 'Autre'
        ]);
    }

    /* =========================
       GET ALL USERS
    ========================== */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM user ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       GET USER BY ID
    ========================== */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       UPDATE USER
    ========================== */
    public function update($id, $data) {

        $stmt = $this->db->prepare("
            UPDATE user 
            SET nom = ?, email = ?, poids = ?, taille = ?, objectif = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['nom'] ?? '',
            $data['email'] ?? '',
            $data['poids'] ?? null,
            $data['taille'] ?? null,
            $data['objectif'] ?? 'Autre',
            $id
        ]);
    }

    /* =========================
       DELETE USER
    ========================== */
    public function delete($id) {

        $stmt = $this->db->prepare("DELETE FROM user WHERE id = ?");
        return $stmt->execute([$id]);
    }
}