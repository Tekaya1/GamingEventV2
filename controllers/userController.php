<?php
// controllers/UserController.php
require_once __DIR__ . '/../models/User.php';

class UserController
{
    private $user;

    public function __construct()
    {
        try {
            $this->user = new User();
        } catch (Exception $e) {
            error_log("UserController constructor error: " . $e->getMessage());
            header('Location: ../views/admin/users.php?error=database_connection_failed');
            exit();
        }
    }

    public function updateUser() {
        session_start();
        // Check if user is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ../login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $role = trim($_POST['role']);
            
            // Handle avatar upload
            $avatar = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/images/avatars/';
                $fileExt = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid() . '.' . $fileExt;
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
                    $avatar = $fileName;
                }
            }
            
            // Get current user data
            $currentUser = $this->user->getUserById($id);
            
            // Check if username is changed and already exists
            if ($currentUser['username'] !== $username && $this->user->usernameExists($username)) {
                header('Location: ../views/admin/users.php?id=' . $id . '&error=username_taken');
                exit();
            }
            
            // Check if email is changed and already exists
            if ($currentUser['email'] !== $email && $this->user->emailExists($email)) {
                header('Location: ../views/admin/users.php?id=' . $id . '&error=email_taken');
                exit();
            }

            // Update user profile
            if ($this->user->updateProfile($id, $username, $email, $avatar)) {
                // Update role if changed
                if ($currentUser['role'] !== $role) {
                    $query = 'UPDATE users SET role = :role WHERE id = :id';
                    $stmt = $this->user->connect()->prepare($query);
                    $stmt->bindParam(':role', $role);
                    $stmt->bindParam(':id', $id);
                    $stmt->execute();
                }
                
                header('Location: ../views/admin/users.php?success=updated');
                exit();
            } else {
                header('Location: ../views/admin/users.php?id=' . $id . '&error=failed');
                exit();
            }
        }
    }

    public function deleteUser()
    {
        session_start();
        // Check if user is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ../login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) $_POST['id'];

            // Prevent deleting own account
            if ($id === $_SESSION['user_id']) {
                header('Location: ../views/admin/users.php?error=self_delete');
                exit();
            }

            try {
                $query = 'DELETE FROM users WHERE id = :id';
                $stmt = $this->user->connect()->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    error_log("User deleted successfully: ID $id");
                    header('Location: ../views/admin/users.php?success=deleted');
                    exit();
                } else {
                    error_log("Failed to delete user: ID $id");
                    header('Location: ../views/admin/users.php?error=failed');
                    exit();
                }
            } catch (Exception $e) {
                error_log("Delete user error: " . $e->getMessage());
                header('Location: ../views/admin/users.php?error=database_error');
                exit();
            }
        }
    }
}

// Handle actions
if (isset($_GET['action'])) {
    $userController = new UserController();
    $action = $_GET['action'];

    switch ($action) {
        case 'update':
            $userController->updateUser();
            break;
        case 'delete':
            $userController->deleteUser();
            break;
        default:
            header('Location: ../index.php');
            exit();
    }
}
?>