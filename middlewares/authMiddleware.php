<?php
function authenticate($requiredRole = null) {
    // Check if session is active
    if (session_status() !== PHP_SESSION_ACTIVE) {
        header('Location: /login.php?error=session_expired');
        exit();
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: ../auth/login.php');
        exit();
    }

    // Verify session security
    if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR'] || 
        $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_unset();
        session_destroy();
        header('Location: ../auth/login.php?error=session_hijack');
        exit();
    }

    // Check session timeout (30 minutes)
    if (time() - $_SESSION['last_activity'] > 1800) {
        session_unset();
        session_destroy();
        header('Location: ../auth/login.php?error=session_timeout');
        exit();
    }

    // Update last activity time
    $_SESSION['last_activity'] = time();

    // Check role if required
    if ($requiredRole && (!isset($_SESSION['role']) || $_SESSION['role'] !== $requiredRole)) {
        header('Location: ../unauthorized.php');
        exit();
    }

    return true;
}