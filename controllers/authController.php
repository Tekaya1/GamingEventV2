<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function login() {
        
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        if ($this->user->login($username, $password)) {
            session_start();
            // Regenerate session ID after successful login
            session_regenerate_id(true);

            
            // Set secure session variables
            $_SESSION['user_id'] = $this->user->getId();
            $_SESSION['username'] = $this->user->getUsername();
            $_SESSION['role'] = $this->user->getRole();
            $_SESSION['last_activity'] = time();
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            
            // Redirect based on role
            switch ($_SESSION['role']) {
                case 'admin':
                    header('Location: ../views/admin/dashboard.php');
                    break;
                case 'player':
                    header('Location: ../views/player/dashboard.php');
                    break;
                default:
                    header('Location: ../index.php');
            }
            exit();
        } else {
            $_SESSION['login_error'] = 'Invalid username or password';
            header('Location: ../views/auth/login.php');
            exit();
        }
    }
}

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $confirm_password = trim($_POST['confirm_password']);
            
            // Validate passwords match
            if ($password !== $confirm_password) {
                header('Location: ../register.php?error=password_mismatch');
                exit();
            }
            
            // Check if username exists
            if ($this->user->usernameExists($username)) {
                header('Location: ../register.php?error=username_taken');
                exit();
            }
            
            // Check if email exists
            if ($this->user->emailExists($email)) {
                header('Location: ../register.php?error=email_taken');
                exit();
            }
            
            // Register user
            if ($this->user->register($username, $email, $password)) {
                // Auto-login after registration
                if ($this->user->login($username, $password)) {
                    header('Location: ../views/player/dashboard.php');
                    exit();
                }
            }
            
            header('Location: ../register.php?error=registration_failed');
            exit();
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ../index.php');
        exit();
    }
}

// Handle actions
if (isset($_GET['action'])) {
    $authController = new AuthController();
    $action = $_GET['action'];
    
    switch ($action) {
        case 'login':
            $authController->login();
            break;
        case 'register':
            $authController->register();
            break;
        case 'logout':
            $authController->logout();
            break;
        default:
            header('Location: ../index.php');
            exit();
    }
}
?>