<?php
require_once '../config/database.php'; // connexion à la BDD
require_once '../config/config.php';   // pour BASE_URL si utilisé

global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    // 1. Récupérer les données du formulaire
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $platform = trim($_POST['platform']);

    // 2. Gérer l'image
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

    // 3. Insérer dans la base de données
    try {
        $stmt = $pdo->prepare("INSERT INTO games (name, type, platform, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $type, $platform, $imageName]);

        // 4. Redirection avec message de succès
        header("Location: " . BASE_URL . "views/admin/games.php?success=1");
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout du jeu : " . $e->getMessage();
    }
} else {
    // Redirection par sécurité si mauvaise requête
    header("Location: " . BASE_URL . "views/admin/games.php");
    exit;
}
