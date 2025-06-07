<?php
require_once __DIR__ . '/../config/database.php';

class Game {
    private $db;

    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    public function getAllGames() {
        $stmt = $this->db->query("SELECT * FROM games ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    // 🔧 MÉTHODE À AJOUTER ICI :
    public function getGameById($id) {
        $stmt = $this->db->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    // suppression 
    public function deleteGame($id) {
    $stmt = $this->db->prepare("DELETE FROM games WHERE id = ?");
    return $stmt->execute([$id]);
}

}

