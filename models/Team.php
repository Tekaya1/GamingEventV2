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

    public function updateMemberRole($teamId, $memberId, $newRole, $requesterId)
    {
        // Verify requester is team leader
        if (!$this->isTeamLeader($teamId, $requesterId)) {
            return false;
        }

        // Validate role
        if (!in_array($newRole, ['member', 'co-leader', 'leader'])) {
            return false;
        }

        // Special handling for leader transfers
        if ($newRole === 'leader') {
            // Demote current leader first
            $this->db->prepare("
            UPDATE team_members 
            SET role = 'member' 
            WHERE team_id = ? AND role = 'leader'
        ")->execute([$teamId]);
        }

        $stmt = $this->db->prepare("
        UPDATE team_members 
        SET role = ? 
        WHERE team_id = ? AND user_id = ?
    ");
        return $stmt->execute([$newRole, $teamId, $memberId]);
    }
    public function sendInvitation($teamId, $senderId, $recipientId)
    {
        // Check if user is already a member
        if ($this->isTeamMember($teamId, $recipientId)) {
            return false;
        }

        // Check if invitation already exists
        $stmt = $this->db->prepare("
        SELECT id FROM team_invitations 
        WHERE team_id = ? AND recipient_id = ? AND status = 'pending'
    ");
        $stmt->execute([$teamId, $recipientId]);
        if ($stmt->fetch()) {
            return false;
        }

        $stmt = $this->db->prepare("
        INSERT INTO team_invitations 
        (team_id, sender_id, recipient_id, status, created_at) 
        VALUES (?, ?, ?, 'pending', NOW())
    ");
        return $stmt->execute([$teamId, $senderId, $recipientId]);
    }

    public function getPendingInvitations($teamId)
    {
        $stmt = $this->db->prepare("
        SELECT ti.*, u.username as recipient_name, u.avatar as recipient_avatar
        FROM team_invitations ti
        JOIN users u ON ti.recipient_id = u.id
        WHERE ti.team_id = ? AND ti.status = 'pending'
    ");
        $stmt->execute([$teamId]);
        return $stmt->fetchAll();
    }

    public function cancelInvitation($inviteId, $teamId, $userId)
    {
        // Verify the user is the team leader
        if (!$this->isTeamLeader($teamId, $userId)) {
            return false;
        }

        $stmt = $this->db->prepare("
        DELETE FROM team_invitations 
        WHERE id = ? AND team_id = ?
    ");
        return $stmt->execute([$inviteId, $teamId]);
    }
    public function removeMember($teamId, $memberId, $requesterId)
    {
        // Verify requester is team leader
        if (!$this->isTeamLeader($teamId, $requesterId)) {
            return false;
        }

        // Can't remove yourself as leader (should use leaveTeam instead)
        if ($memberId == $requesterId) {
            return false;
        }

        $stmt = $this->db->prepare("
        DELETE FROM team_members 
        WHERE team_id = ? AND user_id = ?
    ");
        return $stmt->execute([$teamId, $memberId]);
    }
}
?>