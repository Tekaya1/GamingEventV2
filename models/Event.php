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

    // Obtenir tous les événements avec le nom du jeu associé

    public function getAllEvents()
    {
        $stmt = $this->db->query("
        SELECT events.*, games.name AS game_name
        FROM events
        LEFT JOIN games ON events.game_id = games.id
        ORDER BY events.start_date DESC
    ");
        return $stmt->fetchAll();
    }


    // Récupérer un événement par son ID
    public function getEventById($id)
    {
        $stmt = $this->db->prepare("
            SELECT e.*, g.name AS game_name
            FROM events e
            LEFT JOIN games g ON e.game_id = g.id
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Créer un nouvel événement
    public function createEvent($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO events (title, description, game_id, start_date, end_date, max_participants, prize_pool, rules, status, created_by)
            VALUES (:title, :description, :game_id, :start_date, :end_date, :max_participants, :prize_pool, :rules, :status, :created_by)
        ");
        return $stmt->execute($data);
    }

    // Supprimer un événement
    public function deleteEvent($id)
    {
        $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Mettre à jour un événement
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

    // Récupérer les événements par statut (ex : ongoing, upcoming)
    public function getEventsByStatus($status)
    {
        $stmt = $this->db->prepare("
            SELECT e.*, g.name AS game_name
            FROM events e
            LEFT JOIN games g ON e.game_id = g.id
            WHERE e.status = ?
        ");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
}
