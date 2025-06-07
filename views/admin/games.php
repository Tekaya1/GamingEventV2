<?php
// 1. Sécurité : accès admin uniquement
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Dépendances
require_once '../../models/User.php';
require_once '../../models/Game.php';
require_once '../../includes/header.php';

$userModel = new User();
$currentUser = $userModel->getUserById($_SESSION['user_id']);

$gameModel = new Game();
$games = $gameModel->getAllGames();
?>

<!-- 3. Interface principale -->
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <?php include 'sidebar.php'; ?> 

        <div class="flex-1">

            <!-- ✅ Alertes -->
            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-600 text-white p-3 rounded mb-4">
                    <?php
                    if ($_GET['success'] == 1) echo "🎮 Game added successfully!";
                    elseif ($_GET['success'] == 2) echo "✅ Game updated successfully!";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="bg-<?php echo $_GET['deleted'] == 1 ? 'red' : 'gray'; ?>-600 text-white p-3 rounded mb-4">
                    <?php echo $_GET['deleted'] == 1 ? "🗑️ Game deleted successfully!" : "⚠️ Failed to delete the game."; ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire d'ajout -->
            <div class="p-6 bg-gray-800 text-white rounded-lg mb-10">
                <h2 class="text-2xl font-bold mb-6">Add New Game</h2>

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
            </div>

            <!-- Liste des jeux -->
            <div class="p-6 bg-gray-800 text-white rounded-lg">
                <h2 class="text-xl font-semibold mb-4">Existing Games</h2>

                <?php if (count($games) > 0): ?>
                    <table class="w-full table-auto bg-gray-900 text-white rounded">
                        <thead>
                            <tr class="bg-gray-700 text-left">
                                <th class="p-3">Image</th>
                                <th class="p-3">Name</th>
                                <th class="p-3">Type</th>
                                <th class="p-3">Platform</th>
                                <th class="p-3">Actions</th>
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
                                    <td class="p-3">
                                        <a href="edit_game.php?id=<?php echo $game['id']; ?>" class="text-yellow-400 hover:text-yellow-300 mr-3">Edit</a>
                                        <a href="delete_game.php?id=<?php echo $game['id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this game?');"
                                           class="text-red-400 hover:text-red-300">Delete</a>
                                    </td>
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

<?php require_once '../../includes/footer.php'; ?>
