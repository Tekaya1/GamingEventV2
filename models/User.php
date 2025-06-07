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
        try {
            // Instantiate Database and get connection
            $database = new Database();
            $this->db = $database->connect();
            if ($this->db === null) {
                throw new Exception('Failed to connect to the database');
            }
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("User class constructor error: " . $e->getMessage());
            // Optionally, handle the error gracefully (e.g., redirect or throw)
            throw new Exception('Database connection failed. Please try again later.');
        }
    }

    public function connect()
    {
        return $this->db;
    }

    // Register new user
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

    // Login user
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

    // Get all users (admin only)
    public function getAllUsers()
    {
        $query = 'SELECT id, username, email, role, avatar, created_at FROM users';
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get user by ID
    public function getUserById($id)
    {
        $query = 'SELECT * FROM users WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // get user count 
    public function getUserCount()
    {
        $query = 'SELECT COUNT(*) as count FROM users';
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    // Update user profile
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

    // Check if username exists
    public function usernameExists($username)
    {
        $query = 'SELECT id FROM users WHERE username = :username';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Check if email exists
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
?>