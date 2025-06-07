<?php
session_start();

// 🔐 Vérification admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// 📦 Chargement des ressources
require_once '../../models/User.php';
require_once '../../models/Game.php';
require_once '../../includes/header.php';

$userModel = new User();
$currentUser = $userModel->getUserById($_SESSION['user_id']);

$gameModel = new Game();

// 🧠 Vérifie que l’ID est présent
if (!isset($_GET['id'])) {
    echo "<p class='text-red-500'>Game ID not provided.</p>";
    exit;
}

$gameId = $_GET['id'];
$game = $gameModel->getGameById($gameId);

if (!$game) {
    echo "<p class='text-red-500'>Game not found.</p>";
    exit;
}
?>

<div class="container mx-auto mt-10 p-6 bg-gray-800 text-white rounded-lg">
    <h2 class="text-2xl font-bold mb-6">Edit Game: <?php echo htmlspecialchars($game['name']); ?></h2>

    <form action="<?php echo BASE_URL; ?>controllers/gameController.php?action=update&id=<?php echo $gameId; ?>" method="POST" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block mb-1">Game Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($game['name']); ?>" class="w-full p-2 rounded text-black" required>
            </div>
            <div>
                <label for="type" class="block mb-1">Type</label>
                <input type="text" name="type" value="<?php echo htmlspecialchars($game['type']); ?>" class="w-full p-2 rounded text-black">
            </div>
            <div>
                <label for="platform" class="block mb-1">Platform</label>
                <input type="text" name="platform" value="<?php echo htmlspecialchars($game['platform']); ?>" class="w-full p-2 rounded text-black">
            </div>
            <div>
                <label for="image" class="block mb-1">New Image (optional)</label>
                <input type="file" name="image" class="w-full p-2 bg-gray-700 rounded">
                <p class="text-sm mt-2 text-gray-400">Current image:</p>
                <img src="<?php echo BASE_URL . 'assets/images/games/' . htmlspecialchars($game['image']); ?>" class="w-20 h-20 mt-2 rounded" alt="Current Game Image">
            </div>
        </div>

        <button type="submit" class="mt-6 bg-yellow-500 hover:bg-yellow-600 px-4 py-2 rounded">Update Game</button>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
