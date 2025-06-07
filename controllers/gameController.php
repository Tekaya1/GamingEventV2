<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../models/Game.php'; // nécessaire pour getGameById

global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    // === CREATE LOGIC ===
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $platform = trim($_POST['platform']);

    $imageName = 'default_game.png';
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../assets/images/games/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageTmp = $_FILES['image']['tmp_name'];
        $imageOriginalName = basename($_FILES['image']['name']);
        $imageName = time() . '_' . $imageOriginalName;

        move_uploaded_file($imageTmp, $uploadDir . $imageName);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO games (name, type, platform, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $type, $platform, $imageName]);
        header("Location: " . BASE_URL . "views/admin/games.php?success=1");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout du jeu : " . $e->getMessage();
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update' && isset($_GET['id'])) {
    // === UPDATE LOGIC ===
    $id = $_GET['id'];
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $platform = trim($_POST['platform']);

    $gameModel = new Game();
    $existingGame = $gameModel->getGameById($id);
    $imageName = $existingGame['image'] ?? 'default_game.png';

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../assets/images/games/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageTmp = $_FILES['image']['tmp_name'];
        $imageOriginalName = basename($_FILES['image']['name']);
        $imageName = time() . '_' . $imageOriginalName;

        move_uploaded_file($imageTmp, $uploadDir . $imageName);
    }

    try {
        $stmt = $pdo->prepare("UPDATE games SET name = ?, type = ?, platform = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $type, $platform, $imageName, $id]);
        header("Location: " . BASE_URL . "views/admin/games.php?success=1");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour : " . $e->getMessage();
    }

} else {
    // Sécurité : mauvaise requête ou accès direct
    header("Location: " . BASE_URL . "views/admin/games.php");
    exit;
}
