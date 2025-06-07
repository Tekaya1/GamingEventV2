<?php
// 🔐 Toujours lancer la session AVANT tout require
session_start();

// 🔒 Vérifie la connexion admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// ✅ Sécurité ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: games.php?deleted=0");
    exit;
}

$gameId = (int) $_GET['id'];

// 🔁 Inclusion propre des fichiers
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Game.php';

// 📦 Chargement et suppression
$gameModel = new Game();
$game = $gameModel->getGameById($gameId);

// ✅ Supprimer image (si différente du default)
if ($game && $game['image'] !== 'default_game.png') {
    $imgPath = __DIR__ . '/../../assets/images/games/' . $game['image'];
    if (file_exists($imgPath)) {
        unlink($imgPath);
    }
}

// 📤 Suppression en BDD
$deleted = $gameModel->deleteGame($gameId);

// ✅ Redirection vers games.php avec message
header("Location: games.php?deleted=" . ($deleted ? 1 : 0));
exit;
