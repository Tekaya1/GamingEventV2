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
}
