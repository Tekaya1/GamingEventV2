<?php
require_once __DIR__ . '/../models/User.php';

class ProfileController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function updateProfile() {
        session_start();
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../login.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_SESSION['user_id'];
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $current_password = trim($_POST['current_password']);
            $new_password = trim($_POST['new_password']);
            $confirm_password = trim($_POST['confirm_password']);
            
            // Get current user data
            $currentUser = $this->user->getUserById($id);
            
            // Check if username is changed and already exists
            if ($currentUser['username'] !== $username && $this->user->usernameExists($username)) {
                header('Location: ../player/profile.php?error=username_taken');
                exit();
            }
            
            // Check if email is changed and already exists
            if ($currentUser['email'] !== $email && $this->user->emailExists($email)) {
                header('Location: ../player/profile.php?error=email_taken');
                exit();
            }
            
            // Handle password change if current password is provided
            if (!empty($current_password)) {
                if (!password_verify($current_password, $currentUser['password'])) {
                    header('Location: ../player/profile.php?error=current_password');
                    exit();
                }
                
                if ($new_password !== $confirm_password) {
                    header('Location: ../player/profile.php?error=password_mismatch');
                    exit();
                }
                
                if (strlen($new_password) < 8) {
                    header('Location: ../player/profile.php?error=password_length');
                    exit();
                }
                
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                
                // Update password
                $query = 'UPDATE users SET password = :password WHERE id = :id';
                $stmt = $this->user->connect()->prepare($query);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }
            
            // Handle avatar upload
            $avatar = $currentUser['avatar'];
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/images/avatars/';
                $fileExt = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid() . '.' . $fileExt;
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
                    // Delete old avatar if it's not the default one
                    if ($avatar !== 'default.png') {
                        @unlink($uploadDir . $avatar);
                    }
                    $avatar = $fileName;
                }
            }
            
            // Update profile
            if ($this->user->updateProfile($id, $username, $email, $avatar)) {
                // Update session username if changed
                if ($_SESSION['username'] !== $username) {
                    $_SESSION['username'] = $username;
                }
                
                header('Location: ../views/player/profile.php?success=1');
                exit();
            } else {
                header('Location: ../views/player/profile.php?error=update_failed');
                exit();
            }
        }
    }
}

// Handle actions
$profileController = new ProfileController();
$profileController->updateProfile();
?>