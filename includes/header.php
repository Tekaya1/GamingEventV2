<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // ⬅️ session démarrée uniquement si elle n'existe pas encore
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../config/config.php';

$user = new User();
$event = new Event();


// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$currentUser = null;

if ($isLoggedIn) {
    $currentUser = $user->getUserById($_SESSION['user_id']);
    if ($currentUser === false) {
        session_unset();
        session_destroy();
        $isLoggedIn = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaming Events Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        .font-gaming {
            font-family: 'Press Start 2P', cursive;
        }
        .bg-pixel {
            background-color: #1a1a2e;
            background-image: 
                linear-gradient(#2a2a3a 1px, transparent 1px),
                linear-gradient(90deg, #2a2a3a 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <header class="bg-pixel border-b-4 border-purple-600">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <a href="<?php echo BASE_URL; ?>" class="font-gaming text-2xl text-purple-400 hover:text-purple-300 transition">GAME EVENTS</a>
                    <nav class="hidden md:flex space-x-6">
                        <a href="<?php echo BASE_URL; ?>views/events.php" class="text-gray-300 hover:text-white transition">Events</a>
                        <a href="<?php echo BASE_URL; ?>views/leaderboard.php" class="text-gray-300 hover:text-white transition">Leaderboard</a>
                        <a href="<?php echo BASE_URL; ?>views/player/teams.php" class="text-gray-300 hover:text-white transition">My Teams</a>
                        <?php if ($isLoggedIn && $currentUser && $currentUser['role'] === 'player'): ?>
                            <a href="<?php echo BASE_URL; ?>views/player/dashboard.php" class="text-gray-300 hover:text-white transition">My Dashboard</a>
                        <?php endif; ?>
                        <?php if ($isLoggedIn && $currentUser && $currentUser['role'] === 'admin'): ?>
                            <a href="<?php echo BASE_URL; ?>views/admin/dashboard.php" class="text-gray-300 hover:text-white transition">Admin Panel</a>
                        <?php endif; ?>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if ($isLoggedIn && $currentUser): ?>
                        <div class="flex items-center space-x-2">
                            <img src="<?php echo BASE_URL . 'assets/images/avatars/' . htmlspecialchars($currentUser['avatar'] ?? 'default.png'); ?>" 
                                 alt="Avatar" class="w-8 h-8 rounded-full border-2 border-purple-500">
                            <span class="text-sm"><?php echo htmlspecialchars($currentUser['username']); ?></span>
                        </div>
                        <a href="<?php echo BASE_URL; ?>views/auth/logout.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-sm font-medium transition">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>views/auth/login.php" class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded-lg text-sm font-medium transition">Login</a>
                        <a href="<?php echo BASE_URL; ?>views/auth/register.php" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-sm font-medium transition">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <main class="container mx-auto px-4 py-8">
