<?php
// 1. Sécurité : accès admin uniquement
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Charger les dépendances
require_once '../../models/User.php';
require_once '../../models/Game.php'; 
require_once '../../includes/header.php';

$userModel = new User();
$currentUser = $userModel->getUserById($_SESSION['user_id']);

$gameModel = new Game();
$games = $gameModel->getAllGames();
?>

<!-- 3. Interface -->
 <div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
 <?php
  include 'sidebar.php';
  ?> 

<div class="container mx-auto mt-10 p-6 bg-gray-800 text-white rounded-lg">
    <h2 class="text-2xl font-bold mb-6">Add New Game</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-600 text-white p-3 rounded mb-4">Game added successfully!</div>
    <?php endif; ?>

    <!-- Formulaire -->
    <form action="<?php echo BASE_URL; ?>controllers/gameController.php?action=create" method="POST" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block mb-1">Game Name</label>
                <input type="text" name="name" class="w-full p-2 rounded text-black" required>
            </div>
            <div>
                <label for="type" class="block mb-1">Type</label>
                <input type="text" name="type" class="w-full p-2 rounded text-black">
            </div>
            <div>
                <label for="platform" class="block mb-1">Platform</label>
                <input type="text" name="platform" class="w-full p-2 rounded text-black">
            </div>
            <div>
                <label for="image" class="block mb-1">Image</label>
                <input type="file" name="image" class="w-full p-2 bg-gray-700 rounded">
            </div>
        </div>
        <button type="submit" class="mt-6 bg-red-600 hover:bg-red-700 px-4 py-2 rounded">Add Game</button>
    </form>

    <!-- 4. Liste des jeux existants -->
<div class="container mx-auto mt-10 p-6 bg-gray-800 text-white rounded-lg">
    <h2 class="text-xl font-semibold mb-4">Existing Games</h2>

    <?php if (count($games) > 0): ?>
        <table class="w-full table-auto bg-gray-900 text-white rounded">
            <thead>
                <tr class="bg-gray-700 text-left">
                    <th class="p-3">Image</th>
                    <th class="p-3">Name</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Platform</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $game): ?>
                    <tr class="border-t border-gray-600 hover:bg-gray-800">
                        <td class="p-3">
                            <img src="<?php echo BASE_URL . 'assets/images/games/' . htmlspecialchars($game['image']); ?>" class="w-16 h-16 object-cover rounded" alt="Game Image">
                        </td>
                        <td class="p-3"><?php echo htmlspecialchars($game['name']); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($game['type']); ?></td>
                        <td class="p-3"><?php echo htmlspecialchars($game['platform']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-gray-300 mt-4">No games available yet.</p>
    <?php endif; ?>
    
</div>
</div>


</div>
</div>

<?php
require_once '../../includes/footer.php';
?>
