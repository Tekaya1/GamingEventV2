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

    public function getAllEvents($status = null)
    {
        if ($status !== null) {
            $stmt = $this->db->prepare("
                SELECT e.*, g.name AS game_name
                FROM events e
                INNER JOIN games g ON e.game_id = g.id
                WHERE e.status = ?
                ORDER BY e.start_date DESC
            ");
            $stmt->execute([$status]);
            return $stmt->fetchAll();
        } else {
            $stmt = $this->db->query("
                SELECT e.*, g.name AS game_name
                FROM events e
                INNER JOIN games g ON e.game_id = g.id
                ORDER BY e.start_date DESC
            ");
            return $stmt->fetchAll();
        }
    }

    // Récupérer les inscriptions à un événement
    public function getEventRegistrations($eventId)
    {
        $stmt = $this->db->prepare("
            SELECT r.*, u.username as username , u.avatar as avatar
            FROM registrations r
            INNER JOIN users u ON r.user_id = u.id
            WHERE r.event_id = ?
        ");
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    // Récupérer un événement par son ID
    //getEventById
    public function getEventById($id)
    {
        $stmt = $this->db->prepare("
            SELECT e.*, g.name AS game_name
            FROM events e
            INNER JOIN games g ON e.game_id = g.id
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Récupérer les inscriptions d'un utilisateur
    public function getUserRegistrations($userId)
    {
        $stmt = $this->db->prepare("
            SELECT r.*, e.title AS event_title, g.name AS game_name ,
                   e.start_date, e.end_date, e.status
            FROM registrations r
            INNER JOIN events e ON r.event_id = e.id
            INNER JOIN games g ON e.game_id = g.id
            WHERE r.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Récupérer le classement des événements
    public function getLeaderboard()
    {
        $stmt = $this->db->query("
            SELECT e.*, g.name AS game_name
            FROM events e
            INNER JOIN games g ON e.game_id = g.id
            WHERE e.status = 'completed'
            ORDER BY e.prize_pool DESC
            LIMIT 10
        ");
        return $stmt->fetchAll();
    }

    // Créer un nouvel événement
    public function createEvent($data)
    {
        $stmt = $this->db->prepare("
        INSERT INTO events (
            title, 
            description, 
            game_id, 
            start_date, 
            end_date, 
            max_participants, 
            prize_pool, 
            rules, 
            status, 
            creator, 
            created_by,
            video_url,
            video_type,
            video_thumbnail
        ) VALUES (
            :title, 
            :description, 
            :game_id, 
            :start_date, 
            :end_date, 
            :max_participants, 
            :prize_pool, 
            :rules, 
            :status, 
            :creator, 
            :created_by,
            :video_url,
            :video_type,
            :video_thumbnail
        )
    ");

        // Ensure all video fields are set in the data array
        $data['video_url'] = $data['video_url'] ?? null;
        $data['video_type'] = $data['video_type'] ?? null;
        $data['video_thumbnail'] = $data['video_thumbnail'] ?? null;

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
        status = :status,
        video_url = :video_url,
        video_type = :video_type,
        video_thumbnail = :video_thumbnail
    WHERE id = :id";

        $stmt = $this->db->prepare($query);

        // Ensure all video fields are set in the data array
        $data['video_url'] = $data['video_url'] ?? null;
        $data['video_type'] = $data['video_type'] ?? null;
        $data['video_thumbnail'] = $data['video_thumbnail'] ?? null;
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


    //registerForEvent
    public function registerForEvent($user_id, $event_id)
    {
        // Vérifier si l'utilisateur est déjà inscrit
        $stmt = $this->db->prepare("SELECT * FROM registrations WHERE event_id = ? AND user_id = ?");
        $stmt->execute([$event_id, $user_id]);

        if ($stmt->rowCount() == 0) {
            // Inscrire l'utilisateur dans l'événement
            $stmt = $this->db->prepare("INSERT INTO registrations (event_id, user_id) VALUES (?, ?)");
            $stmt->execute([$event_id, $user_id]);

            // Incrémenter le nombre de participants dans l'événement
            $stmt = $this->db->prepare("UPDATE events SET current_participants = current_participants + 1 WHERE id = ?");
            $stmt->execute([$event_id]);

            return true; // L'inscription a réussi
        }

        return false; // L'utilisateur est déjà inscrit
    }
}
