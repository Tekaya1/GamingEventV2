<?php
require_once __DIR__ . '/../config/database.php';

class Event
{
    private $db;

    public function __construct()
    {
        global $pdo;

        if (!$pdo) {
            throw new Exception("Connexion PDO introuvable.");
        }

        $this->db = $pdo;
    }

    public function getAllEvents()
    {
        $stmt = $this->db->query("SELECT * FROM events ORDER BY start_date DESC");
        return $stmt->fetchAll();
    }

    public function getEventById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createEvent($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO events (title, description, game_id, start_date, end_date, max_participants, prize_pool, rules, status, created_by)
            VALUES (:title, :description, :game_id, :start_date, :end_date, :max_participants, :prize_pool, :rules, :status, :created_by)
        ");
        return $stmt->execute($data);
    }

    public function deleteEvent($id)
    {
        $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateEvent($id, $data)
    {
        $query = "UPDATE events SET 
            title = :title,
            description = :description,
            game_id = :game_id,
            start_date = :start_date,
            end_date = :end_date,
            max_participants = :max_participants,
            prize_pool = :prize_pool,
            rules = :rules,
            status = :status
        WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $data['id'] = $id;

        return $stmt->execute($data);
    }

    public function getEventsByStatus($status)
    {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE status = ?");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
}
