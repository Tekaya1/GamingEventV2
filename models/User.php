<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    private $db;
    private $id;
    private $username;
    private $email;
    private $role;
    private $avatar;

    public function __construct()
    {
        // Utiliser le PDO défini globalement
        global $pdo;

        if (!$pdo) {
            throw new Exception('Database connection not initialized.');
        }

        $this->db = $pdo;
    }

    public function connect()
    {
        return $this->db;
    }

    public function register($username, $email, $password, $role = 'player')
    {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $query = 'INSERT INTO users (username, email, password, role) 
                  VALUES (:username, :email, :password, :role)';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);

        return $stmt->execute();
    }

    public function login($username, $password)
    {
        $query = 'SELECT * FROM users WHERE username = :username';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->username = $user['username'];
            $this->email = $user['email'];
            $this->role = $user['role'];
            $this->avatar = $user['avatar'];

            $_SESSION['user_id'] = $this->id;
            $_SESSION['username'] = $this->username;
            $_SESSION['role'] = $this->role;

            return true;
        }

        return false;
    }

    public function getAllUsers()
    {
        $query = 'SELECT id, username, email, role, avatar, created_at FROM users';
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id)
    {
        $query = 'SELECT * FROM users WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserCount()
    {
        $query = 'SELECT COUNT(*) as count FROM users';
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function updateProfile($id, $username, $email, $avatar = null)
    {
        $query = 'UPDATE users SET username = :username, email = :email';
        $params = [':username' => $username, ':email' => $email, ':id' => $id];

        if ($avatar) {
            $query .= ', avatar = :avatar';
            $params[':avatar'] = $avatar;
        }

        $query .= ' WHERE id = :id';

        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function usernameExists($username)
    {
        $query = 'SELECT id FROM users WHERE username = :username';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    public function searchUsers($query, $team_id)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT id, username, avatar 
            FROM users 
            WHERE username LIKE :query
            AND id NOT IN (
                SELECT user_id FROM team_members WHERE team_id = :team_id
            )
            AND id NOT IN (
                SELECT recipient_id FROM team_invitations 
                WHERE team_id = :team_id2 AND status = 'pending'
            )
            LIMIT 10
        ");

            $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
            $stmt->bindValue(':team_id', $team_id, PDO::PARAM_INT);
            $stmt->bindValue(':team_id2', $team_id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in searchUsers: " . $e->getMessage());
            return [];
        }
    }



    public function emailExists($email)
    {
        $query = 'SELECT id FROM users WHERE email = :email';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getRole()
    {
        return $this->role;
    }
    public function getAvatar()
    {
        return $this->avatar;
    }
}
