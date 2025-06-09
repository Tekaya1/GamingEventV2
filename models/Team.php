<?php
require_once __DIR__ . '/../config/database.php';
class Team
{
    private $db;

    public function __construct()
    {
        // Utiliser le PDO défini globalement
        global $pdo;

        if (!$pdo) {
            throw new Exception('Database connection not initialized.');
        }

        $this->db = $pdo;
    }

    // Create a new team
    public function createTeam($data)
    {
        $stmt = $this->db->prepare("INSERT INTO teams (name, description, max_members, logo, created_by) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['name'], $data['description'], $data['max_members'], $data['logo'], $data['created_by']]);
    }

    // Get all teams
    public function getAllTeams()
    {
        $stmt = $this->db->query("
            SELECT t.*, u.username as creator_name, 
                   COUNT(tm.user_id) as member_count
            FROM teams t
            LEFT JOIN users u ON t.created_by = u.id
            LEFT JOIN team_members tm ON t.id = tm.team_id
            GROUP BY t.id
            ORDER BY t.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get team by ID
    public function getTeamById($id)
    {
        $stmt = $this->db->prepare("
            SELECT t.*, u.username as creator_name
            FROM teams t
            LEFT JOIN users u ON t.created_by = u.id
            WHERE t.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get team members
    public function getTeamMembers($teamId)
    {
            $stmt = $this->db->prepare("
                SELECT tm.*, u.username, u.avatar
                FROM team_members tm
                JOIN users u ON tm.user_id = u.id
                WHERE tm.team_id = ?
                ORDER BY tm.role DESC, tm.joined_at ASC
            ");
        $stmt->execute([$teamId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result !== false ? $result : [];
    }

    // Join a team
    public function joinTeam($teamId, $userId)
    {
        $stmt = $this->db->prepare("
            INSERT INTO team_members (team_id, user_id, role) 
            VALUES (?, ?, 'member')
            ON DUPLICATE KEY UPDATE role=VALUES(role)
        ");
        return $stmt->execute([$teamId, $userId]);
    }

    // Leave a team
    public function leaveTeam($teamId, $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM team_members WHERE team_id = ? AND user_id = ?");
        return $stmt->execute([$teamId, $userId]);
    }

    // Check if user is team member
    public function isTeamMember($teamId, $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM team_members WHERE team_id = ? AND user_id = ?");
        $stmt->execute([$teamId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Check if user is team leader
    public function isTeamLeader($teamId, $userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM team_members WHERE team_id = ? AND user_id = ? AND role = 'leader'");
        $stmt->execute([$teamId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    // Delete team
    public function deleteTeam($teamId)
    {
        $stmt = $this->db->prepare("DELETE FROM teams WHERE id = ?");
        return $stmt->execute([$teamId]);
    }

    public function getLastInsertId()
    {
        return $this->db->lastInsertId();
    }
}
?>