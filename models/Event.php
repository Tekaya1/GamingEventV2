<?php
class Event {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Create new event
    public function createEvent($title, $description, $game_type, $start_date, $end_date, $max_participants, $prize_pool, $rules, $created_by) {
        $query = 'INSERT INTO events 
                 (title, description, game_type, start_date, end_date, max_participants, prize_pool, rules, created_by) 
                 VALUES (:title, :description, :game_type, :start_date, :end_date, :max_participants, :prize_pool, :rules, :created_by)';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':game_type', $game_type);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':max_participants', $max_participants);
        $stmt->bindParam(':prize_pool', $prize_pool);
        $stmt->bindParam(':rules', $rules);
        $stmt->bindParam(':created_by', $created_by);
        
        return $stmt->execute();
    }

    // Get all events
    public function getAllEvents($status = null) {
        $query = 'SELECT e.*, u.username as creator 
                 FROM events e 
                 JOIN users u ON e.created_by = u.id';
        
        if ($status) {
            $query .= ' WHERE e.status = :status';
        }
        
        $query .= ' ORDER BY e.start_date ASC';
        
        $stmt = $this->db->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get event by ID
    public function getEventById($id) {
        $query = 'SELECT e.*, u.username as creator 
                 FROM events e 
                 JOIN users u ON e.created_by = u.id 
                 WHERE e.id = :id';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update event
    public function updateEvent($id, $title, $description, $game_type, $start_date, $end_date, $max_participants, $prize_pool, $rules, $status) {
        $query = 'UPDATE events SET 
                 title = :title, 
                 description = :description, 
                 game_type = :game_type, 
                 start_date = :start_date, 
                 end_date = :end_date, 
                 max_participants = :max_participants, 
                 prize_pool = :prize_pool, 
                 rules = :rules, 
                 status = :status 
                 WHERE id = :id';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':game_type', $game_type);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':max_participants', $max_participants);
        $stmt->bindParam(':prize_pool', $prize_pool);
        $stmt->bindParam(':rules', $rules);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Delete event
    public function deleteEvent($id) {
        $query = 'DELETE FROM events WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Register user for event
    public function registerForEvent($user_id, $event_id) {
        // Check if registration already exists
        $check_query = 'SELECT id FROM registrations WHERE user_id = :user_id AND event_id = :event_id';
        $check_stmt = $this->db->prepare($check_query);
        $check_stmt->bindParam(':user_id', $user_id);
        $check_stmt->bindParam(':event_id', $event_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            return false; // Already registered
        }
        
        // Check if event has available slots
        $event = $this->getEventById($event_id);
        if ($event['max_participants'] && $event['current_participants'] >= $event['max_participants']) {
            return false; // Event is full
        }
        
        // Create registration
        $query = 'INSERT INTO registrations (user_id, event_id) VALUES (:user_id, :event_id)';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);
        
        if ($stmt->execute()) {
            // Update participant count
            $update_query = 'UPDATE events SET current_participants = current_participants + 1 WHERE id = :event_id';
            $update_stmt = $this->db->prepare($update_query);
            $update_stmt->bindParam(':event_id', $event_id);
            $update_stmt->execute();
            
            return true;
        }
        
        return false;
    }

    // Get registrations for event
    public function getEventRegistrations($event_id) {
        $query = 'SELECT r.*, u.username, u.avatar 
                 FROM registrations r 
                 JOIN users u ON r.user_id = u.id 
                 WHERE r.event_id = :event_id 
                 ORDER BY r.ranking ASC, r.score DESC';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get user's registered events
    public function getUserRegistrations($user_id) {
        $query = 'SELECT e.*, r.status as registration_status 
                 FROM events e 
                 JOIN registrations r ON e.id = r.event_id 
                 WHERE r.user_id = :user_id 
                 ORDER BY e.start_date ASC';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //getEventsCount
    public function getEventsCount() {
        $query = 'SELECT COUNT(*) as count FROM events';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }

    // Update registration status (attended/no_show)
    public function updateRegistrationStatus($registration_id, $status) {
        $query = 'UPDATE registrations SET status = :status WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $registration_id);
        
        return $stmt->execute();
    }

    // Update player score and ranking
    public function updatePlayerScore($registration_id, $score, $ranking) {
        $query = 'UPDATE registrations SET score = :score, ranking = :ranking WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':score', $score);
        $stmt->bindParam(':ranking', $ranking);
        $stmt->bindParam(':id', $registration_id);
        
        return $stmt->execute();
    }

    // Get leaderboard for a game type
    public function getLeaderboard($game_type, $limit = 10) {
        $query = 'SELECT l.*, u.username, u.avatar 
                 FROM leaderboard l 
                 JOIN users u ON l.user_id = u.id 
                 JOIN events e ON l.event_id = e.id 
                 WHERE e.game_type = :game_type 
                 ORDER BY l.total_score DESC 
                 LIMIT :limit';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':game_type', $game_type);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>